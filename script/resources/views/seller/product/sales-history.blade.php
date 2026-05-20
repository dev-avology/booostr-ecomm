@extends('layouts.backend.app')

@section('title','Purchase History')

@section('style')
<style>
    .purchase-history-card { padding: 16px 18px; }

    .purchase-history-card table thead th {
        background: #f7f7f7;
        font-size: 13px;
        font-weight: 700;
        color: #555;
        padding: 15px 18px;
        border: none;
        white-space: nowrap;
    }

    .purchase-history-card table tbody td {
        padding: 14px 18px;
        border: none;
        color: #777;
        font-size: 13px;
        vertical-align: middle;
    }

.btn-action {
    background: #08bff3 !important;
    color: #fff !important;
    border: 1px solid #08bff3 !important;
    padding: 7px 13px;
    font-size: 12px;
    border-radius: 3px;
}

.ticket-id {
    max-width: 170px;
    min-width: 170px;
    word-break: break-word;
    line-height: 18px;
    padding-right: 20px !important;
    padding-bottom: 12px !important;
    font-size: 11px !important;
}

    .dropdown-menu { min-width: 180px; }
    .dropdown-item { font-size: 13px; padding: 8px 14px; }
    
    .btn-action:hover,
.btn-action:focus,
.btn-action:active,
.btn-action.dropdown-toggle.show {
    background: #08bff3 !important;
    color: #fff !important;
    border-color: #08bff3 !important;
    box-shadow: none !important;
}

.btn-light:hover,
.btn-light:focus,
.btn-light:active {
    background: #d9d9d9 !important;
    border-color: #d9d9d9 !important;
    color: #fff !important;
    box-shadow: none !important;
}

.ticket-action-disabled,
.ticket-action-disabled:hover,
.ticket-action-disabled:focus,
.ticket-action-disabled:active {
    background: #d9d9d9 !important;
    border: none !important;
    color: #fff !important;
    box-shadow: none !important;
    cursor: not-allowed;
    opacity: 1;
}

.sales-history-actions-label {
    font-size: 13px;
    font-weight: 700;
    color: #555;
    margin-bottom: 8px;
}

.btn-edit-crm-sync {
    background: #fff;
    color: #08bff3;
    border: 1px solid #d9d9d9;
    padding: 7px 14px;
    font-size: 12px;
    border-radius: 3px;
    white-space: nowrap;
}

.btn-edit-crm-sync:hover,
.btn-edit-crm-sync:focus {
    background: #fff;
    color: #08bff3;
    border-color: #08bff3;
    box-shadow: none;
}

.sales-history-actions-row {
    display: flex;
    justify-content: flex-end;
    align-items: center;
    flex-wrap: wrap;
    gap: 8px;
}

#crmSyncModal .modal-content {
    border: none;
    border-radius: 4px;
    overflow: hidden;
}

#crmSyncModal .modal-header {
    background: #08bff3;
    border-bottom: none;
    padding: 16px 20px;
    align-items: center;
}

#crmSyncModal .modal-header .modal-title {
    color: #fff;
    font-size: 18px;
    font-weight: 700;
    text-align: left;
}

#crmSyncModal .modal-header .close {
    color: #fff;
    opacity: 1;
    text-shadow: none;
    font-size: 26px;
    font-weight: 300;
    padding: 0;
    margin: 0;
}

#crmSyncModal .modal-body {
    padding: 22px 26px 10px;
    font-size: 13px;
    color: #4a4a4a;
}

#crmSyncModal .crm-sync-desc {
    line-height: 1.65;
    color: #4a4a4a;
    margin-bottom: 14px;
}

#crmSyncModal .crm-sync-last-date {
    font-weight: 700;
    color: #333;
    margin-bottom: 22px;
}

#crmSyncModal .crm-sync-field-label {
    display: block;
    font-weight: 400;
    color: #9a9a9a;
    font-size: 13px;
    margin-bottom: 8px;
    line-height: 1.4;
}

#crmSyncModal .crm-sync-field-label .text-danger {
    color: #e74c3c !important;
}

#crmSyncModal .crm-sync-field-label .fa-info-circle {
    color: #08bff3;
    font-size: 14px;
}

#crmSyncModal .crm-sync-radio-group {
    margin-bottom: 22px;
    padding-left: 2px;
}

#crmSyncModal .crm-sync-radio-group .custom-control {
    margin-bottom: 10px;
    min-height: 22px;
    padding-left: 1.6rem;
}

#crmSyncModal .crm-sync-radio-group .custom-control-label {
    color: #4a4a4a;
    font-size: 13px;
    padding-top: 1px;
    cursor: default;
}

#crmSyncModal .crm-sync-radio-group .custom-control-label::before {
    border-color: #ccc;
    width: 1.1rem;
    height: 1.1rem;
    top: 0.12rem;
}

#crmSyncModal .crm-sync-radio-group .custom-control-label::after {
    width: 1.1rem;
    height: 1.1rem;
    top: 0.12rem;
}

#crmSyncModal .crm-sync-radio-group .custom-control-input:checked ~ .custom-control-label::before {
    border-color: #28a745;
    background-color: #fff;
}

#crmSyncModal .crm-sync-radio-group .custom-control-input:checked ~ .custom-control-label::after {
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='-4 -4 8 8'%3e%3ccircle r='3' fill='%2328a745'/%3e%3c/svg%3e");
}

