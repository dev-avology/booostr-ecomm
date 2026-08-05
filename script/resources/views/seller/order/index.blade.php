@extends('layouts.backend.app')

@section('title','Dashboard')

@section('style')
<style>
    span.risk-badge-text img {
        margin-left: 12px;
    }

.order-list-pagination {
    display: flex;
    justify-content: center;
    margin-top: 1.5rem;
}

.order-list-pagination .pagination {
    justify-content: center;
    margin-bottom: 0;
}

.order-list-card-header .order-list-card-title {
    margin-bottom: 0;
}

.order-list-toolbar {
    display: flex;
    flex-wrap: wrap;
    align-items: flex-end;
    justify-content: space-between;
    gap: 12px;
    margin-bottom: 1rem;
}

.order-list-bulk-actions {
    flex: 0 1 auto;
    min-width: 0;
}

.order-list-bulk-actions .input-group {
    margin-bottom: 0;
}

.order-list-header-tools {
    display: flex;
    align-items: flex-end;
    gap: 10px;
    margin-left: auto;
}

.order-list-search-form {
    margin: 0;
    width: 300px;
    max-width: 100%;
}

.order-list-search-field {
    position: relative;
    display: flex;
    align-items: center;
    height: 38px;
    border: 1px solid #dce3ea;
    border-radius: 6px;
    background: #fff;
    transition: border-color 0.2s ease, box-shadow 0.2s ease;
}

.order-list-search-field:focus-within {
    border-color: #00aeef;
    box-shadow: 0 0 0 0.15rem rgba(0, 174, 239, 0.12);
}

.order-list-search-input {
    width: 100%;
    height: 100%;
    border: 0;
    background: transparent;
    padding: 0 44px 0 12px;
    border-radius: 6px;
    box-shadow: none;
    font-size: 14px;
    line-height: 1.4;
}

.order-list-search-input:focus {
    outline: none;
    box-shadow: none;
}

.order-list-search-btn {
    position: absolute;
    top: 4px;
    right: 4px;
    bottom: 4px;
    width: 34px;
    padding: 0;
    border: 0;
    border-radius: 4px;
    background: #00aeef;
    color: #fff;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    box-shadow: none;
}

.order-list-search-btn:hover,
.order-list-search-btn:focus {
    background: #0099d6;
    color: #fff;
    outline: none;
    box-shadow: none;
}

.order-list-filter-btn {
    height: 38px;
    padding: 0 16px;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    white-space: nowrap;
    box-shadow: none;
}

.order-list-filter-btn .fe {
    font-size: 15px;
    line-height: 1;
}

.order-list-per-page-form {
    margin: 0;
}

.order-list-per-page-select {
    height: 38px;
    min-width: 132px;
    padding: 0 12px;
    border: 1px solid #dce3ea;
    border-radius: 6px;
    background: #fff;
    font-size: 14px;
    font-weight: 500;
    color: #34395e;
    box-shadow: none;
    cursor: pointer;
}

.order-list-per-page-select:focus {
    border-color: #00aeef;
    outline: none;
    box-shadow: 0 0 0 0.15rem rgba(0, 174, 239, 0.12);
}

@media (max-width: 767px) {
    .order-list-toolbar {
        flex-direction: column;
        align-items: stretch;
    }

    .order-list-header-tools {
        width: 100%;
        margin-left: 0;
    }

    .order-list-search-form {
        flex: 1;
        width: auto;
        min-width: 0;
    }
}

.order-filter-modal .modal-dialog {
    max-width: 640px;
}

.order-filter-dropdown {
    position: relative;
}

