<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Seller\OrderController;
use App\Lib\NotifyToUser;
use App\Models\Getway;
use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Stripe\Stripe;

/**
 * Additive POS refund API — separate from seller web refund and other POS endpoints.
 * Supports full, per-item, and dollar-amount refunds for POS orders (order_from 4/5).
 */
class PosRefundApiController extends OrderController
{
    public function refundPayment(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'paymentId'   => 'required|string',
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

        try {
            $order = $this->resolvePosOrderByPaymentId($request->input('paymentId'));
            $this->assertPosOrderRefundable($order);

            $refundType = $this->determinePosRefundType($request);
            $reason = (string) $request->input('reason', '');

            switch ($refundType) {
                case 'item':
                    $payload = $this->executePosPartialItemRefund($order, $request, $reason);
                    break;
                case 'dollar':
                    $payload = $this->executePosPartialDollarRefund($order, $request, $reason);
                    break;
                default:
                    $payload = $this->executePosFullRefund($order, $request, $reason);
                    break;
            }

            return response()->json($payload);
        } catch (\Throwable $e) {
            \Log::error('POS refund API failed', [
                'payment_id' => $request->input('paymentId'),
                'error' => $e->getMessage(),
            ]);

            return $this->errorResponse($e->getMessage(), 422);
        }
    }

    protected function resolvePosOrderByPaymentId(string $paymentId): Order
    {
        $query = Order::with('orderstatus', 'orderlasttrans', 'orderitems.term', 'getway', 'user', 'shippingwithinfo', 'ordermeta', 'schedule')
            ->whereIn('order_from', [4, 5]);

        $order = (clone $query)->where('transaction_id', $paymentId)->first();

        if (!$order && preg_match('/^(pi_|ch_)/', $paymentId)) {
            $order = (clone $query)->where('transaction_id', 'like', '%' . $paymentId . '%')->first();
        }

        if (!$order && ctype_digit($paymentId)) {
            $order = (clone $query)->where('id', (int) $paymentId)->first();
        }

        if (!$order) {
            $normalizedInvoice = ltrim($paymentId, '0');
            if ($normalizedInvoice !== '') {
                $order = (clone $query)->where('id', (int) $normalizedInvoice)->first();
            }
        }

        if (!$order) {
            throw new \Exception('POS order not found for the provided paymentId.');
        }

        return $order;
    }

    protected function assertPosOrderRefundable(Order $order): void
    {
        if ((int) $order->payment_status === 5) {
            throw new \Exception('This POS order has already been fully refunded.');
        }

        if (!in_array((int) $order->payment_status, [1], true)) {
            throw new \Exception('Only captured/paid POS orders can be refunded.');
        }
    }

    protected function determinePosRefundType(Request $request): string
    {
        $explicit = strtolower((string) $request->input('refundType', ''));

        if (in_array($explicit, ['full', 'item', 'dollar'], true)) {
            return $explicit;
        }

        $items = $this->normalizePosRefundItems($request);
        if (!empty($items)) {
            foreach ($items as $item) {
                if (array_key_exists('amount', $item)) {
                    return 'dollar';
                }
                if (array_key_exists('qty', $item)) {
                    return 'item';
                }
            }
        }

        return 'full';
    }

    protected function normalizePosRefundItems(Request $request): array
    {
        $items = $request->input('items', []);

        if (!is_array($items)) {
            return [];
        }

        $normalized = [];

        foreach ($items as $item) {
            if (!is_array($item)) {
                continue;
            }

            $orderItemId = (int) ($item['order_item_id'] ?? $item['item_id'] ?? 0);
            if ($orderItemId <= 0) {
                continue;
            }

            $row = ['item_id' => $orderItemId];

            if (isset($item['qty'])) {
                $row['qty'] = (int) $item['qty'];
            }

            if (isset($item['amount'])) {
                $row['amount'] = round((float) $item['amount'], 2);
            }

            $normalized[] = $row;
        }

        return $normalized;
    }

    protected function executePosFullRefund(Order $order, Request $request, string $reason): array
    {
        if ($this->isPosCashOrder($order)) {
            return $this->executePosCashFullRefund($order, $reason);
        }

        return $this->executePosStripeFullRefund($order, $request, $reason);
    }

    protected function executePosPartialItemRefund(Order $order, Request $request, string $reason): array
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

        $refundDetails = $this->buildPartialRefundDetails($order, $selectedItems);

