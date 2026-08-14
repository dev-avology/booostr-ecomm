<?php

namespace App\Services;

use App\Mail\RefundReceiptMail;
use App\Models\Category;
use App\Models\Order;
use App\Models\Ordermeta;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class RefundReceiptEmailService
{
    /**
     * Send a customer Refund Receipt email (same visual design as the order receipt).
     *
     * @param  string|null  $refundType  full|item|dollar
     */
    public function sendForRefund(
        Order $order,
        ?float $refundAmount = null,
        ?array $refundDetails = null,
        ?string $refundReferenceId = null,
        ?string $refundType = null
    ): bool {
        $order->loadMissing([
            'orderstatus',
            'orderitems.term',
            'orderitems.term.termcategories',
            'getway',
            'user',
            'shippingwithinfo',
            'ordermeta',
            'orderlasttrans',
        ]);

        $ordermeta = json_decode(optional($order->ordermeta)->value ?? '');
        $email = trim((string) ($ordermeta->email ?? $order->user->email ?? ''));

        if ($email === '') {
            return false;
        }

        $resolvedType = $this->resolveRefundType($refundType, $refundDetails);
        $detail = $this->buildRefundDetail($order, $resolvedType, $refundDetails, $refundAmount);
        $grandTotal = (float) ($detail['grand_total'] ?? $refundAmount ?? 0);

        if ($grandTotal <= 0 && $refundAmount !== null) {
            $grandTotal = (float) $refundAmount;
        }

        $transactionJson = optional($order->orderlasttrans)->value
            ?? optional(
                Ordermeta::where('order_id', $order->id)
                    ->where('key', 'transcation_log')
                    ->orderByDesc('id')
                    ->first()
            )->value
            ?? '';

        $orderlasttrans = json_decode($transactionJson ?: '{}');
        $lastDigit = $orderlasttrans->source->last4 ?? null;
        $cardNumber = $lastDigit !== null ? str_pad((string) $lastDigit, 16, '*', STR_PAD_LEFT) : null;

        $currency = get_option('currency_info');
        $invoiceInfo = get_option('invoice_data', true);
        $storeName = $invoiceInfo->store_legal_name ?? (tenant_club_info()['club_name'] ?? 'Store');

        $mailData = [
            'from' => '',
            'from_name' => null,
            'reply_to' => null,
            'currency' => $currency,
            'invoice_info' => $invoiceInfo,
            'order' => $order,
            'ordermeta' => $ordermeta,
            'order_type' => $this->resolveOrderType($order),
            'card_number' => $cardNumber,
            'payment_method_label' => $this->resolvePaymentMethodLabel($order, $ordermeta, $orderlasttrans),
            'refund' => [
                'type' => $resolvedType,
                'title' => $detail['title'] ?? 'Refund',
                'date' => $detail['date'] ?? Carbon::now()->setTimezone(config('app.timezone'))->format('m/d/Y'),
                'reference_id' => $refundReferenceId ?? '',
                'item_total' => (float) ($detail['refund_amount'] ?? ($refundDetails['item_total'] ?? 0)),
                'tax_total' => (float) ($detail['tax_amount'] ?? ($refundDetails['tax_total'] ?? 0)),
                'grand_total' => $grandTotal,
                'lines' => $detail['lines'] ?? [],
                'items' => $this->normalizeRefundItems($order, $refundDetails, $resolvedType),
            ],
        ];

        if (function_exists('store_receipt_mail_from')) {
            $receiptFrom = store_receipt_mail_from();
            $mailData['from'] = $receiptFrom['address'] ?? '';
            $mailData['from_name'] = $receiptFrom['name'] ?? null;
            $mailData['reply_to'] = $receiptFrom['reply_to'] ?? null;
        }

        $subject = 'Refund Receipt for your ' . $storeName . ' Store order: #' . $order->invoice_no;

        try {
            Mail::to($email)->send(new RefundReceiptMail($mailData, $subject));

            return true;
        } catch (\Throwable $e) {
            Log::error('Refund receipt email failed', [
                'order_id' => $order->id,
                'email' => $email,
                'refund_type' => $resolvedType,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    protected function resolveRefundType(?string $refundType, ?array $refundDetails): string
    {
        $type = strtolower(trim((string) $refundType));

        if (in_array($type, ['full', 'item', 'dollar'], true)) {
            return $type;
        }

        if (is_array($refundDetails) && !empty($refundDetails['items'])) {
            foreach ($refundDetails['items'] as $item) {
                if (!is_array($item)) {
                    continue;
                }

                if (isset($item['amount']) && !isset($item['qty'])) {
                    return 'dollar';
                }

                if (isset($item['amount']) && (float) ($item['amount'] ?? 0) > 0 && (int) ($item['qty'] ?? 0) <= 0) {
                    return 'dollar';
                }
            }

            return 'item';
        }

        return 'full';
    }

    protected function buildRefundDetail(Order $order, string $refundType, ?array $refundDetails, ?float $refundAmount): array
    {
        if ($refundType === 'full' || empty($refundDetails)) {
            $detail = function_exists('financial_manager_full_refund_detail')
                ? financial_manager_full_refund_detail($order)
                : [];

            if ($refundAmount !== null) {
                $detail['grand_total'] = round((float) $refundAmount, 2);
            }

            return $detail;
        }

        $entry = [
            'type' => $refundType === 'dollar' ? 'dollar' : '',
            'refunded_at' => Carbon::now()->setTimezone(config('app.timezone'))->toDateTimeString(),
            'items' => $refundDetails['items'] ?? [],
            'item_amount' => (float) ($refundDetails['item_total'] ?? 0),
            'tax_amount' => (float) ($refundDetails['tax_total'] ?? 0),
            'grand_total' => (float) ($refundDetails['grand_total'] ?? $refundAmount ?? 0),
        ];

        if (function_exists('financial_manager_partial_refund_detail')) {
            return financial_manager_partial_refund_detail($entry, $order);
        }

        return [
            'title' => $refundType === 'dollar'
                ? 'Refund & Cancel Partial Order By Dollar Amount'
                : 'Refund & Cancel Partial Order By Item',
            'date' => Carbon::now()->setTimezone(config('app.timezone'))->format('m/d/Y'),
            'refund_amount' => $entry['item_amount'],
            'tax_amount' => $entry['tax_amount'],
            'grand_total' => $entry['grand_total'],
            'lines' => [],
        ];
    }

    protected function normalizeRefundItems(Order $order, ?array $refundDetails, string $refundType): array
    {
        if ($refundType === 'full' || empty($refundDetails['items'])) {
            $items = [];

            foreach ($order->orderitems as $orderItem) {
                $items[] = [
                    'label' => $orderItem->term->title ?? 'Item',
                    'qty' => (int) $orderItem->qty,
                    'amount' => (float) $orderItem->amount,
                    'line_total' => (float) $orderItem->amount * (int) $orderItem->qty,
                ];
            }

            return $items;
        }

        $normalized = [];

        foreach ($refundDetails['items'] as $item) {
            if (!is_array($item)) {
                continue;
            }

            $itemId = (int) ($item['item_id'] ?? 0);
            $orderItem = $itemId > 0 ? $order->orderitems->firstWhere('id', $itemId) : null;
            $label = trim((string) ($item['label'] ?? ''));
            if ($label === '') {
                $label = $orderItem->term->title ?? 'Item';
            }

            $qty = (int) ($item['qty'] ?? 0);
            $amount = (float) ($item['amount'] ?? 0);

            if ($refundType === 'item' && $qty <= 0) {
                $qty = 1;
            }

            $normalized[] = [
                'label' => $label,
                'qty' => $qty > 0 ? $qty : 1,
                'amount' => $refundType === 'dollar' && $qty <= 0
                    ? $amount
                    : ($qty > 0 ? round($amount / max($qty, 1), 2) : $amount),
                'line_total' => $amount,
                'tax' => (float) ($item['tax'] ?? 0),
            ];
        }

        return $normalized;
    }

    protected function resolveOrderType(Order $order): string
    {
        $productType = Category::where('type', 'product_type')
            ->select('id', 'slug', 'name')
            ->orderBy('id', 'ASC')
            ->get();

        $selectedProductType = [];

        foreach ($order->orderitems ?? [] as $row) {
            $pTypes = $productType->pluck('id')->all();
            $selectedProductType[] = $row->term->termcategories
                ->pluck('category_id')
                ->intersect($pTypes)
                ->values()
                ->all();
        }

        $selectedProductType = Arr::flatten($selectedProductType);
        $count = count($selectedProductType);

        return match (true) {
            $count > 1 => 'Mixed',
            $count === 1 => optional(
                $productType->firstWhere('id', $selectedProductType[0])
            )->slug === 'digital_product' ? 'Digital' : 'Goods',
            default => 'Goods',
        };
    }

    protected function resolvePaymentMethodLabel(Order $order, $ordermeta, $orderlasttrans): string
    {
        $metaLabel = trim((string) ($ordermeta->payment_method_label ?? ''));

        if ($metaLabel !== '') {
            return ucwords(str_replace(['/', '_'], ' ', $metaLabel));
        }

        if (($orderlasttrans->payment_method ?? '') === 'cash') {
            return 'Cash/Check';
        }

        $gatewayName = strtolower(trim((string) optional($order->getway)->name));

        if ($gatewayName === 'cash' || (int) $order->order_from === 5) {
            return 'Cash/Check';
        }

        if ($gatewayName !== '') {
            return ucwords($gatewayName);
        }

        return 'Card';
    }
}
