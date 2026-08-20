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

.sales-history-top {
    margin-bottom: 18px;
}

.sales-history-desc {
    font-size: 13px;
    color: #777;
    margin-bottom: 14px;
}

.sales-history-toolbar {
    display: flex;
    flex-wrap: wrap;
    align-items: flex-end;
    justify-content: space-between;
    gap: 16px 20px;
}

.sales-history-search-group {
    flex: 0 1 320px;
    min-width: 220px;
    max-width: 360px;
}

.sales-history-search-group label {
    font-size: 13px;
    font-weight: 700;
    color: #555;
    margin-bottom: 8px;
    display: block;
}

.sales-history-actions-wrap {
    flex: 0 1 auto;
    margin-left: auto;
}

.sales-history-actions-label {
    font-size: 13px;
    font-weight: 700;
    color: #555;
    margin-bottom: 8px;
    display: block;
    text-align: right;
}

.sales-history-actions-row {
    display: flex;
    justify-content: flex-end;
    align-items: center;
    flex-wrap: nowrap;
    gap: 12px;
}

.btn-edit-crm-sync {
    background: #fff;
    color: #08bff3;
    border: 1px solid #d9d9d9;
    padding: 7px 14px;
    font-size: 12px;
    border-radius: 3px;
    white-space: nowrap;
    height: 38px;
    line-height: 1.2;
    display: inline-flex;
    align-items: center;
    flex: 0 0 auto;
}

.btn-edit-crm-sync:hover,
.btn-edit-crm-sync:focus {
    background: #fff;
    color: #08bff3;
    border-color: #08bff3;
    box-shadow: none;
}

.sales-history-search-group .form-control {
    height: 38px;
}

.sales-history-action-form {
    flex: 0 1 auto;
}

.sales-history-action-form .input-group {
    flex-wrap: nowrap;
}

.sales-history-action-form #sales_history_list_action {
    min-width: 200px;
    width: 200px;
    flex: 0 0 200px;
    padding-right: 28px;
    height: 38px;
}

.sales-history-action-form .btn-primary {
    height: 38px;
}

#crmSyncModal .modal-content {
    border: 1px solid #e6e6e6;
    border-radius: 4px;
    overflow: hidden;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
}

#crmSyncModal .modal-header {
    background: #00aeef;
    border-bottom: none;
    padding: 14px 18px;
    align-items: center;
}

#crmSyncModal .modal-header .modal-title {
    color: #fff;
    font-size: 17px;
    font-weight: 600;
    text-align: left;
    letter-spacing: 0.01em;
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
    color: #00aeef;
    font-size: 14px;
}

#crmSyncModal .crm-sync-create-list-wrap {
    margin-top: 8px;
}

#crmSyncModal .crm-sync-create-list-input {
    height: 38px;
    border: 1px solid #d9d9d9;
    border-radius: 4px;
    font-size: 13px;
}

#crmSyncModal .crm-sync-create-list-note {
    margin-top: 5px;
    font-size: 11px;
    color: #8b8b8b;
    line-height: 1.3;
}

#crmSyncModal .crm-sync-create-list-error {
    margin-top: 6px;
    font-size: 11px;
    color: #e74c3c;
    line-height: 1.3;
    display: none;
}

.crm-sync-info-wrap {
    position: relative;
    display: inline-block;
    vertical-align: middle;
    margin-left: 4px;
}

.crm-sync-info-trigger {
    color: #00aeef;
    font-size: 14px;
    cursor: pointer;
    line-height: 1;
}

.crm-sync-info-box {
    display: none;
    position: absolute;
    top: 50%;
    left: calc(100% + 14px);
    transform: translateY(-50%);
    width: 260px;
    padding: 12px 14px;
    background: #ededed;
    border: 1px solid #d9d9d9;
    border-radius: 4px;
    box-shadow: 0 4px 14px rgba(0, 0, 0, 0.12);
    font-size: 11px;
    font-weight: 400;
    color: #555;
    line-height: 1.35;
    text-align: left;
    z-index: 1060;
}

.crm-sync-info-box::after {
    content: '';
    position: absolute;
    top: 50%;
    left: -6px;
    transform: translateY(-50%) rotate(45deg);
    width: 10px;
    height: 10px;
    background: #ededed;
    border-left: 1px solid #d9d9d9;
    border-bottom: 1px solid #d9d9d9;
}

.crm-sync-info-box p {
    margin: 0 0 8px 0;
    line-height: 1.35;
}

.crm-sync-info-box p:last-child {
    margin-bottom: 0;
}

.crm-sync-info-box strong {
    display: block;
    color: #333;
    font-weight: 700;
    margin-bottom: 1px;
    line-height: 1.35;
}

@media (max-width: 575.98px) {
    .crm-sync-info-box {
        position: absolute;
        top: calc(100% + 10px);
        left: 0;
        right: auto;
        transform: none;
        width: 260px;
        max-width: calc(100vw - 40px);
    }
    .crm-sync-info-box::after {
        top: -6px;
        left: 14px;
        transform: rotate(45deg);
        border-left: 1px solid #d9d9d9;
        border-top: 1px solid #d9d9d9;
        border-right: 0;
        border-bottom: 0;
    }
}

.crm-sync-info-wrap:hover .crm-sync-info-box,
.crm-sync-info-wrap.is-open .crm-sync-info-box {
    display: block;
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
    border-color: #00aeef;
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
    background: #00aeef;
    border-color: #00aeef;
    color: #fff;
    min-width: 200px;
    padding: 11px 28px;
    font-weight: 700;
    font-size: 14px;
    border-radius: 4px;
}

#crmSyncModal .btn-update-crm-sync:hover,
#crmSyncModal .btn-update-crm-sync:focus {
    background: #0099d6;
    border-color: #0099d6;
    color: #fff;
    box-shadow: none;
}

#crmSyncModal .btn-update-crm-sync:disabled {
    opacity: 0.75;
    cursor: not-allowed;
}

#crmSyncModal.crm-sync-modal--status .modal-dialog {
    max-width: 500px;
}

#crmSyncModal.crm-sync-modal--status .modal-body {
    padding: 38px 42px 8px;
}

#crmSyncModal.crm-sync-modal--status .modal-footer {
    padding: 30px 42px 38px;
}

#crmSyncModal .crm-sync-status-view {
    text-align: center;
    padding: 0;
}

#crmSyncModal .crm-sync-status-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 24px;
}

#crmSyncModal .crm-sync-status-icon-progress {
    width: auto;
    height: auto;
    color: #00aeef;
    line-height: 1;
}

#crmSyncModal .crm-sync-icon-img {
    display: block;
    width: 100px;
    max-width: 100%;
    height: auto;
    margin: 0 auto;
}

#crmSyncModal .crm-sync-status-icon-completed {
    width: auto;
    height: auto;
    border: none;
    background: transparent;
    border-radius: 0;
}

#crmSyncModal .crm-sync-check-svg {
    display: block;
    width: 94px;
    height: 94px;
    margin: 0 auto;
    color: #168bda;
}

#crmSyncModal .crm-sync-status-title {
    font-size: 18px;
    font-weight: 700;
    color: #3d3d3d;
    margin-bottom: 12px;
    line-height: 1.4;
}

#crmSyncModal .crm-sync-status-date {
    font-size: 14px;
    color: #666;
    margin-bottom: 18px;
    line-height: 1.45;
}

#crmSyncModal .crm-sync-status-date span:first-child {
    font-weight: 700;
    color: #3d3d3d;
}

#crmSyncModal .crm-sync-status-date #crm_sync_progress_date,
#crmSyncModal .crm-sync-status-date #crm_sync_completed_date {
    font-weight: 400;
    color: #666;
}