#crmSyncModal .form-group {
    margin-bottom: 20px;
}

#crmSyncModal .crm-sync-select {
    height: 44px;
    border: 1px solid #e0e0e0;
    border-radius: 4px;
    color: #9a9a9a;
    font-size: 13px;
    padding: 8px 36px 8px 14px;
    background-color: #fff;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3e%3cpath fill='%23999' d='M1.41 0L6 4.58 10.59 0 12 1.41l-6 6-6-6z'/%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right 14px center;
    background-size: 12px 8px;
    -webkit-appearance: none;
    -moz-appearance: none;
    appearance: none;
}

#crmSyncModal .crm-sync-select.crm-sync-select-active {
    color: #333;
    font-weight: 700;
}

#crmSyncModal .crm-sync-tags-wrap {
    border: 1px solid #e0e0e0;
    border-radius: 4px;
    padding: 10px 12px;
    min-height: 48px;
    background: #fff;
    display: flex;
    flex-wrap: wrap;
    align-items: center;
}

#crmSyncModal .crm-sync-tag {
    display: inline-flex;
    align-items: center;
    background: #ececec;
    color: #666;
    border-radius: 4px;
    padding: 5px 10px;
    font-size: 12px;
    margin-right: 8px;
    margin-bottom: 4px;
}

#crmSyncModal .crm-sync-tag .crm-sync-tag-remove {
    margin-left: 8px;
    color: #999;
    font-size: 14px;
    line-height: 1;
    cursor: pointer;
}

#crmSyncModal .crm-sync-tag .crm-sync-tag-remove:hover {
    color: #666;
}

#crmSyncModal .crm-sync-tags-wrap.is-focused {
    border-color: #08bff3;
}

#crmSyncModal .crm-sync-tags-input {
    border: none;
    outline: none;
    min-width: 100px;
    flex: 1 1 120px;
    font-size: 13px;
    padding: 4px 0;
    background: transparent;
}

#crmSyncModal .crm-sync-helper {
    font-size: 11px;
    color: #b0b0b0;
    margin-bottom: 10px;
    line-height: 1.55;
}

#crmSyncModal .modal-footer {
    border-top: none;
    justify-content: center;
    padding: 8px 26px 28px;
}

#crmSyncModal .btn-update-crm-sync {
    background: #08bff3;
    border-color: #08bff3;
    color: #fff;
    min-width: 200px;
    padding: 11px 28px;
    font-weight: 700;
    font-size: 14px;
    border-radius: 4px;
}

#crmSyncModal .btn-update-crm-sync:hover,
#crmSyncModal .btn-update-crm-sync:focus {
    background: #07a8db;
    border-color: #07a8db;
    color: #fff;
    box-shadow: none;
}

#crmSyncModal .btn-update-crm-sync:disabled {
    opacity: 0.75;
    cursor: not-allowed;
}
</style>
@endsection

@section('head')
<div class="section-header">
    <h1>
        <a href="{{ route('seller.product.index') }}" style="color:#1f2d5c;text-decoration:none;margin-right:8px;">
            <i class="fas fa-arrow-left"></i>
        </a>
        Purchase History - {{ $product->title }}
    </h1>

    <div class="section-header-breadcrumb">
        <div class="breadcrumb-item">seller</div>
        <div class="breadcrumb-item">product</div>
        <div class="breadcrumb-item">{{ $product->id }}</div>
        <div class="breadcrumb-item">sales-history</div>
    </div>
</div>
@endsection

@section('content')

@php
    $isTicket = $product->is_variation == 2;
@endphp

@if($sales->total() == 0)

<div class="card purchase-history-card">
    <div class="card-body d-flex align-items-center justify-content-center" style="min-height:520px;">
        <div class="text-center">

            <h4 style="font-weight:700;color:#555;">
                There have been 0 sales of {{ $product->title }} to date.
            </h4>

            <br>

            <a href="{{ route('seller.product.index') }}"
               style="font-size:20px;font-weight:700;color:#08bff3;text-decoration:underline;">
                Go back to Product List
            </a>

        </div>
    </div>
</div>

@else