.order-filter-dropdown-toggle {
    width: 100%;
    height: 42px;
    text-align: left;
    background: #fff;
    border: 1px solid #dce3ea;
    border-radius: 6px;
    padding: 0 36px 0 12px;
    font-size: 14px;
    color: #34395e;
    position: relative;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.order-filter-dropdown-toggle::after {
    content: '';
    position: absolute;
    right: 14px;
    top: 50%;
    margin-top: -2px;
    border: 5px solid transparent;
    border-top-color: #6c757d;
}

.order-filter-dropdown-menu {
    display: none;
    position: absolute;
    top: calc(100% + 4px);
    left: 0;
    right: 0;
    z-index: 1055;
    background: #fff;
    border: 1px solid #dce3ea;
    border-radius: 6px;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
    padding: 8px 0;
    max-height: 240px;
    overflow-y: auto;
}

.order-filter-dropdown.is-open .order-filter-dropdown-menu {
    display: block;
}

.order-filter-dropdown-option {
    display: flex;
    align-items: center;
    gap: 10px;
    margin: 0;
    padding: 8px 14px;
    font-size: 14px;
    color: #34395e;
    cursor: pointer;
}

.order-filter-dropdown-option:hover {
    background: #f7f9fb;
}

.order-filter-dropdown-option input {
    margin: 0;
}

.order-filter-between-row {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 10px;
}

.order-filter-between-row .order-filter-between-label {
    font-weight: 600;
    color: #34395e;
    margin-bottom: 0;
    white-space: nowrap;
}

.order-filter-between-row .order-filter-between-sep {
    color: #6c757d;
    font-size: 14px;
}

.order-filter-between-input {
    flex: 1 1 120px;
    min-width: 110px;
    max-width: 180px;
}

.order-filter-date-input {
    flex: 1 1 150px;
    min-width: 140px;
    max-width: 200px;
}

.table:not(.table-sm) thead th .order-sort-link {
    color: #666;
    font-family: inherit;
    font-size: inherit;
    font-weight: inherit;
    line-height: inherit;
    letter-spacing: inherit;
    text-transform: uppercase;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    white-space: nowrap;
    transition: none;
    -webkit-transition: none;
    -o-transition: none;
}

.table:not(.table-sm) thead th .order-sort-link:hover,
.table:not(.table-sm) thead th .order-sort-link:focus,
.table:not(.table-sm) thead th .order-sort-link:visited {
    color: #666;
    text-decoration: none;
}

.order-sort-arrows {
    display: inline-flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    line-height: 0.7;
    font-size: 11px;
    margin-left: 2px;
}

.order-sort-arrow {
    color: #c4cdd5;
}

.order-sort-arrow.is-active {
    color: #00aeef;
}

/* Order list refund / cancel color coding (screenshot) */
.order-total-partial-refund {
    color: #8b2f8b;
    font-weight: 600;
}

.order-total-refunded {
    color: #dc3545;
    font-weight: 600;
}

.badge-partial-refund {
    background-color: #8b2f8b !important;
    color: #fff !important;
}

.badge-payment-refunded {
    background-color: #dc3545 !important;
    color: #fff !important;
}

/* Distinct from Complete (green), Authorized/Refunded (red), Partial Refund (purple) */
.badge-cash-check {
    background-color: #fd7e14 !important;
    color: #fff !important;
}

.badge-fulfillment-cancel {
    background-color: #dc3545 !important;
    color: #fff !important;
}

</style>
@endsection

@section('content')

<section class="section">
    <div class="section-header row">
        <div class="col-sm-12">
            <ul class="nav nav-pills">
                <li class="nav-item">
                    <a class="nav-link  {{ $request_status == null ? 'active' : '' }} " href="{{ route('seller.order.index') }}">All</a>
                </li>
                @foreach($status as $row)
                <li class="nav-item">
                <a class="nav-link  {{ $request_status == $row->id ? 'active' : '' }}" href="{{ url('seller/order?status='.$row->id) }}">{{$row->name}} <span class="badge badge-secondary">{{$row->orderstatus_count}}</span></a>
                </li>
                @endforeach
            </ul>
        </div>
    </div>
</section>

<x-storenotification></x-storenotification>

 <div class="card">
    <div class="card-header">
        <h4 class="order-list-card-title">{{ __('Orders') }}</h4>
    </div>
    <div class="card-body">
        <div class="order-list-toolbar">
            <div class="order-list-bulk-actions">
                @if(count($orders) > 0)
                <div class="input-group">
                     <select class="form-control selectric" name="method" form="bulkActionForm">
                        <option disabled selected>Select Capture/Fulfillment</option>
                        <option value="capture_authorized">Capture Authorized Payments</option>
                        <option value="set_capture_payment">set capture payment</option>
                        <option value="complete_fulfillment">Complete Fulfillment</option>
                        <option value="cancel_order">Cancel Order</option>
                        <option value="mark_pending">Mark As Pending</option>
                        <option value="delete">Delete Permanently</option>
                    </select>

                    <div class="input-group-append">
                        <button class="btn btn-primary basicbtn" type="submit" form="bulkActionForm">{{ __('Submit') }}</button>
                    </div>
                </div>
                @endif
            </div>
            <div class="order-list-header-tools">
                <form class="order-list-search-form">
                    <div class="order-list-search-field">
                        <input type="text" name="src" value="{{ $request->src ?? '' }}" class="form-control order-list-search-input" required="" placeholder="{{ __('Search by Order # or Customer Name') }}" />
                        <button type="submit" class="order-list-search-btn" aria-label="{{ __('Search orders') }}">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>
                <button class="btn btn-primary order-list-filter-btn" type="button" data-toggle="modal" data-target="#searchmodal">
                    <i class="fe fe-sliders"></i>
                    <span>{{ __('Filter') }}</span>
                    <span class="badge badge-light ml-1 d-none">0</span>
                </button>
                <form class="order-list-per-page-form" method="get" action="{{ route('seller.order.index') }}">
                    @foreach(collect(request()->query())->except(['per_page', 'page']) as $queryKey => $queryValue)
                        @if(!is_array($queryValue))
                            <input type="hidden" name="{{ $queryKey }}" value="{{ $queryValue }}">
                        @endif
                    @endforeach
                    <select class="form-control order-list-per-page-select" name="per_page" id="order_per_page" onchange="this.form.submit()">
                        @foreach($per_page_options ?? [10, 20, 30, 50, 100, 200, 500] as $per)
                            <option value="{{ $per }}" {{ ($selected_per_page ?? 30) == $per ? 'selected' : '' }}>
                                {{ __('Per page') }} {{ $per }}
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>
        </div>
        <form method="post" action="{{ route('seller.order.multipledelete') }}" class="" id="bulkActionForm">
            @csrf
            <div class="table-responsive">
                @php
                    $activeSort = strtolower((string) ($sortColumn ?? request('sort', '')));
                    $activeDir = strtolower((string) ($sortDirection ?? request('dir', 'desc'))) === 'asc' ? 'asc' : 'desc';

                    $orderSortUrl = function (string $column, string $defaultDir = 'desc') use ($activeSort, $activeDir) {
                        $nextDir = ($activeSort === $column)
                            ? ($activeDir === 'asc' ? 'desc' : 'asc')
                            : $defaultDir;

                        $query = array_merge(request()->query(), [
                            'sort' => $column,
                            'dir' => $nextDir,
                        ]);
                        unset($query['page']);

                        return route('seller.order.index', $query);
                    };

                    $orderSortHeader = function (string $column, string $label, string $defaultDir, ?string $align = null) use ($orderSortUrl, $activeSort, $activeDir) {
                        $isActive = $activeSort === $column;
                        $ascActive = $isActive && $activeDir === 'asc';
                        $descActive = $isActive && $activeDir === 'desc';
                        $alignClass = $align ? ' text-' . $align : '';

                        return '<th class="' . trim($alignClass) . '">'
                            . '<a href="' . e($orderSortUrl($column, $defaultDir)) . '" class="order-sort-link">'
                            . e(__($label))
                            . '<span class="order-sort-arrows" aria-hidden="true">'
                            . '<i class="fas fa-caret-up order-sort-arrow' . ($ascActive ? ' is-active' : '') . '"></i>'
                            . '<i class="fas fa-caret-down order-sort-arrow' . ($descActive ? ' is-active' : '') . '"></i>'
                            . '</span></a></th>';
                    };
                @endphp
                <table class="table table-hover table-nowrap card-table text-center">
                    <thead>
                        <tr>
                            <th class="text-left" ><div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input checkAll" id="selectAll">
                            <label class="custom-control-label checkAll" for="selectAll"></label>
                            </div></th>
                            {!! $orderSortHeader('order', 'Order', 'desc', 'left') !!}
                            {!! $orderSortHeader('date', 'Date', 'desc', null) !!}
                            {!! $orderSortHeader('customer', 'Customer', 'asc', null) !!}
                            <th class="text-left" >{{ __('Order Channel') }}</th>
                            {!! $orderSortHeader('total', 'total', 'desc', 'right') !!}
                            <th>{{ __('Payment') }}</th>
                            <th>{{ __('Fulfillment') }}</th>
                            <th class="text-right">{{ __('Type') }}</th>
                            {!! $orderSortHeader('items', 'Item(s)', 'desc', 'right') !!}
                            <th class="text-right">{{ __('Print') }}</th>
                        </tr>
                    </thead>
                    <tbody class="list font-size-base rowlink" data-link="row">
                        @php
                            $orderListIds = collect($orders->items())->pluck('id')->filter()->values()->all();

                            // One query for partial-refund markers (qty + dollar), avoids N+1 and keeps controller untouched.
                            $partialRefundOrderIds = \App\Models\Ordermeta::whereIn('order_id', $orderListIds)
                                ->whereIn('key', ['partial_refunded_items', 'partial_dollar_refunded_items'])
                                ->whereNotNull('value')
                                ->where('value', '!=', '')
                                ->where('value', '!=', '{}')
                                ->where('value', '!=', '[]')
                                ->pluck('order_id')
                                ->unique()
                                ->flip()
                                ->all();

                            // Refund log amounts for remaining-total display (same source as order details).
                            $partialRefundAmountsByOrderId = [];
                            if (!empty($orderListIds)) {
                                $partialRefundLogRows = \App\Models\Ordermeta::whereIn('order_id', $orderListIds)
                                    ->where('key', 'partial_refund_logs')
                                    ->get(['order_id', 'value']);

                                foreach ($partialRefundLogRows as $partialRefundLogRow) {
                                    $logs = json_decode($partialRefundLogRow->value ?? '[]', true) ?: [];
                                    $refundedItemSum = 0.0;
                                    $refundedTaxSum = 0.0;
                                    $seenFingerprints = [];

                                    foreach ($logs as $log) {
                                        if (!is_array($log)) {
                                            continue;
                                        }

                                        $fingerprint = md5(json_encode([
                                            'amount' => round((float) ($log['amount'] ?? 0), 2),
                                            'type' => (string) ($log['type'] ?? ''),
                                            'items' => $log['items'] ?? [],
                                        ]));

                                        if (isset($seenFingerprints[$fingerprint])) {
                                            continue;
                                        }
                                        $seenFingerprints[$fingerprint] = true;

                                        $logItemAmount = 0.0;
                                        $logTaxAmount = 0.0;
                                        foreach ($log['items'] ?? [] as $logItem) {
                                            if (!is_array($logItem)) {
                                                continue;
                                            }
                                            $logItemAmount += (float) ($logItem['amount'] ?? 0);
                                            $logTaxAmount += (float) ($logItem['tax'] ?? 0);
                                        }

                                        if ($logItemAmount <= 0 && empty($log['items'])) {
                                            $logItemAmount = (float) ($log['amount'] ?? 0);
                                        }

                                        $refundedItemSum += $logItemAmount;
                                        $refundedTaxSum += $logTaxAmount;
                                    }

                                    $partialRefundAmountsByOrderId[(int) $partialRefundLogRow->order_id] = [
                                        'item_total' => round($refundedItemSum, 2),
                                        'tax_total' => round($refundedTaxSum, 2),
                                    ];
                                }
                            }
                        @endphp
                        @foreach($orders ?? [] as $key => $row)
                        @php 
                        // All product type IDs
                            $p_types = $product_type->pluck('id')->all();

                            // Gather category IDs from order items that match product types
                            $selected_product_type = collect($row->orderitems ?? [])
                                ->flatMap(function ($item) {
                                    if (!$item->term) {
                                        return [];
                                    }

                                    return $item->term->termcategories->pluck('category_id');
                                })
                                ->filter(fn($id) => in_array($id, $p_types))
                                ->unique()
                                ->values()
                                ->all();

                            // Default order type
                            $order_type = 'Goods';
                            $count = count($selected_product_type);

                            if ($count === 1) {
                                // Find the product type model by ID
                                $pt = $product_type->firstWhere('id', $selected_product_type[0]);

                                // Decide order type by slug
                                if ($pt && $pt->slug === 'digital_product') {
                                    $order_type = 'Digital';
                                } elseif ($pt && $pt->slug === 'physical_product') {
                                    $order_type = 'Goods';
                                } elseif ($pt && $pt->slug === 'online_ticketing') {
                                    $order_type = 'Tickets';
                                }
                            } elseif ($count > 1) {
                                $order_type = 'Mixed';
                            }

                        $ordermeta = json_decode(optional($row->ordermeta)->value ?? '');
                        $customerName = optional($ordermeta)->name ?? optional($row->user)->name ?? __('Guest User');
                        $gatewayName = optional($row->getway)->name;
                        $isFullyRefunded = (int) $row->payment_status === 5;
                        $isPartialRefund = !$isFullyRefunded
                            && (int) $row->payment_status === 1
                            && isset($partialRefundOrderIds[$row->id]);
                        $fulfillmentName = optional($row->orderstatus)->name ?? '';
                        $isCancelFulfillment = $isFullyRefunded
                            || strcasecmp(trim((string) $fulfillmentName), 'Cancel') === 0
                            || strcasecmp(trim((string) $fulfillmentName), 'Cancelled') === 0
                            || strcasecmp(trim((string) $fulfillmentName), 'Canceled') === 0;

                        // Display total: unchanged for normal orders; remaining after refund for partial/full.
                        $displayOrderTotal = (float) $row->total;
                        if ($isFullyRefunded) {
                            $displayOrderTotal = 0.0;
                        } elseif ($isPartialRefund) {
                            $refundedAmounts = $partialRefundAmountsByOrderId[(int) $row->id] ?? ['item_total' => 0.0, 'tax_total' => 0.0];
                            $refundedItemTotal = (float) ($refundedAmounts['item_total'] ?? 0);
                            $refundedTaxTotal = order_has_sales_tax($row)
                                ? (float) ($refundedAmounts['tax_total'] ?? 0)
                                : 0.0;
                            $displayOrderTotal = max(0, round((float) $row->total - $refundedItemTotal - $refundedTaxTotal, 2));
                        }
                    @endphp
                        <tr>
                            <td  class="text-left">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" name="ids[]" class="custom-control-input" id="customCheck{{ $row->id }}" value="{{ $row->id }}">
                                    <label class="custom-control-label" for="customCheck{{ $row->id }}"></label>
                                </div>
                            </td>
                            <td class="text-left">
                                <a href="{{ route('seller.order.show',$row->id) }}">{{ $row->invoice_no }}</a> 
                             
                                <span class="risk-badge">
                                  @if($row->risk_level == 'normal')
                                    <span class="risk-badge-text"  data-toggle="tooltip" data-placement="left" title="Normal"><img src="{{ asset('uploads/security-1.png') }}" alt="Low"></span>
                                  @elseif($row->risk_level == 'elevated')
                                    <span class="risk-badge-text" data-toggle="tooltip" data-placement="left" title="Medium"><img src="{{ asset('uploads/security-3.png') }}" alt="Medium"></span>
                                  @elseif($row->risk_level == 'highest')
                                    <span class="risk-badge-text" data-toggle="tooltip" data-placement="left" title="Highest"><img src="{{ asset('uploads/security-2.png') }}" alt="Highest"></span>
                                  @elseif($row->risk_level == 'unknown')
                                    <span class="risk-badge-text" data-toggle="tooltip" data-placement="left" title="Unknown"><img src="{{ asset('uploads/shiled-5.png') }}" alt="Unknown"></span>
                                  @elseif($row->risk_level == 'not_assessed')
                                    <span class="risk-badge-text" data-toggle="tooltip" data-placement="left" title="Not Assessed"><img src="{{ asset('uploads/shiled-4.png') }}" alt="Not Assessed"></span>
                                  @endif
                                </span>
                            </td>

                            <td><a href="{{ route('seller.order.show',$row->id) }}">{{ $row->created_at->format('d-F-Y') }}</a></td>

                            <td>
                               @if($row->user_id == null && $row->order_from == 1)
                                 {{ $customerName }}
                                @elseif($row->user_id !== null)
                                 <a href="{{ route('seller.user.show',$row->user_id) }}">{{ $customerName }}</a>
                                @else 
                                 {{ __('Guest User') }}
                                @endif 
                            </td>

                            @if($row->order_from == 4 || $row->order_from == 5)

                            <td class="text-left">
                                POS(point of sale)
                            </td>

                            @elseif ($row->order_from == 0)
                            <td class="text-left">
                                POS(Web)
                            </td>
                            @else

                            <td class="text-left">
                                ECOM(online)
                            </td>

                            @endif


                            <td class="@if($isPartialRefund) order-total-partial-refund @elseif($isFullyRefunded) order-total-refunded @endif">{{ currency_formate($displayOrderTotal) }}</td>
                            <td>
                                @if($isPartialRefund)
                                <span class="badge badge-partial-refund">{{ __('Partial Refund') }}</span>
                                @elseif($row->payment_status==2)
                                <span class="badge badge-warning">{{ __('Pending') }}</span>
                                @elseif($row->payment_status==1)

                                     @if(!empty(optional($ordermeta)->payment_method_label) && optional($ordermeta)->payment_method_label === 'cash/check')
                                        <span class="badge badge-cash-check">{{ __('cash/check') }}</span>
                                     @elseif($row->order_from == 4 || ($row->order_from == 0 && $gatewayName !== 'cash'))
                                        <span class="badge badge-success">{{ __('CC Complete') }}</span>
                                     @elseif($row->order_from == 5 || ($row->order_from == 0 && $gatewayName === 'cash'))
                                        <span class="badge badge-success">{{ __('Cash Complete') }}</span>
                                     @else
                                        <span class="badge badge-success">{{ __('Complete') }}</span>
                                     @endif


                                @elseif($row->payment_status==0)
                                <span class="badge badge-danger">{{ __('Cancel') }}</span> 
                                @elseif($row->payment_status==3)
                                <span class="badge badge-danger">{{ __('Incomplete') }}</span> 
                                @elseif($row->payment_status==4)
                                    @if(!empty(optional($ordermeta)->payment_method_label) && optional($ordermeta)->payment_method_label === 'cash/check')
                                        <span class="badge badge-cash-check">{{ __('cash/check') }}</span>
                                    @else
    							<span class="badge badge-danger">{{ __('Authorized') }}</span>
                                    @endif
                                @elseif($row->payment_status==5)
                                <span class="badge badge-payment-refunded">{{ __('Refunded') }}</span>
                                @endif
                            </td>

                            <td>
                                @if($row->order_from == 4 || $row->order_from == 5)
                               

                                <span class="badge badge-success text-white" style="background-color:#028a74">POS (In Person)</span>

                                @elseif($row->order_from == 0)

                                <span class="badge badge-success text-white" style="background-color:#028a74">POS (Web)</span>

                                @elseif($isCancelFulfillment)

                                <span class="badge badge-fulfillment-cancel text-white">{{ $fulfillmentName !== '' ? $fulfillmentName : __('Cancel') }}</span>

                                @else

                                <span class="badge {{ $row->orderstatus == null ? 'badge-warning' :'' }} text-white" style="background-color: {{ optional($row->orderstatus)->slug ?? '#ffc107' }}">{{ optional($row->orderstatus)->name ?? '' }}</span>

                                @endif 


                            </td>
                            {{-- <td>{{ $row->order_method }}</td> --}}
                            <td> {{$order_type}} </td>
                            <td>{{ $row->orderitems_count }}</td>
                            <td>
                                <a target="_blank" href="{{ route('seller.order.print',$row->id) }}" class="btn btn-primary">Print</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                   
                </table>
            </div>
            <div class="order-list-pagination">
                @if(count($request->all()) > 0)
                {{ $orders->appends($request->all())->links('vendor.pagination.bootstrap-4') }}
                @else
                {{ $orders->links('vendor.pagination.bootstrap-4') }}
                @endif
            </div>
        </form>
    </div>
</div>

<!-- Modal -->
@php
    $selectedOrderTypes = collect((array) $request->input('order_type', ['all']))
        ->map(fn ($value) => strtolower(trim((string) $value)))
        ->filter()
        ->values()
        ->all();
    if (empty($selectedOrderTypes)) {
        $selectedOrderTypes = ['all'];
    }
    $orderTypeAllSelected = in_array('all', $selectedOrderTypes, true);

    $selectedOrderChannels = collect((array) $request->input('order_channel', ['all']))
        ->map(fn ($value) => strtolower(trim((string) $value)))
        ->filter()
        ->values()
        ->all();
    if (empty($selectedOrderChannels)) {
        $selectedOrderChannels = ['all'];
    }
    $orderChannelAllSelected = in_array('all', $selectedOrderChannels, true);

    $orderTypeOptions = [
        'all' => __('All'),
        'goods' => __('Goods'),
        'digital' => __('Digital'),
        'tickets' => __('Tickets'),
        'mixed' => __('Mixed'),
    ];

    $orderChannelOptions = [
        'all' => __('All'),
        'both_web_pos' => __('Both Web and POS'),
        'web_only' => __('Web Only'),
        'pos_only' => __('POS Only'),
        'form_page_product' => __('Form Page Product'),
    ];
@endphp
<div class="modal fade order-filter-modal" id="searchmodal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="card-header-title">{{ __('Filters') }}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="get" action="{{ route('seller.order.index') }}" id="orderFilterForm">
            <div class="modal-body">
                @if($request->filled('src'))
                    <input type="hidden" name="src" value="{{ $request->src }}">
                @endif
                @if($request->filled('per_page'))
                    <input type="hidden" name="per_page" value="{{ $request->per_page }}">
                @endif
                @if($request->filled('sort'))
                    <input type="hidden" name="sort" value="{{ $request->sort }}">
                @endif
                @if($request->filled('dir'))
                    <input type="hidden" name="dir" value="{{ $request->dir }}">
                @endif
                <div class="form-group row mb-4">
                    <label class="col-sm-4 col-form-label">{{ __('Order Type') }}</label>
                    <div class="col-sm-8">
                        <div class="order-filter-dropdown" data-filter-group="order_type" data-default-label="{{ __('All') }}">
                            <button type="button" class="order-filter-dropdown-toggle" aria-haspopup="true" aria-expanded="false"></button>
                            <div class="order-filter-dropdown-menu">
                                @foreach($orderTypeOptions as $value => $label)
                                    <label class="order-filter-dropdown-option">
                                        <input type="checkbox"
                                               name="order_type[]"
                                               value="{{ $value }}"
                                               data-filter-label="{{ $label }}"
                                               {{ ($value === 'all' ? $orderTypeAllSelected : (!$orderTypeAllSelected && in_array($value, $selectedOrderTypes, true))) ? 'checked' : '' }}>
                                        <span>{{ $label }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                <hr />
                <div class="form-group row mb-4">
                    <label class="col-sm-4 col-form-label">{{ __('Order Channel') }}</label>
                    <div class="col-sm-8">
                        <div class="order-filter-dropdown" data-filter-group="order_channel" data-default-label="{{ __('All') }}">
                            <button type="button" class="order-filter-dropdown-toggle" aria-haspopup="true" aria-expanded="false"></button>
                            <div class="order-filter-dropdown-menu">
                                @foreach($orderChannelOptions as $value => $label)
                                    <label class="order-filter-dropdown-option">
                                        <input type="checkbox"
                                               name="order_channel[]"
                                               value="{{ $value }}"
                                               data-filter-label="{{ $label }}"
                                               {{ ($value === 'all' ? $orderChannelAllSelected : (!$orderChannelAllSelected && in_array($value, $selectedOrderChannels, true))) ? 'checked' : '' }}>
                                        <span>{{ $label }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                <hr />
                <div class="form-group row mb-4">
                    <label class="col-sm-7">{{ __('Payment Status') }}</label>
                    <div class="col-sm-5">
                        <select class="form-control selectric" name="payment_status" id="payment_status">
                            <option value="" {{ !$request->filled('payment_status') ? 'selected' : '' }}>{{ __('Select') }}</option>
                            <option value="2" {{ (string) $request->payment_status === '2' ? 'selected' : '' }}>{{ __('Pending') }}</option>
                            <option value="1" {{ (string) $request->payment_status === '1' ? 'selected' : '' }}>{{ __('Complete') }}</option>
                            <option value="3" {{ (string) $request->payment_status === '3' ? 'selected' : '' }}>{{ __('Incomplete') }}</option>
                            <option value="0" {{ (string) $request->payment_status === '0' ? 'selected' : '' }}>{{ __('Cancel') }}</option>
                        </select>
                    </div>
                </div>
                <hr />
                <div class="form-group row mb-4">
                    <label class="col-sm-7">{{ __('Fulfillment status') }}</label>
                    <div class="col-sm-5">
                        <select class="form-control selectric" name="status" id="status" >
                            <option value="" {{ !$request->filled('status') ? 'selected' : '' }}>{{ __('Select') }}</option>
                          @foreach($status as $row)
                            <option value="{{ $row->id }}" {{ $request->filled('status') && (string) $request->status === (string) $row->id ? 'selected' : '' }}>{{ $row->name }}</option>
                           @endforeach
                        </select>
                    </div>
                </div>
                <hr />
                <div class="form-group mb-4">
                    <div class="order-filter-between-row">
                        <span class="order-filter-between-label">{{ __('Order Date Between:') }}</span>
                        <input type="date" name="start" class="form-control order-filter-date-input" value="{{ $request->start }}" />
                        <span class="order-filter-between-sep">{{ __('and') }}</span>
                        <input type="date" name="end" class="form-control order-filter-date-input" value="{{ $request->end }}" />
                    </div>
                </div>
                <hr />
                <div class="form-group mb-4">
                    <div class="order-filter-between-row">
                        <span class="order-filter-between-label">{{ __('Order Number Between:') }}</span>
                        <input type="text"
                               name="order_no_from"
                               class="form-control order-filter-between-input order-filter-number-input"
                               value="{{ $request->order_no_from }}"
                               inputmode="numeric"
                               pattern="[0-9]*"
                               placeholder="#" />
                        <span class="order-filter-between-sep">{{ __('and') }}</span>
                        <input type="text"
                               name="order_no_to"
                               class="form-control order-filter-between-input order-filter-number-input"
                               value="{{ $request->order_no_to }}"
                               inputmode="numeric"
                               pattern="[0-9]*"
                               placeholder="#" />
                    </div>
                </div>
                <hr />
                <div class="form-group mb-0">
                    <div class="order-filter-between-row">
                        <span class="order-filter-between-label">{{ __('Order Total Between:') }}</span>
                        <input type="text"
                               name="total_from"
                               class="form-control order-filter-between-input order-filter-currency-input"
                               value="{{ $request->total_from }}"
                               inputmode="decimal"
                               placeholder="$" />
                        <span class="order-filter-between-sep">{{ __('and') }}</span>
                        <input type="text"
                               name="total_to"
                               class="form-control order-filter-between-input order-filter-currency-input"
                               value="{{ $request->total_to }}"
                               inputmode="decimal"
                               placeholder="$" />
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <a href="{{ route('seller.order.index') }}" class="btn btn-secondary">{{ __('Clear Filter') }}</a>
                <button type="submit" class="btn btn-primary">{{ __('Filter') }}</button>
            </div>
            </form>
        </div>
    </div>
</div>
<input type="hidden" id="payment" value="{{ $request->payment_status ?? '' }}">
<input type="hidden" id="order_status" value="{{ $request->status ?? '' }}">
@endsection

@push('js')
<!--<script src="{{ asset('assets/js/form.js') }}"></script>-->
<!--<script src="{{ asset('assets/js/order_index.js') }}"></script>-->
@endpush
<!-- jQuery (must be before your custom JS) -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<script>
$(document).ready(function() {
    function updateOrderFilterDropdownLabel($dropdown) {
        var defaultLabel = $dropdown.data('default-label') || 'All';
        var $toggle = $dropdown.find('.order-filter-dropdown-toggle');
        var $checked = $dropdown.find('input[type="checkbox"]:checked');
        var labels = [];

        $checked.each(function() {
            labels.push($(this).data('filter-label') || $(this).val());
        });

        if (!$checked.length || $dropdown.find('input[value="all"]').is(':checked')) {
            $toggle.text(defaultLabel);
            return;
        }

        $toggle.text(labels.join(', '));
    }

    function initOrderFilterDropdowns() {
        $('.order-filter-dropdown').each(function() {
            updateOrderFilterDropdownLabel($(this));
        });
    }

    $('.order-filter-dropdown-toggle').on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();

        var $dropdown = $(this).closest('.order-filter-dropdown');
        var isOpen = $dropdown.hasClass('is-open');

        $('.order-filter-dropdown').removeClass('is-open')
            .find('.order-filter-dropdown-toggle').attr('aria-expanded', 'false');

        if (!isOpen) {
            $dropdown.addClass('is-open');
            $(this).attr('aria-expanded', 'true');
        }
    });

    $('.order-filter-dropdown').on('change', 'input[type="checkbox"]', function() {
        var $dropdown = $(this).closest('.order-filter-dropdown');
        var $all = $dropdown.find('input[value="all"]');
        var $specific = $dropdown.find('input[type="checkbox"]:not([value="all"])');

        if ($(this).val() === 'all') {
            if ($all.is(':checked')) {
                $specific.prop('checked', false);
            } else if (!$specific.filter(':checked').length) {
                $all.prop('checked', true);
            }
        } else if ($(this).is(':checked')) {
            $all.prop('checked', false);
        } else if (!$specific.filter(':checked').length) {
            $all.prop('checked', true);
        }

        updateOrderFilterDropdownLabel($dropdown);
    });

    $(document).on('click', function(e) {
        if (!$(e.target).closest('.order-filter-dropdown').length) {
            $('.order-filter-dropdown').removeClass('is-open')
                .find('.order-filter-dropdown-toggle').attr('aria-expanded', 'false');
        }
    });

    $('#orderFilterForm').on('submit', function() {
        $('.order-filter-dropdown').each(function() {
            var $dropdown = $(this);
            if ($dropdown.find('input[value="all"]').is(':checked')) {
                $dropdown.find('input[type="checkbox"]:not([value="all"])').prop('disabled', true);
            }
        });
    });

    $('.order-filter-number-input').on('input', function() {
        this.value = this.value.replace(/\D/g, '');
    });

    $('.order-filter-currency-input').on('input', function() {
        var cleaned = this.value.replace(/[^\d.]/g, '');
        var parts = cleaned.split('.');

        if (parts.length > 2) {
            cleaned = parts[0] + '.' + parts.slice(1).join('');
        }

        if (parts.length === 2 && parts[1].length > 2) {
            cleaned = parts[0] + '.' + parts[1].substring(0, 2);
        }

        this.value = cleaned;
    });

    if ($('#payment').val()) {
        $('#payment_status').val($('#payment').val());
    }

    if ($('#order_status').val()) {
        $('#status').val($('#order_status').val());
    }

    initOrderFilterDropdowns();

    // Submit handler for the bulk form
    $('#bulkActionForm').on('submit', function(e){
        e.preventDefault(); // stop page refresh

        let method = $('select[name="method"][form="bulkActionForm"]').val();
        if(method === 'capture_authorized'){
            $('#captureModal').modal('show');
        } else if (method === 'set_capture_payment'){
            let checked = $('input[name="ids[]"]:checked');
            if (checked.length === 0) {
                alert('Please select at least one order.');
                return;
            }
            $('#setCapturePaymentModal').modal('show');
        } else if (method === 'complete_fulfillment'){ 
            $('#fulfillModal').modal('show'); 
        } else if (method === 'cancel_order'){
            let checked = $('input[name="ids[]"]:checked');

            if (checked.length === 0) {
                alert('Please select at least one order.');
                return;
            }

            if (checked.length > 1) {
                alert('Please select only one order to cancel and refund.');
                return;
            }

            let orderId = checked.first().val();
            window.location.href = @json(url('/seller/order')) + '/' + orderId + '?refund=1';
        } else if (method === 'mark_pending'){ 
            $('#pendingModal').modal('show'); 
        } else if (method === 'delete'){ 
            $('#deleteModal').modal('show'); 
        } else {
            sendAjax($(this)); // other actions
        }
    });

    // Proceed in Capture modal
    $('#proceedCapture').on('click', function(){
        let form = $('#bulkActionForm');
        sendAjax(form);
        $('#captureModal').modal('hide');
    });

    // Proceed in set capture payment modal (cash/check only)
    $('#proceedSetCapturePayment').on('click', function(){
        let form = $('#bulkActionForm');
        sendAjax(form);
        $('#setCapturePaymentModal').modal('hide');
    });

    // Proceed in Fulfill modal
    $('#proceedFulfill').on('click', function(){
        let form = $('#bulkActionForm');
        sendAjax(form);
        $('#fulfillModal').modal('hide');
    });

    // Proceed in Cancel modal
    $('#proceedCancel').on('click', function(){
        let form = $('#bulkActionForm');
        sendAjax(form);
        $('#cancelModal').modal('hide');
    });

    // Proceed in Pending modal
    $('#proceedPending').on('click', function(){
        let form = $('#bulkActionForm');
        sendAjax(form);
        $('#pendingModal').modal('hide');
    });

    // Proceed in Delete modal
    $('#proceedDelete').on('click', function(){
        let form = $('#bulkActionForm');
        sendAjax(form);
        $('#deleteModal').modal('hide');
    });

    function sendAjax(form){
        var data = form.serialize();
        var $methodSelect = $('select[name="method"][form="bulkActionForm"]');

        if ($methodSelect.length) {
            data += (data ? '&' : '') + encodeURIComponent($methodSelect.attr('name')) + '=' + encodeURIComponent($methodSelect.val() || '');
        }

        $.ajax({
            url: form.attr('action'),
            method: form.attr('method'),
            data: data,
            success: function(res){
                alert(res.message);
                location.reload(); // reload page
            },
            error: function(err){
                alert(err.responseJSON?.error || 'Something went wrong!');
            }
        });
    }
});

</script>

<!-- Capture Authorized Payments Modal -->
<div class="modal fade" id="captureModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header text-center d-block">
        <h5 class="modal-title w-100 font-weight-bold">Fraud Protection</h5>
      </div>
      <div class="modal-body text-center">
        <p>
          Bulk capturing order payments will only capture authorized payments for <strong>low risk</strong> orders.<br>
          If you have selected any other risk level, those orders will be skipped, and you will need to manually capture those higher-risk orders individually.
        </p>
      </div>
      <div class="modal-footer justify-content-center">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" id="proceedCapture">Proceed</button>
      </div>
    </div>
  </div>
</div>

<!-- Set Capture Payment Modal (cash/check only) -->
<div class="modal fade" id="setCapturePaymentModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header text-center d-block">
        <h5 class="modal-title w-100 font-weight-bold">set capture payment</h5>
      </div>
      <div class="modal-body text-center">
        <p>
          This action only works for <strong>cash/check</strong> orders.<br>
          It will sync the selected cash/check payment(s) to Financial Manager and send the user receipt.
        </p>
      </div>
      <div class="modal-footer justify-content-center">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" id="proceedSetCapturePayment">Proceed</button>
      </div>
    </div>
  </div>
</div>

<!-- Complete Fulfillment Modal -->
<div class="modal fade" id="fulfillModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header text-center d-block">
        <h5 class="modal-title w-100 font-weight-bold">Limitations</h5>
      </div>
      <div class="modal-body text-center">
        <p>
          Choosing bulk <strong>Complete Order Fulfillment</strong> will only complete digital orders that:
          <br><br>
          - Payment status is <strong>Complete (Paid)<br>
          - Do not require shipping/tracking information<br><br>
          Any order with authorized payment status, refunded payment status, or the order contains physical goods that need to be shipped will need to be managed and/or completed manually.
        </p>
      </div>
      <div class="modal-footer justify-content-center">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" id="proceedFulfill">Proceed</button>
      </div>
    </div>
  </div>
</div>


<!-- Cancel Order Modal -->
<div class="modal fade" id="cancelModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header text-center d-block">
        <h5 class="modal-title w-100 font-weight-bold">Cancel Order Confirmation</h5>
      </div>
      <div class="modal-body text-center">
        <p>
          This will cancel the selected orders and process refunds where applicable.<br>
          This action cannot be undone for refunded orders.
        </p>
      </div>
      <div class="modal-footer justify-content-center">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-danger" id="proceedCancel">Proceed</button>
      </div>
    </div>
  </div>
</div>

<!-- Mark As Pending Modal -->
<div class="modal fade" id="pendingModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header text-center d-block">
        <h5 class="modal-title w-100 font-weight-bold">Mark As Pending Confirmation</h5>
      </div>
      <div class="modal-body text-center">
        <p>
          This will mark the selected orders as pending.<br>
          This will update the fulfillment status for the orders.
        </p>
      </div>
      <div class="modal-footer justify-content-center">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-warning" id="proceedPending">Proceed</button>
      </div>
    </div>
  </div>
</div>

<!-- Delete Permanently Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header text-center d-block">
        <h5 class="modal-title w-100 font-weight-bold">Delete Confirmation</h5>
      </div>
      <div class="modal-body text-center">
        <p>
          This will permanently delete uncaptured orders only.<br>
          Captured or refunded orders will be skipped.
        </p>
      </div>
      <div class="modal-footer justify-content-center">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-danger" id="proceedDelete">Proceed</button>
      </div>
    </div>
  </div>
</div>