#crmSyncModal .crm-sync-status-help {
    font-size: 14px;
    font-weight: 400;
    color: #a8a8a8;
    line-height: 1.65;
    max-width: 420px;
    margin: 0 auto;
}

#crmSyncModal .btn-close-crm-sync {
    background: #00aeef;
    border-color: #00aeef;
    color: #fff;
    min-width: 200px;
    padding: 11px 32px;
    font-weight: 700;
    font-size: 14px;
    border-radius: 4px;
    box-shadow: 0 3px 10px rgba(0, 174, 239, 0.35);
}

#crmSyncModal .btn-close-crm-sync:hover,
#crmSyncModal .btn-close-crm-sync:focus {
    background: #0099d6;
    border-color: #0099d6;
    color: #fff;
    box-shadow: 0 3px 10px rgba(0, 174, 239, 0.35);
}

#crmSyncModal .crm-sync-continuous-alert {
    background: #fff8e6;
    border: 1px solid #f0d58a;
    color: #6b5a2e;
    padding: 12px 14px;
    border-radius: 4px;
    font-size: 13px;
    line-height: 1.55;
    margin-bottom: 18px;
}

#crmSyncModal .crm-sync-existing-group-warning {
    margin-top: 10px;
    padding: 10px 12px;
    border-radius: 6px;
    background: #fff8e6;
    border: 1px solid #f0d58c;
    color: #6b4f00;
    font-size: 13px;
    line-height: 1.45;
}

#crmSyncModal .crm-sync-page-scope-notice {
    background: #eef9fd;
    border: 1px solid #9edcf2;
    color: #1f5f73;
    padding: 12px 14px;
    border-radius: 4px;
    font-size: 13px;
    line-height: 1.55;
    margin-top: 10px;
    margin-bottom: 0;
}

#crmSyncModal .btn-stop-crm-sync {
    background: #fff;
    border: 1px solid #d9534f;
    color: #d9534f;
    min-width: 210px;
    padding: 11px 20px;
    font-weight: 700;
    font-size: 14px;
    border-radius: 4px;
    margin-right: 10px;
}

#crmSyncModal .btn-stop-crm-sync:hover,
#crmSyncModal .btn-stop-crm-sync:focus {
    background: #d9534f;
    border-color: #d9534f;
    color: #fff;
    box-shadow: none;
}

#crmSyncModal .btn-restart-crm-sync {
    background: #fff;
    border: 1px solid #00aeef;
    color: #00aeef;
    min-width: 210px;
    padding: 11px 20px;
    font-weight: 700;
    font-size: 14px;
    border-radius: 4px;
    margin-right: 10px;
}

#crmSyncModal .btn-restart-crm-sync:hover,
#crmSyncModal .btn-restart-crm-sync:focus {
    background: #00aeef;
    border-color: #00aeef;
    color: #fff;
    box-shadow: none;
}

#crmSyncModal .btn-stop-crm-sync.is-loading,
#crmSyncModal .btn-restart-crm-sync.is-loading,
#crmSyncModal .btn-stop-crm-sync:disabled,
#crmSyncModal .btn-restart-crm-sync:disabled {
    opacity: 0.55;
    cursor: not-allowed;
    pointer-events: none;
    filter: grayscale(0.15);
    box-shadow: none;
}