@php
    $crmSyncSanitize = function ($value) {
        $value = trim((string) ($value ?? ''));
        return ($value === '-' || $value === '') ? '' : $value;
    };

    $crmSyncMapSale = function ($sale) use ($isTicket, $crmSyncSanitize) {
        if ($isTicket) {
            $ticket = $sale;
            $order = $ticket->order ?? null;
            $ordermeta = json_decode($order->ordermeta->value ?? '{}');

            $fullName = $ticket->attendee_name ?? ($ordermeta->name ?? 'Guest User');
            $email = $ticket->attendee_email ?? ($ordermeta->email ?? '-');
            $phone = $ticket->attendee_phone ?? ($ordermeta->phone ?? '-');
            $city = '';

            if (is_object($ordermeta)) {
                if (!empty($ordermeta->city)) {
                    $city = $ordermeta->city;
                } elseif (!empty($ordermeta->billing) && is_object($ordermeta->billing) && !empty($ordermeta->billing->city)) {
                    $city = $ordermeta->billing->city;
                } elseif (!empty($ordermeta->shipping) && is_object($ordermeta->shipping) && !empty($ordermeta->shipping->city)) {
                    $city = $ordermeta->shipping->city;
                }
            }

            $nameParts = explode(' ', trim($fullName), 2);

            return [
                'first_name' => $crmSyncSanitize($nameParts[0] ?? ''),
                'last_name' => $crmSyncSanitize($nameParts[1] ?? ''),
                'email' => $crmSyncSanitize($email),
                'phone_number' => $crmSyncSanitize($phone),
                'city' => $crmSyncSanitize($city),
            ];
        }

        $order = $sale->order ?? null;
        $ordermeta = json_decode($order->ordermeta->value ?? '{}');

        $fullName = $ordermeta->name ?? 'Guest User';
        $email = $ordermeta->email ?? '-';
        $phone = $ordermeta->phone ?? '-';
        $city = '';

        if (is_object($ordermeta)) {
            if (!empty($ordermeta->city)) {
                $city = $ordermeta->city;
            } elseif (!empty($ordermeta->billing) && is_object($ordermeta->billing) && !empty($ordermeta->billing->city)) {
                $city = $ordermeta->billing->city;
            } elseif (!empty($ordermeta->shipping) && is_object($ordermeta->shipping) && !empty($ordermeta->shipping->city)) {
                $city = $ordermeta->shipping->city;
            }
        }

        $ticket = $sale->eventTicket ?? null;

        if ($isTicket && $ticket) {
            $fullName = $ticket->attendee_name ?? $fullName;
            $email = $ticket->attendee_email ?? $email;
            $phone = $ticket->attendee_phone ?? $phone;
        }

        $nameParts = explode(' ', trim($fullName), 2);

        return [
            'first_name' => $crmSyncSanitize($nameParts[0] ?? ''),
            'last_name' => $crmSyncSanitize($nameParts[1] ?? ''),
            'email' => $crmSyncSanitize($email),
            'phone_number' => $crmSyncSanitize($phone),
            'city' => $crmSyncSanitize($city),
        ];
    };

    $crmSyncPageContacts = [];
    foreach ($sales as $saleItem) {
        $crmSyncPageContacts[] = $crmSyncMapSale($saleItem);
    }

    $crmSyncAllQuery = $isTicket
        ? \App\Models\EventTicket::with(['order.ordermeta', 'orderItem'])
            ->where('term_id', $product->id)
        : \App\Models\Orderitem::with(['order.ordermeta', 'eventTicket'])
            ->where('term_id', $product->id);

    if (request()->filled('src')) {
        $crmSyncSearch = request('src');

        if ($isTicket) {
            $crmSyncAllQuery->where(function ($q) use ($crmSyncSearch) {
                $q->where('attendee_name', 'LIKE', "%{$crmSyncSearch}%")
                    ->orWhere('attendee_email', 'LIKE', "%{$crmSyncSearch}%")
                    ->orWhere('attendee_phone', 'LIKE', "%{$crmSyncSearch}%")
                    ->orWhere('ticket_uuid', 'LIKE', "%{$crmSyncSearch}%")
                    ->orWhere('status', 'LIKE', "%{$crmSyncSearch}%")
                    ->orWhereHas('order', function ($orderQuery) use ($crmSyncSearch) {
                        $orderQuery->where('invoice_no', 'LIKE', "%{$crmSyncSearch}%");
                    })
                    ->orWhereHas('order.ordermeta', function ($metaQuery) use ($crmSyncSearch) {
                        $metaQuery->where('value', 'LIKE', "%{$crmSyncSearch}%");
                    })
                    ->orWhereHas('orderItem', function ($itemQuery) use ($crmSyncSearch) {
                        $itemQuery->where('info', 'LIKE', "%{$crmSyncSearch}%");
                    });
            });
        } else {
            $crmSyncAllQuery->where(function ($q) use ($crmSyncSearch) {
                $q->where('info', 'LIKE', "%{$crmSyncSearch}%")
                    ->orWhereHas('order', function ($orderQuery) use ($crmSyncSearch) {
                        $orderQuery->where('invoice_no', 'LIKE', "%{$crmSyncSearch}%");
                    })
                    ->orWhereHas('order.ordermeta', function ($metaQuery) use ($crmSyncSearch) {
                        $metaQuery->where('value', 'LIKE', "%{$crmSyncSearch}%");
                    })
                    ->orWhereHas('eventTicket', function ($ticketQuery) use ($crmSyncSearch) {
                        $ticketQuery->where('attendee_name', 'LIKE', "%{$crmSyncSearch}%")
                            ->orWhere('attendee_email', 'LIKE', "%{$crmSyncSearch}%")
                            ->orWhere('attendee_phone', 'LIKE', "%{$crmSyncSearch}%")
                            ->orWhere('ticket_uuid', 'LIKE', "%{$crmSyncSearch}%")
                            ->orWhere('status', 'LIKE', "%{$crmSyncSearch}%");
                    });
            });
        }
    }

    $salesHistoryMapExportRow = function ($sale) use ($isTicket) {
        if ($isTicket) {
            $ticket = $sale;
            $order = $ticket->order ?? null;
            $ordermeta = json_decode($order->ordermeta->value ?? '{}');

            $fullName = $ticket->attendee_name ?? ($ordermeta->name ?? 'Guest User');
            $email = $ticket->attendee_email ?? ($ordermeta->email ?? '-');
            $phone = $ticket->attendee_phone ?? ($ordermeta->phone ?? '-');
            $variantText = '-';
            $ticketId = $ticket->ticket_uuid ?? '-';
            $ticketStatus = 'Not Checked In';

            $info = json_decode((optional($ticket->orderItem)->info ?? '{}'), true);

            if ($ticket->status == 'used') {
                $ticketStatus = 'Checked In';
            } elseif ($ticket->status == 'cancelled') {
                $ticketStatus = 'Cancelled & Refunded';
            } else {
                $ticketStatus = 'Not Checked In';
            }

            $nameParts = explode(' ', trim($fullName), 2);
            $firstName = $nameParts[0] ?? '-';
            $lastName = $nameParts[1] ?? '-';

            if ($order && ($order->order_from == 4 || $order->order_from == 5)) {
                $channel = 'Point of Sale';
            } elseif ($order && $order->order_from == 0) {
                $channel = 'POS(Web)';
            } else {
                $channel = 'Ecommerce';
            }

            $orderNo = '-';
            if (!empty($order->id)) {
                $orderNo = $order->invoice_no ?? str_pad($order->id, 7, '0', STR_PAD_LEFT);
            }

            return [
                $firstName ?: '-',
                $lastName ?: '-',
                $email,
                $phone,
                !empty($order->created_at) ? date('m/d/Y', strtotime($order->created_at)) : '-',
                $orderNo,
                $channel,
                $ticketId,
                $ticketStatus,
            ];
        }

        $order = $sale->order ?? null;
        $ordermeta = json_decode($order->ordermeta->value ?? '{}');

        $fullName = $ordermeta->name ?? 'Guest User';
        $email = $ordermeta->email ?? '-';
        $phone = $ordermeta->phone ?? '-';
        $variantText = '-';
        $ticketId = '-';
        $ticketStatus = 'Not Checked In';
        $info = json_decode($sale->info ?? '{}', true);

        if (!empty($info['options']) && is_array($info['options'])) {
            $variantNames = [];

            foreach ($info['options'] as $option) {
                if (!empty($option['varitions']) && is_array($option['varitions'])) {
                    foreach ($option['varitions'] as $variation) {
                        if (!empty($variation['name'])) {
                            $variantNames[] = $variation['name'];
                        }
                    }
                }
            }

            if (!empty($variantNames)) {
                $variantText = implode(', ', $variantNames);
            }
        }

        $ticket = $sale->eventTicket ?? null;

        if ($isTicket && $ticket) {
            $fullName = $ticket->attendee_name ?? $fullName;
            $email = $ticket->attendee_email ?? $email;
            $phone = $ticket->attendee_phone ?? $phone;
            $ticketId = $ticket->ticket_uuid ?? '-';

            if ($ticket->status == 'used') {
                $ticketStatus = 'Checked In';
            } elseif ($ticket->status == 'cancelled') {
                $ticketStatus = 'Cancelled & Refunded';
            } else {
                $ticketStatus = 'Not Checked In';
            }
        }

        $nameParts = explode(' ', trim($fullName), 2);
        $firstName = $nameParts[0] ?? '-';
        $lastName = $nameParts[1] ?? '-';

        if ($order && ($order->order_from == 4 || $order->order_from == 5)) {
            $channel = 'Point of Sale';
        } elseif ($order && $order->order_from == 0) {
            $channel = 'POS(Web)';
        } else {
            $channel = 'Ecommerce';
        }

        $orderNo = '-';
        if (!empty($order->id)) {
            $orderNo = $order->invoice_no ?? str_pad($order->id, 7, '0', STR_PAD_LEFT);
        }

        $row = [
            $firstName ?: '-',
            $lastName ?: '-',
            $email,
            $phone,
            !empty($order->created_at) ? date('m/d/Y', strtotime($order->created_at)) : '-',
            $orderNo,
            $channel,
        ];

        if ($isTicket) {
            $row[] = $ticketId;
            $row[] = $ticketStatus;
        } else {
            $row[] = $variantText;
        }

        return $row;
    };

    $salesHistoryExportHeaders = $isTicket
        ? ['FIRST NAME', 'LAST NAME', 'EMAIL', 'PHONE', 'ORDER DATE', 'ORDER #', 'CHANNEL', 'TICKET ID', 'TICKET STATUS']
        : ['FIRST NAME', 'LAST NAME', 'EMAIL', 'PHONE', 'ORDER DATE', 'ORDER #', 'CHANNEL', 'VARIANT ORDERED'];

    $crmSyncAllContacts = [];
    $salesHistoryExportRows = [];
    foreach ($crmSyncAllQuery->orderBy('id', 'desc')->get() as $saleItem) {
        $crmSyncAllContacts[] = $crmSyncMapSale($saleItem);
        $salesHistoryExportRows[] = $salesHistoryMapExportRow($saleItem);
    }

    $exportFilenameBase = 'sales-history-' . (\Illuminate\Support\Str::slug($product->title) ?: 'product') . '-' . time();

