<?php

namespace App\Http\Controllers\Api;

use App\Models\Order;
use App\Models\Ordermeta;
use App\Models\QuickSaleOrderItem;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;

/**
 * Additive POS Quick Sale refund API — separate from regular POS product refunds.
 * Same request/response shape as pos-refund-payment (full / item / dollar).
 * items[].order_item_id / item_id = quick_sale_order_items.id
 */
class PosQuickSaleRefundApiController extends PosRefundApiController
{
    public function refundPayment(Request $request): JsonResponse
    {
        $this->posRefundReceiptEmail = null;

        $validator = Validator::make($request->all(), [
            'paymentId'   => 'required|string',
            'email'       => 'nullable|email|max:255',
            'refundAmount'=> 'nullable|numeric|min:0',
            'reason'      => 'nullable|string|max:500',
            'refundType'  => 'nullable|in:full,item,dollar',
            'items'       => 'nullable|array',
            'items.*.order_item_id' => 'nullable|integer|min:1',
            'items.*.item_id'       => 'nullable|integer|min:1',
            'items.*.qty'           => 'nullable|integer|min:1',
            'items.*.amount'        => 'nullable|numeric|min:0.01',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation failed.', 422, [
                'errors' => $validator->errors(),
            ]);
        }

        if ($request->filled('email')) {
            $this->posRefundReceiptEmail = trim((string) $request->input('email'));
        }