#crmSyncModal .crm-sync-footer-actions {
    display: flex;
    align-items: center;
    justify-content: center;
    flex-wrap: wrap;
    gap: 10px;
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
                'source_type' => 'event_ticket',
                'source_id' => (int) ($ticket->id ?? 0),
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
            'source_type' => 'orderitem',
            'source_id' => (int) ($sale->id ?? 0),
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

    $orderProductQtyTotals = $isTicket
        ? \App\Models\EventTicket::query()
            ->where('term_id', $product->id)
            ->selectRaw('order_id, COUNT(*) as total_qty')
            ->groupBy('order_id')
            ->pluck('total_qty', 'order_id')
            ->map(function ($qty) {
                return (int) $qty;
            })
            ->all()
        : \App\Models\Orderitem::query()
            ->where('term_id', $product->id)
            ->selectRaw('order_id, SUM(COALESCE(qty, 0)) as total_qty')
            ->groupBy('order_id')
            ->pluck('total_qty', 'order_id')
            ->map(function ($qty) {
                return (int) $qty;
            })
            ->all();

    $resolveSaleQuantity = function ($sale) use ($isTicket, $orderProductQtyTotals) {
        $orderId = $sale->order_id ?? optional($sale->order)->id;

        if (!$orderId) {
            return $isTicket ? 1 : ((isset($sale->qty) && $sale->qty !== null && $sale->qty !== '') ? (int) $sale->qty : 1);
        }

        if (array_key_exists($orderId, $orderProductQtyTotals)) {
            return (int) $orderProductQtyTotals[$orderId];
        }

        if ($isTicket) {
            return 1;
        }

        if (isset($sale->qty) && $sale->qty !== null && $sale->qty !== '') {
            return (int) $sale->qty;
        }

        return 1;
    };

    $salesHistoryMapExportRow = function ($sale) use ($isTicket, $resolveSaleQuantity) {
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
                $resolveSaleQuantity($sale),
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
            $resolveSaleQuantity($sale),
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
        ? ['FIRST NAME', 'LAST NAME', 'EMAIL', 'PHONE', 'ORDER DATE', 'ORDER #', 'QUANTITY', 'CHANNEL', 'TICKET ID', 'TICKET STATUS']
        : ['FIRST NAME', 'LAST NAME', 'EMAIL', 'PHONE', 'ORDER DATE', 'ORDER #', 'QUANTITY', 'CHANNEL', 'VARIANT ORDERED'];

    $crmSyncAllContacts = [];
    $salesHistoryExportRows = [];
    foreach ($crmSyncAllQuery->orderBy('id', 'desc')->get() as $saleItem) {
        $crmSyncAllContacts[] = $crmSyncMapSale($saleItem);
        $salesHistoryExportRows[] = $salesHistoryMapExportRow($saleItem);
    }

    $exportFilenameBase = 'sales-history-' . (\Illuminate\Support\Str::slug($product->title) ?: 'product') . '-' . time();

    $crmSyncStatus = $crmSyncStatus ?? [
        'continuous_active' => false,
        'continuous_paused' => false,
        'sync_status' => null,
        'sync_mode' => null,
        'last_synced_at' => null,
        'total_synced_contacts' => 0,
        'initial_sync_in_progress' => false,
        'contact_tags' => '',
        'crm_list_name' => '',
        'last_sync_type' => null,
    ];

    // Create vs Edit decision is fully backend-driven:
    // any persisted sync (one-time or continuous, any status) for this product = Edit.
    $hasCrmSyncConfig = !empty($crmSyncStatus['has_sync_history'])
        || !empty($crmSyncStatus['continuous_active'])
        || !empty($crmSyncStatus['last_synced_at']);
    $crmSyncButtonLabel = $hasCrmSyncConfig ? 'Edit CRM Sync' : 'Create CRM Sync';
    $crmSyncModalTitle = $hasCrmSyncConfig ? 'Edit Booostr CRM Sync' : 'Create Booostr CRM Sync';
    $crmSyncFooterLabel = $hasCrmSyncConfig ? 'Update CRM Sync' : 'Create CRM Sync';

@endphp

<script type="application/json" id="crm-sync-contacts-page">@json($crmSyncPageContacts)</script>
<script type="application/json" id="crm-sync-contacts-all">@json($crmSyncAllContacts)</script>
<script type="application/json" id="crm-sync-contact-groups">@json($crmContactGroupOptions ?? [])</script>
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

        <div class="sales-history-top">
            <p class="sales-history-desc mb-0">
                @if($isTicket)
                    Below is a list of total sales of {{ $product->title }} tickets online.
                    You can update Ticket Status using the Action button.
                @else
                    Below is a list of total sales of {{ $product->title }}
                    {{ $product->is_variation == 1 ? 'variant' : 'simple' }} product.
                @endif
            </p>

            <div class="sales-history-toolbar">
                <form method="GET" class="sales-history-search-group mb-0">
                    <label for="sales_history_search">Search</label>
                    <input type="text"
                           id="sales_history_search"
                           name="src"
                           class="form-control"
                           placeholder="search..."
                           value="{{ request('src') }}">
                </form>

                <div class="sales-history-actions-wrap">
                    <span class="sales-history-actions-label">Sales History List Actions</span>
                    <div class="sales-history-actions-row">
                        <button type="button"
                                class="btn btn-edit-crm-sync"
                                id="crm_sync_edit_btn">
                            <i class="fas fa-sync-alt"></i>&nbsp;<span id="crm_sync_btn_label">{{ $crmSyncButtonLabel }}</span>
                        </button>
                        <form class="mb-0 sales-history-action-form" id="sales_history_list_action_form" onsubmit="return false;">
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
                        <th>QUANTITY</th>
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

                            $saleQuantity = $resolveSaleQuantity($sale);
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
                            <td>{{ $saleQuantity }}</td>
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
                                                <a class="dropdown-item ticket-status-update" href="#" data-id="{{ $ticket->id }}" data-status="cancelled" data-order-id="{{ $order->id ?? '' }}">Cancelled & Refunded</a>
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
                            <td colspan="{{ $isTicket ? 11 : 10 }}" class="text-center py-5">
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
                <h5 class="modal-title" id="crmSyncModalLabel">{{ $crmSyncModalTitle }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="crm_sync_view_form">
                    <div id="crm_sync_continuous_alert" class="crm-sync-continuous-alert" style="display:none;">
                        Continuous sync is already enabled for this product. You can run sync again anytime to send new contacts to your CRM, or pause continuous sync if you need to temporarily stop automatic updates.
                    </div>
                    <div id="crm_sync_continuous_paused_alert" class="crm-sync-continuous-alert" style="display:none;">
                        Continuous sync is paused for this product. Click Restart Continuous Sync to resume syncing new purchase records to your CRM.
                    </div>
                    <div id="crm_sync_existing_group_warning" class="crm-sync-existing-group-warning" style="display:none;" role="alert"></div>

                    <p class="crm-sync-desc mb-0">
                        Create a data sync of product sales history data into your CRM. Set a one-time or continual sync of this product customer contact data to your CRM custom contact groups and/or add additional contact tags to give you more supporter insights.
                    </p>

                    <p class="crm-sync-last-date mb-0" id="crm_sync_last_date_display">
                        <span>Last Sync Date:</span> <span id="crm_sync_last_date_value">—</span>
                    </p>

                    <div class="form-group mb-0">
                        <label class="crm-sync-field-label">
                            Choose Sync recurrence for this product history user data
                            <span class="crm-sync-info-wrap">
                                <i class="fas fa-info-circle crm-sync-info-trigger" aria-hidden="true"></i>
                                <span class="crm-sync-info-box" role="tooltip">
                                    <p>Recurrence determines how often the data sync occurs. This can be updated at any time. All data syncs run a check for new data only and append any data not already captured in the CRM contact record.</p>
                                    <p>
                                        <strong>One-Time Sync</strong>
                                        This means that this will only sync the data one time and will only include current data. No new data after clicking the create/update sync will be added.
                                    </p>
                                    <p>
                                        <strong>Continuous Sync</strong>
                                        This means current and future data will be synced to CRM unless it is modified in the future
                                    </p>
                                </span>
                            </span>
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
                            <option value="page">Only this page of result</option>
                        </select>
                        <p id="crm_sync_page_scope_notice" class="crm-sync-page-scope-notice mb-0" style="display:none;">
                            To change from All Results to Only This Page Of Results, you must create a new contact group below.
                        </p>
                    </div>

                    <div class="form-group">
                        <label class="crm-sync-field-label" for="crm_sync_list">
                            Choose an existing Contact Manager list or create new contact list.
                        </label>
                        <select id="crm_sync_list" class="form-control crm-sync-select">
                            <option value="" selected disabled>Select contact list</option>
                            @foreach($crmContactGroupOptions ?? [] as $group)
                                <option value="{{ $group['name'] }}"
                                        data-group-id="{{ $group['group_id'] ?? '' }}">{{ $group['name'] }}</option>
                            @endforeach
                            <option value="__create_new__">+ Create New Contact List</option>
                        </select>
                        <div id="crm_sync_create_list_wrap" class="crm-sync-create-list-wrap" style="display:none;">
                            <input type="text"
                                   id="crm_sync_create_list_input"
                                   class="form-control crm-sync-create-list-input"
                                   placeholder="Type new contact list name and press Enter"
                                   maxlength="255">
                            <p class="crm-sync-create-list-note mb-0">Press Enter to create and select this contact list.</p>
                            <p class="crm-sync-create-list-error mb-0" id="crm_sync_create_list_error"></p>
                        </div>
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

                <div id="crm_sync_view_progress" class="crm-sync-status-view" style="display:none;">
                    <div class="crm-sync-status-icon crm-sync-status-icon-progress" aria-hidden="true">
                        <img src="{{ url('/icon/sync-data-image-newww.png') }}?v=4" alt="" class="crm-sync-icon-img">
                    </div>
                    <p class="crm-sync-status-title mb-2">Data Sync to CRM in progress.</p>
                    <p class="crm-sync-status-date mb-4">
                        <span>Sync Date:</span> <span id="crm_sync_progress_date"></span>
                    </p>
                    <p class="crm-sync-status-help mb-0">
                        You can close this window while your data is<br>syncing to your Booostr Contact Manager.
                    </p>
                </div>

                <div id="crm_sync_view_completed" class="crm-sync-status-view" style="display:none;">
                    <div class="crm-sync-status-icon crm-sync-status-icon-completed" aria-hidden="true">
                        <svg class="crm-sync-check-svg" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg" focusable="false">
                            <circle cx="50" cy="50" r="43" fill="none" stroke="currentColor" stroke-width="7.5"/>
                            <path fill="none" stroke="currentColor" stroke-width="8.5" stroke-linecap="round" stroke-linejoin="round" d="M29 51 L44 66 L71 35"/>
                        </svg>
                    </div>
                    <p class="crm-sync-status-title mb-2">Data Sync complete.</p>
                    <p class="crm-sync-status-date mb-4">
                        <span>Sync Date:</span> <span id="crm_sync_completed_date"></span>
                    </p>
                    <p class="crm-sync-status-help mb-0">
                        We have completed your data sync to Booostr Contact Manager. You can close this window.
                    </p>
                </div>
            </div>
            <div class="modal-footer">
                <div id="crm_sync_footer_form">
                    <div class="crm-sync-footer-actions">
                        <button type="button" class="btn btn-stop-crm-sync" id="crm_sync_pause_restart_btn" style="display:none;">Pause Continuous Sync</button>
                        <button type="button" class="btn btn-update-crm-sync" id="crm_sync_submit_btn">{{ $crmSyncFooterLabel }}</button>
                    </div>
                </div>
                <div id="crm_sync_footer_status" style="display:none;">
                    <button type="button" class="btn btn-close-crm-sync" id="crm_sync_close_btn">Close Window</button>
                </div>
            </div>
        </div>
    </div>
</div>

@endif

@push('script')
<script>
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
        var orderId = $(this).data('order-id');
        if (!orderId) {
            alert('Order not found for this ticket.');
            return;
        }

        window.location.href = @json(url('/seller/order')) + '/' + orderId + '?refund=1';
        return;
    }

    submitTicketStatusUpdate(ticketId, status);
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
        if (typeof applyContinuousUiState === 'function') {
            applyContinuousUiState();
        }
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

    $modal.on('click', '.crm-sync-info-trigger', function (e) {
        e.preventDefault();
        e.stopPropagation();
        var $wrap = $(this).closest('.crm-sync-info-wrap');
        $wrap.toggleClass('is-open');
        $modal.find('.crm-sync-info-wrap').not($wrap).removeClass('is-open');
    });

    $(document).on('click', function (e) {
        if (!$(e.target).closest('.crm-sync-info-wrap').length) {
            $modal.find('.crm-sync-info-wrap').removeClass('is-open');
        }
    });

    var CRM_SYNC_API = 'https://app.booostr.co/wp-json/booostr/v1/save-contact';
    var CRM_SYNC_BOOSTER_ID = {{ tenant('club_id') }};
    var CRM_SYNC_PRODUCT_ID = {{ (int) $product->id }};
    var CRM_SYNC_STORAGE_KEY = 'crm_sync_state_' + CRM_SYNC_PRODUCT_ID;
    var CRM_SYNC_STATUS = @json($crmSyncStatus);
    var CRM_SYNC_ROUTES = {
        status: "{{ route('seller.product.crm.sync.status', $product->id) }}",
        enable: "{{ route('seller.product.crm.sync.enable', $product->id) }}",
        process: "{{ route('seller.product.crm.sync.process', $product->id) }}",
        stop: "{{ route('seller.product.crm.sync.stop', $product->id) }}",
        pause: "{{ route('seller.product.crm.sync.pause', $product->id) }}",
        restart: "{{ route('seller.product.crm.sync.restart', $product->id) }}",
        recordOneTime: "{{ route('seller.product.crm.sync.record_one_time', $product->id) }}",
        createContactGroup: "{{ route('seller.product.crm.sync.contact_groups.create', $product->id) }}"
    };
    var CRM_SYNC_PAGE_FILTERS = {
        src: @json(request('src')),
        page: {{ (int) $sales->currentPage() }},
        per_page: {{ (int) $sales->perPage() }}
    };
    var $syncBtn = $modal.find('#crm_sync_submit_btn');
    var $pauseRestartBtn = $modal.find('#crm_sync_pause_restart_btn');
    var $continuousAlert = $modal.find('#crm_sync_continuous_alert');
    var $continuousPausedAlert = $modal.find('#crm_sync_continuous_paused_alert');
    var $editBtn = $('#crm_sync_edit_btn');
    var $closeBtn = $modal.find('#crm_sync_close_btn');
    var $viewForm = $modal.find('#crm_sync_view_form');
    var $viewProgress = $modal.find('#crm_sync_view_progress');
    var $viewCompleted = $modal.find('#crm_sync_view_completed');
    var $footerForm = $modal.find('#crm_sync_footer_form');
    var $footerStatus = $modal.find('#crm_sync_footer_status');
    var $lastSyncDateValue = $modal.find('#crm_sync_last_date_value');
    var $crmSyncList = $modal.find('#crm_sync_list');
    var $createListWrap = $modal.find('#crm_sync_create_list_wrap');
    var $createListInput = $modal.find('#crm_sync_create_list_input');
    var $createListError = $modal.find('#crm_sync_create_list_error');
    var $pageScopeNotice = $modal.find('#crm_sync_page_scope_notice');
    var $existingGroupWarning = $modal.find('#crm_sync_existing_group_warning');
    var $passAmount = $modal.find('#crm_sync_pass_amount');
    var lastValidCrmListValue = $crmSyncList.val();
    var originalCrmListName = '';
    var originalCrmGroupId = '';
    var originalPassScope = 'all';
    var originalSyncType = 'continuous';
    var originalContactTags = '';
    var pageScopeContactGroupName = '';
    var pageScopeContactGroupId = '';
    var currentView = 'form';
    var syncRunning = false;
    var continuousBatchRunning = false;
    var pauseRestartPending = false;

    function getPageScopeGroupStorageKey() {
        var originalKey = $.trim(originalCrmListName || 'default')
            .toLowerCase()
            .replace(/[^a-z0-9]+/g, '_')
            .substring(0, 60);

        return 'crm_page_scope_group_' + CRM_SYNC_PRODUCT_ID + '_' + originalKey;
    }

    function readPageScopeContactGroup() {
        try {
            var raw = sessionStorage.getItem(getPageScopeGroupStorageKey());
            return raw ? JSON.parse(raw) : null;
        } catch (e) {
            return null;
        }
    }

    function writePageScopeContactGroup(name, groupId) {
        try {
            if (!$.trim(name || '')) {
                sessionStorage.removeItem(getPageScopeGroupStorageKey());
                return;
            }

            sessionStorage.setItem(getPageScopeGroupStorageKey(), JSON.stringify({
                name: $.trim(name || ''),
                group_id: $.trim(groupId || '')
            }));
        } catch (e) {
            // Ignore storage errors.
        }
    }

    function rememberPageScopeContactGroup(name, groupId) {
        pageScopeContactGroupName = $.trim(name || '');
        pageScopeContactGroupId = $.trim(groupId || '');
        writePageScopeContactGroup(pageScopeContactGroupName, pageScopeContactGroupId);
    }

    function loadStoredPageScopeContactGroup() {
        var stored = readPageScopeContactGroup();
        if (!stored || !$.trim(stored.name || '')) {
            return;
        }

        pageScopeContactGroupName = $.trim(stored.name || '');
        pageScopeContactGroupId = $.trim(stored.group_id || '');
    }

    function selectContactGroupOptionIfExists(name, groupId) {
        var normalized = $.trim(name || '');
        if (!normalized) {
            return false;
        }

        var $option = $crmSyncList.find('option').filter(function () {
            return $.trim($(this).val()).toLowerCase() === normalized.toLowerCase()
                && $(this).val() !== '__create_new__';
        }).first();

        if (!$option.length) {
            return false;
        }

        if ($.trim(groupId || '') !== '') {
            $option.attr('data-group-id', $.trim(groupId));
        }

        $crmSyncList.val($option.val());
        lastValidCrmListValue = $option.val();
        $crmSyncList.addClass('crm-sync-select-active');
        return true;
    }

    function applyPageScopeContactGroupIfAvailable() {
        if (!isChangingAllResultsToPageScope() || !pageScopeContactGroupName) {
            return false;
        }

        var original = $.trim(originalCrmListName || '').toLowerCase();
        if (pageScopeContactGroupName.toLowerCase() === original) {
            return false;
        }

        if (!selectContactGroupOptionIfExists(pageScopeContactGroupName, pageScopeContactGroupId)) {
            return false;
        }

        resetCreateNewContactGroupUi();
        updatePageScopeNotice();
        return true;
    }

    function appendContactGroupOption(name, groupId, makeSelected) {
        var normalized = $.trim(name || '');
        if (!normalized) {
            return;
        }

        var existing = $crmSyncList.find('option').filter(function () {
            return $.trim($(this).val()).toLowerCase() === normalized.toLowerCase();
        });

        if (!existing.length) {
            var $createOption = $crmSyncList.find('option[value="__create_new__"]');
            $('<option>', {
                value: normalized,
                text: normalized,
                'data-group-id': $.trim(groupId || '')
            }).insertBefore($createOption);
        } else if ($.trim(groupId || '') !== '') {
            existing.attr('data-group-id', $.trim(groupId || ''));
        }

        if (makeSelected) {
            $crmSyncList.val(normalized);
            lastValidCrmListValue = normalized;
            $crmSyncList.addClass('crm-sync-select-active');
        }
    }

    function refreshCrmContactGroupOptions(groups, keepSelectedName) {
        var hasExplicitSelection = keepSelectedName !== undefined;
        var selectedName = hasExplicitSelection
            ? $.trim(keepSelectedName || '')
            : $.trim(getSelectedCrmListName() || '');
        var selectedGroupId = $.trim(getSelectedCrmListGroupId() || '');
        var normalizedSeen = {};

        $crmSyncList.find('option').not('[value="__create_new__"]').not('[value=""]').remove();

        if (Array.isArray(groups)) {
            groups.forEach(function (group) {
                var name = $.trim((group && group.name) ? group.name : '');
                var key = name.toLowerCase();
                if (!name || normalizedSeen[key]) {
                    return;
                }
                normalizedSeen[key] = true;
                $('<option>', {
                    value: name,
                    text: name,
                    'data-group-id': $.trim((group && group.group_id) ? group.group_id : '')
                }).insertBefore($crmSyncList.find('option[value="__create_new__"]'));
            });

            $('#crm-sync-contact-groups').text(JSON.stringify(groups));
        }

        if (selectedName && normalizedSeen[selectedName.toLowerCase()]) {
            if (!selectedGroupId) {
                (groups || []).some(function (group) {
                    var name = $.trim((group && group.name) ? group.name : '');
                    if (name.toLowerCase() === selectedName.toLowerCase()) {
                        selectedGroupId = $.trim((group && group.group_id) ? group.group_id : '');
                        return true;
                    }
                    return false;
                });
            }
            appendContactGroupOption(selectedName, selectedGroupId, true);
        } else {
            $crmSyncList.val('').removeClass('crm-sync-select-active');
            lastValidCrmListValue = '';
            resetCreateNewContactGroupUi();
        }
    }

    function parseCrmContactGroups() {
        return parseCrmContacts('crm-sync-contact-groups');
    }

    function fetchFreshCrmContactGroups(callback) {
        $.ajax({
            url: window.location.pathname,
            data: {
                crm_sync_groups: 1,
                _: Date.now()
            },
            dataType: 'json'
        })
            .done(function (response) {
                var groups = (response && response.success && Array.isArray(response.groups))
                    ? response.groups
                    : parseCrmContactGroups();
                if (typeof callback === 'function') {
                    callback(groups);
                }
            })
            .fail(function () {
                if (typeof callback === 'function') {
                    callback(parseCrmContactGroups());
                }
            });
    }

    function createContactGroup(name) {
        return $.post(CRM_SYNC_ROUTES.createContactGroup, {
            _token: "{{ csrf_token() }}",
            name: name
        });
    }

    function showCreateListError(message) {
        $createListError.text(message || 'Unable to create contact group.').show();
    }

    function clearCreateListError() {
        $createListError.text('').hide();
    }

    function submitCreateContactGroup() {
        var groupName = $.trim($createListInput.val() || '');
        if (!groupName) {
            showCreateListError('Please enter contact list name.');
            $createListInput.focus();
            return;
        }

        clearCreateListError();
        $createListInput.prop('disabled', true);

        createContactGroup(groupName)
            .done(function (response) {
                if (response && response.success && response.contact_group && response.contact_group.name) {
                    appendContactGroupOption(response.contact_group.name, response.contact_group.group_id || '', true);
                    if (isChangingAllResultsToPageScope()) {
                        rememberPageScopeContactGroup(
                            response.contact_group.name,
                            response.contact_group.group_id || ''
                        );
                    }
                    $createListInput.val('').prop('disabled', false);
                    $createListWrap.hide();
                    clearCreateListError();
                    applyContinuousUiState();
                    return;
                }

                $createListInput.prop('disabled', false);
                showCreateListError((response && response.message) ? response.message : 'Unable to create contact group.');
            })
            .fail(function (xhr) {
                $createListInput.prop('disabled', false);
                var message = (xhr.responseJSON && xhr.responseJSON.message)
                    ? xhr.responseJSON.message
                    : 'Unable to create contact group.';
                showCreateListError(message);
            });
    }

    function getSelectedCrmListName() {
        var selected = $.trim($crmSyncList.val() || '');
        if (selected && selected !== '__create_new__') {
            return selected;
        }
        return $.trim(lastValidCrmListValue || '');
    }

    function getSelectedCrmListGroupId() {
        var selected = $.trim($crmSyncList.val() || '');
        if (!selected || selected === '__create_new__') {
            selected = $.trim(lastValidCrmListValue || '');
        }
        if (!selected) {
            return '';
        }

        var $option = $crmSyncList.find('option').filter(function () {
            return $.trim($(this).val()).toLowerCase() === selected.toLowerCase();
        }).first();

        return $.trim($option.attr('data-group-id') || '');
    }

    $crmSyncList.on('focus', function () {
        var value = $crmSyncList.val();
        if (value && value !== '__create_new__') {
            lastValidCrmListValue = value;
        }
    });

    $crmSyncList.on('change', function () {
        if ($crmSyncList.val() !== '__create_new__') {
            if (isChangingAllResultsToPageScope() && hasContactGroupChanged()) {
                rememberPageScopeContactGroup(getSelectedCrmListName(), getSelectedCrmListGroupId());
            }

            lastValidCrmListValue = $crmSyncList.val();
            $crmSyncList.addClass('crm-sync-select-active');
            $createListWrap.hide();
            $createListInput.val('').prop('disabled', false);
            clearCreateListError();
            applyContinuousUiState();
            return;
        }

        clearCreateListError();
        $createListWrap.show();
        $createListInput.focus();
        applyContinuousUiState();
    });

    $passAmount.on('change', function () {
        if (isChangingAllResultsToPageScope()) {
            if (hasContactGroupChanged()) {
                rememberPageScopeContactGroup(getSelectedCrmListName(), getSelectedCrmListGroupId());
            } else if (!applyPageScopeContactGroupIfAvailable()) {
                promptCreateNewContactGroupForPageScope();
            }
        } else if (isRevertingAllResultsToPageScopeChange()) {
            restoreOriginalContactGroupSelection();
        }

        updatePageScopeNotice();
        applyContinuousUiState();
    });

    $createListInput.on('keydown', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            submitCreateContactGroup();
        }
    });

    function formatSyncDateLabel(syncType) {
        var d = new Date();
        var mm = String(d.getMonth() + 1).padStart(2, '0');
        var dd = String(d.getDate()).padStart(2, '0');
        var yyyy = d.getFullYear();
        var typeLabel = syncType === 'one_time' ? 'one time' : 'continuous';
        return mm + '/' + dd + '/' + yyyy + ' (' + typeLabel + ')';
    }

    function readSyncState() {
        try {
            var raw = sessionStorage.getItem(CRM_SYNC_STORAGE_KEY);
            return raw ? JSON.parse(raw) : null;
        } catch (e) {
            return null;
        }
    }

    function writeSyncState(state) {
        try {
            if (!state) {
                sessionStorage.removeItem(CRM_SYNC_STORAGE_KEY);
                return;
            }
            sessionStorage.setItem(CRM_SYNC_STORAGE_KEY, JSON.stringify(state));
        } catch (e) {
            // Ignore storage errors.
        }
    }

    function updateCrmSyncLabels() {
        var hasConfig = CRM_SYNC_STATUS && (
            CRM_SYNC_STATUS.has_sync_history
            || CRM_SYNC_STATUS.continuous_active
            || CRM_SYNC_STATUS.last_synced_at
        );
        var pageLabel = hasConfig ? 'Edit CRM Sync' : 'Create CRM Sync';
        var modalTitle = hasConfig ? 'Edit Booostr CRM Sync' : 'Create Booostr CRM Sync';
        var footerLabel = hasConfig ? 'Update CRM Sync' : 'Create CRM Sync';

        $('#crm_sync_btn_label').text(pageLabel);
        $('#crmSyncModalLabel').text(modalTitle);
        $syncBtn.text(footerLabel);
    }

    function updateLastSyncDateDisplay() {
        if (CRM_SYNC_STATUS && CRM_SYNC_STATUS.last_synced_at) {
            var typeLabel = (CRM_SYNC_STATUS.last_sync_type === 'one_time') ? 'one time' : 'continuous';
            $lastSyncDateValue.text(CRM_SYNC_STATUS.last_synced_at + ' (' + typeLabel + ')');
            return;
        }

        // Always DB-driven. Do not fallback to temporary session state.
        $lastSyncDateValue.text('—');
    }

    function normalizeContactTags(value) {
        return $.trim(value || '')
            .split(',')
            .map(function (tag) { return $.trim(tag).toLowerCase(); })
            .filter(function (tag) { return tag !== ''; })
            .sort()
            .join(',');
    }

    function getCurrentSyncType() {
        return $modal.find('input[name="crm_sync_recurrence"]:checked').val() || 'continuous';
    }

    function getCurrentContactTags() {
        return $.trim($hidden.val() || tags.join(','));
    }

    function hasContactGroupChanged() {
        if (!isEditingExistingSync()) {
            return false;
        }

        var currentName = $.trim(getSelectedCrmListName() || '').toLowerCase();
        var originalName = $.trim(originalCrmListName || '').toLowerCase();
        var currentGroupId = $.trim(getSelectedCrmListGroupId() || '');
        var originalGroupId = $.trim(originalCrmGroupId || '');

        if (!currentName || currentName === '__create_new__') {
            return false;
        }

        if (originalGroupId && currentGroupId && originalGroupId !== currentGroupId) {
            return true;
        }

        if (!originalName) {
            return true;
        }

        if (currentName !== originalName) {
            return true;
        }

        // Same list name but a newly created/reselected CRM group id.
        return !!(currentGroupId && !originalGroupId && originalName);
    }

    function hasCrmListChanged() {
        return hasContactGroupChanged();
    }

    function hasSyncRecurrenceChanged() {
        if (!isEditingExistingSync()) {
            return false;
        }

        return getCurrentSyncType() !== originalSyncType;
    }

    function hasContactTagsChanged() {
        if (!isEditingExistingSync()) {
            return false;
        }

        return normalizeContactTags(getCurrentContactTags()) !== normalizeContactTags(originalContactTags);
    }

    function hasNonContactGroupConfigChanged() {
        return hasSyncScopeChanged()
            || hasSyncRecurrenceChanged()
            || hasContactTagsChanged();
    }

    function hasAnySyncConfigChanged() {
        return hasContactGroupChanged() || hasNonContactGroupConfigChanged();
    }

    function getExistingContactGroupWarningMessage() {
        return 'You have updated your syncing parameters but did not update the Contact Manager Group it is syncing to. '
            + 'If this is correct, click OK. If you want to change the Contact Group this data syncs to, click cancel and update the group.';
    }

    function shouldWarnAboutExistingContactGroup() {
        return isEditingExistingSync()
            && hasNonContactGroupConfigChanged()
            && !hasContactGroupChanged();
    }

    function updateExistingGroupWarning() {
        if (!$existingGroupWarning.length) {
            return;
        }

        if (!shouldWarnAboutExistingContactGroup()) {
            $existingGroupWarning.hide().empty();
            return;
        }

        $existingGroupWarning.text(
            'You have updated your syncing parameters but did not update the Contact Manager Group it is syncing to. '
            + 'Update the contact group below if you want this data to sync elsewhere.'
        ).show();
    }

    function ensureExistingContactGroupSyncConfirmed() {
        if (!shouldWarnAboutExistingContactGroup()) {
            return true;
        }

        return confirm(getExistingContactGroupWarningMessage());
    }

    function isEditingExistingSync() {
        return !!(CRM_SYNC_STATUS && CRM_SYNC_STATUS.has_sync_history);
    }

    function getCurrentPassScope() {
        return $passAmount.val() || 'all';
    }

    function passScopeFromSyncMode(syncMode) {
        return syncMode === 'current_page' ? 'page' : 'all';
    }

    function isChangingAllResultsToPageScope() {
        return isEditingExistingSync()
            && originalPassScope === 'all'
            && getCurrentPassScope() === 'page';
    }

    function isChangingPageScopeToAllResults() {
        return isEditingExistingSync()
            && originalPassScope === 'page'
            && getCurrentPassScope() === 'all';
    }

    function hasSyncScopeChanged() {
        return isEditingExistingSync() && originalPassScope !== getCurrentPassScope();
    }

    function hasValidNewContactGroupForPageScopeChange() {
        if (!isChangingAllResultsToPageScope()) {
            return true;
        }

        return hasContactGroupChanged();
    }

    function updatePageScopeNotice() {
        $pageScopeNotice.toggle(
            isChangingAllResultsToPageScope() && !hasContactGroupChanged()
        );
    }

    function promptCreateNewContactGroupForPageScope() {
        alert('To change from All Results to Only This Page Of Results, please create a new contact group.');
        if ($crmSyncList.val() !== '__create_new__') {
            lastValidCrmListValue = $crmSyncList.val();
        }
        $crmSyncList.val('__create_new__');
        $createListWrap.show();
        $createListInput.focus();
        clearCreateListError();
    }

    function resetCreateNewContactGroupUi() {
        $createListWrap.hide();
        $createListInput.val('').prop('disabled', false);
        clearCreateListError();
    }

    function restoreOriginalContactGroupSelection() {
        var listName = $.trim(originalCrmListName || lastValidCrmListValue || '');

        if (!listName || listName === '__create_new__') {
            resetCreateNewContactGroupUi();
            $crmSyncList.val('').removeClass('crm-sync-select-active');
            lastValidCrmListValue = '';
            return;
        }

        var $matchingOption = $crmSyncList.find('option').filter(function () {
            return $.trim($(this).val()).toLowerCase() === listName.toLowerCase()
                && $(this).val() !== '__create_new__';
        }).first();

        if ($matchingOption.length) {
            $crmSyncList.val($matchingOption.val());
        } else {
            $crmSyncList.val('').removeClass('crm-sync-select-active');
            lastValidCrmListValue = '';
        }

        lastValidCrmListValue = $.trim($crmSyncList.val() || '');
        resetCreateNewContactGroupUi();
    }

    function isRevertingAllResultsToPageScopeChange() {
        return originalPassScope === 'all' && getCurrentPassScope() === 'all';
    }

    function ensureValidContactGroupForPageScopeChange() {
        if (hasValidNewContactGroupForPageScopeChange()) {
            return true;
        }

        promptCreateNewContactGroupForPageScope();
        return false;
    }

    function ensurePageScopeToAllResultsConfirmed() {
        if (!isChangingPageScopeToAllResults()) {
            return true;
        }

        // User already chose a new/different contact group — proceed without extra scope confirm.
        if (hasContactGroupChanged()) {
            return true;
        }

        var groupName = getSelectedCrmListName() || 'your selected contact group';
        return confirm(
            'Switch this CRM sync from Only This Page Of Results to All Results? '
            + 'We will start adding all purchase records to "' + groupName + '".'
        );
    }

    function prefillCrmSyncFormFromStatus() {
        if (!CRM_SYNC_STATUS) {
            originalCrmListName = $.trim(getSelectedCrmListName() || '');
            originalCrmGroupId = $.trim(getSelectedCrmListGroupId() || '');
            originalPassScope = 'all';
            originalSyncType = 'continuous';
            originalContactTags = '';
            updatePageScopeNotice();
            updateExistingGroupWarning();
            return;
        }

        var listName = $.trim(CRM_SYNC_STATUS.crm_list_name || '');

        tags = $.trim(CRM_SYNC_STATUS.contact_tags || '')
            .split(',')
            .map(function (tag) { return $.trim(tag); })
            .filter(function (tag) { return tag !== ''; });
        renderTags();

        if (CRM_SYNC_STATUS.last_sync_type === 'one_time') {
            $modal.find('#crm_sync_onetime').prop('checked', true);
            originalSyncType = 'one_time';
        } else {
            $modal.find('#crm_sync_continuous').prop('checked', true);
            originalSyncType = 'continuous';
        }

        originalPassScope = isEditingExistingSync()
            ? passScopeFromSyncMode($.trim(CRM_SYNC_STATUS.sync_mode || ''))
            : 'all';
        $passAmount.val(originalPassScope);

        originalCrmListName = listName || $.trim(getSelectedCrmListName() || '');
        originalCrmGroupId = $.trim(CRM_SYNC_STATUS.crm_group_id || '') || $.trim(getSelectedCrmListGroupId() || '');
        originalContactTags = $.trim(CRM_SYNC_STATUS.contact_tags || '');
        loadStoredPageScopeContactGroup();
        updatePageScopeNotice();
    }

    function isContinuousSyncFormSelected() {
        return ($modal.find('input[name="crm_sync_recurrence"]:checked').val() || 'continuous') === 'continuous';
    }

    function setPauseRestartBtnPending(isPending) {
        pauseRestartPending = !!isPending;
        $pauseRestartBtn
            .prop('disabled', pauseRestartPending)
            .toggleClass('is-loading', pauseRestartPending)
            .attr('aria-busy', pauseRestartPending ? 'true' : 'false');
    }

    function applyContinuousUiState() {
        var continuousActive = CRM_SYNC_STATUS && CRM_SYNC_STATUS.continuous_active;
        var continuousPaused = CRM_SYNC_STATUS && CRM_SYNC_STATUS.continuous_paused;
        var initialInProgress = CRM_SYNC_STATUS && CRM_SYNC_STATUS.initial_sync_in_progress;
        var configChanged = hasAnySyncConfigChanged();
        var showContinuousControls = isContinuousSyncFormSelected() && !configChanged;

        $continuousAlert.toggle(continuousActive && !initialInProgress && showContinuousControls);
        $continuousPausedAlert.toggle(continuousPaused && showContinuousControls);

        if (showContinuousControls && (continuousActive || continuousPaused)) {
            $pauseRestartBtn.show();

            if (continuousPaused) {
                $pauseRestartBtn
                    .text('Restart Continuous Sync')
                    .removeClass('btn-stop-crm-sync')
                    .addClass('btn-restart-crm-sync');
            } else {
                $pauseRestartBtn
                    .text('Pause Continuous Sync')
                    .removeClass('btn-restart-crm-sync')
                    .addClass('btn-stop-crm-sync');
            }
        } else {
            $pauseRestartBtn.hide();
        }

        if (pauseRestartPending && $pauseRestartBtn.is(':visible')) {
            setPauseRestartBtnPending(true);
        } else if ($pauseRestartBtn.is(':visible')) {
            setPauseRestartBtnPending(false);
        }

        $syncBtn.prop('disabled', syncRunning || continuousBatchRunning);
        updatePageScopeNotice();
        updateExistingGroupWarning();
    }

    function refreshContinuousStatus(callback) {
        $.get(CRM_SYNC_ROUTES.status)
            .done(function (status) {
                CRM_SYNC_STATUS = status || CRM_SYNC_STATUS;
                updateLastSyncDateDisplay();
                updateCrmSyncLabels();
                applyContinuousUiState();
                if (typeof callback === 'function') {
                    callback();
                }
            })
            .fail(function () {
                if (typeof callback === 'function') {
                    callback();
                }
            });
    }

    function showView(viewName, syncDateText) {
        currentView = viewName;

        $viewForm.toggle(viewName === 'form');
        $viewProgress.toggle(viewName === 'progress');
        $viewCompleted.toggle(viewName === 'completed');

        $footerForm.toggle(viewName === 'form');
        $footerStatus.toggle(viewName === 'progress' || viewName === 'completed');
        $modal.toggleClass('crm-sync-modal--status', viewName === 'progress' || viewName === 'completed');

        if (viewName === 'progress' && syncDateText) {
            $modal.find('#crm_sync_progress_date').text(syncDateText);
        }

        if (viewName === 'completed' && syncDateText) {
            $modal.find('#crm_sync_completed_date').text(syncDateText);
        }
    }

    function openModalWithState() {
        refreshContinuousStatus(function () {
            fetchFreshCrmContactGroups(function (groups) {
                var keepSelectedName = '';
                if (CRM_SYNC_STATUS && isEditingExistingSync()) {
                    keepSelectedName = $.trim(CRM_SYNC_STATUS.crm_list_name || '');
                }

                refreshCrmContactGroupOptions(groups, keepSelectedName);
                prefillCrmSyncFormFromStatus();

                if (isEditingExistingSync()) {
                    originalCrmGroupId = $.trim(getSelectedCrmListGroupId() || '') || $.trim(originalCrmGroupId || '');
                }

                var state = readSyncState();

                if (syncRunning || (state && state.status === 'in_progress')) {
                    showView('progress', state ? state.syncDate : '');
                } else {
                    showView('form');
                }

                applyContinuousUiState();
                $modal.modal('show');
            });
        });
    }

    function markSyncCompleted(syncDate, syncType) {
        syncRunning = false;
        writeSyncState({
            status: 'idle',
            syncType: syncType,
            lastSyncDate: syncDate
        });
        updateLastSyncDateDisplay();
    }

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

    function postCrmContact(contact, contactTags, groupIds) {
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
                contact_tags: contactTags,
                group_ids: Array.isArray(groupIds) ? groupIds : []
            })
        });
    }

    function recordOneTimeSyncOnServer(syncType, contactTags, totalSyncedContacts, convertFromContinuous, syncedContacts) {
        if (syncType !== 'one_time' || !CRM_SYNC_ROUTES.recordOneTime) {
            return $.Deferred().resolve().promise();
        }

        var passScope = $modal.find('#crm_sync_pass_amount').val();
        var crmListName = getSelectedCrmListName();
        var crmGroupId = getSelectedCrmListGroupId();

        return $.post(CRM_SYNC_ROUTES.recordOneTime, {
            _token: "{{ csrf_token() }}",
            sync_mode: passScope,
            contact_tags: contactTags,
            crm_list_name: crmListName,
            crm_group_id: crmGroupId,
            convert_from_continuous: convertFromContinuous ? 1 : 0,
            total_synced_contacts: totalSyncedContacts,
            synced_contacts: JSON.stringify(syncedContacts || []),
            src: CRM_SYNC_PAGE_FILTERS.src,
            page: CRM_SYNC_PAGE_FILTERS.page,
            per_page: CRM_SYNC_PAGE_FILTERS.per_page
        }).done(function (response) {
            if (response && response.status) {
                CRM_SYNC_STATUS = response.status;
            } else if (CRM_SYNC_STATUS) {
                CRM_SYNC_STATUS.has_sync_history = true;
            }
            updateCrmSyncLabels();
            updateLastSyncDateDisplay();
            applyContinuousUiState();
        });
    }

    function runBackgroundSync(contacts, contactTags, syncType, syncDate, groupIds, convertFromContinuous) {
        syncRunning = true;

        writeSyncState({
            status: 'in_progress',
            syncType: syncType,
            syncDate: syncDate
        });

        var successCount = 0;
        var failedCount = 0;
        var syncedContacts = [];

        function syncNext(index) {
            if (index >= contacts.length) {
                markSyncCompleted(syncDate, syncType);

                recordOneTimeSyncOnServer(syncType, contactTags, successCount, !!convertFromContinuous, syncedContacts)
                    .always(function () {
                        if ($modal.hasClass('show') && currentView === 'progress') {
                            showView('completed', syncDate);
                        }
                    });

                return;
            }

            postCrmContact(contacts[index], contactTags, groupIds)
                .done(function () {
                    successCount++;
                    if (contacts[index].source_type && contacts[index].source_id) {
                        syncedContacts.push({
                            source_type: contacts[index].source_type,
                            source_id: contacts[index].source_id,
                            email: contacts[index].email || ''
                        });
                    }
                })
                .fail(function () {
                    failedCount++;
                })
                .always(function () {
                    syncNext(index + 1);
                });
        }

        syncNext(0);
    }

    function finishInitialContinuousSync(syncDate, response) {
        continuousBatchRunning = false;
        syncRunning = false;

        if (response && response.status) {
            CRM_SYNC_STATUS = response.status;
        }

        updateCrmSyncLabels();
        updateLastSyncDateDisplay();
        applyContinuousUiState();

        if ($modal.hasClass('show') && currentView === 'progress') {
            showView('completed', syncDate);
        }
    }

    function runInitialContinuousSync(syncDate) {
        if (continuousBatchRunning) {
            return;
        }

        continuousBatchRunning = true;
        syncRunning = true;
        applyContinuousUiState();

        $.post(CRM_SYNC_ROUTES.process, {
            _token: "{{ csrf_token() }}"
        })
            .done(function (response) {
                finishInitialContinuousSync(syncDate, response);
            })
            .fail(function (xhr) {
                continuousBatchRunning = false;
                syncRunning = false;
                applyContinuousUiState();

                var message = (xhr.responseJSON && xhr.responseJSON.message)
                    ? xhr.responseJSON.message
                    : 'Continuous sync failed.';
                alert(message);
            });
    }

    function enableContinuousSyncOnServer(syncDate) {
        var passScope = $modal.find('#crm_sync_pass_amount').val();
        var contactTags = $.trim($hidden.val() || tags.join(','));
        var crmListName = getSelectedCrmListName();
        var crmGroupId = getSelectedCrmListGroupId();

        $.post(CRM_SYNC_ROUTES.enable, {
            _token: "{{ csrf_token() }}",
            sync_mode: passScope,
            contact_tags: contactTags,
            crm_list_name: crmListName,
            crm_group_id: crmGroupId,
            src: CRM_SYNC_PAGE_FILTERS.src,
            page: CRM_SYNC_PAGE_FILTERS.page,
            per_page: CRM_SYNC_PAGE_FILTERS.per_page
        })
            .done(function (response) {
                if (response.status) {
                    CRM_SYNC_STATUS = response.status;
                }

                updateCrmSyncLabels();
                showView('progress', syncDate);
                runInitialContinuousSync(syncDate);
            })
            .fail(function (xhr) {
                var message = (xhr.responseJSON && xhr.responseJSON.message)
                    ? xhr.responseJSON.message
                    : 'Unable to enable continuous sync.';
                alert(message);
            });
    }

    $pauseRestartBtn.on('click', function () {
        if (pauseRestartPending || $pauseRestartBtn.prop('disabled')) {
            return;
        }

        var continuousPaused = CRM_SYNC_STATUS && CRM_SYNC_STATUS.continuous_paused;

        if (continuousPaused) {
            setPauseRestartBtnPending(true);

            $.post(CRM_SYNC_ROUTES.restart, {
                _token: "{{ csrf_token() }}"
            })
                .done(function (response) {
                    CRM_SYNC_STATUS = response.status || CRM_SYNC_STATUS;
                    updateLastSyncDateDisplay();
                    updateCrmSyncLabels();
                })
                .fail(function (xhr) {
                    var message = (xhr.responseJSON && xhr.responseJSON.message)
                        ? xhr.responseJSON.message
                        : 'Unable to restart continuous sync.';
                    alert(message);
                })
                .always(function () {
                    setPauseRestartBtnPending(false);
                    applyContinuousUiState();
                });

            return;
        }

        if (!confirm('Pause continuous sync for this product? You can restart it later without losing your sync settings.')) {
            return;
        }

        setPauseRestartBtnPending(true);

        $.post(CRM_SYNC_ROUTES.pause, {
            _token: "{{ csrf_token() }}"
        })
            .done(function (response) {
                CRM_SYNC_STATUS = response.status || {
                    continuous_active: false,
                    continuous_paused: true,
                    initial_sync_in_progress: false
                };
                updateLastSyncDateDisplay();
                updateCrmSyncLabels();
            })
            .fail(function (xhr) {
                var message = (xhr.responseJSON && xhr.responseJSON.message)
                    ? xhr.responseJSON.message
                    : 'Unable to pause continuous sync.';
                alert(message);
            })
            .always(function () {
                setPauseRestartBtnPending(false);
                applyContinuousUiState();
            });
    });

    $modal.find('input[name="crm_sync_recurrence"]').on('change', function () {
        applyContinuousUiState();
    });

    $editBtn.on('click', function () {
        openModalWithState();
    });

    $closeBtn.on('click', function () {
        $modal.modal('hide');
    });

    $modal.on('hidden.bs.modal', function () {
        if (currentView === 'completed') {
            showView('form');
        }
    });

    (function cleanupStaleSyncState() {
        var state = readSyncState();
        if (state && state.status === 'in_progress') {
            writeSyncState(state.lastSyncDate ? {
                status: 'idle',
                syncType: state.syncType,
                lastSyncDate: state.lastSyncDate
            } : null);
        }
    })();

    updateLastSyncDateDisplay();
    updateCrmSyncLabels();
    applyContinuousUiState();

    $syncBtn.on('click', function () {
        if ($syncBtn.prop('disabled') || syncRunning || continuousBatchRunning) {
            return;
        }

        if (!ensureValidContactGroupForPageScopeChange()) {
            return;
        }

        if (!ensureExistingContactGroupSyncConfirmed()) {
            return;
        }

        if (!ensurePageScopeToAllResultsConfirmed()) {
            return;
        }

        if (!getSelectedCrmListName()) {
            alert('Please select an existing contact list or create a new one.');
            if ($crmSyncList.val() === '__create_new__') {
                $createListInput.focus();
            } else {
                $crmSyncList.focus();
            }
            return;
        }

        var passScope = $modal.find('#crm_sync_pass_amount').val();
        var contacts = passScope === 'page'
            ? parseCrmContacts('crm-sync-contacts-page')
            : parseCrmContacts('crm-sync-contacts-all');

        var contactTags = $.trim($hidden.val() || tags.join(','));
        var syncType = $modal.find('input[name="crm_sync_recurrence"]:checked').val() || 'continuous';
        var syncDate = formatSyncDateLabel(syncType);
        var selectedGroupId = $.trim(getSelectedCrmListGroupId() || '');
        var selectedGroupIds = [];
        if (selectedGroupId) {
            selectedGroupIds.push(/^\d+$/.test(selectedGroupId) ? parseInt(selectedGroupId, 10) : selectedGroupId);
        }

        if (syncType === 'continuous') {
            var convertFromOneTime = !!(CRM_SYNC_STATUS
                && !CRM_SYNC_STATUS.continuous_active
                && CRM_SYNC_STATUS.last_sync_type === 'one_time');

            if (convertFromOneTime) {
                var proceedContinuous = confirm('Switch this CRM sync from One-Time to Continuous? We will check for new product purchases and then confirm the continuous sync change.');
                if (!proceedContinuous) {
                    return;
                }
            }

            enableContinuousSyncOnServer(syncDate);
            return;
        }

        var convertFromContinuous = !!(CRM_SYNC_STATUS && CRM_SYNC_STATUS.continuous_active && syncType === 'one_time');
        if (convertFromContinuous) {
            var proceed = confirm('Switch this CRM sync from Continuous to One-Time? We will run a final check for new purchases before confirming the change.');
            if (!proceed) {
                return;
            }
        }

        if (!contacts.length) {
            alert('No contacts available to sync.');
            return;
        }

        showView('progress', syncDate);
        runBackgroundSync(contacts, contactTags, syncType, syncDate, selectedGroupIds, convertFromContinuous);
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