@endphp

<script type="application/json" id="crm-sync-contacts-page">@json($crmSyncPageContacts)</script>
<script type="application/json" id="crm-sync-contacts-all">@json($crmSyncAllContacts)</script>
<script type="application/json" id="sales-history-export-data">
{!! json_encode([
    'headers' => $salesHistoryExportHeaders,
    'rows' => $salesHistoryExportRows,
    'title' => $product->title,
    'export_filename_base' => $exportFilenameBase
]) !!}
</script>

<div class="card purchase-history-card">
    <div class="card-body">

        <div class="row mb-3 align-items-end">
            <div class="col-md-4">
                <form method="GET">
                    <label>Search</label>
                
                    <input type="text"
                           name="src"
                           class="form-control"
                           placeholder="search..."
                           value="{{ request('src') }}">
                </form>
                
            </div>

            <div class="col-md-5">
                <p class="mb-0">
                    @if($isTicket)
                        Below is a list of total sales of {{ $product->title }} tickets online.
                        You can update Ticket Status using the Action button.
                    @else
                        Below is a list of total sales of {{ $product->title }}
                        {{ $product->is_variation == 1 ? 'variant' : 'simple' }} product.
                    @endif
                </p>
            </div>

            <div class="col-md-3">
                <p class="sales-history-actions-label text-right mb-0">Sales History List Actions</p>
                <div class="sales-history-actions-row mt-2">
                    <button type="button"
                            class="btn btn-edit-crm-sync"
                            data-toggle="modal"
                            data-target="#crmSyncModal">
                        <i class="fas fa-sync-alt"></i> Edit CRM Sync
                    </button>
                    <form class="mb-0" id="sales_history_list_action_form" onsubmit="return false;">
                        <div class="input-group">
                            <select class="form-control" id="sales_history_list_action">
                                <option value="">Select Action</option>
                                <option value="export_excel">Export to Excel</option>
                                <option value="export_csv">Export to CSV</option>
                                <option value="print">Print Results</option>
                            </select>
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="button" id="sales_history_list_action_submit">Submit</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="table-responsive custom-table">
            <table class="table">
                <thead>
                    <tr>
                        <th>FIRST NAME</th>
                        <th>LAST NAME</th>
                        <th>EMAIL</th>
                        <th>PHONE</th>
                        <th>ORDER DATE</th>
                        <th>ORDER #</th>
                        <th>CHANNEL</th>

                        @if($isTicket)
                            <th>TICKET ID</th>
                            <th>TICKET STATUS</th>
                        @else
                            <th>VARIANT ORDERED</th>
                        @endif

                        <th class="text-right">ACTION</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($sales as $sale)
                        @php
                            if ($isTicket) {
                                $ticket = $sale;
                                $info = json_decode((optional($sale->orderItem)->info ?? '{}'), true);
                            } else {
                                $ticket = $sale->eventTicket ?? null;
                                $info = json_decode($sale->info ?? '{}', true);
                            }

                            $order = $sale->order ?? null;
                            $ordermeta = json_decode($order->ordermeta->value ?? '{}');

                            $fullName = $ordermeta->name ?? 'Guest User';
                            $email = $ordermeta->email ?? '-';
                            $phone = $ordermeta->phone ?? '-';

                            $variantText = '-';
                            $ticketId = '-';
                            $ticketStatus = 'Not Checked In';

                            if (!empty($info['options']) && is_array($info['options'])) {
                                $variantNames = [];

                                foreach ($info['options'] as $option) {
                                    if (!empty($option['varitions']) && is_array($option['varitions'])) {
                                        foreach ($option['varitions'] as $variation) {
                                            if (!empty($variation['name'])) {
                                                $variantNames[] = $variation['name'];
                                            }
                                        }
                                    }
                                }

                                if (!empty($variantNames)) {
                                    $variantText = implode(', ', $variantNames);
                                }
                            }

                            if ($isTicket && $ticket) {
                                $fullName = $ticket->attendee_name ?? $fullName;
                                $email = $ticket->attendee_email ?? $email;
                                $phone = $ticket->attendee_phone ?? $phone;
                                $ticketId = $ticket->ticket_uuid ?? '-';

                                if ($ticket->status == 'used') {
                                    $ticketStatus = 'Checked In';
                                } elseif ($ticket->status == 'cancelled') {
                                    $ticketStatus = 'Cancelled & Refunded';
                                } else {
                                    $ticketStatus = 'Not Checked In';
                                }
                            }

                            $nameParts = explode(' ', trim($fullName), 2);
                            $firstName = $nameParts[0] ?? '-';
                            $lastName = $nameParts[1] ?? '-';

                            if ($order && ($order->order_from == 4 || $order->order_from == 5)) {
                                $channel = 'Point of Sale';
                            } elseif ($order && $order->order_from == 0) {
                                $channel = 'POS(Web)';
                            } else {
                                $channel = 'Ecommerce';
                            }
                        @endphp

                        <tr>
                            <td>{{ $firstName ?: '-' }}</td>
                            <td>{{ $lastName ?: '-' }}</td>
                            <td>{{ $email }}</td>
                            <td>{{ $phone }}</td>
                            <td>{{ !empty($order->created_at) ? date('m/d/Y', strtotime($order->created_at)) : '-' }}</td>
                            <td>
                                @if(!empty($order->id))
                                    <a href="{{ route('seller.order.show', $order->id) }}">
                                        {{ $order->invoice_no ?? str_pad($order->id, 7, '0', STR_PAD_LEFT) }}
                                    </a>
                                @else
                                    -
                                @endif
                            </td>
                            <td>{{ $channel }}</td>

                            @if($isTicket)
                                <td class="ticket-id">{{ $ticketId }}</td>
                                <td>{{ $ticketStatus }}</td>
                            @else
                                <td>{{ $variantText }}</td>
                            @endif

                            <td class="text-right">
                                @if($isTicket && !empty($ticket))
                                    @if($ticket->status == 'cancelled')
                                        <button class="btn btn-light btn-sm ticket-action-disabled"
                                                type="button"
                                                disabled>
                                            Action <i class="fas fa-caret-down"></i>
                                        </button>
                                    @else
                                        <div class="dropdown">
                                            <button class="btn btn-action dropdown-toggle" type="button" data-toggle="dropdown">
                                                Action
                                            </button>

                                            <div class="dropdown-menu">
                                                <a class="dropdown-item ticket-status-update" href="#" data-id="{{ $ticket->id }}" data-status="used">Checked In</a>
                                                <a class="dropdown-item ticket-status-update" href="#" data-id="{{ $ticket->id }}" data-status="active">Not Checked In</a>
                                                <a class="dropdown-item ticket-status-update" href="#" data-id="{{ $ticket->id }}" data-status="cancelled">Cancelled & Refunded</a>
                                            </div>
                                        </div>
                                    @endif
                                @else
                                   <button class="btn btn-light btn-sm"
                                        style="background:#d9d9d9;color:#fff;border:none;"
                                        type="button">
                                        Action <i class="fas fa-caret-down"></i>
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $isTicket ? 10 : 9 }}" class="text-center py-5">
                                No sales found for this product.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            {{ $sales->appends(request()->all())->links('vendor.pagination.bootstrap-4') }}
        </div>

    </div>
