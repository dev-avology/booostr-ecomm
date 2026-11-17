@if($orders->count() > 0)
<div class="table-responsive orders-table-wrapper">
    <table class="table align-middle mb-0 orders-table">
        <thead class="orders-table-head">
            <tr>
                <th scope="col" style="min-width: 140px;">{{ __('Order No') }}</th>
                <th scope="col" style="min-width: 140px;">{{ __('Placed At') }}</th>
                <th scope="col" class="text-center" style="min-width: 110px;">{{ __('Total Items') }}</th>
                <th scope="col" class="text-center" style="min-width: 130px;">{{ __('Order Status') }}</th>
                <th scope="col" class="text-center" style="min-width: 130px;">{{ __('Payment Status') }}</th>
                <th scope="col" class="text-end" style="min-width: 110px;">{{ __('Action') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orders as $row)
            @php
                // True total items quantity
                $totalQty = $row->orderitems->sum('qty');
                if (!$totalQty || $totalQty < 1) {
                    $totalQty = $row->orderitems_count ?? 1;
                }

                // Payment Status
                if ($row->payment_status == 1) {
                    $paymentText = __('Paid');
                    $paymentClass = 'payment-badge-paid';
                    $paymentIcon = 'icofont-check-circled';
                } elseif ($row->payment_status == 2) {
                    $paymentText = __('Pending');
                    $paymentClass = 'payment-badge-pending';
                    $paymentIcon = 'icofont-clock-time';
                } else {
                    $paymentText = __('Payment Failed');
                    $paymentClass = 'payment-badge-failed';
                    $paymentIcon = 'icofont-close-circled';
                }

                // Order Status styling
                $statusName = $row->orderstatus->name ?? __('Pending');
                $statusSlug = $row->orderstatus->slug ?? '';
                $statusLower = strtolower($statusName);
                $orderStatusClass = 'order-badge-pending';

                if (strpos($statusLower, 'complete') !== false || strpos($statusLower, 'deliver') !== false) {
                    $orderStatusClass = 'order-badge-completed';
                } elseif (strpos($statusLower, 'cancel') !== false || strpos($statusLower, 'fail') !== false) {
                    $orderStatusClass = 'order-badge-cancelled';
                } elseif (strpos($statusLower, 'process') !== false) {
                    $orderStatusClass = 'order-badge-processing';
                }
            @endphp
            <tr class="order-table-row">
                <td>
                    <a href="{{ url('/customer/order/'.$row->id) }}" class="fw-bold order-invoice-link text-decoration-none">
                        #{{ $row->invoice_no }}
                    </a>
                    <div class="order-price-sub mt-1">
                        <span class="render_currency fw-bold text-dark">{{ number_format($row->total, 2) }}</span>
                    </div>
                </td>
                <td>
                    <div class="fw-medium text-dark">{{ $row->created_at->format('M d, Y') }}</div>
                    <div class="text-muted small">{{ $row->created_at->diffForHumans() }}</div>
                </td>
                <td class="text-center">
                    <span class="badge items-count-badge">
                        <i class="icofont-box me-1"></i>{{ $totalQty }} {{ $totalQty == 1 ? __('item') : __('items') }}
                    </span>
                </td>
                <td class="text-center">
                    <span class="badge order-status-badge {{ $orderStatusClass }}">
                        {{ $statusName }}
                    </span>
                </td>
                <td class="text-center">
                    <span class="badge payment-status-badge {{ $paymentClass }}">
                        <i class="{{ $paymentIcon }} me-1"></i>{{ $paymentText }}
                    </span>
                </td>
                <td class="text-end">
                    <a href="{{ url('/customer/order/'.$row->id) }}" class="btn btn-sm view-order-btn">
                        <i class="icofont-eye me-1"></i>{{ __('Details') }}
                    </a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

{{-- Pagination & Count Info --}}
<div class="orders-pagination-container d-flex justify-content-between align-items-center mt-4 pt-3 border-top flex-wrap gap-2">
    <div class="text-muted small">
        {{ __('Showing') }} <span class="fw-bold text-dark">{{ $orders->firstItem() ?? 0 }}</span> {{ __('to') }} <span class="fw-bold text-dark">{{ $orders->lastItem() ?? 0 }}</span> {{ __('of') }} <span class="fw-bold text-dark">{{ $orders->total() }}</span> {{ __('orders') }}
    </div>
    <div class="ajax-pagination">
        {{ $orders->links('vendor.pagination.bootstrap-4') }}
    </div>
</div>
@else
{{-- Empty State --}}
<div class="orders-empty-state text-center py-5">
    <div class="empty-icon-wrapper mb-3">
        <i class="icofont-shopping-cart"></i>
    </div>
    <h4 class="fw-bold text-dark mb-2">{{ __('No orders found') }}</h4>
    <p class="text-muted mb-4" style="max-width: 440px; margin: 0 auto; font-size: 14.5px;">
        {{ __('We couldn\'t find any orders matching your criteria. Try adjusting your search query or reset your filters.') }}
    </p>
    <button type="button" class="btn btn-outline-primary btn-reset-filters px-4 py-2" onclick="resetOrderFilters()">
        <i class="icofont-refresh me-1"></i>{{ __('Reset All Filters') }}
    </button>
</div>
@endif