        if ($this->isPosCashOrder($order)) {
            $netRefundable = round((float) $order->total, 2);
            $alreadyRefunded = $this->getTotalPartialRefundedAmount($order);
            if (($alreadyRefunded + $refundDetails['grand_total']) > ($netRefundable + 0.01)) {
                throw new \Exception('Refund amount exceeds the remaining refundable balance for this order.');
            }

            $transactionLog = $this->buildPosCashRefundTransactionLog($order, (float) $refundDetails['grand_total'], $reason);
            $cancelledTickets = $this->finalizePartialRefundedOrder($order, $transactionLog, $refundDetails);
            $refundId = $transactionLog['id'] ?? null;
        } else {
            $refund = $this->createPosStripeRefund($order, (float) $refundDetails['grand_total'], $reason);
            $cancelledTickets = $this->finalizePartialRefundedOrder($order, $refund->toArray(), $refundDetails);
            $refundId = $refund->id ?? null;
        }

        $this->runPosRefundSideEffects($order, (float) $refundDetails['grand_total'], $refundId, $cancelledTickets, $refundDetails, 'item');

        return $this->formatPosRefundResponse(
            $order,
            (string) ($order->transaction_id ?: (string) $order->id),
            (string) $refundId,
            (float) $refundDetails['grand_total'],
            'succeeded',
            'item',
            $refundDetails
        );
    }

    protected function executePosPartialDollarRefund(Order $order, Request $request, string $reason): array
    {
        $selectedItems = $this->normalizePosRefundItems($request);

        if (empty($selectedItems) && $request->filled('refundAmount')) {
            $singleItemId = (int) $request->input('order_item_id', 0);
            if ($singleItemId <= 0 && $order->orderitems->count() === 1) {
                $singleItemId = (int) $order->orderitems->first()->id;
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

        $refundDetails = $this->buildPartialDollarRefundDetails($order, $selectedItems);

        $requestedCents = $request->filled('refundAmount') ? (int) round((float) $request->input('refundAmount')) : null;
        $calculatedCents = (int) round((float) $refundDetails['grand_total'] * 100);

        if ($requestedCents !== null && abs($requestedCents - $calculatedCents) > 1) {
            throw new \Exception('refundAmount does not match the calculated dollar refund total.');
        }

        if ($this->isPosCashOrder($order)) {
            $netRefundable = round((float) $order->total, 2);
            $alreadyRefunded = $this->getTotalPartialRefundedAmount($order);
            if (($alreadyRefunded + $refundDetails['grand_total']) > ($netRefundable + 0.01)) {
                throw new \Exception('Refund amount exceeds the remaining refundable balance for this order.');
            }

            $transactionLog = $this->buildPosCashRefundTransactionLog($order, (float) $refundDetails['grand_total'], $reason);
            $cancelledTickets = $this->finalizePartialDollarRefundedOrder($order, $transactionLog, $refundDetails);
            $refundId = $transactionLog['id'] ?? null;
        } else {
            $refund = $this->createPosStripeRefund($order, (float) $refundDetails['grand_total'], $reason);
            $cancelledTickets = $this->finalizePartialDollarRefundedOrder($order, $refund->toArray(), $refundDetails);
            $refundId = $refund->id ?? null;
        }

        $this->runPosRefundSideEffects($order, (float) $refundDetails['grand_total'], $refundId, $cancelledTickets, $refundDetails, 'dollar');

        return $this->formatPosRefundResponse(
            $order,
            (string) ($order->transaction_id ?: (string) $order->id),
            (string) $refundId,
            (float) $refundDetails['grand_total'],
            'succeeded',
            'dollar',
            $refundDetails
        );
    }

    protected function executePosStripeFullRefund(Order $order, Request $request, string $reason): array
    {
        $ordermeta = json_decode(optional($order->ordermeta)->value ?? '{}');
        $refundAmountDollars = $this->calculateRefundNetTotal($order, $ordermeta);

        if ($request->filled('refundAmount')) {
            $requestedDollars = $this->centsToDollars((float) $request->input('refundAmount'));
            if (abs($requestedDollars - $refundAmountDollars) > 0.02) {
                throw new \Exception('refundAmount does not match the remaining full refund total for this order.');
            }
        }

        $alreadyRefunded = $this->getTotalPartialRefundedAmount($order);
        if ($alreadyRefunded > 0.01) {
            throw new \Exception('This order has partial refunds; use item or dollar refund type for the remaining balance.');
        }

        $gateway = $this->resolveStripeGateway();
        $gatewayData = json_decode($gateway->data ?? '{}');

        Stripe::setApiKey($gateway->test_mode == 1 ? $gatewayData->test_secret_key : $gatewayData->secret_key);

        $chargeId = $this->resolveStripeChargeId($order);
        $charge = \Stripe\Charge::retrieve($chargeId);

        if ($charge->refunded) {
            $cancelledTickets = $this->finalizeRefundedOrder($order, $charge->toArray());
            $refundId = $charge->refunds->data[0]->id ?? null;
        } else {
            $refundAmountCents = max(1, (int) round($refundAmountDollars * 100));
            $coverFee = (float) ($ordermeta->cover_fee ?? 0);

            $refundParams = [
                'charge' => $chargeId,
                'amount' => $refundAmountCents,
            ];

            if ($reason !== '') {
                $refundParams['reason'] = 'requested_by_customer';
                $refundParams['metadata'] = ['pos_refund_reason' => $reason];
            }

            if ($coverFee <= 0) {
                $refundParams['refund_application_fee'] = true;
            }

            $refundParams['reverse_transfer'] = true;

            $refund = \Stripe\Refund::create($refundParams);

            if (!in_array($refund->status, ['succeeded', 'pending'], true)) {
                throw new \Exception('Stripe refund was not completed.');
            }

            $cancelledTickets = $this->finalizeRefundedOrder($order, $refund->toArray());
            $refundId = $refund->id;
            $refundAmountDollars = round($refund->amount / 100, 2);
        }

        $this->runPosRefundSideEffects($order, $refundAmountDollars, $refundId, $cancelledTickets, null, 'full');

        return $this->formatPosRefundResponse(
            $order,
            (string) $order->transaction_id,
            (string) $refundId,
            $refundAmountDollars,
            'succeeded',
            'full'
        );
    }

    protected function executePosCashFullRefund(Order $order, string $reason): array
    {
        $alreadyRefunded = $this->getTotalPartialRefundedAmount($order);
        if ($alreadyRefunded > 0.01) {
            throw new \Exception('This order has partial refunds; use item or dollar refund type for the remaining balance.');
        }

        $refundAmountDollars = round((float) $order->total, 2);
        $transactionLog = $this->buildPosCashRefundTransactionLog($order, $refundAmountDollars, $reason);
        $cancelledTickets = $this->finalizeRefundedOrder($order, $transactionLog);
        $refundId = $transactionLog['id'] ?? null;

        $this->runPosRefundSideEffects($order, $refundAmountDollars, $refundId, $cancelledTickets, null, 'full');

        return $this->formatPosRefundResponse(
            $order,
            (string) $order->id,
            (string) $refundId,
            $refundAmountDollars,
            'succeeded',
            'full'
        );
    }

    protected function createPosStripeRefund(Order $order, float $refundAmountDollars, string $reason)
    {
        $ordermeta = json_decode(optional($order->ordermeta)->value ?? '{}');
        $netRefundable = $this->calculateRefundNetTotal($order, $ordermeta);
        $alreadyRefunded = $this->getTotalPartialRefundedAmount($order);

        if (($alreadyRefunded + $refundAmountDollars) > ($netRefundable + 0.01)) {
            throw new \Exception('Refund amount exceeds the remaining refundable balance for this order.');
        }

        $gateway = $this->resolveStripeGateway();
        $gatewayData = json_decode($gateway->data ?? '{}');

        Stripe::setApiKey($gateway->test_mode == 1 ? $gatewayData->test_secret_key : $gatewayData->secret_key);

        $chargeId = $this->resolveStripeChargeId($order);
        $refundAmountCents = max(1, (int) round($refundAmountDollars * 100));

        $refundParams = [
            'charge' => $chargeId,
            'amount' => $refundAmountCents,
            'reverse_transfer' => true,
        ];

        if ($reason !== '') {
            $refundParams['reason'] = 'requested_by_customer';
            $refundParams['metadata'] = ['pos_refund_reason' => $reason];
        }

        $refund = \Stripe\Refund::create($refundParams);

        if (!in_array($refund->status, ['succeeded', 'pending'], true)) {
            throw new \Exception('Stripe refund was not completed.');
        }

        return $refund;
    }

    protected function resolveStripeGateway(): Getway
    {
        $gateway = Getway::where('status', '!=', 0)
            ->where('namespace', '=', 'App\Lib\Stripe')
            ->first();

        if (!$gateway) {
            throw new \Exception('Stripe payment gateway is not configured.');
        }

        $gatewayData = json_decode($gateway->data ?? '{}');
        if (!$gatewayData) {
            throw new \Exception('Stripe gateway credentials are missing.');
        }

        return $gateway;
    }

    protected function resolveStripeChargeId(Order $order): string
    {
        $transactionId = $order->transaction_id;
        if (!$transactionId) {
            throw new \Exception('No transaction ID provided for this POS order.');
        }

        if (str_starts_with($transactionId, 'pi_')) {
            $paymentIntent = \Stripe\PaymentIntent::retrieve($transactionId);

            if ($paymentIntent->status !== 'succeeded') {
                throw new \Exception('Payment is not in a refundable state.');
            }

            $chargeId = $paymentIntent->latest_charge;
        } elseif (str_starts_with($transactionId, 'ch_')) {
            $chargeId = $transactionId;
        } else {
            throw new \Exception('Invalid transaction ID format.');
        }

        if (!$chargeId) {
            throw new \Exception('Unable to resolve Stripe charge for refund.');
        }

        return $chargeId;
    }

    protected function buildPosCashRefundTransactionLog(Order $order, float $refundAmountDollars, string $reason = ''): array
    {
        $refundAmountCents = max(1, (int) round($refundAmountDollars * 100));
        $cashRefundId = 'cash_rfd_pos_' . $order->id . '_' . time();

        return [
            'id' => $cashRefundId,
            'object' => 'refund',
            'amount' => $refundAmountCents,
            'amount_refunded' => $refundAmountCents,
            'currency' => 'usd',
            'status' => 'succeeded',
            'payment_method' => 'cash',
            'reason' => $reason !== '' ? $reason : 'pos_cash_refund',
            'created' => time(),
        ];
    }

    protected function isPosCashOrder(Order $order): bool
    {
        return optional($order->getway)->name === 'cash' || (int) $order->order_from === 5;
    }

    protected function runPosRefundSideEffects(
        Order $order,
        float $refundAmount,
        ?string $refundId,
        ?Collection $cancelledTickets = null,
        ?array $refundDetails = null,
        ?string $refundType = 'full'
    ): void {
        $order = Order::with('orderstatus', 'orderlasttrans', 'orderitems', 'getway', 'user', 'shippingwithinfo', 'ordermeta', 'schedule')
            ->findOrFail($order->id);

        try {
            $this->post_order_data_POS($order, 'refund');
        } catch (\Throwable $e) {
            \Log::error('post_order_data_POS failed after POS refund', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
        }

        try {
            trigger_tenant_financial_manager_sync_after_refund((int) $order->id);
        } catch (\Throwable $e) {
            \Log::error('tenant FM sync dispatch failed after POS refund', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
        }

        $adminEmail = optional(User::where('role_id', 3)->first())->email ?? '';

        $this->sendTicketCancelledRefundEmails(
            $order,
            $cancelledTickets ?? collect(),
            $refundAmount,
            $refundId,
            $refundDetails
        );

        NotifyToUser::sendEmail($order, $adminEmail, 'admin');

        if ($order->notify_driver == 'mail') {
            $ordermeta = json_decode($order->ordermeta->value ?? '');
            $mailTo = $ordermeta->email ?? $order->user->email ?? '';

            if (!empty($mailTo)) {
                NotifyToUser::sendEmail($order, $mailTo, 'user');
            }
        }

        $this->sendRefundReceiptEmail(
            $order,
            $refundAmount,
            $refundDetails,
            $refundId,
            $refundType
        );
    }

    protected function formatPosRefundResponse(
        Order $order,
        string $paymentId,
        string $refundId,
        float $refundAmountDollars,
        string $status,
        string $refundType,
        ?array $refundDetails = null
    ): array {
        $payload = [
            'paymentId' => $paymentId,
            'refundId'  => $refundId,
            'amount'    => (int) round($refundAmountDollars * 100),
            'status'    => $status,
            'currency'  => 'usd',
            'refundType'=> $refundType,
            'orderId'   => $order->id,
            'invoiceNo' => $order->invoice_no,
        ];

        if ($refundDetails) {
            $payload['refundDetails'] = $refundDetails;
        }

        return $payload;
    }

    protected function centsToDollars(float $amount): float
    {
        return round($amount / 100, 2);
    }

    protected function errorResponse(string $message, int $status = 422, array $extra = []): JsonResponse
    {
        return response()->json(array_merge([
            'error'   => true,
            'message' => $message,
        ], $extra), $status);
    }
}