</div>

{{-- Booostr CRM Sync modal (UI only) --}}
<div class="modal fade" id="crmSyncModal" tabindex="-1" role="dialog" aria-labelledby="crmSyncModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="crmSyncModalLabel">Create Booostr CRM Sync</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p class="crm-sync-desc mb-0">
                    Create a data sync of product sales history data into your CRM. Set a one-time or continual sync of this product customer contact data to your CRM custom contact groups and/or add additional contact tags to give you more supporter insights.
                </p>

                <p class="crm-sync-last-date mb-0">
                    <span>Last Sync Date:</span> 03/24/2026 (one time)
                </p>

                <div class="form-group mb-0">
                    <label class="crm-sync-field-label">
                        Choose Sync recurrence for this product history user data
                        <i class="fas fa-info-circle ml-1" title="Choose how often sales history contacts are synced to your CRM."></i>
                    </label>
                    <div class="crm-sync-radio-group">
                        <div class="custom-control custom-radio">
                            <input type="radio" id="crm_sync_onetime" name="crm_sync_recurrence" class="custom-control-input" value="one_time">
                            <label class="custom-control-label" for="crm_sync_onetime">One-Time Sync (only current data)</label>
                        </div>
                        <div class="custom-control custom-radio">
                            <input type="radio" id="crm_sync_continuous" name="crm_sync_recurrence" class="custom-control-input" value="continuous" checked>
                            <label class="custom-control-label" for="crm_sync_continuous">Continuous Sync (current &amp; future data)</label>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="crm-sync-field-label" for="crm_sync_pass_amount">
                        How much of this list do you want to pass?<span class="text-danger">*</span>
                    </label>
                    <select id="crm_sync_pass_amount" class="form-control crm-sync-select">
                        <option value="all" selected>All Results</option>
                        <option value="page">Just Only this page of result</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="crm-sync-field-label" for="crm_sync_list">
                        Choose an existing Contact Manager list or create new contact list.
                    </label>
                    <select id="crm_sync_list" class="form-control crm-sync-select crm-sync-select-active">
                        <option selected>{{ $product->title }} Purchasers</option>
                    </select>
                </div>

                <div class="form-group mb-0">
                    <label class="crm-sync-field-label" for="crm_sync_tags_input">
                        Add Contact Manager tags to contact record
                    </label>
                    <p class="crm-sync-helper mb-0">
                        Start typing tags to choose from existing CRM tags. To add new tags, type tag and hit enter after each tag. To delete a typed tag, click the &ldquo;x&rdquo; next to it. Tags will be added when submitted.
                    </p>
                    <div class="crm-sync-tags-wrap mt-2" id="crm_sync_tags_wrap">
                        <input type="text"
                               id="crm_sync_tags_input"
                               class="crm-sync-tags-input"
                               placeholder=""
                               autocomplete="off"
                               aria-label="Add Contact Manager tags">
                    </div>
                    <input type="hidden" id="crm_sync_tags" name="crm_sync_tags" value="">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-update-crm-sync" id="crm_sync_submit_btn">Update CRM Sync</button>
            </div>
        </div>
    </div>