        try {
            $order = $this->resolvePosQuickSaleOrderByPaymentId($request->input('paymentId'));
            $this->assertPosQuickSaleOrderRefundable($order);

            $refundType = $this->determinePosRefundType($request);
            $reason = (string) $request->input('reason', '');

            switch ($refundType) {
                case 'item':
                    $payload = $this->executePosQuickSalePartialItemRefund($order, $request, $reason);
                    break;
                case 'dollar':
                    $payload = $this->executePosQuickSalePartialDollarRefund($order, $request, $reason);
                    break;
                default:
                    $payload = $this->executePosQuickSaleFullRefund($order, $request, $reason);
                    break;
            }

            return response()->json($payload);
        } catch (\Throwable $e) {
            \Log::error('POS Quick Sale refund API failed', [
                'payment_id' => $request->input('paymentId'),
                'error' => $e->getMessage(),
            ]);

            return $this->errorResponse($e->getMessage(), 422);
        }
    }

    protected function resolvePosQuickSaleOrderByPaymentId(string $paymentId): Order
    {
        $order = $this->resolvePosOrderByPaymentId($paymentId);
        $order->loadMissing('quickSaleOrderItems');

        if ($order->quickSaleOrderItems->isEmpty()) {
            throw new \Exception('No Quick Sale items found on this POS order.');
        }

        return $order;
    }

    protected function assertPosQuickSaleOrderRefundable(Order $order): void
    {
        $hasRemainingQuickSale = app(\App\Services\QuickSaleOrderService::class)
            ->hasRemainingRefundableLines($order);

        if ((int) $order->payment_status === 5 && !$hasRemainingQuickSale) {
            throw new \Exception('This POS order has already been fully refunded.');
        }

        if ((int) $order->payment_status === 5 && $hasRemainingQuickSale) {
            return;
        }

        if (!in_array((int) $order->payment_status, [1], true)) {
            throw new \Exception('Only captured/paid POS orders can be refunded.');
        }
    }

    protected function executePosQuickSaleFullRefund(Order $order, Request $request, string $reason): array
    {
        $hasRegularItems = $order->orderitems->isNotEmpty();
        $alreadyRefunded = $this->getTotalPartialRefundedAmount($order);

        // QS-only order with no prior partials: same full-refund path as regular POS products.
        if (!$hasRegularItems && $alreadyRefunded <= 0.01) {
            return $this->executePosFullRefund($order, $request, $reason);
        }

        $refundDetails = $this->buildQuickSaleRemainingRefundDetails($order);

        return $this->executePosQuickSaleRefundWithDetails($order, $request, $reason, $refundDetails, 'full');
    }

    protected function executePosQuickSalePartialItemRefund(Order $order, Request $request, string $reason): array
    {
        $selectedItems = $this->normalizePosRefundItems($request);

        foreach ($selectedItems as $index => $item) {
            if (empty($item['qty']) || (int) $item['qty'] <= 0) {
                throw new \Exception('Each item refund entry must include qty greater than 0.');
            }
            $selectedItems[$index]['qty'] = (int) $item['qty'];
        }

        if (empty($selectedItems)) {
            throw new \Exception('Please provide items with order_item_id and qty for item refunds.');
        }

        $refundDetails = $this->buildQuickSalePartialRefundDetails($order, $selectedItems);

        return $this->executePosQuickSaleRefundWithDetails($order, $request, $reason, $refundDetails, 'item');
    }

    protected function executePosQuickSalePartialDollarRefund(Order $order, Request $request, string $reason): array
    {
        $selectedItems = $this->normalizePosRefundItems($request);

        if (empty($selectedItems) && $request->filled('refundAmount')) {
            $singleItemId = (int) $request->input('order_item_id', 0);
            if ($singleItemId <= 0 && $order->quickSaleOrderItems->count() === 1) {
                $singleItemId = (int) $order->quickSaleOrderItems->first()->id;
            }

            if ($singleItemId <= 0) {
                throw new \Exception('Provide items with order_item_id and amount, or order_item_id with refundAmount.');
            }

            $selectedItems[] = [
                'item_id' => $singleItemId,
                'amount'  => $this->centsToDollars((float) $request->input('refundAmount')),
            ];
        }

        if (empty($selectedItems)) {
            throw new \Exception('Please provide items with order_item_id and amount for dollar refunds.');
        }

        $refundDetails = $this->buildQuickSalePartialDollarRefundDetails($order, $selectedItems);

        return $this->executePosQuickSaleRefundWithDetails($order, $request, $reason, $refundDetails, 'dollar');
    }

    protected function executePosQuickSaleRefundWithDetails(
        Order $order,
        Request $request,
        string $reason,
        array $refundDetails,
        string $refundType
    ): array {
        if ($refundType === 'dollar' && $request->filled('refundAmount')) {
            $requestedCents = (int) round((float) $request->input('refundAmount'));
            $calculatedCents = (int) round((float) $refundDetails['grand_total'] * 100);

            if (abs($requestedCents - $calculatedCents) > 1) {
                throw new \Exception('refundAmount does not match the calculated dollar refund total.');
            }
        }

        if ($this->isPosCashOrder($order)) {
            $netRefundable = round((float) $order->total, 2);
            $alreadyRefunded = $this->getTotalPartialRefundedAmount($order);
            if (($alreadyRefunded + $refundDetails['grand_total']) > ($netRefundable + 0.01)) {
                throw new \Exception('Refund amount exceeds the remaining refundable balance for this order.');
            }

            $transactionLog = $this->buildPosCashRefundTransactionLog($order, (float) $refundDetails['grand_total'], $reason);
            $this->finalizeQuickSalePartialRefundedOrder($order, $transactionLog, $refundDetails, $refundType);
            $refundId = $transactionLog['id'] ?? null;
        } else {
            $refund = $this->createPosStripeRefund($order, (float) $refundDetails['grand_total'], $reason);
            $this->finalizeQuickSalePartialRefundedOrder($order, $refund->toArray(), $refundDetails, $refundType);
            $refundId = $refund->id ?? null;
        }

        $this->runPosRefundSideEffects(
            $order,
            (float) $refundDetails['grand_total'],
            $refundId,
            collect(),
            $refundDetails,
            $refundType === 'full' ? 'item' : $refundType
        );

        return $this->formatPosRefundResponse(
            $order,
            (string) ($order->transaction_id ?: (string) $order->id),
            (string) $refundId,
            (float) $refundDetails['grand_total'],
            'succeeded',
            $refundType,
            $refundDetails
        );
    }

    protected function buildQuickSalePartialRefundDetails(Order $order, array $selectedItems): array
    {
        $items = [];
        $itemTotal = 0;
        $taxTotal = 0;

        foreach ($selectedItems as $selectedItem) {
            $itemId = (int) ($selectedItem['item_id'] ?? 0);
            $qty = (int) ($selectedItem['qty'] ?? 0);

            if ($itemId <= 0 || $qty <= 0) {
                throw new \Exception('Invalid item selection for partial refund.');
            }

            $line = $this->findQuickSaleOrderItem($order, $itemId);
            $unitAmount = round((float) $line->unit_amount, 2);
            $remainingLineTotal = $this->getRemainingQuickSaleLineTotal($order, $line);
            $remainingQty = $unitAmount > 0 ? (int) floor(($remainingLineTotal + 0.0001) / $unitAmount) : 0;

            if ($qty > $remainingQty) {
                throw new \Exception('Selected quantity exceeds the remaining refundable quantity for one or more items.');
            }

            $originalQty = max(1, (int) $line->qty);
            $lineTaxTotal = round((float) $line->tax_amount, 2);
            $refundLineAmount = round($unitAmount * $qty, 2);
            $refundLineTax = round(($lineTaxTotal / $originalQty) * $qty, 2);

            $items[] = [
                'item_id' => $itemId,
                'type' => 'quick_sale',
                'qty' => $qty,
                'label' => $qty . ' x ' . ($line->title ?: $line->descriptor_name ?: 'Quick Sale Item'),
                'amount' => $refundLineAmount,
                'tax' => $refundLineTax,
            ];

            $itemTotal += $refundLineAmount;
            $taxTotal += $refundLineTax;
        }

        if (empty($items)) {
            throw new \Exception('Please select at least one item to refund.');
        }

        return [
            'items' => $items,
            'item_total' => round($itemTotal, 2),
            'tax_total' => round($taxTotal, 2),
            'grand_total' => round($itemTotal + $taxTotal, 2),
        ];
    }

    protected function buildQuickSalePartialDollarRefundDetails(Order $order, array $selectedItems): array
    {
        $items = [];
        $itemTotal = 0;
        $taxTotal = 0;

        foreach ($selectedItems as $selectedItem) {
            $itemId = (int) ($selectedItem['item_id'] ?? 0);
            $refundAmount = round((float) ($selectedItem['amount'] ?? 0), 2);

            if ($itemId <= 0 || $refundAmount <= 0) {
                continue;
            }

            $line = $this->findQuickSaleOrderItem($order, $itemId);
            $remainingLineTotal = $this->getRemainingQuickSaleLineTotal($order, $line);

            if ($refundAmount > ($remainingLineTotal + 0.01)) {
                throw new \Exception('Refund amount cannot be greater than the line item total.');
            }

            $unitAmount = round((float) $line->unit_amount, 2);
            $originalQty = max(1, (int) $line->qty);
            $lineTotal = round($unitAmount * $originalQty, 2);
            $lineTaxTotal = round((float) $line->tax_amount, 2);
            $refundLineTax = $lineTotal > 0 ? round(($refundAmount / $lineTotal) * $lineTaxTotal, 2) : 0;
            $ticketCancelQty = $unitAmount > 0 ? (int) floor($refundAmount / $unitAmount) : 0;
            $ticketCancelQty = min($ticketCancelQty, $originalQty);

            $items[] = [
                'item_id' => $itemId,
                'type' => 'quick_sale',
                'amount' => $refundAmount,
                'qty' => $ticketCancelQty,
                'label' => $line->title ?: $line->descriptor_name ?: 'Quick Sale Item',
                'tax' => $refundLineTax,
            ];

            $itemTotal += $refundAmount;
            $taxTotal += $refundLineTax;
        }

        if (empty($items)) {
            throw new \Exception('Please enter a refund amount for at least one item.');
        }

        return [
            'items' => $items,
            'item_total' => round($itemTotal, 2),
            'tax_total' => round($taxTotal, 2),
            'grand_total' => round($itemTotal + $taxTotal, 2),
        ];
    }

    protected function buildQuickSaleRemainingRefundDetails(Order $order): array
    {
        $selectedItems = [];

        foreach ($order->quickSaleOrderItems as $line) {
            $remaining = $this->getRemainingQuickSaleLineTotal($order, $line);
            if ($remaining <= 0.01) {
                continue;
            }

            $selectedItems[] = [
                'item_id' => (int) $line->id,
                'amount' => $remaining,
            ];
        }

        if (empty($selectedItems)) {
            throw new \Exception('Quick Sale items on this order have already been fully refunded.');
        }

        return $this->buildQuickSalePartialDollarRefundDetails($order, $selectedItems);
    }

    protected function findQuickSaleOrderItem(Order $order, int $itemId): QuickSaleOrderItem
    {
        $line = $order->quickSaleOrderItems->firstWhere('id', $itemId);

        if (!$line) {
            throw new \Exception('One or more selected items were not found on this order.');
        }

        return $line;
    }

    protected function getQuickSalePartialRefundedQuantities(Order $order): array
    {
        $meta = Ordermeta::where('order_id', $order->id)->where('key', 'quick_sale_partial_refunded_items')->first();

        return json_decode($meta->value ?? '{}', true) ?: [];
    }

    protected function getQuickSalePartialDollarRefundedAmounts(Order $order): array
    {
        $meta = Ordermeta::where('order_id', $order->id)->where('key', 'quick_sale_partial_dollar_refunded_items')->first();

        return json_decode($meta->value ?? '{}', true) ?: [];
    }

    protected function getRemainingQuickSaleLineTotal(Order $order, QuickSaleOrderItem $line): float
    {
        $unitAmount = round((float) $line->unit_amount, 2);
        $lineTotal = round($unitAmount * max(1, (int) $line->qty), 2);
        $refundedQuantities = $this->getQuickSalePartialRefundedQuantities($order);
        $refundedDollarAmounts = $this->getQuickSalePartialDollarRefundedAmounts($order);

        $qtyRefundedValue = ((int) ($refundedQuantities[$line->id] ?? 0)) * $unitAmount;
        $dollarRefundedValue = (float) ($refundedDollarAmounts[$line->id] ?? 0);

        return max(0, round($lineTotal - $qtyRefundedValue - $dollarRefundedValue, 2));
    }

    protected function finalizeQuickSalePartialRefundedOrder(
        Order $order,
        array $transactionLog,
        array $refundDetails,
        string $refundType
    ): Collection {
        $refundedQuantities = $this->getQuickSalePartialRefundedQuantities($order);
        $refundedDollarAmounts = $this->getQuickSalePartialDollarRefundedAmounts($order);

        foreach ($refundDetails['items'] as $item) {
            $itemId = (int) ($item['item_id'] ?? 0);
            if ($itemId <= 0) {
                continue;
            }

            if ($refundType === 'item') {
                $refundedQuantities[$itemId] = (int) ($refundedQuantities[$itemId] ?? 0) + (int) ($item['qty'] ?? 0);
            } else {
                $refundedDollarAmounts[$itemId] = round(
                    (float) ($refundedDollarAmounts[$itemId] ?? 0) + (float) ($item['amount'] ?? 0),
                    2
                );
            }
        }

        $qtyMeta = Ordermeta::firstOrNew([
            'order_id' => $order->id,
            'key' => 'quick_sale_partial_refunded_items',
        ]);
        $qtyMeta->value = json_encode($refundedQuantities);
        $qtyMeta->save();

        $dollarMeta = Ordermeta::firstOrNew([
            'order_id' => $order->id,
            'key' => 'quick_sale_partial_dollar_refunded_items',
        ]);
        $dollarMeta->value = json_encode($refundedDollarAmounts);
        $dollarMeta->save();

        $partialRefundLogsMeta = Ordermeta::where('order_id', $order->id)->where('key', 'partial_refund_logs')->first();
        $logs = json_decode($partialRefundLogsMeta->value ?? '[]', true) ?: [];
        $logs[] = [
            'amount' => $refundDetails['grand_total'],
            'type' => $refundType === 'item' ? 'item' : 'dollar',
            'source' => 'quick_sale',
            'items' => $refundDetails['items'],
            'stripe_refund_id' => $transactionLog['id'] ?? null,
            'refunded_at' => Carbon::now()->setTimezone(config('app.timezone'))->toDateTimeString(),
        ];

        if ($partialRefundLogsMeta) {
            $partialRefundLogsMeta->value = json_encode($logs);
            $partialRefundLogsMeta->save();
        } else {
            $partialRefundLogsMeta = new Ordermeta;
            $partialRefundLogsMeta->order_id = $order->id;
            $partialRefundLogsMeta->key = 'partial_refund_logs';
            $partialRefundLogsMeta->value = json_encode($logs);
            $partialRefundLogsMeta->save();
        }

        $this->persistRefundTransactionLog($order, $transactionLog);

        if ($this->arePosOrderLinesFullyRefunded($order, $refundedQuantities, $refundedDollarAmounts)) {
            $order->payment_status = 5;
            $order->status_id = $order->status_id == 1 ? 1 : 2;
            $order->refunded_at = Carbon::now()->setTimezone(config('app.timezone'));
        } elseif ((int) $order->payment_status === 5) {
            $order->payment_status = 1;
            $order->refunded_at = null;
        }

        $order->save();

        return collect();
    }

    protected function arePosOrderLinesFullyRefunded(
        Order $order,
        array $qsRefundedQuantities,
        array $qsRefundedDollarAmounts
    ): bool {
        $regularRefundedQuantities = $this->getPartialRefundedQuantities($order);
        $regularRefundedDollarAmounts = $this->getPartialDollarRefundedAmounts($order);

        foreach ($order->orderitems as $orderItem) {
            $unitAmount = $this->getOrderItemUnitAmount($orderItem);
            $lineTotal = $unitAmount * (int) $orderItem->qty;
            $qtyRefundedValue = ((int) ($regularRefundedQuantities[$orderItem->id] ?? 0)) * $unitAmount;
            $dollarRefundedValue = (float) ($regularRefundedDollarAmounts[$orderItem->id] ?? 0);

            if (($qtyRefundedValue + $dollarRefundedValue) < ($lineTotal - 0.01)) {
                return false;
            }
        }

        foreach ($order->quickSaleOrderItems as $line) {
            $unitAmount = round((float) $line->unit_amount, 2);
            $lineTotal = round($unitAmount * max(1, (int) $line->qty), 2);
            $qtyRefundedValue = ((int) ($qsRefundedQuantities[$line->id] ?? 0)) * $unitAmount;
            $dollarRefundedValue = (float) ($qsRefundedDollarAmounts[$line->id] ?? 0);

            if (($qtyRefundedValue + $dollarRefundedValue) < ($lineTotal - 0.01)) {
                return false;
            }
        }

        return true;
    }
}
