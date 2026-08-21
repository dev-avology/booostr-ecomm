<?php

namespace App\Services;

use App\Models\Order;
use App\Models\QuickSaleDescriptor;
use App\Models\QuickSaleOrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class QuickSaleOrderService
{
    /**
     * Persist POS Quick Sale line items for an order (separate from product orderitems).
     *
     * @param  array<int, array<string, mixed>>  $items
     * @return Collection<int, QuickSaleOrderItem>
     */
    public function createLinesForOrder(Order $order, array $items, Request $request): Collection
    {
        $saved = collect();

        foreach ($items as $item) {
            if (!is_array($item)) {
                continue;
            }

            $descriptorName = trim((string) ($item['descriptor'] ?? ''));
            $descriptorId = (int) ($item['descriptor_id'] ?? 0);
            $qty = max(1, (int) ($item['cart_quantity'] ?? $item['quantity'] ?? $item['qty'] ?? 1));
            $unitAmount = round((float) ($item['amount'] ?? 0), 2);
            $lineSubtotal = round($unitAmount * $qty, 2);
            $taxAmount = round((float) ($item['tax_amount'] ?? 0), 2);
            $lineTotal = round((float) ($item['line_total'] ?? ($lineSubtotal + $taxAmount)), 2);

            if ($descriptorName === '' && $descriptorId > 0) {
                $descriptorName = (string) optional(QuickSaleDescriptor::find($descriptorId))->name;
            }

            if ($descriptorName === '') {
                $descriptorName = 'Miscellaneous Item';
            }

            $title = trim((string) ($item['title'] ?? ''));
            if ($title === '') {
                $title = 'Quick Sale - ' . $descriptorName;
            }

            $saved->push(QuickSaleOrderItem::create([
                'order_id' => $order->id,
                'descriptor_id' => $descriptorId > 0 ? $descriptorId : null,
                'descriptor_name' => $descriptorName,
                'title' => $title,
                'unit_amount' => $unitAmount,
                'qty' => $qty,
                'line_subtotal' => $lineSubtotal,
                'tax_amount' => $taxAmount,
                'line_total' => $lineTotal,
                'order_invoice_no' => $order->invoice_no,
                'order_placed_at' => $order->placed_at,
                'payment_method' => (string) $request->input('payment_method', ''),
                'order_from' => (int) $order->order_from,
                'payment_status' => (int) $order->payment_status,
                'wpuid' => (int) $request->input('wpuid', 0) ?: null,
                'meta' => [
                    'type' => 'quick_sale',
                    'display_label' => 'Quick Sale Item - ' . $descriptorName,
                    'payment_identifiers' => $request->input('payment_identifiers'),
                ],
            ]));
        }

        return $saved;
    }

    /**
     * @param  array<int, array<string, mixed>>  $items
     */
    public function partitionPosOrderItems(array $items): array
    {
        $productItems = [];
        $quickSaleItems = [];

        foreach ($items as $item) {
            if (!is_array($item)) {
                continue;
            }

            $type = strtolower(trim((string) ($item['type'] ?? 'product')));

            if ($type === 'quick_sale') {
                $quickSaleItems[] = $item;
                continue;
            }

            $productItems[] = $item;
        }

        return [$productItems, $quickSaleItems];
    }

    public function isQuickSaleItem(array $item): bool
    {
        return strtolower(trim((string) ($item['type'] ?? ''))) === 'quick_sale';
    }
}