</div>

@if($isTicket)
{{-- Ticket cancel & refund confirmation --}}
<div class="modal fade" id="ticketCancelRefundModal" tabindex="-1" role="dialog" aria-labelledby="ticketCancelRefundModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content rounded shadow">
            <div class="modal-header">
                <h5 class="modal-title" id="ticketCancelRefundModalLabel">Confirm Cancel &amp; Refund</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Are you sure you want to Cancel &amp; Refund this ticket? This is not reversible and will automatically refund the ticket amount to the purchaser.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" id="ticket_cancel_refund_confirm">Yes, Cancel Ticket</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>
@endif

@endif

@push('script')
<script>
var pendingTicketStatusUpdate = null;

function submitTicketStatusUpdate(ticketId, status) {
    $.ajax({
        url: "{{ route('seller.product.ticket.status.update') }}",
        type: "POST",
        data: {
            _token: "{{ csrf_token() }}",
            ticket_id: ticketId,
            status: status
        },
        success: function(response) {
            if (response && response.success === false) {
                alert(response.message || 'Something went wrong');
                return;
            }
            location.reload();
        },
        error: function(xhr) {
            var message = (xhr.responseJSON && xhr.responseJSON.message)
                ? xhr.responseJSON.message
                : 'Something went wrong';
            alert(message);
        }
    });
}

$(document).on('click', '.ticket-status-update', function(e) {
    e.preventDefault();

    var ticketId = $(this).data('id');
    var status = $(this).data('status');

    if (status === 'cancelled') {
        pendingTicketStatusUpdate = {
            ticketId: ticketId,
            status: status
        };
        $('#ticketCancelRefundModal').modal('show');
        return;
    }

    submitTicketStatusUpdate(ticketId, status);
});

$('#ticket_cancel_refund_confirm').on('click', function() {
    if (!pendingTicketStatusUpdate) {
        return;
    }

    submitTicketStatusUpdate(pendingTicketStatusUpdate.ticketId, pendingTicketStatusUpdate.status);
    $('#ticketCancelRefundModal').modal('hide');
});

$('#ticketCancelRefundModal').on('hidden.bs.modal', function() {
    pendingTicketStatusUpdate = null;
});

(function () {
    var $modal = $('#crmSyncModal');
    if (!$modal.length) {
        return;
    }

    var $wrap = $modal.find('#crm_sync_tags_wrap');
    var $input = $modal.find('#crm_sync_tags_input');
    var $hidden = $modal.find('#crm_sync_tags');
    var tags = [];

    function syncHidden() {
        $hidden.val(tags.join(','));
    }

    function renderTags() {
        $wrap.find('.crm-sync-tag').remove();

        tags.forEach(function (tag) {
            var $tag = $('<span class="crm-sync-tag"></span>').attr('data-tag', tag);
            $tag.append(document.createTextNode(tag));
            $tag.append(
                $('<span class="crm-sync-tag-remove" role="button" tabindex="0" aria-label="Remove tag"></span>')
                    .html('&times;')
            );
            $input.before($tag);
        });

        syncHidden();
    }

    function addTag(value) {
        var tag = $.trim(value);

        if (!tag) {
            return;
        }

        var exists = tags.some(function (item) {
            return item.toLowerCase() === tag.toLowerCase();
        });

        if (exists) {
            $input.val('');
            return;
        }

        tags.push(tag);
        $input.val('');
        renderTags();
    }

    function removeTag(tag) {
        tags = tags.filter(function (item) {
            return item !== tag;
        });
        renderTags();
    }

    $wrap.on('click', function () {
        $input.focus();
    });

    $input.on('focus', function () {
        $wrap.addClass('is-focused');
    });

    $input.on('blur', function () {
        $wrap.removeClass('is-focused');
    });

    $input.on('keydown', function (e) {
        if (e.key === 'Enter' || e.key === ',') {
            e.preventDefault();
            addTag($input.val());
            return;
        }

        if (e.key === 'Backspace' && !$input.val() && tags.length) {
            tags.pop();
            renderTags();
        }
    });

    $wrap.on('click', '.crm-sync-tag-remove', function (e) {
        e.preventDefault();
        e.stopPropagation();
        removeTag($(this).closest('.crm-sync-tag').attr('data-tag'));
    });

    $wrap.on('keydown', '.crm-sync-tag-remove', function (e) {
        if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            removeTag($(this).closest('.crm-sync-tag').attr('data-tag'));
        }
    });

    var CRM_SYNC_API = 'https://app4.booostr.co/wp-json/booostr/v1/save-contact';
    var CRM_SYNC_BOOSTER_ID = 36115;
    var $syncBtn = $modal.find('#crm_sync_submit_btn');

    function parseCrmContacts(elementId) {
        var $el = $('#' + elementId);
        if (!$el.length) {
            return [];
        }
        try {
            var parsed = JSON.parse($el.text() || '[]');
            return Array.isArray(parsed) ? parsed : [];
        } catch (e) {
            return [];
        }
    }

    function postCrmContact(contact, contactTags) {
        return $.ajax({
            url: CRM_SYNC_API,
            type: 'POST',
            contentType: 'application/json',
            dataType: 'json',
            data: JSON.stringify({
                booster_id: CRM_SYNC_BOOSTER_ID,
                first_name: contact.first_name || '',
                last_name: contact.last_name || '',
                email: contact.email || '',
                phone_number: contact.phone_number || '',
                city: contact.city || '',
                contact_tags: contactTags
            })
        });
    }

    $syncBtn.on('click', function () {
        if ($syncBtn.prop('disabled')) {
            return;
        }

        var passScope = $modal.find('#crm_sync_pass_amount').val();
        var contacts = passScope === 'page'
            ? parseCrmContacts('crm-sync-contacts-page')
            : parseCrmContacts('crm-sync-contacts-all');

        var contactTags = $.trim($hidden.val() || tags.join(','));

        if (!contacts.length) {
            alert('No contacts available to sync.');
            return;
        }

        var successCount = 0;
        var failedCount = 0;
        var originalText = $syncBtn.text();

        $syncBtn.prop('disabled', true).text('Syncing...');

        function syncNext(index) {
            if (index >= contacts.length) {
                $syncBtn.prop('disabled', false).text(originalText);
                alert('CRM sync finished. Success: ' + successCount + ', Failed: ' + failedCount + '.');
                return;
            }

            postCrmContact(contacts[index], contactTags)
                .done(function () {
                    successCount++;
                })
                .fail(function () {
                    failedCount++;
                })
                .always(function () {
                    syncNext(index + 1);
                });
        }

        syncNext(0);
    });
})();

(function () {
    var $exportDataEl = $('#sales-history-export-data');
    var $actionSelect = $('#sales_history_list_action');
    var $actionSubmit = $('#sales_history_list_action_submit');

    if (!$exportDataEl.length || !$actionSelect.length || !$actionSubmit.length) {
        return;
    }

    var exportPayload = { headers: [], rows: [], title: 'Purchase History' };

    try {
        exportPayload = JSON.parse($exportDataEl.text() || '{}');
    } catch (e) {
        exportPayload = { headers: [], rows: [], title: 'Purchase History' };
    }

    function escapeCsvValue(value) {
        var text = String(value == null ? '' : value);
        if (text.search(/("|,|\r|\n)/g) >= 0) {
            return '"' + text.replace(/"/g, '""') + '"';
        }
        return text;
    }

    function buildCsvContent() {
        var lines = [];
        lines.push(exportPayload.headers.map(escapeCsvValue).join(','));
        (exportPayload.rows || []).forEach(function (row) {
            lines.push((row || []).map(escapeCsvValue).join(','));
        });
        return lines.join('\r\n');
    }

    function downloadFile(filename, content, mimeType) {
        var blob = new Blob([content], { type: mimeType });
        var url = URL.createObjectURL(blob);
        var link = document.createElement('a');
        link.href = url;
        link.download = filename;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        URL.revokeObjectURL(url);
    }

    function getExportFilename(extension) {
        var base = exportPayload.export_filename_base || ('sales-history-' + Date.now());
        return base + '.' + extension;
    }

    function exportToCsv() {
        if (!(exportPayload.rows || []).length) {
            alert('No results available to export.');
            return;
        }
        var csv = buildCsvContent();
        downloadFile(getExportFilename('csv'), '\ufeff' + csv, 'text/csv;charset=utf-8;');
    }

    function exportToExcel() {
        if (!(exportPayload.rows || []).length) {
            alert('No results available to export.');
            return;
        }
        var html = '<html><head><meta charset="UTF-8"></head><body>';
        html += '<table border="1"><thead><tr>';
        (exportPayload.headers || []).forEach(function (header) {
            html += '<th>' + $('<div>').text(header).html() + '</th>';
        });
        html += '</tr></thead><tbody>';
        (exportPayload.rows || []).forEach(function (row) {
            html += '<tr>';
            (row || []).forEach(function (cell) {
                html += '<td>' + $('<div>').text(cell).html() + '</td>';
            });
            html += '</tr>';
        });
        html += '</tbody></table></body></html>';
        downloadFile(getExportFilename('xls'), html, 'application/vnd.ms-excel;charset=utf-8;');
    }

    function printResults() {
        if (!(exportPayload.rows || []).length) {
            alert('No results available to print.');
            return;
        }

        var printWindow = window.open('', '_blank');
        if (!printWindow) {
            alert('Please allow pop-ups to print results.');
            return;
        }

        var doc = printWindow.document;
        var title = exportPayload.title || 'Purchase History';
        var styles = 'body{font-family:Arial,sans-serif;font-size:12px;padding:20px;color:#333;}h2{margin:0 0 16px;}table{width:100%;border-collapse:collapse;}th,td{border:1px solid #ddd;padding:8px;text-align:left;}th{background:#f7f7f7;font-weight:700;}';
        var html = '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>' + $('<div>').text(title).html() + ' - Sales History</title><style>' + styles + '</style></head><body>';
        html += '<h2>' + $('<div>').text(title).html() + ' - Sales History</h2>';
        html += '<table><thead><tr>';
        (exportPayload.headers || []).forEach(function (header) {
            html += '<th>' + $('<div>').text(header).html() + '</th>';
        });
        html += '</tr></thead><tbody>';
        (exportPayload.rows || []).forEach(function (row) {
            html += '<tr>';
            (row || []).forEach(function (cell) {
                html += '<td>' + $('<div>').text(cell).html() + '</td>';
            });
            html += '</tr>';
        });
        html += '</tbody></table></body></html>';

        doc.open();
        doc.write(html);
        doc.close();
        printWindow.focus();
        printWindow.print();
    }

    $actionSubmit.on('click', function () {
        var action = $actionSelect.val();

        if (!action) {
            alert('Please select an action from the dropdown.');
            return;
        }

        if (action === 'export_excel') {
            exportToExcel();
        } else if (action === 'export_csv') {
            exportToCsv();
        } else if (action === 'print') {
            printResults();
        }

        $actionSelect.val('');
    });
})();
</script>
@endpush
@endsection