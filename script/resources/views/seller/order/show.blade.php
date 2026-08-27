@extends('layouts.backend.app')

@section('title', 'Dashboard')

@section('head')
    @include('layouts.backend.partials.headersection', [
        'title' => $info->invoice_no,
        'risk_level' => $info->risk_level,
        'prev' => url('seller/order'),
    ])
@endsection

@section('style')
<style>
    .section-header-risk-level {
    background: #fe0000;
    color: #fff;
    padding: 10px;
    flex-grow: 1;
    text-align: center;
    max-width: 800px;
    margin: 0 auto;
}

.section-header-risk-level .risk-level-text1 {
    font-size: 20px;
    color: #fff;
}
.risk-level {
    display: grid;
    grid-template-columns: repeat(3, 1fr );
    gap: 5px;
}

.risk-level span {
    width: 100%;
    height: 10px;
    background: #e0e0e0;
}

.high-risk.active {
    background: #fe0000;
}
.low-risk.active {
    background: #84ff8b;
}
.medium-risk.active {
    background: #ffc107;
}


.risk-level-text {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 5px;
    align-items: center;
}

.risk-level-text p {
    margin: 0;
    text-align: center;
    opacity: 0;
}

.risk-level-text p.active {
    opacity: 1;
}

.risk-level.not-assessed span {
    background-color: #a9e0ff;
}

.order-refund-card {
    border-top: 3px solid #00aeef;
    border-radius: 4px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
}

.order-refund-card .card-body {
    padding: 28px 32px 32px;
}

.order-refund-card .order-refund-title {
    font-size: 22px;
    font-weight: 700;
    color: #1f2d3d;
    margin-bottom: 16px;
}

.order-refund-card .order-refund-desc {
    color: #6c757d;
    font-size: 14px;
    line-height: 1.65;
    max-width: 920px;
    margin-bottom: 28px;
}

.order-refund-card .order-refund-field-label {
    font-weight: 600;
    color: #1f2d3d;
    font-size: 14px;
    margin-bottom: 8px;
}

.order-refund-card .order-refund-select {
    max-width: 420px;
    height: 42px;
}

.order-refund-card .btn-cancel-refund-process {
    background: #b5b5b5;
    border-color: #b5b5b5;
    color: #fff;
    font-weight: 600;
    padding: 10px 22px;
    margin-top: 22px;
    border-radius: 4px;
}

.order-refund-card .btn-cancel-refund-process:hover,
.order-refund-card .btn-cancel-refund-process:focus,
.order-refund-card .btn-cancel-refund-process:active {
    background: #a3a3a3 !important;
    border-color: #a3a3a3 !important;
    color: #fff !important;
    opacity: 1 !important;
    visibility: visible !important;
}

#order-refund-full-details {
    width: 100%;
}

.order-refund-details-col {
    margin-top: 28px;
}

.order-refund-breakdown {
    border: 1px solid #e8e8e8;
    border-radius: 4px;
    width: 100%;
}

.order-refund-breakdown .list-group-item {
    padding: 14px 18px;
}

.order-refund-not-refundable-badge {
    display: inline-block;
    background: #fde8e8;
    color: #d9534f;
    font-size: 12px;
    font-weight: 600;
    padding: 4px 10px;
    border-radius: 3px;
    white-space: nowrap;
}

.order-refund-total-bar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: #d4edda;
    border: 1px solid #b8dfc4;
    color: #1f4d2c;
    padding: 14px 18px;
    margin-top: 0;
    width: 100%;
    font-weight: 600;
}

.order-refund-total-bar .order-refund-total-amount {
    font-size: 18px;
    font-weight: 700;
}

.order-refund-actions {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 12px;
    margin-top: 24px;
}

.order-refund-actions-full {
    width: 100%;
    margin-top: 28px;
    padding-top: 4px;
}

.order-refund-actions .btn-cancel-refund-process {
    margin-top: 0;
}

.order-refund-actions .btn-complete-refund {
    background: #00aeef !important;
    border-color: #00aeef !important;
    color: #fff !important;
    font-weight: 700;
    padding: 10px 22px;
    border-radius: 4px;
}

.order-refund-actions .btn-complete-refund:hover,
.order-refund-actions .btn-complete-refund:focus,
.order-refund-actions .btn-complete-refund:active {
    background: #0099d6 !important;
    border-color: #0099d6 !important;
    color: #fff !important;
    opacity: 1 !important;
    visibility: visible !important;
}

.order-refund-confirm-dialog {
    max-width: 760px;
}

.order-refund-confirm-content {
    border: none;
    border-radius: 6px;
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
}

.order-refund-confirm-content .modal-body,
.order-refund-success-content .modal-body {
    padding: 40px 36px 32px;
    text-align: left;
}

.order-refund-confirm-title,
.order-refund-success-title {
    font-weight: 700;
    font-size: 22px;
    color: #1f2d3d;
    margin: 0 0 18px;
    line-height: 1.35;
    text-align: center;
}

.order-refund-confirm-desc,
.order-refund-success-desc {
    color: #6c757d;
    font-size: 14px;
    line-height: 1.65;
    margin-bottom: 28px;
    padding: 0;
    text-align: left;
}

.order-refund-confirm-actions {
    display: flex;
    align-items: center;
    justify-content: center;
    flex-wrap: wrap;
    gap: 14px;
}

.order-refund-confirm-actions .btn-refund-go-back {
    background: #b5b5b5 !important;
    border-color: #b5b5b5 !important;
    color: #fff !important;
    font-weight: 600;
    padding: 10px 18px;
    border-radius: 4px;
}

.order-refund-confirm-actions .btn-refund-go-back:hover,
.order-refund-confirm-actions .btn-refund-go-back:focus,
.order-refund-confirm-actions .btn-refund-go-back:active {
    background: #a3a3a3 !important;
    border-color: #a3a3a3 !important;
    color: #fff !important;
}

.order-refund-confirm-actions .btn-refund-confirm-submit {
    background: #00aeef !important;
    border-color: #00aeef !important;
    color: #fff !important;
    font-weight: 600;
    padding: 10px 18px;
    border-radius: 4px;
}

.order-refund-confirm-actions .btn-refund-confirm-submit:hover,
.order-refund-confirm-actions .btn-refund-confirm-submit:focus,
.order-refund-confirm-actions .btn-refund-confirm-submit:active {
    background: #0099d6 !important;
    border-color: #0099d6 !important;
    color: #fff !important;
}

.order-refund-partial-confirm-details {
    text-align: left;
    padding: 0 0 28px;
}

.order-refund-partial-confirm-detail-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    padding: 8px 0;
    color: #1f2d3d;
    font-size: 16px;
}

.order-refund-partial-confirm-detail-row .detail-label,
.order-refund-partial-confirm-detail-row .detail-value,
.order-refund-partial-confirm-detail-row strong {
    font-weight: 700;
    color: #1f2d3d;
    font-size: 16px;
}

.order-refund-partial-confirm-detail-row .detail-label {
    flex: 1;
    line-height: 1.45;
    text-align: left;
}

.order-refund-partial-confirm-detail-row .detail-value {
    white-space: nowrap;
    text-align: right;
    flex-shrink: 0;
}

.order-refund-partial-confirm-detail-row.is-total {
    padding-top: 8px;
}

.order-refund-partial-confirm-detail-row.is-total .detail-label,
.order-refund-partial-confirm-detail-row.is-total .detail-value {
    font-weight: 700;
    font-size: 16px;
    color: #1f2d3d;
}

.order-refund-success-dialog {
    max-width: 760px;
}

.order-refund-success-content {
    border: none;
    border-radius: 6px;
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
}

.order-refund-success-detail-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    padding: 8px 0;
    color: #1f2d3d;
    font-size: 16px;
}

.order-refund-success-detail-row strong {
    font-weight: 700;
    color: #1f2d3d;
    font-size: 16px;
}

.order-refund-success-detail-row .order-refund-success-detail-value {
    font-size: 16px;
    font-weight: 700;
    color: #1f2d3d;
    text-align: right;
    word-break: break-word;
    flex-shrink: 0;
}

.order-refund-success-actions {
    display: flex;
    align-items: center;
    justify-content: center;
}

.order-refund-success-actions .btn-refund-success-close {
    background: #00aeef !important;
    border-color: #00aeef !important;
    color: #fff !important;
    font-weight: 600;
    padding: 10px 18px;
    border-radius: 4px;
}

.order-refund-success-actions .btn-refund-success-close:hover,
.order-refund-success-actions .btn-refund-success-close:focus,
.order-refund-success-actions .btn-refund-success-close:active {
    background: #0099d6 !important;
    border-color: #0099d6 !important;
    color: #fff !important;
}

#partialDollarRefundSuccessModal .order-refund-success-actions .btn-refund-success-close {
    min-width: 235px;
}

#order-refund-partial-details .order-refund-partial-layout {
    display: flex;
    align-items: flex-start;
    gap: 0;
    width: 100%;
    margin-top: 28px;
}

.order-refund-partial-qty-sidebar {
    flex: 0 0 220px;
    max-width: 220px;
}

.order-refund-partial-qty-header {
    padding: 14px 18px;
    margin: 0;
    min-height: 0;
    display: flex;
    align-items: center;
    background: #fff;
    border: 1px solid #e8e8e8;
    border-right: none;
    border-radius: 4px 0 0 0;
    line-height: 1.5;
    font-size: 1rem;
}

.order-refund-partial-qty-header strong,
.order-refund-partial-products-panel .order-refund-breakdown > .list-group-item:first-child strong {
    font-weight: 700;
    color: #6c757d;
}

.order-refund-partial-qty-row {
    display: flex;
    align-items: center;
    justify-content: flex-start;
    box-sizing: border-box;
    min-height: 0;
    padding: 14px 18px;
    background: #e8f4fc;
    border-left: 1px solid #e8e8e8;
    border-bottom: 1px solid #e8e8e8;
    transition: background-color 0.2s ease;
}

.order-refund-partial-qty-row.is-selected {
    background: #d4edda;
}

.order-refund-partial-products-panel {
    flex: 1;
    min-width: 0;
}

.order-refund-partial-products-panel .order-refund-breakdown {
    width: 100%;
    border-radius: 0 4px 4px 4px;
    border-left: none;
}

.order-refund-partial-products-panel .order-refund-breakdown > .list-group-item:first-child {
    border-radius: 0;
}

.order-refund-partial-product-row {
    background: #e8f4fc;
    transition: background-color 0.2s ease;
}

.order-refund-partial-product-row.is-selected {
    background: #d4edda;
}

.order-refund-partial-qty-select,
.order-refund-partial-qty-select.form-control {
    width: 100%;
    max-width: 170px;
    height: 38px;
    margin: 0;
    font-weight: 600;
    color: #fff !important;
    border: 1px solid #00aeef !important;
    border-radius: 4px;
    -webkit-appearance: none;
    -moz-appearance: none;
    appearance: none;
    background: #00aeef url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath fill='%23ffffff' d='M1.41 0L6 4.58 10.59 0 12 1.41l-6 6-6-6z'/%3E%3C/svg%3E") no-repeat right 12px center !important;
    padding: 6px 34px 6px 12px;
    line-height: 1.2;
    box-shadow: none !important;
}

.order-refund-partial-qty-select option {
    color: #212529;
    background: #fff;
}

.order-refund-partial-qty-select:focus,
.order-refund-partial-qty-select.form-control:focus {
    border-color: #0099d6 !important;
    color: #fff !important;
    box-shadow: 0 0 0 0.1rem rgba(0, 174, 239, 0.2) !important;
    background: #0099d6 url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath fill='%23ffffff' d='M1.41 0L6 4.58 10.59 0 12 1.41l-6 6-6-6z'/%3E%3C/svg%3E") no-repeat right 12px center !important;
}

.order-refund-partial-per-item-badge {
    display: inline-block;
    background: #d9edf7;
    color: #31708f;
    font-size: 12px;
    font-weight: 600;
    padding: 4px 10px;
    border-radius: 3px;
    white-space: nowrap;
}

.order-refund-partial-summary-bar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 16px;
    background: #d4edda;
    border: 1px solid #b8dfc4;
    color: #1f4d2c;
    padding: 18px 20px;
    margin-top: 0;
    width: 100%;
}

.order-refund-partial-summary-block {
    min-width: 120px;
}

.order-refund-partial-summary-block .summary-label {
    display: block;
    font-size: 13px;
    font-weight: 600;
    color: #4a5568;
    margin-bottom: 4px;
}

.order-refund-partial-summary-block .summary-value {
    font-size: 22px;
    font-weight: 700;
    color: #1f2d3d;
    line-height: 1.2;
}

.order-refund-partial-summary-block .summary-value-sm {
    font-size: 18px;
    font-weight: 700;
    color: #1f2d3d;
}

.order-refund-partial-summary-equals {
    font-size: 24px;
    font-weight: 700;
    color: #1f2d3d;
    align-self: flex-end;
    padding-bottom: 4px;
}

.order-refund-partial-summary-plus {
    font-size: 24px;
    font-weight: 700;
    color: #1f2d3d;
    align-self: flex-end;
    padding-bottom: 4px;
}

@media (max-width: 991px) {
    .order-refund-partial-layout {
        flex-direction: column;
    }

    .order-refund-partial-qty-sidebar {
        flex: 0 0 auto;
        max-width: 100%;
        width: 100%;
    }

    .order-refund-partial-qty-header {
        padding-left: 18px;
        border-right: 1px solid #e8e8e8;
        border-radius: 4px 4px 0 0;
    }

    .order-refund-partial-qty-row {
        padding-left: 18px;
        border-right: 1px solid #e8e8e8;
    }

    .order-refund-partial-products-panel .order-refund-breakdown {
        border-left: 1px solid #e8e8e8;
        border-radius: 4px;
    }

    .order-refund-partial-summary-bar {
        flex-direction: column;
        align-items: flex-start;
    }

    .order-refund-partial-summary-equals,
    .order-refund-partial-summary-plus {
        align-self: flex-start;
    }
}

#order-refund-partial-dollar-details .order-refund-partial-dollar-layout {
    display: flex;
    align-items: flex-start;
    gap: 0;
    width: 100%;
    margin-top: 28px;
}

.order-refund-partial-dollar-sidebar {
    flex: 0 0 220px;
    max-width: 220px;
}

.order-refund-partial-dollar-header {
    padding: 14px 18px;
    margin: 0;
    min-height: 0;
    display: flex;
    align-items: center;
    background: #fff;
    border: 1px solid #e8e8e8;
    border-right: none;
    border-radius: 4px 0 0 0;
    line-height: 1.5;
    font-size: 1rem;
}

.order-refund-partial-dollar-row {
    display: flex;
    align-items: center;
    justify-content: flex-start;
    box-sizing: border-box;
    min-height: 0;
    padding: 14px 18px;
    background: #e8f4fc;
    border-left: 1px solid #e8e8e8;
    border-bottom: 1px solid #e8e8e8;
    transition: background-color 0.2s ease;
}

.order-refund-partial-dollar-row.is-selected {
    background: #d4edda;
}

.order-refund-partial-dollar-row.has-error {
    background: #f8d7da;
}

.order-refund-partial-dollar-input-wrap {
    display: flex;
    align-items: center;
    width: 100%;
    max-width: 170px;
}

.order-refund-partial-dollar-prefix {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    height: 38px;
    padding: 0 10px;
    background: #00aeef;
    color: #fff;
    font-weight: 700;
    border: 1px solid #00aeef;
    border-right: none;
    border-radius: 4px 0 0 4px;
}

.order-refund-partial-dollar-input,
.order-refund-partial-dollar-input.form-control {
    flex: 1;
    height: 38px;
    min-width: 0;
    font-weight: 600;
    color: #1f2d3d;
    border: 1px solid #00aeef;
    border-radius: 0 4px 4px 0;
    padding: 6px 10px;
    box-shadow: none !important;
}

.order-refund-partial-dollar-input:focus,
.order-refund-partial-dollar-input.form-control:focus {
    border-color: #0099d6;
    box-shadow: 0 0 0 0.1rem rgba(0, 174, 239, 0.2) !important;
}

.order-refund-partial-dollar-input.is-invalid,
.order-refund-partial-dollar-input.is-invalid:focus {
    border-color: #dc3545;
    background: #fff5f5;
}

.order-refund-partial-dollar-row.has-error .order-refund-partial-dollar-prefix {
    background: #dc3545;
    border-color: #dc3545;
}

.order-refund-partial-dollar-error-banner {
    display: none;
    background: #f8d7da;
    border: 1px solid #f5c6cb;
    color: #721c24;
    font-weight: 700;
    padding: 16px 20px;
    margin-top: 18px;
    border-radius: 4px;
    width: 100%;
}

.order-refund-partial-dollar-error-banner.is-visible {
    display: block;
}

@media (max-width: 991px) {
    #order-refund-partial-dollar-details .order-refund-partial-dollar-layout {
        flex-direction: column;
    }

    .order-refund-partial-dollar-sidebar {
        flex: 0 0 auto;
        max-width: 100%;
        width: 100%;
    }

    .order-refund-partial-dollar-header {
        padding-left: 18px;
        border-right: 1px solid #e8e8e8;
        border-radius: 4px 4px 0 0;
    }

    .order-refund-partial-dollar-row {
        padding-left: 18px;
        border-right: 1px solid #e8e8e8;
    }
}

/* Order details refund history (screenshot) */
.order-refund-history-row,
.order-refund-history-row .text-right {
    color: #a11a1a !important;
}

.order-refund-history-row .order-refund-history-main {
    font-weight: 600;
}

.order-refund-history-row .order-refund-history-sub {
    display: block;
    font-weight: 500;
    margin-top: 2px;
    color: #a11a1a !important;
}

.order-refund-history-row .order-refund-history-tax {
    padding-left: 0;
    font-weight: 500;
}

.order-refund-history-updated-net {
    font-weight: 600;
    color: #1f2d3d;
}

.badge-order-partial-refund {
    background-color: #8b2f8b !important;
    color: #fff !important;
}

.badge-cash-check {
    background-color: #fd7e14 !important;
    color: #fff !important;
}

.badge-order-payment-refunded {
    background-color: #dc3545 !important;
    color: #fff !important;
}
</style>
@endsection
@section('content')
    @php
        $refundSuccess = session('refund_success') ?: session('cash_refund_success');
        $partialRefundSuccess = session('partial_refund_success');
        $partialDollarRefundSuccess = session('partial_dollar_refund_success');

        // Shared cash/check flags for Status button + refund modals (additive).
        $statusOrdermeta = json_decode(optional($info->ordermeta)->value ?? '', true) ?: [];
        $isCashCheckOrder = ($statusOrdermeta['payment_method_label'] ?? '') === 'cash/check';
        $isCashCheckCaptured = $isCashCheckOrder && !empty($statusOrdermeta['cash_check_captured_at']);

        // Build refund history rows for order details totals (display only).
        $orderRefundHistoryLogs = json_decode(
            \App\Models\Ordermeta::where('order_id', $info->id)->where('key', 'partial_refund_logs')->value('value') ?? '[]',
            true
        ) ?: [];

        $orderRefundHistoryRows = [];
        $orderRefundHistoryTotal = 0.0;
        $orderRefundHistorySeen = [];
        $hasItemPartialRefundLogs = false;
        $hasDollarPartialRefundLogs = false;

        foreach ($orderRefundHistoryLogs as $refundLog) {
            if (!is_array($refundLog)) {
                continue;
            }

            $logItems = is_array($refundLog['items'] ?? null) ? $refundLog['items'] : [];
            $itemAmount = 0.0;
            $taxAmount = 0.0;
            $itemLabels = [];
            $normalizedItems = [];

            foreach ($logItems as $logItem) {
                if (!is_array($logItem)) {
                    continue;
                }

                $lineAmount = round((float) ($logItem['amount'] ?? 0), 2);
                $lineTax = round((float) ($logItem['tax'] ?? 0), 2);
                $itemAmount += $lineAmount;
                $taxAmount += $lineTax;

                if (!empty($logItem['label'])) {
                    $itemLabels[] = $logItem['label'];
                }

                $normalizedItems[] = [
                    'item_id' => (int) ($logItem['item_id'] ?? 0),
                    'amount' => $lineAmount,
                    'tax' => $lineTax,
                    'qty' => (int) ($logItem['qty'] ?? 0),
                    'label' => (string) ($logItem['label'] ?? ''),
                ];
            }

            // Fallback if older logs only stored grand total.
            if ($itemAmount <= 0 && empty($logItems)) {
                $itemAmount = round((float) ($refundLog['amount'] ?? 0), 2);
            }

            // Skip duplicate log entries in UI (same refund content written more than once).
            $refundFingerprint = md5(json_encode([
                'amount' => round((float) ($refundLog['amount'] ?? ($itemAmount + $taxAmount)), 2),
                'type' => (string) ($refundLog['type'] ?? ''),
                'items' => $normalizedItems,
            ]));

            if (isset($orderRefundHistorySeen[$refundFingerprint])) {
                continue;
            }
            $orderRefundHistorySeen[$refundFingerprint] = true;

            $taxAmount = sanitize_refund_tax_for_order($info, $taxAmount);

            $refundDate = !empty($refundLog['refunded_at'])
                ? \Carbon\Carbon::parse($refundLog['refunded_at'])->format('m/d/Y')
                : '';

            $isDollarType = (($refundLog['type'] ?? '') === 'dollar');
            if ($isDollarType) {
                $hasDollarPartialRefundLogs = true;
                $refundTitle = __('Refund & Cancel Partial Order By Dollar Amount');
                $refundSubtitle = !empty($itemLabels) ? implode(', ', $itemLabels) : '';
            } else {
                $hasItemPartialRefundLogs = true;
                $refundTitle = __('Refund & Cancel Partial Order By Item');
                // Screenshot: (1 x Product Name)
                $refundSubtitle = !empty($itemLabels) ? '(' . implode(', ', $itemLabels) . ')' : '';
            }

            $orderRefundHistoryRows[] = [
                'date' => $refundDate,
                'title' => $refundTitle,
                'subtitle' => $refundSubtitle,
                'item_amount' => round($itemAmount, 2),
                'tax_amount' => round($taxAmount, 2),
                'is_item_partial' => !$isDollarType,
            ];

            $orderRefundHistoryTotal += $itemAmount + $taxAmount;
        }

        // Full Order screenshot format only when fully refunded AND no partial item/dollar logs.
        if (
            (int) $info->payment_status === 5
            && !$hasItemPartialRefundLogs
            && !$hasDollarPartialRefundLogs
        ) {
            $fullRefundItemTotal = 0.0;
            foreach ($info->orderitems ?? [] as $fullRefundItem) {
                $fullRefundVariations = json_decode($fullRefundItem->info ?? '');
                $fullRefundOptions = $fullRefundVariations->options ?? [];
                $fullRefundUnit = (float) $fullRefundItem->amount;
                if (!is_array($fullRefundOptions) && is_object($fullRefundOptions) && isset($fullRefundOptions->varition_options)) {
                    $fullRefundUnit = (float) $fullRefundOptions->price;
                }
                $fullRefundItemTotal += $fullRefundUnit * (int) $fullRefundItem->qty;
            }

            $fullRefundTax = sanitize_refund_tax_for_order($info, (float) ($info->tax ?? 0));
            $fullRefundDateSource = $info->refunded_at ?: $info->updated_at;
            $fullRefundDate = $fullRefundDateSource
                ? \Carbon\Carbon::parse($fullRefundDateSource)->format('m/d/Y')
                : '';

            $orderRefundHistoryRows = [[
                'date' => $fullRefundDate,
                'title' => __('Refund & Cancel Full Order') . ' ' . __('(All Items)'),
                'subtitle' => '',
                'item_amount' => round($fullRefundItemTotal, 2),
                'tax_amount' => round($fullRefundTax, 2),
                'is_full_order' => true,
            ]];
            $orderRefundHistoryTotal = round($fullRefundItemTotal + $fullRefundTax, 2);
        }

        $hasOrderRefundHistory = !empty($orderRefundHistoryRows);
        // Purple Partial Refund badge for line-item / dollar partial refunds (screenshot).
        $isOrderPartialRefundDisplay = $hasItemPartialRefundLogs || $hasDollarPartialRefundLogs;
        $isOrderFullyRefundedDisplay = (int) $info->payment_status === 5
            && !$hasItemPartialRefundLogs
            && !$hasDollarPartialRefundLogs;
    @endphp

    @if(session('success') && !$refundSuccess && !$partialRefundSuccess && !$partialDollarRefundSuccess)
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div id="order-details-view">
    <div class="row" id="order">
        <div class="col-12 col-lg-8">
            <div class="card card-primary">
                <div class="card-body">

                    <ul class="list-group list-group-lg list-group-flush list">
                        <li class="list-group-item">
                            <div class="row align-items-center">
                                <div class="col-6">
                                    <strong>{{ __('Product') }}</strong>
                                </div>
                                <div class="col-3 text-right">
                                    <strong>{{ __('Amount') }}</strong>
                                </div>
                                <div class="col-3 text-right">
                                    <strong>{{ __('Total') }}</strong>
                                </div>
                            </div>
                        </li>
                        @php 
                        $subtotal = 0; 
                        $selected_product_type = [];
                        @endphp
                        @foreach ($info->orderitems ?? [] as $row)
                            @php
                            $p_types = $product_type->pluck('id')->all();

                            $selected_product_type = $row->term->termcategories
                                ->pluck('category_id')
                                ->intersect($p_types)
                                ->values()
                                ->all();
                            @endphp    

                            <li class="list-group-item">
                                <div class="row align-items-center">
                                    <div class="col-6">
                                        @php
                                            $variations = json_decode($row->info ?? '');
                                            $options = $variations->options ?? [];
                                            $varition_price = null;
                                        @endphp

                                        <a href="{{ url('/seller/product/' . $row->term->id . '/edit') }}">{{ $row->term->title ?? '' }}
                                            @if ($options == '' && !empty($variations->sku))
                                                ({{ $row->term->full_id }})
                                            @elseif($options == '' && !empty($variations->sku))
                                                ({{ $variations->sku }})
                                            @elseif($options == '')
                                                ({{ $row->term->full_id }})
                                            @endif
                                            <br>
                                        </a>
                                        @if(is_array($options))
                                        @foreach ($options ?? [] as $key => $item)
                                        @if (is_object($item) && isset($item->varition_options))
                                            @php  $product_options = $item->varition_options; @endphp
                                            @foreach($item->varitions as $sel_val)
                                                @php $cur_opt_name = array_filter($product_options,function ($x) use ($sel_val) {
                                                    return $x->id == $sel_val->pivot->productoption_id;
                                                });
                                                @endphp
                                            <strong>{{reset($cur_opt_name)->category->name}} : </strong>{{$sel_val->name}}<br>
                                            @endforeach
                                                <hr>
                                            @endif
                                            @endforeach
                                        @else
                                          @if(is_object($options) && isset($options->varition_options))
                                            @php  $product_options = $options->varition_options; @endphp
                                             @foreach($options->varitions as $sel_val)
                                                @php $cur_opt_name = array_filter($product_options,function ($x) use ($sel_val) {
                                                    return $x->id == $sel_val->pivot->productoption_id;
                                                });
                                                @endphp
                                            <strong>{{reset($cur_opt_name)->category->name}} : </strong>{{$sel_val->name}}<br>
                                            @endforeach
                                            @php $varition_price = $options->price;  
                                             $row->amount = $options->price;
                                            @endphp
                                            
                                          @endif
                                         
                                        @endif
                                    </div>
                                    <div class="col-3 text-right">
                                        {{ currency_formate($row->amount) }} × {{ $row->qty }}
                                    </div>
                                    <div class="col-3 text-right">
                                        @php $subtotal = $subtotal + $row->amount*$row->qty; @endphp
                                        {{ currency_formate($row->amount * $row->qty) }}
                                    </div>
                                </div>
                            </li>
                        @endforeach

                        @foreach ($info->quickSaleOrderItems ?? [] as $quickSaleRow)
                            @php
                                $quickSaleQty = max(1, (int) ($quickSaleRow->qty ?? 1));
                                $quickSaleUnit = round((float) ($quickSaleRow->unit_amount ?? 0), 2);
                                $quickSaleLineTotal = round((float) ($quickSaleRow->line_subtotal ?? ($quickSaleUnit * $quickSaleQty)), 2);
                                $quickSaleTitle = trim((string) ($quickSaleRow->title ?? ''));
                                if ($quickSaleTitle === '') {
                                    $quickSaleTitle = 'Quick Sale - ' . ($quickSaleRow->descriptor_name ?: 'Miscellaneous Item');
                                }
                            @endphp
                            <li class="list-group-item">
                                <div class="row align-items-center">
                                    <div class="col-6">
                                        {{ $quickSaleTitle }}
                                    </div>
                                    <div class="col-3 text-right">
                                        {{ currency_formate($quickSaleUnit) }} × {{ $quickSaleQty }}
                                    </div>
                                    <div class="col-3 text-right">
                                        @php $subtotal = $subtotal + $quickSaleLineTotal; @endphp
                                        {{ currency_formate($quickSaleLineTotal) }}
                                    </div>
                                </div>
                            </li>
                        @endforeach

                        @php
                        $count = count($selected_product_type);

                        $order_type = match (true) {
                            $count > 1 => 'Mixed',
                            $count === 1 => optional(
                                $product_type->firstWhere('id', $selected_product_type[0])
                            )->slug === 'digital_product' ? 'Digital' : 'Goods',
                            default => 'Goods',
                        };
                        @endphp  
                        
                        <li class="list-group-item">
                            <div class="row align-items-center">
                                <div class="col-9 text-right">{{ __('SubTotal') }}</div>
                                <div class="col-3 text-right"> {{ currency_formate($subtotal) }}</div>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="row align-items-center">
                                <div class="col-9 text-right">{{ __('Order Discount') }}@isset($info->coupon_code) ({{ $info->coupon_code }}) @endisset</div>
                                <div class="col-3 text-right"> - {{ currency_formate($info->discount) }} </div>
                            </div>
                        </li>
                        @if ($info->order_method == 'delivery' && $order_type !== 'Digital')
                            @php
                                $shipping_price = $info->shippingwithinfo->shipping_price ?? 0;
                            @endphp
                            <li class="list-group-item">
                                <div class="row align-items-center">
                                    <div class="col-6">
                                        {{ $info->shipping_info->shipping_method->name ?? '' }}
                                    </div>
                                    <div class="col-3 text-right">
                                        {{ __('Shipping Fee') }}
                                    </div>
                                    <div class="col-3 text-right">
                                        {{ currency_formate($shipping_price) }}
                                    </div>
                                </div>
                            </li>
                        @endif
                        <li class="list-group-item">
                            <div class="row align-items-center">
                                <div class="col-9 text-right">{{ __('Tax') }}</div>
                                <div class="col-3 text-right"> {{ currency_formate($info->tax) }} </div>
                            </div>
                        </li>

                        @php
                            $credit_card_fee = 0;
                            $booster_platform_fee = 0;
                            $shipping_price = $shipping_price ?? 0;
                            $cover_fee = $booster_platform_fee + $credit_card_fee;
                            if (!empty($ordermeta) && $info->getway->name !== 'cash' ) {
                                $credit_card_fee = isset($ordermeta->credit_card_fee) ? $ordermeta->credit_card_fee : 0;
                                $booster_platform_fee =isset($ordermeta->booster_platform_fee) ? $ordermeta->booster_platform_fee : 0 ;
                                $cover_fee = isset($ordermeta->cover_fee) ? $ordermeta->cover_fee : 0;
                            }

                        @endphp
                       @if(!empty($info->shippingwithinfo) && $info->shippingwithinfo->shipping_driver == 'local')
                                @php
                                       $shippingwithinfo_price = json_decode($info->shippingwithinfo->info ?? '');
                                       $credit_card_fee = isset($shippingwithinfo_price->credit_card_fee) ? $shippingwithinfo_price->credit_card_fee : 0;
                                       $booster_platform_fee =isset($shippingwithinfo_price->booster_platform_fee) ? $shippingwithinfo_price->booster_platform_fee : 0 ;
                                @endphp
                        @endif
                       @if($cover_fee > 0)
                        <li class="list-group-item">
                            <div class="row align-items-center">
                                <div class="col-9 text-right">{{ __('Supporter Covered Fees') }}</div>
                                <div class="col-3 text-right">{{ currency_formate($cover_fee) }}</div>
                            </div>
                        </li>
                        @endif
                        
                        <li class="list-group-item">
                            <div class="row align-items-center">
                                <div class="col-9 text-right">{{ __('Order Total') }}</div>
                                <div class="col-3 text-right">{{ currency_formate($info->total) }}</div>
                            </div>
                        </li>
                        @php
                            //$club_info = tenant_club_info();
                            $pro_club = tenant_club_is_pro();

                        @endphp

                       @if($info->getway->name !== 'cash')
                        <li class="list-group-item">
                            <div class="row align-items-center text-grey">
                                <div class="col-9 text-right">{{ __('Credit Card Processing ') }}</div>
                                @if($cover_fee > 0)
                                <div class="col-3 text-right">{{ __('Supporter Covered Fees') }}</div>
                               @else
                                 <div class="col-3 text-right">{{ currency_formate($credit_card_fee) }}</div>
                                @endif
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="row align-items-center text-grey">
                                <!-- <div class="col-9 text-right">{{ __('Booostr Platform Fee') }}
                                    {{ !empty($club_info['is_pro']) ? '(1.75%)' : '(3.5%)' }}</div> -->
                                <div class="col-9 text-right">{{ __('Booostr Platform Fee') }}
                                    {{ ($pro_club) ? '(1.75%)' : '(3.5%)' }}</div>
                                @if($cover_fee > 0)
                                 <div class="col-3 text-right">{{ __('Supporter Covered Fees') }}</div>
                               @else
                                 <div class="col-3 text-right">{{ currency_formate($booster_platform_fee) }}</div>
                               @endif
                            </div>
                        </li>
                       
                        <li class="list-group-item">
                            <div class="row align-items-center text-grey">
                                <div class="col-9 text-right">
                                    {{ __('Net Order Total (Order Total - Credit Card Processing and Booostr Platform Fee)') }}
                                </div>

                                <div class="col-3 text-right">
                                    @if($cover_fee > 0)
                                    {{ currency_formate($info->total - $cover_fee) }}
                                    @else
                                    {{ currency_formate($info->total - $credit_card_fee - $booster_platform_fee) }}
                                    @endif
                                </div>
                            </div>
                        </li>
                        @endif

                        @if($hasOrderRefundHistory)
                            @php
                                $orderRemainingAfterRefund = calculate_order_remaining_after_partial_refunds($info, $ordermeta);

                                // Full refund screenshot: Updated Net is always $0.00
                                if (!empty($isOrderFullyRefundedDisplay)) {
                                    $updatedNetOrderTotal = 0;
                                } else {
                                    $updatedNetOrderTotal = (float) ($orderRemainingAfterRefund['remaining_net'] ?? 0);
                                }
                            @endphp

                            @foreach($orderRefundHistoryRows as $refundHistoryRow)
                                <li class="list-group-item order-refund-history-row">
                                    <div class="row align-items-center">
                                        <div class="col-9 text-right">
                                            <span class="order-refund-history-main">
                                                {{ $refundHistoryRow['date'] }}{{ $refundHistoryRow['date'] !== '' ? ' - ' : '' }}{{ $refundHistoryRow['title'] }}
                                            </span>
                                            @if(!empty($refundHistoryRow['subtitle']))
                                                <span class="order-refund-history-sub">{{ $refundHistoryRow['subtitle'] }}</span>
                                            @endif
                                        </div>
                                        <div class="col-3 text-right">
                                            {{ currency_formate($refundHistoryRow['item_amount']) }}
                                        </div>
                                    </div>
                                </li>
                                @if(($refundHistoryRow['tax_amount'] ?? 0) > 0)
                                    <li class="list-group-item order-refund-history-row">
                                        <div class="row align-items-center">
                                            <div class="col-9 text-right">
                                                <span class="order-refund-history-tax">{{ __('Related Tax Adjustment Refund') }}</span>
                                            </div>
                                            <div class="col-3 text-right">
                                                {{ currency_formate($refundHistoryRow['tax_amount']) }}
                                            </div>
                                        </div>
                                    </li>
                                @endif
                            @endforeach

                            <li class="list-group-item">
                                <div class="row align-items-center order-refund-history-updated-net">
                                    <div class="col-9 text-right">{{ __('Updated Net Order Total') }}</div>
                                    <div class="col-3 text-right">{{ currency_formate($updatedNetOrderTotal) }}</div>
                                </div>
                            </li>
                        @endif
                    </ul>
                </div>
                <div class="card-footer">
                    <div class="text-right">
                        <form method="POST" action="{{ route('seller.order.update', $info->id . '_' . $info->user_id) }}"
                            accept-charset="UTF-8" class="d-inline ajaxform">
                            @csrf
                            @method('PUT')
                            <div class="form-row">
								<!-- @if ($info->order_method == 'delivery')
									@php
										$rider = $info->shippingwithinfo->user_id ?? '';
									@endphp
									<div class="col-sm-4">
									<div class="form-group text-left">
									<label >Select Rider</label>
									<select class="form-control selectric" name="rider">
									<option value=""><b>{{ __('Select Rider') }}</b></option>
									@foreach ($riders as $row)
										<option value="{{ $row->id }}" @if ($rider == $row->id) selected="" @endif>{{ $row->name }} (#{{ $row->id }})</option>
										@endforeach

									</select>
									</div>
									</div>

									@endif -->

								<!-- <div class="col-sm-4">
								<div class="form-group text-left">
									<label>Select Payment Status</label>
									<select class="form-control selectric" name="payment_status" required="">
									<option value=""><b>{{ __('Select Payment Status') }}</b></option>
									<option value="1" @if ($info->payment_status == '1') selected="" @endif>{{ __('Payment Complete') }}</option>
									<option value="2" @if ($info->payment_status == '2') selected="" @endif>{{ __('Payment Pending') }}</option>
									<option value="0" @if ($info->payment_status == '0') selected="" @endif>{{ __('Payment Cancel') }}</option>
									<option value="3" @if ($info->payment_status == '3') selected="" @endif>{{ __('Payment Incomplete') }}</option>
									</select>
								</div>
								</div> -->

                                <div class="col-sm-4">
                                    <div class="form-group text-left">
                                        <label>Select Order Status </label>
                                        <select class="form-control selectric" id="mainSelect" name="status" required="">
                                            <option value=""><b>{{ __('Select Order Status') }}</b></option>
                                            @foreach ($order_status ?? [] as $row)
                                                <option value="{{ $row->id }}"
                                                    @if ($info->status_id == $row->id) selected="" @endif>
                                                    {{ $row->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                @php
                                $shipping_servics = ['FedEx','UPS','US Postal Service'];
                                if($info->shippingwithinfo !== null && $order_type != 'Digital'){
                                @endphp
                              <div class="col-sm-4" id="hiddenChooseTracking" @if($info->shippingwithinfo->shipping_driver == 'local')style="display:none;" @endif>
                                    <div class="form-group text-left">
                                        <label>Select shipping service </label>
                                        <select class="form-control selectric" id="chooseTracking" name="chooseTracking">
                                            <option value="" selected><b>{{ __('Select option') }}</b></option>
                                            <option value="FedEx" @if ($info->shippingwithinfo->shipping_driver == 'FedEx') selected="" @endif>{{ __('FedEx') }}</option>
                                            <option value="UPS" @if ($info->shippingwithinfo->shipping_driver == 'UPS') selected="" @endif><b>{{ __('UPS') }}</b></option>
                                            <option value="US Postal Service" @if ($info->shippingwithinfo->shipping_driver == 'US Postal Service') selected="" @endif><b>{{ __('US Postal Service') }}</b></option>
                                            <option value="Other" @if ($info->shippingwithinfo->shipping_driver != 'local' && !in_array($info->shippingwithinfo->shipping_driver,$shipping_servics)) selected="" @endif><b>{{ __('Other') }}</b></option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-3" id="hide_shipping_service" @if($info->shippingwithinfo->shipping_driver == 'local' || $info->shippingwithinfo->shipping_driver == 'FedEx' || $info->shippingwithinfo->shipping_driver == 'UPS' || $info->shippingwithinfo->shipping_driver == 'US Postal Service')style="display:none;"@endif>
									<div class="form-group text-left">
										<label>Enter shipping service</label>
									<input type="text" class="form-control" name="shipping_service" id="shipping_service" value="{{$info->shippingwithinfo->shipping_driver??''}}">
									</div>
								</div>

								<div class="col-sm-3" id="hide_tacking_number" @if($info->shippingwithinfo->shipping_driver == 'local')style="display:none;"@endif>
									<div class="form-group text-left">
										<label>Enter tracking number</label>
										<input type="text" value="{{$info->shippingwithinfo->tracking_no}}" class="form-control" name="tacking_number" id="tacking_number" id="tacking_number">
									</div>
								</div>
							@php 
                                }
                            @endphp
              

                            </div>
                            <div class="form-group">
                                <label class="custom-switch mt-2">
                                    <input type="hidden" name="mail_notify" value="1" class="custom-switch-input">
                                    <!-- <span class="custom-switch-indicator"></span> -->
                                    <!-- <span class="custom-switch-description">{{ __('Notify To Customer') }}</span> -->
                                </label>
                                @if ($info->order_method == 'delivery' && $order_type !== 'Digital')
                                    <label class="custom-switch mt-2">
                                        <input type="hidden" name="rider_notify" value="1"
                                            class="custom-switch-input">
                                        <!-- <span class="custom-switch-indicator"></span> -->
                                        <!-- <span class="custom-switch-description">{{ __('Notify To Admin') }}</span> -->
                                    </label>
                                @endif
                            </div>
                    </div>
                    <button type="submit"
                        class="btn btn-primary float-right mt-2 ml-2 basicbtn">{{ __('Save Changes') }}</button>
                    <a href="{{ route('seller.order.edit', $info->id) }}"
                        class="btn btn-primary text-right float-right mt-2">{{ __('Print Invoice') }}</a>
                    </form>
                </div>
            </div>
            @if (!empty($ordermeta))
                <div class="card card-primary">
                    <div class="card-header">
                        <h4 class="card-header-title">{{ __('Shipping details') }}</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-6">
                                <p class="mb-0">{{ __('Customer Name') }}: {{ $ordermeta->name ?? '' }}</p>
                                <p class="mb-0">{{ __('Customer Email') }}: {{ $ordermeta->email ?? '' }}</p>
                                <p class="mb-0">{{ __('Customer Phone') }}: {{ $ordermeta->phone ?? '' }}</p>
                            </div>
                        </div>
                        @if ($info->order_method == 'delivery' && $order_type !== 'Digital')
                            @php
                                $shipping_info = json_decode($info->shippingwithinfo->info ?? '');
                                //$location=$info->shippingwithinfo->location->name ?? '';
                                $address = $shipping_info->address ?? '';
                                $shipping_method = $shipping_info->shipping_label ?? '';
                            @endphp
                            {{-- <p class="mb-0">{{ __('Location') }}: {{ $location }}</p> --}}
                            <p class="mb-0">{{ __('Zip Code') }}: {{ $shipping_info->post_code ?? '' }}</p>
                            <p class="mb-0">{{ __('Address') }}: {{ $address }}</p>
                            <p class="mb-0">{{ __('Shipping Method') }}: {{ $shipping_method ?? '' }}</p>
                        @endif
                        
                        @if ($info->order_method == 'pickup')
                            @php
                                $pickup = $pickup_details ?? null; // order meta
                            @endphp
                        
                            <p class="mb-0">{{ __('Shipping Method') }}: In-Person Pick Up</p>
                        
                            @if(!empty($pickup))
                                <p class="mb-0"><b>Pick Up Address:</b>
                                    {{ $pickup['address_line1'] ?? '' }} {{ $pickup['address_line2'] ?? '' }},
                                    {{ $pickup['city'] ?? '' }}, {{ $pickup['state'] ?? '' }} {{ $pickup['zip'] ?? '' }}
                                </p>
                        
                                <p class="mb-0"><b>Pick Up Instructions:</b> {{ $pickup['instructions'] ?? '' }}</p>
                            @endif
                        @endif


                        @if ($info->order_method == 'delivery' && !empty($info->shippingwithinfo))
                            <div id="map" class="map-canvas"></div>
                        @endif


                        {{-- PICKUP (In-Person Pick Up details) --}}
                    @if ($info->order_method == 'pickup' && $order_type !== 'Digital')
                        @php
                            // pickup details option se aa rahe hain (same as checkout)
                            $pickupOption = \App\Models\Option::where('key', 'inperson_pickup_details')->first();
                            $pickupData = $pickupOption && $pickupOption->value ? json_decode($pickupOption->value, true) : [];

                            // details keys (as per your dump)
                            $d = $pickupData ?? [];
                            $a1 = $d['address_line1'] ?? '';
                            $a2 = $d['address_line2'] ?? '';
                            $city = $d['city'] ?? '';
                            $state = $d['state'] ?? '';
                            $zip = $d['zip'] ?? '';

                            $instructions = trim((string)($d['instructions'] ?? ''));
                        @endphp

                        <p class="mb-0"><b>{{ __('Shipping Method') }}:</b> {{ __('In-Person Pick Up') }}</p>

                        <div class="mt-3">
                            <h6 class="mb-2"><b>{{ __('Location & Instructions') }}</b></h6>

                            {{-- Address --}}
                            <div class="mb-2">
                                <div>{{ $a1 }}</div>
                                @if(!empty($a2)) <div>{{ $a2 }}</div> @endif
                                <div>{{ trim($city . ( $city && ($state || $zip) ? ', ' : '' ) . $state . ' ' . $zip) }}</div>
                            </div>

                            {{-- Instructions (same format as client screenshot) --}}
                            @if(!empty($instructions))
                                <div style="white-space: pre-line; color:#666;">
                                    {{ $instructions }}
                                </div>
                            @endif
                        </div>
                    @endif

                    </div>
                </div>
            @endif
        </div>
        <div class="col-12 col-lg-4">
            <div class="card-grouping">
                <div class="card card-primary">
                    <div class="card-header" style="justify-content: space-between;">
                        <h4>{{ __('Status') }}</h4>
                        @php
                            // Prefer flags computed at top of page; keep local decode as fallback.
                            $statusOrdermeta = $statusOrdermeta ?? (json_decode(optional($info->ordermeta)->value ?? '', true) ?: []);
                            $isCashCheckOrder = $isCashCheckOrder ?? (($statusOrdermeta['payment_method_label'] ?? '') === 'cash/check');
                            $isCashCheckCaptured = $isCashCheckCaptured ?? ($isCashCheckOrder && !empty($statusOrdermeta['cash_check_captured_at']));
                        @endphp

                        @if ($info->payment_status == 4 && ($isCashCheckOrder || optional($info->getway)->name !== 'cash'))
                            <div class="capture-btn">
                                <form method="POST" action="{{ route('seller.order.capture', $info->id) }}">
                                    @csrf
                                    <button type="submit" name="capture_payment"
                                        class="btn btn-primary float-right mt-2 text-right">Capture Payment</button>
                                </form>
                            </div>
                        @endif

                        @if ($info->payment_status == 1 && optional($info->getway)->name !== 'cash')
                        <div class="capture-btn">
                            <button type="button"
                                class="btn btn-primary float-right mt-2 text-right"
                                id="show-order-refund-view-btn">
                                Cancel Order & Refund Payment
                            </button>
                        </div>

                            <!-- <div class="capture-btn">
                                <form method="POST" action="{{ route('seller.order.refund', $info->id) }}">
                                    @csrf
                                    <button type="submit" name="refund_payment"
                                        class="btn btn-primary float-right mt-2 text-right">Cancel Order & Refund
                                        Payment</button>
                                </form>
                            </div> -->
                        @endif

                        {{-- Additive: cash/check captured — same refund UI as Stripe (full/partial/dollar). --}}
                        @if ($info->payment_status == 1 && $isCashCheckCaptured)
                            <div class="capture-btn">
                                <button type="button"
                                    class="btn btn-primary float-right mt-2 text-right"
                                    id="show-cash-check-refund-view-btn">
                                    {{ __('Cancel Order & Refund Cash Payment') }}
                                </button>
                            </div>
                        @endif

                    </div>
                    <div class="card-body">
                        <p>{{ __('Payment Status') }}
                            @if ($isOrderPartialRefundDisplay)
                                <span class="badge badge-order-partial-refund float-right">{{ __('Partial Refund') }}</span>
                            @elseif ($info->payment_status == 2)
                                <span class="badge badge-warning float-right">{{ __('Pending') }}</span>
                            @elseif($info->payment_status == 1)
                                @if($isCashCheckOrder && !empty($statusOrdermeta['cash_check_captured_at']))
                                    {{-- Captured via "set capture payment": same label as the order list. --}}
                                    <span class="badge badge-success float-right">{{ __('Complete') }}</span>
                                @elseif($isCashCheckOrder)
                                    <span class="badge badge-cash-check float-right">{{ __('cash/check') }}</span>
                                @else
                                    <span class="badge badge-success float-right">{{ __('Paid') }}</span>
                                @endif
                            @elseif($info->payment_status == 0)
                                <span class="badge badge-danger float-right">{{ __('Cancel') }}</span>
                            @elseif($info->payment_status == 3)
                                <span class="badge badge-danger float-right">{{ __('Incomplete') }}</span>
                            @elseif($info->payment_status == 4)
                                <span class="badge badge-danger float-right">{{ __('Authorized') }}</span>
                            @elseif($info->payment_status == 5)
                                <span class="badge badge-order-payment-refunded float-right">{{ __('Refunded') }}</span>
                            @endif
                        </p>

                        <p>{{ __('Order Status') }}
                        @if($info->order_from == 4 || $info->order_from == 5)
                        <span class="badge badge-success float-right text-white" style="background-color:#028a74">POS (In Person)</span>
                        @elseif($info->order_from == 0)
                        <span class="badge badge-success float-right text-white" style="background-color:#028a74">POS Web (In Person)</span>
                        @elseif ($info->status_id != null)
                                @php
                                    $orderStatusName = optional($info->orderstatus)->name ?? '';
                                    $orderStatusIsPending = strcasecmp(trim((string) $orderStatusName), 'Pending') === 0;
                                @endphp
                                <span class="badge float-right text-white"
                                    style="background-color: {{ $orderStatusIsPending ? '#e9a825' : (optional($info->orderstatus)->slug ?? '#ffc107') }}">{{ $orderStatusName }}</span>
                        @endif
                        </p>

                        <p>{{ __('Order Type') }}
                            @if($order_type == 'Digital')
                              <span class="badge badge-success float-right"> Digital {{ $info->order_method }}</span>
                            @elseif($order_type == 'Goods')
                              <span class="badge badge-success float-right">{{ $info->order_method }}</span>
                            @else
                              <span class="badge badge-success float-right">{{ $info->order_method }}, Digital Delivery</span>
                            @endif

                        </p>

                        <p>{{ __('Order Placed') }}
                            <span class="badge badge-success float-right">
                                {{ \Carbon\Carbon::parse($info->placed_at)->format('m/d/Y h:i A') }}
                            </span>
                        </p>

                        @if($info->captured_at != null)
                        <p>{{ __('Order Capture') }}
                            <span class="badge badge-danger float-right">
                            {{ \Carbon\Carbon::parse($info->captured_at)->format('m/d/Y h:i A') }}
                            </span>
                        </p>
                        @endif

                        @if($info->refunded_at != null)

                        <p>{{ __('Order Refund') }}
                            <span class="badge badge-warning float-right">
                                {{ \Carbon\Carbon::parse($info->refunded_at)->format('m/d/Y h:i A') }}
                            </span>
                        </p>
                        @endif
                        
                    </div>
                </div>
                @if (!empty($info->schedule))
                    <div class="card card-primary">
                        <div class="card-header">
                            <h4>{{ __('Pre Order Information') }}</h4>
                        </div>
                        <div class="card-body">
                            <p>{{ __('Date Of Order') }}
                                <span class="float-right"><b>{{ $info->schedule->date ?? '' }}</b></span>
                            </p>
                            <p>{{ __('Order Time') }}
                                <span class="float-right"><b>{{ $info->schedule->time ?? '' }}</b></span>
                            </p>
                        </div>
                    </div>
                @endif
                @if ($info->order_method == 'table')
                    <div class="card card-primary">
                        <div class="card-header">
                            <h4>{{ __('Table Information') }}</h4>
                        </div>
                        <div class="card-body">
                            @foreach ($info->ordertable ?? [] as $row)
                                <p>{{ __('Table No :') }}
                                    <a href="{{ route('seller.table.edit', $row->id) }}"><span
                                            class="float-right"><b>{{ $row->name }}</b></span></a>
                                </p>
                            @endforeach
                        </div>
                    </div>
                @endif
                <div class="card card-primary">
                    <div class="card-header">
                        <h4>{{ __('Payment Mode') }}</h4>
                    </div>
                    <div class="card-body">
                        @if ($info->getway_id != null)
                            <p>{{ __('Transaction Method') }} <span
                                    class="badge  badge-success  float-right">{{ $info->getway->name ?? '' }} </span></p>
                            <p>{{ __('Transaction Id') }} <span
                                    class="float-right">{{ $info->transaction_id ?? '' }}</span></p>
                        @else
                            <p>{{ __('Incomplete Payment') }}</p>
                        @endif
                    </div>
                </div>

                <div class="card card-primary">
                    <div class="card-header">
                        <h4>{{ __('Order Risk Level') }}</h4>
                    </div>
                    <div class="card-body">
                        @if ($info->risk_level == 'not_assessed')
                              <div class="risk-level not-assessed"> 
                                <span class="low-risk"></span>
                                <span class="medium-risk"></span>
                                <span class="high-risk"></span>
                             </div> 
                             <div class="risk-level-text">
                                <p>low</p>
                                <p class="active">Not Assessed</p>
                                <p >High</p>
                            </div> 
                            <span>Chargeback Risk is Not Assessed either because your organization has opted out of fraud assessment OR the payment type is not a credit or debit card (ie. ACH Transfer)</span>
                        @elseif($info->risk_level == 'normal')
                             <div class="risk-level"> 
                                <span class="low-risk active"></span>
                                <span class="medium-risk"></span>
                                <span class="high-risk"></span>
                             </div> 
                             <div class="risk-level-text">
                                <p class="active">Low</p>
                                <p>Medium</p>
                                <p>High</p>
                            </div>                           
                         <span>Chargeback Risk is Low, which is normal and can be processed.</span>
                        @elseif($info->risk_level == 'elevated')
                            <div class="risk-level"> 
                                <span class="low-risk"></span>
                                <span class="medium-risk active"></span>
                                <span class="high-risk"></span>
                             </div> 
                             <div class="risk-level-text">
                                <p>low</p>
                                <p class="active">Medium</p>
                                <p>High</p>
                            </div>                
                            <span>Chargeback Risk is Medium, confirm with customer before capturing payment and fulfilling the order.</span>
                        @elseif($info->risk_level == 'highest')
                            <div class="risk-level"> 
                                <span class="low-risk"></span>
                                <span class="medium-risk"></span>
                                <span class="high-risk active"></span>
                             </div> 
                             <div class="risk-level-text">
                                <p>low</p>
                                <p>Medium</p>
                                <p class="active">High</p>
                            </div> 
                            <span>Chargeback Risk is HIGH, consider canceling this order.</span>
                        @elseif($info->risk_level == 'unknown')
                            <div class="risk-level unknown"> 
                                <span class="low-risk"></span>
                                <span class="medium-risk"></span>
                                <span class="high-risk"></span>
                             </div> 

                             <div class="risk-level-text">
                                <p>low</p>
                                <p class="active">Unknown</p>
                                <p >High</p>
                            </div> 
                            <span>Chargeback Risk is Unknown due to incomplete risk verification. Review customer order history, this order and make a determination to accept or cancel the order.</span>
                        @endif

                    </div>
                </div>


                @if (!empty($ordermeta->comment ?? ''))
                    <div class="card card-primary">
                        <div class="card-header">
                            <h4 class="card-header-title">{{ __('Note') }}</h4>
                        </div>
                        <div class="card-body">
                            <p class="mb-0">{{ $ordermeta->comment ?? '' }}</p>
                        </div>
                    </div>
                @endif
                @if ($info->user_id != null)
                    <div class="card card-primary">
                        <div class="card-header">
                            <h4 class="card-header-title">{{ __('Customer') }}</h4>
                        </div>
                        <div class="card-body">
                            @if ($info->user != null)
                                <a href="{{ route('seller.user.show', $info->user->id) }}">{{ $ordermeta->name ?? $info->user->name }}
                                    (#{{ $info->user->id }})</a>
                            @else
                                {{ __('Guest Customer') }}
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
    </div>

    <div id="order-refund-cancellation-view" style="display:none;">
        @php
            $refund_subtotal = 0;
            $refund_selected_product_type = [];

            foreach ($info->orderitems ?? [] as $refund_row) {
                $refund_p_types = $product_type->pluck('id')->all();
                $refund_selected_product_type = $refund_row->term->termcategories
                    ->pluck('category_id')
                    ->intersect($refund_p_types)
                    ->values()
                    ->all();

                $refund_variations = json_decode($refund_row->info ?? '');
                $refund_options = $refund_variations->options ?? [];
                $refund_row_amount = $refund_row->amount;

                if (!is_array($refund_options) && is_object($refund_options) && isset($refund_options->varition_options)) {
                    $refund_row_amount = $refund_options->price;
                }

                $refund_subtotal += $refund_row_amount * $refund_row->qty;
            }

            $refund_order_tax = order_has_sales_tax($info) ? (float) ($info->tax ?? 0) : 0.0;

            $refund_type_count = count($refund_selected_product_type);
            $refund_order_type = match (true) {
                $refund_type_count > 1 => 'Mixed',
                $refund_type_count === 1 => optional(
                    $product_type->firstWhere('id', $refund_selected_product_type[0])
                )->slug === 'digital_product' ? 'Digital' : 'Goods',
                default => 'Goods',
            };

            $refund_credit_card_fee = 0;
            $refund_booster_platform_fee = 0;
            $refund_shipping_price = 0;
            $refund_cover_fee = $refund_booster_platform_fee + $refund_credit_card_fee;

            if (!empty($ordermeta) && $info->getway->name !== 'cash') {
                $refund_credit_card_fee = $ordermeta->credit_card_fee ?? 0;
                $refund_booster_platform_fee = $ordermeta->booster_platform_fee ?? 0;
                $refund_cover_fee = $ordermeta->cover_fee ?? 0;
            }

            if (!empty($info->shippingwithinfo) && $info->shippingwithinfo->shipping_driver == 'local') {
                $refund_shipping_info_price = json_decode($info->shippingwithinfo->info ?? '');
                $refund_credit_card_fee = $refund_shipping_info_price->credit_card_fee ?? 0;
                $refund_booster_platform_fee = $refund_shipping_info_price->booster_platform_fee ?? 0;
            }

            if ($info->order_method == 'delivery' && $refund_order_type !== 'Digital') {
                $refund_shipping_price = $info->shippingwithinfo->shipping_price ?? 0;
            }

            $refund_pro_club = tenant_club_is_pro();
            $refund_net_total = $refund_cover_fee > 0
                ? ($info->total - $refund_cover_fee)
                : ($info->total - $refund_credit_card_fee - $refund_booster_platform_fee);
        @endphp

        <div class="card card-primary order-refund-card">
            <div class="card-body">
                <h4 class="order-refund-title" id="order-refund-section-title">{{ __('Order Refund & Cancellation') }}</h4>
                <p class="order-refund-desc mb-0">
                    @if (!empty($isCashCheckCaptured))
                        {{ __('Refund & cancel full or partial cash/check orders. Refunds are recorded in Store Manager without Stripe. Please return the cash/check amount to the purchaser outside of Stripe.') }}
                    @else
                        {{ __('Refund & cancel full or partial orders. We do not charge any fees for refunds, but the full or partial original processing fee is not refunded. All refunds are handled via Stripe will be deducted from your connected bank account and sent to the customer\'s original payment method used for the transaction. Payment processing fees cannot be refunded.') }}
                    @endif
                </p>

                <div class="form-group mb-0 mt-4">
                    <label class="order-refund-field-label d-block" for="order_refund_type_select">
                        {{ __('Full or partial order refund & cancellation:') }}
                    </label>
                    <select id="order_refund_type_select" class="form-control order-refund-select">
                        <option value="">{{ __('Choose') }}</option>
                        <option value="full">{{ __('Refund & Cancel Full Order') }}</option>
                        <option value="partial">{{ __('Refund & Cancel Partial Order By Item') }}</option>
                        <option value="partial_dollar">{{ __('Refund & Cancel Partial Order By Dollar Amount') }}</option>
                    </select>
                </div>

                <div id="order-refund-full-details" style="display:none;">
                    <div class="row justify-content-center">
                        <div class="col-12 col-lg-8 order-refund-details-col">
                    <ul class="list-group list-group-lg list-group-flush list order-refund-breakdown">
                        <li class="list-group-item">
                            <div class="row align-items-center">
                                <div class="col-6"><strong>{{ __('Product') }}</strong></div>
                                <div class="col-3 text-right"><strong>{{ __('Amount') }}</strong></div>
                                <div class="col-3 text-right"><strong>{{ __('Total') }}</strong></div>
                            </div>
                        </li>

                        @foreach ($info->orderitems ?? [] as $refund_row)
                            @php
                                $refund_variations = json_decode($refund_row->info ?? '');
                                $refund_options = $refund_variations->options ?? [];
                                $refund_line_amount = $refund_row->amount;

                                if (!is_array($refund_options) && is_object($refund_options) && isset($refund_options->varition_options)) {
                                    $refund_line_amount = $refund_options->price;
                                }
                            @endphp
                            <li class="list-group-item">
                                <div class="row align-items-center">
                                    <div class="col-6">
                                        <a href="{{ url('/seller/product/' . $refund_row->term->id . '/edit') }}">{{ $refund_row->term->title ?? '' }}
                                            @if (!is_array($refund_options) && $refund_options == '' && !empty($refund_variations->sku))
                                                ({{ $refund_row->term->full_id }})
                                            @elseif(!is_array($refund_options) && $refund_options == '')
                                                ({{ $refund_row->term->full_id }})
                                            @endif
                                            <br>
                                        </a>
                                        @if(is_array($refund_options))
                                            @foreach ($refund_options ?? [] as $refund_key => $refund_item)
                                                @if (is_object($refund_item) && isset($refund_item->varition_options))
                                                    @php $refund_product_options = $refund_item->varition_options; @endphp
                                                    @foreach($refund_item->varitions as $refund_sel_val)
                                                        @php $refund_cur_opt_name = array_filter($refund_product_options, function ($x) use ($refund_sel_val) {
                                                            return $x->id == $refund_sel_val->pivot->productoption_id;
                                                        }); @endphp
                                                        <strong>{{ reset($refund_cur_opt_name)->category->name }} : </strong>{{ $refund_sel_val->name }}<br>
                                                    @endforeach
                                                    <hr>
                                                @endif
                                            @endforeach
                                        @elseif(is_object($refund_options) && isset($refund_options->varition_options))
                                            @php $refund_product_options = $refund_options->varition_options; @endphp
                                            @foreach($refund_options->varitions as $refund_sel_val)
                                                @php $refund_cur_opt_name = array_filter($refund_product_options, function ($x) use ($refund_sel_val) {
                                                    return $x->id == $refund_sel_val->pivot->productoption_id;
                                                }); @endphp
                                                <strong>{{ reset($refund_cur_opt_name)->category->name }} : </strong>{{ $refund_sel_val->name }}<br>
                                            @endforeach
                                        @endif
                                    </div>
                                    <div class="col-3 text-right">
                                        {{ currency_formate($refund_line_amount) }} × {{ $refund_row->qty }}
                                    </div>
                                    <div class="col-3 text-right">
                                        {{ currency_formate($refund_line_amount * $refund_row->qty) }}
                                    </div>
                                </div>
                            </li>
                        @endforeach

                        <li class="list-group-item">
                            <div class="row align-items-center">
                                <div class="col-9 text-right">{{ __('SubTotal') }}</div>
                                <div class="col-3 text-right">{{ currency_formate($refund_subtotal) }}</div>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="row align-items-center">
                                <div class="col-9 text-right">{{ __('Order Discount') }}@isset($info->coupon_code) ({{ $info->coupon_code }}) @endisset</div>
                                <div class="col-3 text-right">- {{ currency_formate($info->discount) }}</div>
                            </div>
                        </li>
                        @if ($info->order_method == 'delivery' && $refund_order_type !== 'Digital')
                            <li class="list-group-item">
                                <div class="row align-items-center">
                                    <div class="col-6">{{ $info->shipping_info->shipping_method->name ?? '' }}</div>
                                    <div class="col-3 text-right">{{ __('Shipping Fee') }}</div>
                                    <div class="col-3 text-right">{{ currency_formate($refund_shipping_price) }}</div>
                                </div>
                            </li>
                        @endif
                        <li class="list-group-item">
                            <div class="row align-items-center">
                                <div class="col-9 text-right">{{ __('Tax') }}</div>
                                <div class="col-3 text-right">{{ currency_formate($info->tax) }}</div>
                            </div>
                        </li>
                        @if($refund_cover_fee > 0)
                            <li class="list-group-item">
                                <div class="row align-items-center">
                                    <div class="col-3">
                                        <span class="order-refund-not-refundable-badge">{{ __('Not refundable') }}</span>
                                    </div>
                                    <div class="col-6 text-right">{{ __('Supporter Covered Fees') }}</div>
                                    <div class="col-3 text-right">{{ currency_formate($refund_cover_fee) }}</div>
                                </div>
                            </li>
                        @endif
                        <li class="list-group-item">
                            <div class="row align-items-center">
                                <div class="col-9 text-right">{{ __('Order Total') }}</div>
                                <div class="col-3 text-right">{{ currency_formate($info->total) }}</div>
                            </div>
                        </li>
                        @if($info->getway->name !== 'cash')
                            <li class="list-group-item">
                                <div class="row align-items-center text-grey">
                                    <div class="col-3">
                                        <span class="order-refund-not-refundable-badge">{{ __('Not refundable') }}</span>
                                    </div>
                                    <div class="col-6 text-right">{{ __('Credit Card Processing ') }}</div>
                                    @if($refund_cover_fee > 0)
                                        <div class="col-3 text-right">{{ __('Supporter Covered Fees') }}</div>
                                    @else
                                        <div class="col-3 text-right">{{ currency_formate($refund_credit_card_fee) }}</div>
                                    @endif
                                </div>
                            </li>
                            <li class="list-group-item">
                                <div class="row align-items-center text-grey">
                                    <div class="col-3">
                                        <span class="order-refund-not-refundable-badge">{{ __('Not refundable') }}</span>
                                    </div>
                                    <div class="col-6 text-right">{{ __('Booostr Platform Fee') }} {{ ($refund_pro_club) ? '(1.75%)' : '(3.5%)' }}</div>
                                    @if($refund_cover_fee > 0)
                                        <div class="col-3 text-right">{{ __('Supporter Covered Fees') }}</div>
                                    @else
                                        <div class="col-3 text-right">{{ currency_formate($refund_booster_platform_fee) }}</div>
                                    @endif
                                </div>
                            </li>
                            <li class="list-group-item">
                                <div class="row align-items-center text-grey">
                                    <div class="col-9 text-right">
                                        {{ __('Net Order Total (Order Total - Credit Card Processing and Booostr Platform Fee)') }}
                                    </div>
                                    <div class="col-3 text-right">{{ currency_formate($refund_net_total) }}</div>
                                </div>
                            </li>
                        @endif
                    </ul>

                    <div class="order-refund-total-bar">
                        <span>{{ __('Total Order Refund Amount:') }}</span>
                        <span class="order-refund-total-amount">{{ currency_formate($refund_net_total) }}</span>
                    </div>
                        </div>
                    </div>

                    <div class="order-refund-actions order-refund-actions-full">
                        <button type="button" class="btn btn-cancel-refund-process" id="cancel-order-refund-process-btn-full">
                            {{ __('Cancel Order Cancellation Process') }}
                        </button>
                        <button type="button"
                            class="btn btn-complete-refund"
                            id="complete-full-refund-btn"
                            data-toggle="modal"
                            data-target="#refundConfirmModal">
                            {{ __('Complete Refund & Cancellation') }}
                        </button>
                    </div>
                </div>

                <div id="order-refund-partial-details" style="display:none;">
                    <div class="row justify-content-center">
                        <div class="col-12 col-lg-10 order-refund-details-col">
                            <div class="order-refund-partial-layout">
                                <div class="order-refund-partial-qty-sidebar">
                                    <div class="order-refund-partial-qty-header"><strong>{{ __('Item Refunded & Cancelled Qty') }}</strong></div>
                                    @foreach ($info->orderitems ?? [] as $partial_refund_row)
                                        @php
                                            $partial_refund_variations = json_decode($partial_refund_row->info ?? '');
                                            $partial_refund_options = $partial_refund_variations->options ?? [];
                                            $partial_refund_line_amount = $partial_refund_row->amount;

                                            if (!is_array($partial_refund_options) && is_object($partial_refund_options) && isset($partial_refund_options->varition_options)) {
                                                $partial_refund_line_amount = $partial_refund_options->price;
                                            }

                                            $partial_refund_line_total = $partial_refund_line_amount * $partial_refund_row->qty;
                                            $partial_refund_item_tax = $refund_subtotal > 0
                                                ? (($partial_refund_line_total / $refund_subtotal) * $refund_order_tax)
                                                : 0;
                                        @endphp
                                        <div class="order-refund-partial-qty-row"
                                            data-item-id="{{ $partial_refund_row->id }}"
                                            data-unit-amount="{{ $partial_refund_line_amount }}"
                                            data-max-qty="{{ $partial_refund_row->qty }}"
                                            data-item-tax-total="{{ $partial_refund_item_tax }}"
                                            data-product-title="{{ e($partial_refund_row->term->title ?? '') }}">
                                            <select class="form-control order-refund-partial-qty-select partial-refund-qty-select">
                                                <option value="0">{{ __('Choose Qty') }}</option>
                                                @for ($partial_qty_option = 1; $partial_qty_option <= $partial_refund_row->qty; $partial_qty_option++)
                                                    <option value="{{ $partial_qty_option }}">{{ $partial_qty_option }}</option>
                                                @endfor
                                            </select>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="order-refund-partial-products-panel">
                            <ul class="list-group list-group-lg list-group-flush list order-refund-breakdown">
                                <li class="list-group-item">
                                    <div class="row align-items-center">
                                        <div class="col-6"><strong>{{ __('Product') }}</strong></div>
                                        <div class="col-3 text-right"><strong>{{ __('Amount') }}</strong></div>
                                        <div class="col-3 text-right"><strong>{{ __('Total') }}</strong></div>
                                    </div>
                                </li>

                                @foreach ($info->orderitems ?? [] as $partial_refund_row)
                                    @php
                                        $partial_refund_variations = json_decode($partial_refund_row->info ?? '');
                                        $partial_refund_options = $partial_refund_variations->options ?? [];
                                        $partial_refund_line_amount = $partial_refund_row->amount;

                                        if (!is_array($partial_refund_options) && is_object($partial_refund_options) && isset($partial_refund_options->varition_options)) {
                                            $partial_refund_line_amount = $partial_refund_options->price;
                                        }

                                        $partial_refund_line_total = $partial_refund_line_amount * $partial_refund_row->qty;
                                    @endphp
                                    <li class="list-group-item order-refund-partial-product-row" data-item-id="{{ $partial_refund_row->id }}">
                                        <div class="row align-items-center">
                                            <div class="col-6">
                                                <a href="{{ url('/seller/product/' . $partial_refund_row->term->id . '/edit') }}">{{ $partial_refund_row->term->title ?? '' }}
                                                    @if (!is_array($partial_refund_options) && $partial_refund_options == '' && !empty($partial_refund_variations->sku))
                                                        ({{ $partial_refund_row->term->full_id }})
                                                    @elseif(!is_array($partial_refund_options) && $partial_refund_options == '')
                                                        ({{ $partial_refund_row->term->full_id }})
                                                    @endif
                                                    <br>
                                                </a>
                                                @if(is_array($partial_refund_options))
                                                    @foreach ($partial_refund_options ?? [] as $partial_refund_key => $partial_refund_item)
                                                        @if (is_object($partial_refund_item) && isset($partial_refund_item->varition_options))
                                                            @php $partial_refund_product_options = $partial_refund_item->varition_options; @endphp
                                                            @foreach($partial_refund_item->varitions as $partial_refund_sel_val)
                                                                @php $partial_refund_cur_opt_name = array_filter($partial_refund_product_options, function ($x) use ($partial_refund_sel_val) {
                                                                    return $x->id == $partial_refund_sel_val->pivot->productoption_id;
                                                                }); @endphp
                                                                <strong>{{ reset($partial_refund_cur_opt_name)->category->name }} : </strong>{{ $partial_refund_sel_val->name }}<br>
                                                            @endforeach
                                                            <hr>
                                                        @endif
                                                    @endforeach
                                                @elseif(is_object($partial_refund_options) && isset($partial_refund_options->varition_options))
                                                    @php $partial_refund_product_options = $partial_refund_options->varition_options; @endphp
                                                    @foreach($partial_refund_options->varitions as $partial_refund_sel_val)
                                                        @php $partial_refund_cur_opt_name = array_filter($partial_refund_product_options, function ($x) use ($partial_refund_sel_val) {
                                                            return $x->id == $partial_refund_sel_val->pivot->productoption_id;
                                                        }); @endphp
                                                        <strong>{{ reset($partial_refund_cur_opt_name)->category->name }} : </strong>{{ $partial_refund_sel_val->name }}<br>
                                                    @endforeach
                                                @endif
                                            </div>
                                            <div class="col-3 text-right">
                                                {{ currency_formate($partial_refund_line_amount) }} × {{ $partial_refund_row->qty }}
                                            </div>
                                            <div class="col-3 text-right">
                                                {{ currency_formate($partial_refund_line_total) }}
                                            </div>
                                        </div>
                                    </li>
                                @endforeach

                                <li class="list-group-item">
                                    <div class="row align-items-center">
                                        <div class="col-9 text-right">{{ __('SubTotal') }}</div>
                                        <div class="col-3 text-right">{{ currency_formate($refund_subtotal) }}</div>
                                    </div>
                                </li>
                                <li class="list-group-item">
                                    <div class="row align-items-center">
                                        <div class="col-3">
                                            <span class="order-refund-not-refundable-badge">{{ __('Not refundable') }}</span>
                                        </div>
                                        <div class="col-6 text-right">{{ __('Order Discount') }}@isset($info->coupon_code) ({{ $info->coupon_code }}) @endisset</div>
                                        <div class="col-3 text-right">- {{ currency_formate($info->discount) }}</div>
                                    </div>
                                </li>
                                @if ($info->order_method == 'delivery' && $refund_order_type !== 'Digital')
                                    <li class="list-group-item">
                                        <div class="row align-items-center">
                                            <div class="col-6">{{ $info->shipping_info->shipping_method->name ?? '' }}</div>
                                            <div class="col-3 text-right">{{ __('Shipping Fee') }}</div>
                                            <div class="col-3 text-right">{{ currency_formate($refund_shipping_price) }}</div>
                                        </div>
                                    </li>
                                @endif
                                <li class="list-group-item">
                                    <div class="row align-items-center">
                                        <div class="col-3">
                                            <span class="order-refund-partial-per-item-badge">{{ __('Partial Per-item Refund') }}</span>
                                        </div>
                                        <div class="col-6 text-right">{{ __('Tax') }}</div>
                                        <div class="col-3 text-right">{{ currency_formate($info->tax) }}</div>
                                    </div>
                                </li>
                                @if($refund_cover_fee > 0)
                                    <li class="list-group-item">
                                        <div class="row align-items-center">
                                            <div class="col-3">
                                                <span class="order-refund-not-refundable-badge">{{ __('Not refundable') }}</span>
                                            </div>
                                            <div class="col-6 text-right">{{ __('Supporter Covered Fees') }}</div>
                                            <div class="col-3 text-right">{{ currency_formate($refund_cover_fee) }}</div>
                                        </div>
                                    </li>
                                @endif
                                <li class="list-group-item">
                                    <div class="row align-items-center">
                                        <div class="col-3">
                                            <span class="order-refund-not-refundable-badge">{{ __('Not refundable') }}</span>
                                        </div>
                                        <div class="col-6 text-right">{{ __('Order Total') }}</div>
                                        <div class="col-3 text-right">{{ currency_formate($info->total) }}</div>
                                    </div>
                                </li>
                                @if($info->getway->name !== 'cash')
                                    <li class="list-group-item">
                                        <div class="row align-items-center text-grey">
                                            <div class="col-3">
                                                <span class="order-refund-not-refundable-badge">{{ __('Not refundable') }}</span>
                                            </div>
                                            <div class="col-6 text-right">{{ __('Credit Card Processing ') }}</div>
                                            @if($refund_cover_fee > 0)
                                                <div class="col-3 text-right">{{ __('Supporter Covered Fees') }}</div>
                                            @else
                                                <div class="col-3 text-right">{{ currency_formate($refund_credit_card_fee) }}</div>
                                            @endif
                                        </div>
                                    </li>
                                    <li class="list-group-item">
                                        <div class="row align-items-center text-grey">
                                            <div class="col-3">
                                                <span class="order-refund-not-refundable-badge">{{ __('Not refundable') }}</span>
                                            </div>
                                            <div class="col-6 text-right">{{ __('Booostr Platform Fee') }} {{ ($refund_pro_club) ? '(1.75%)' : '(3.5%)' }}</div>
                                            @if($refund_cover_fee > 0)
                                                <div class="col-3 text-right">{{ __('Supporter Covered Fees') }}</div>
                                            @else
                                                <div class="col-3 text-right">{{ currency_formate($refund_booster_platform_fee) }}</div>
                                            @endif
                                        </div>
                                    </li>
                                    <li class="list-group-item">
                                        <div class="row align-items-center text-grey">
                                            <div class="col-9 text-right">
                                                {{ __('Net Order Total (Order Total - Credit Card Processing and Booostr Platform Fee)') }}
                                            </div>
                                            <div class="col-3 text-right">{{ currency_formate($refund_net_total) }}</div>
                                        </div>
                                    </li>
                                @endif
                            </ul>

                            <div class="order-refund-partial-summary-bar">
                                <div class="order-refund-partial-summary-block">
                                    <span class="summary-label">{{ __('Partial Order Refund Amount:') }}</span>
                                    <span class="summary-value" id="partial_refund_items_label">{{ __('0 items selected') }}</span>
                                </div>
                                <div class="order-refund-partial-summary-block">
                                    <span class="summary-label">{{ __('item refund total') }}</span>
                                    <span class="summary-value-sm" id="partial_refund_item_total">{{ currency_formate(0) }}</span>
                                </div>
                                <span class="order-refund-partial-summary-plus">+</span>
                                <div class="order-refund-partial-summary-block">
                                    <span class="summary-label">{{ __('item total tax') }}</span>
                                    <span class="summary-value-sm" id="partial_refund_tax_total">{{ currency_formate(0) }}</span>
                                </div>
                                <span class="order-refund-partial-summary-equals">=</span>
                                <div class="order-refund-partial-summary-block">
                                    <span class="summary-label">{{ __('total refund amount') }}</span>
                                    <span class="summary-value" id="partial_refund_grand_total">{{ currency_formate(0) }}</span>
                                </div>
                            </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="order-refund-actions order-refund-actions-full">
                        <button type="button" class="btn btn-cancel-refund-process" id="cancel-order-refund-process-btn-partial">
                            {{ __('Cancel Order Cancellation Process') }}
                        </button>
                        <button type="button"
                            class="btn btn-complete-refund"
                            id="complete-partial-refund-btn"
                            disabled>
                            {{ __('Complete Refund & Cancellation') }}
                        </button>
                    </div>
                </div>

                <div id="order-refund-partial-dollar-details" style="display:none;">
                    @php
                        $dollar_refunded_qty_meta = \App\Models\Ordermeta::where('order_id', $info->id)->where('key', 'partial_refunded_items')->value('value');
                        $dollar_refunded_qtys = json_decode($dollar_refunded_qty_meta ?? '{}', true) ?: [];
                        $dollar_refunded_dollar_meta = \App\Models\Ordermeta::where('order_id', $info->id)->where('key', 'partial_dollar_refunded_items')->value('value');
                        $dollar_refunded_amounts = json_decode($dollar_refunded_dollar_meta ?? '{}', true) ?: [];
                    @endphp
                    <div class="row justify-content-center">
                        <div class="col-12 col-lg-10 order-refund-details-col">
                            <div class="order-refund-partial-dollar-layout">
                                <div class="order-refund-partial-dollar-sidebar">
                                    <div class="order-refund-partial-dollar-header"><strong>{{ __('Amount to Refund') }}</strong></div>
                                    @foreach ($info->orderitems ?? [] as $dollar_refund_row)
                                        @php
                                            $dollar_refund_variations = json_decode($dollar_refund_row->info ?? '');
                                            $dollar_refund_options = $dollar_refund_variations->options ?? [];
                                            $dollar_refund_line_amount = $dollar_refund_row->amount;

                                            if (!is_array($dollar_refund_options) && is_object($dollar_refund_options) && isset($dollar_refund_options->varition_options)) {
                                                $dollar_refund_line_amount = $dollar_refund_options->price;
                                            }

                                            $dollar_refund_line_total = $dollar_refund_line_amount * $dollar_refund_row->qty;
                                            $dollar_refund_item_tax = $refund_subtotal > 0
                                                ? (($dollar_refund_line_total / $refund_subtotal) * $refund_order_tax)
                                                : 0;

                                            $dollar_qty_refunded_value = ((int) ($dollar_refunded_qtys[$dollar_refund_row->id] ?? 0)) * $dollar_refund_line_amount;
                                            $dollar_already_refunded = (float) ($dollar_refunded_amounts[$dollar_refund_row->id] ?? 0);
                                            $dollar_remaining_line_total = max(0, round($dollar_refund_line_total - $dollar_qty_refunded_value - $dollar_already_refunded, 2));
                                        @endphp
                                        <div class="order-refund-partial-dollar-row"
                                            data-item-id="{{ $dollar_refund_row->id }}"
                                            data-line-total="{{ $dollar_remaining_line_total }}"
                                            data-item-tax-total="{{ $dollar_refund_item_tax }}"
                                            data-line-total-original="{{ $dollar_refund_line_total }}"
                                            data-product-title="{{ e($dollar_refund_row->term->title ?? '') }}"
                                            data-item-qty="{{ $dollar_refund_row->qty }}">
                                            <div class="order-refund-partial-dollar-input-wrap">
                                                <span class="order-refund-partial-dollar-prefix">{{ get_option('currency_data', true)->currency_icon ?? '$' }}</span>
                                                <input type="text"
                                                    class="form-control order-refund-partial-dollar-input partial-dollar-refund-amount-input"
                                                    inputmode="decimal"
                                                    pattern="^\d*\.?\d{0,2}$"
                                                    placeholder="0.00"
                                                    autocomplete="off"
                                                    @if($dollar_remaining_line_total <= 0) disabled @endif>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="order-refund-partial-products-panel">
                            <ul class="list-group list-group-lg list-group-flush list order-refund-breakdown">
                                <li class="list-group-item">
                                    <div class="row align-items-center">
                                        <div class="col-6"><strong>{{ __('Product') }}</strong></div>
                                        <div class="col-3 text-right"><strong>{{ __('Amount') }}</strong></div>
                                        <div class="col-3 text-right"><strong>{{ __('Total') }}</strong></div>
                                    </div>
                                </li>

                                @foreach ($info->orderitems ?? [] as $dollar_refund_row)
                                    @php
                                        $dollar_refund_variations = json_decode($dollar_refund_row->info ?? '');
                                        $dollar_refund_options = $dollar_refund_variations->options ?? [];
                                        $dollar_refund_line_amount = $dollar_refund_row->amount;

                                        if (!is_array($dollar_refund_options) && is_object($dollar_refund_options) && isset($dollar_refund_options->varition_options)) {
                                            $dollar_refund_line_amount = $dollar_refund_options->price;
                                        }

                                        $dollar_refund_line_total = $dollar_refund_line_amount * $dollar_refund_row->qty;
                                    @endphp
                                    <li class="list-group-item order-refund-partial-dollar-product-row" data-item-id="{{ $dollar_refund_row->id }}">
                                        <div class="row align-items-center">
                                            <div class="col-6">
                                                <a href="{{ url('/seller/product/' . $dollar_refund_row->term->id . '/edit') }}">{{ $dollar_refund_row->term->title ?? '' }}
                                                    @if (!is_array($dollar_refund_options) && $dollar_refund_options == '' && !empty($dollar_refund_variations->sku))
                                                        ({{ $dollar_refund_row->term->full_id }})
                                                    @elseif(!is_array($dollar_refund_options) && $dollar_refund_options == '')
                                                        ({{ $dollar_refund_row->term->full_id }})
                                                    @endif
                                                    <br>
                                                </a>
                                                @if(is_array($dollar_refund_options))
                                                    @foreach ($dollar_refund_options ?? [] as $dollar_refund_key => $dollar_refund_item)
                                                        @if (is_object($dollar_refund_item) && isset($dollar_refund_item->varition_options))
                                                            @php $dollar_refund_product_options = $dollar_refund_item->varition_options; @endphp
                                                            @foreach($dollar_refund_item->varitions as $dollar_refund_sel_val)
                                                                @php $dollar_refund_cur_opt_name = array_filter($dollar_refund_product_options, function ($x) use ($dollar_refund_sel_val) {
                                                                    return $x->id == $dollar_refund_sel_val->pivot->productoption_id;
                                                                }); @endphp
                                                                <strong>{{ reset($dollar_refund_cur_opt_name)->category->name }} : </strong>{{ $dollar_refund_sel_val->name }}<br>
                                                            @endforeach
                                                            <hr>
                                                        @endif
                                                    @endforeach
                                                @elseif(is_object($dollar_refund_options) && isset($dollar_refund_options->varition_options))
                                                    @php $dollar_refund_product_options = $dollar_refund_options->varition_options; @endphp
                                                    @foreach($dollar_refund_options->varitions as $dollar_refund_sel_val)
                                                        @php $dollar_refund_cur_opt_name = array_filter($dollar_refund_product_options, function ($x) use ($dollar_refund_sel_val) {
                                                            return $x->id == $dollar_refund_sel_val->pivot->productoption_id;
                                                        }); @endphp
                                                        <strong>{{ reset($dollar_refund_cur_opt_name)->category->name }} : </strong>{{ $dollar_refund_sel_val->name }}<br>
                                                    @endforeach
                                                @endif
                                            </div>
                                            <div class="col-3 text-right">
                                                {{ currency_formate($dollar_refund_line_amount) }} × {{ $dollar_refund_row->qty }}
                                            </div>
                                            <div class="col-3 text-right">
                                                {{ currency_formate($dollar_refund_line_total) }}
                                            </div>
                                        </div>
                                    </li>
                                @endforeach

                                <li class="list-group-item">
                                    <div class="row align-items-center">
                                        <div class="col-9 text-right">{{ __('SubTotal') }}</div>
                                        <div class="col-3 text-right">{{ currency_formate($refund_subtotal) }}</div>
                                    </div>
                                </li>
                                <li class="list-group-item">
                                    <div class="row align-items-center">
                                        <div class="col-3">
                                            <span class="order-refund-not-refundable-badge">{{ __('Not refundable') }}</span>
                                        </div>
                                        <div class="col-6 text-right">{{ __('Order Discount') }}@isset($info->coupon_code) ({{ $info->coupon_code }}) @endisset</div>
                                        <div class="col-3 text-right">- {{ currency_formate($info->discount) }}</div>
                                    </div>
                                </li>
                                @if ($info->order_method == 'delivery' && $refund_order_type !== 'Digital')
                                    <li class="list-group-item">
                                        <div class="row align-items-center">
                                            <div class="col-6">{{ $info->shipping_info->shipping_method->name ?? '' }}</div>
                                            <div class="col-3 text-right">{{ __('Shipping Fee') }}</div>
                                            <div class="col-3 text-right">{{ currency_formate($refund_shipping_price) }}</div>
                                        </div>
                                    </li>
                                @endif
                                <li class="list-group-item">
                                    <div class="row align-items-center">
                                        <div class="col-3">
                                            <span class="order-refund-partial-per-item-badge">{{ __('Partial Per-item Refund') }}</span>
                                        </div>
                                        <div class="col-6 text-right">{{ __('Tax') }}</div>
                                        <div class="col-3 text-right">{{ currency_formate($info->tax) }}</div>
                                    </div>
                                </li>
                                @if($refund_cover_fee > 0)
                                    <li class="list-group-item">
                                        <div class="row align-items-center">
                                            <div class="col-3">
                                                <span class="order-refund-not-refundable-badge">{{ __('Not refundable') }}</span>
                                            </div>
                                            <div class="col-6 text-right">{{ __('Supporter Covered Fees') }}</div>
                                            <div class="col-3 text-right">{{ currency_formate($refund_cover_fee) }}</div>
                                        </div>
                                    </li>
                                @endif
                                <li class="list-group-item">
                                    <div class="row align-items-center">
                                        <div class="col-3">
                                            <span class="order-refund-not-refundable-badge">{{ __('Not refundable') }}</span>
                                        </div>
                                        <div class="col-6 text-right">{{ __('Order Total') }}</div>
                                        <div class="col-3 text-right">{{ currency_formate($info->total) }}</div>
                                    </div>
                                </li>
                                @if($info->getway->name !== 'cash')
                                    <li class="list-group-item">
                                        <div class="row align-items-center text-grey">
                                            <div class="col-3">
                                                <span class="order-refund-not-refundable-badge">{{ __('Not refundable') }}</span>
                                            </div>
                                            <div class="col-6 text-right">{{ __('Credit Card Processing ') }}</div>
                                            @if($refund_cover_fee > 0)
                                                <div class="col-3 text-right">{{ __('Supporter Covered Fees') }}</div>
                                            @else
                                                <div class="col-3 text-right">{{ currency_formate($refund_credit_card_fee) }}</div>
                                            @endif
                                        </div>
                                    </li>
                                    <li class="list-group-item">
                                        <div class="row align-items-center text-grey">
                                            <div class="col-3">
                                                <span class="order-refund-not-refundable-badge">{{ __('Not refundable') }}</span>
                                            </div>
                                            <div class="col-6 text-right">{{ __('Booostr Platform Fee') }} {{ ($refund_pro_club) ? '(1.75%)' : '(3.5%)' }}</div>
                                            @if($refund_cover_fee > 0)
                                                <div class="col-3 text-right">{{ __('Supporter Covered Fees') }}</div>
                                            @else
                                                <div class="col-3 text-right">{{ currency_formate($refund_booster_platform_fee) }}</div>
                                            @endif
                                        </div>
                                    </li>
                                    <li class="list-group-item">
                                        <div class="row align-items-center text-grey">
                                            <div class="col-9 text-right">
                                                {{ __('Net Order Total (Order Total - Credit Card Processing and Booostr Platform Fee)') }}
                                            </div>
                                            <div class="col-3 text-right">{{ currency_formate($refund_net_total) }}</div>
                                        </div>
                                    </li>
                                @endif
                            </ul>

                            <div class="order-refund-partial-summary-bar">
                                <div class="order-refund-partial-summary-block">
                                    <span class="summary-label">{{ __('Partial Order Refund Amount:') }}</span>
                                    <span class="summary-value" id="partial_dollar_refund_items_label">{{ __('0 items selected') }}</span>
                                </div>
                                <div class="order-refund-partial-summary-block">
                                    <span class="summary-label">{{ __('item refund total') }}</span>
                                    <span class="summary-value-sm" id="partial_dollar_refund_item_total">{{ currency_formate(0) }}</span>
                                </div>
                                <span class="order-refund-partial-summary-plus">+</span>
                                <div class="order-refund-partial-summary-block">
                                    <span class="summary-label">{{ __('item total tax') }}</span>
                                    <span class="summary-value-sm" id="partial_dollar_refund_tax_total">{{ currency_formate(0) }}</span>
                                </div>
                                <span class="order-refund-partial-summary-equals">=</span>
                                <div class="order-refund-partial-summary-block">
                                    <span class="summary-label">{{ __('total refund amount') }}</span>
                                    <span class="summary-value" id="partial_dollar_refund_grand_total">{{ currency_formate(0) }}</span>
                                </div>
                            </div>
                                </div>
                            </div>

                            <div class="order-refund-partial-dollar-error-banner" id="partial_dollar_refund_error_banner">
                                {{ __('Refund amount cannot be greater than the line item total. Please fix.') }}
                            </div>
                        </div>
                    </div>

                    <div class="order-refund-actions order-refund-actions-full">
                        <button type="button" class="btn btn-cancel-refund-process" id="cancel-order-refund-process-btn-partial-dollar">
                            {{ __('Cancel Order Cancellation Process') }}
                        </button>
                        <button type="button"
                            class="btn btn-complete-refund"
                            id="complete-partial-dollar-refund-btn"
                            disabled>
                            {{ __('Complete Refund & Cancellation') }}
                        </button>
                    </div>
                </div>

                <div id="order-refund-simple-actions">
                    <button type="button" class="btn btn-cancel-refund-process" id="cancel-order-refund-process-btn">
                        {{ __('Cancel Order Cancellation Process') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    @if ($info->payment_status == 1 && (optional($info->getway)->name !== 'cash' || !empty($isCashCheckCaptured)))
        <div class="modal fade" id="refundConfirmModal" tabindex="-1" role="dialog" aria-labelledby="refundConfirmModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered order-refund-confirm-dialog" role="document">
                <div class="modal-content order-refund-confirm-content">
                    <div class="modal-body">
                        <h5 class="order-refund-confirm-title" id="refundConfirmModalLabel">
                            {{ __('Are you sure you want to refund & cancel order:') }} {{ $info->invoice_no }}?
                        </h5>
                        <p class="order-refund-confirm-desc mb-0">
                            {{ __('Please confirm that you would like to cancel and refund this order. This action cannot be undone. To go back, click the \'No, close window and go back\' grey button. To proceed with the refund and cancellation click the \'Yes, complete refund & cancellation\' blue button.') }}
                        </p>

                        <div class="order-refund-partial-confirm-details">
                            <div class="order-refund-partial-confirm-detail-row is-total">
                                <strong class="detail-label">{{ __('Total Order Refund Amount:') }}</strong>
                                <strong class="detail-value" id="refund_confirm_amount_value">{{ currency_formate($refund_net_total ?? 0) }}</strong>
                            </div>
                        </div>

                        <div class="order-refund-confirm-actions">
                            <button type="button" class="btn btn-refund-go-back" data-dismiss="modal">
                                {{ __('No, close window and go back') }}
                            </button>
                            <form method="POST" action="{{ route('seller.order.refund', $info->id) }}" class="d-inline mb-0">
                                @csrf
                                <button type="submit" name="refund_payment" class="btn btn-refund-confirm-submit">
                                    {{ __('Yes, complete refund & cancellation') }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="partialRefundConfirmModal" tabindex="-1" role="dialog" aria-labelledby="partialRefundConfirmModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered order-refund-confirm-dialog" role="document">
                <div class="modal-content order-refund-confirm-content">
                    <div class="modal-body">
                        <h5 class="order-refund-confirm-title" id="partialRefundConfirmModalLabel">
                            {{ __('Are you sure you want to refund & cancel items from order:') }} {{ $info->invoice_no }}?
                        </h5>
                        <p class="order-refund-confirm-desc mb-0">
                            {{ __('Please confirm that you would like to cancel and refund items from this order. This action cannot be undone. To go back, click the \'No, close window and go back\' grey button. To proceed with the refund and cancellation click the \'Yes, complete refund & cancellation\' blue button.') }}
                        </p>

                        <div id="partial_refund_confirm_details" class="order-refund-partial-confirm-details"></div>

                        <div class="order-refund-confirm-actions">
                            <button type="button" class="btn btn-refund-go-back" data-dismiss="modal">
                                {{ __('No, close window and go back') }}
                            </button>
                            <form method="POST" action="{{ route('seller.order.refund', $info->id) }}" class="d-inline mb-0" id="partialRefundConfirmForm">
                                @csrf
                                <input type="hidden" name="partial_refund_items" id="partial_refund_items_input" value="">
                                <button type="submit" name="partial_refund_payment" class="btn btn-refund-confirm-submit" id="partial_refund_confirm_submit_btn">
                                    {{ __('Yes, complete refund & cancellation') }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="partialDollarRefundConfirmModal" tabindex="-1" role="dialog" aria-labelledby="partialDollarRefundConfirmModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered order-refund-confirm-dialog" role="document">
                <div class="modal-content order-refund-confirm-content">
                    <div class="modal-body">
                        <h5 class="order-refund-confirm-title" id="partialDollarRefundConfirmModalLabel">
                            {{ __('Are you sure you want to refund the amount from order:') }} {{ $info->invoice_no }}?
                        </h5>
                        <p class="order-refund-confirm-desc mb-0">
                            {{ __('Please confirm that you would like to partially refund the amount below from this order. This action cannot be undone. To go back, click the \'No, closer window and go back\' grey button. To proceed with the refund and cancellation click the \'Yes, complete refund & cancellation\' blue button.') }}
                        </p>

                        <div id="partial_dollar_refund_confirm_details" class="order-refund-partial-confirm-details"></div>

                        <div class="order-refund-confirm-actions">
                            <button type="button" class="btn btn-refund-go-back" data-dismiss="modal">
                                {{ __('No, closer window and go back') }}
                            </button>
                            <form method="POST" action="{{ route('seller.order.refund', $info->id) }}" class="d-inline mb-0" id="partialDollarRefundConfirmForm">
                                @csrf
                                <input type="hidden" name="partial_dollar_refund_items" id="partial_dollar_refund_items_input" value="">
                                <button type="submit" name="partial_dollar_refund_payment" class="btn btn-refund-confirm-submit" id="partial_dollar_refund_confirm_submit_btn">
                                    {{ __('Yes, complete refund & cancellation') }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if ($refundSuccess)
        <div class="modal fade" id="refundSuccessModal" tabindex="-1" role="dialog" aria-labelledby="refundSuccessModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered order-refund-success-dialog" role="document">
                <div class="modal-content order-refund-success-content">
                    <div class="modal-body">
                        <h5 class="order-refund-success-title" id="refundSuccessModalLabel">
                            {{ __('Order:') }} {{ $refundSuccess['invoice_no'] }} {{ __('has been refunded and cancelled.') }}
                        </h5>
                        <p class="order-refund-success-desc mb-0">
                            @if (!empty($refundSuccess['is_cash_check']))
                                {{ __('We have successfully refunded and cancelled this cash/check order. Please return the cash/check amount to the purchaser outside of Stripe. The details of the refund are below and the order has been updated to cancelled in your Store Manager.') }}
                            @else
                                {{ __('We have successfully refunded and cancelled this order. the refunded amount will be applied back to the purchasers payment method within 5-10 business days through Stripe. The details of the refund are below and the order has been updated to cancelled in your Store Manager.') }}
                            @endif
                        </p>

                        <div class="order-refund-partial-confirm-details">
                            <div class="order-refund-partial-confirm-detail-row is-total">
                                <strong class="detail-label">{{ __('Total Order Refund Amount:') }}</strong>
                                <strong class="detail-value">{{ currency_formate($refundSuccess['amount'] ?? 0) }}</strong>
                            </div>
                            <div class="order-refund-success-detail-row">
                                <strong>{{ __('Refund Receipt Email To:') }}</strong>
                                <strong class="order-refund-success-detail-value">{{ $refundSuccess['email'] ?? '' }}</strong>
                            </div>
                            <div class="order-refund-success-detail-row">
                                <strong>{{ __('Refund Reference ID:') }}</strong>
                                <strong class="order-refund-success-detail-value">{{ $refundSuccess['reference_id'] ?? '' }}</strong>
                            </div>
                        </div>

                        <div class="order-refund-success-actions">
                            <button type="button" class="btn btn-refund-success-close" data-dismiss="modal">
                                {{ __('Yes, complete refund & cancellation') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if ($partialRefundSuccess)
        <div class="modal fade" id="partialRefundSuccessModal" tabindex="-1" role="dialog" aria-labelledby="partialRefundSuccessModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered order-refund-success-dialog" role="document">
                <div class="modal-content order-refund-success-content">
                    <div class="modal-body">
                        <h5 class="order-refund-success-title" id="partialRefundSuccessModalLabel">
                            {{ __('The below items from order:') }} {{ $partialRefundSuccess['invoice_no'] }} {{ __('have been refunded and cancelled.') }}
                        </h5>
                        <p class="order-refund-success-desc mb-0">
                            @if (!empty($partialRefundSuccess['is_cash_check']))
                                {{ __('We have successfully refunded and cancelled the items below from this cash/check order. Please return the cash/check amount to the purchaser outside of Stripe. The details of the refund are below and the order has been updated in your Store Manager.') }}
                            @else
                                {{ __('We have successfully refunded and cancelled the items below from this order. the refunded amount will be applied back to the purchasers payment method within 5-10 business days through Stripe. The details of the refund are below and the order has been updated to cancelled in your Store Manager.') }}
                            @endif
                        </p>

                        <div class="order-refund-partial-confirm-details">
                            @foreach ($partialRefundSuccess['items'] ?? [] as $partialSuccessItem)
                                <div class="order-refund-partial-confirm-detail-row">
                                    <strong class="detail-label">{{ __('Refund & Cancel:') }} {{ $partialSuccessItem['label'] ?? '' }}:</strong>
                                    <strong class="detail-value">{{ currency_formate($partialSuccessItem['amount'] ?? 0) }}</strong>
                                </div>
                            @endforeach
                            <div class="order-refund-partial-confirm-detail-row">
                                <strong class="detail-label">{{ __('Refund: Partial Tax On Items:') }}</strong>
                                <strong class="detail-value">{{ currency_formate($partialRefundSuccess['tax_total'] ?? 0) }}</strong>
                            </div>
                            <div class="order-refund-partial-confirm-detail-row is-total">
                                <strong class="detail-label">{{ __('Total Order Refund Amount:') }}</strong>
                                <strong class="detail-value">{{ currency_formate($partialRefundSuccess['amount'] ?? 0) }}</strong>
                            </div>
                            <div class="order-refund-success-detail-row">
                                <strong>{{ __('Refund Receipt Email To:') }}</strong>
                                <strong class="order-refund-success-detail-value">{{ $partialRefundSuccess['email'] ?? '' }}</strong>
                            </div>
                            <div class="order-refund-success-detail-row">
                                <strong>{{ __('Refund Reference ID:') }}</strong>
                                <strong class="order-refund-success-detail-value">{{ $partialRefundSuccess['reference_id'] ?? '' }}</strong>
                            </div>
                        </div>

                        <div class="order-refund-success-actions">
                            <button type="button" class="btn btn-refund-success-close" data-dismiss="modal">
                                {{ __('Yes, complete refund & cancellation') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if ($partialDollarRefundSuccess)
        <div class="modal fade" id="partialDollarRefundSuccessModal" tabindex="-1" role="dialog" aria-labelledby="partialDollarRefundSuccessModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered order-refund-success-dialog" role="document">
                <div class="modal-content order-refund-success-content">
                    <div class="modal-body">
                        <h5 class="order-refund-success-title" id="partialDollarRefundSuccessModalLabel">
                            {{ __('The amount below has been refunded from order:') }} {{ $partialDollarRefundSuccess['invoice_no'] }}.
                        </h5>
                        <p class="order-refund-success-desc mb-0">
                            @if (!empty($partialDollarRefundSuccess['is_cash_check']))
                                {{ __('We have successfully refunded the amount below from this cash/check order. Please return the cash/check amount to the purchaser outside of Stripe. The details of the refund are below and the order has been updated to partially refunded in your Store Manager.') }}
                            @else
                                {{ __('We have successfully refunded the amount below from this order. the refunded amount will be applied back to the purchasers payment method within 5-10 business days through Stripe. The details of the refund are below and the order has been updated to partially refunded in your Store Manager.') }}
                            @endif
                        </p>

                        <div class="order-refund-partial-confirm-details">
                            @foreach ($partialDollarRefundSuccess['items'] ?? [] as $partialDollarSuccessItem)
                                <div class="order-refund-partial-confirm-detail-row">
                                    <strong class="detail-label">{{ __('Refund & Cancel:') }} {{ max(1, (int) ($partialDollarSuccessItem['qty'] ?? 1)) }} x {{ $partialDollarSuccessItem['label'] ?? '' }}:</strong>
                                    <strong class="detail-value">{{ currency_formate($partialDollarSuccessItem['amount'] ?? 0) }}</strong>
                                </div>
                            @endforeach
                            <div class="order-refund-partial-confirm-detail-row">
                                <strong class="detail-label">{{ __('Refund: Partial Tax On Items:') }}</strong>
                                <strong class="detail-value">{{ currency_formate($partialDollarRefundSuccess['tax_total'] ?? 0) }}</strong>
                            </div>
                            <div class="order-refund-partial-confirm-detail-row is-total">
                                <strong class="detail-label">{{ __('Total Order Refund Amount:') }}</strong>
                                <strong class="detail-value">{{ currency_formate($partialDollarRefundSuccess['amount'] ?? 0) }}</strong>
                            </div>
                            <div class="order-refund-success-detail-row">
                                <strong>{{ __('Refund Receipt Email To:') }}</strong>
                                <strong class="order-refund-success-detail-value">{{ $partialDollarRefundSuccess['email'] ?? '' }}</strong>
                            </div>
                            <div class="order-refund-success-detail-row">
                                <strong>{{ __('Refund Reference ID:') }}</strong>
                                <strong class="order-refund-success-detail-value">{{ $partialDollarRefundSuccess['reference_id'] ?? '' }}</strong>
                            </div>
                        </div>

                        <div class="order-refund-success-actions">
                            <a href="{{ route('seller.order.index') }}" class="btn btn-refund-success-close">
                                {{ __('Close and Take Me To Orders') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection

@push('script')
    <script>
        $(function () {
            var $orderDetailsView = $('#order-details-view');
            var $orderRefundView = $('#order-refund-cancellation-view');
            var $orderHeaderTitle = $('.section-header h1');
            var defaultHeaderTitle = $.trim($orderHeaderTitle.text() || '');
            var refundHeaderTitle = @json($info->invoice_no . ' Order Details');
            var refundTitleBase = @json(__('Order Refund & Cancellation'));
            var orderInvoiceNo = @json($info->invoice_no);
            var refundCurrencyIcon = @json(get_option('currency_data', true)->currency_icon ?? '$');

            function formatRefundMoney(amount) {
                return refundCurrencyIcon + parseFloat(amount || 0).toFixed(2);
            }

            function syncPartialRefundRowHeights() {
                var $qtyHeader = $('.order-refund-partial-qty-header');
                var $productHeader = $('#order-refund-partial-details .order-refund-partial-products-panel .order-refund-breakdown > .list-group-item').first();

                if ($qtyHeader.length && $productHeader.length) {
                    var headerHeight = $productHeader.outerHeight();
                    $qtyHeader.css({
                        'min-height': headerHeight + 'px',
                        'height': headerHeight + 'px'
                    });
                }

                $('#order-refund-partial-details .order-refund-partial-qty-row').each(function (index) {
                    var $qtyRow = $(this);
                    var $productRow = $('#order-refund-partial-details .order-refund-partial-product-row').eq(index);

                    if ($productRow.length) {
                        var rowHeight = $productRow.outerHeight();
                        $qtyRow.css({
                            'min-height': rowHeight + 'px',
                            'height': rowHeight + 'px'
                        });
                    }
                });
            }

            function syncPartialDollarRefundRowHeights() {
                var $dollarHeader = $('.order-refund-partial-dollar-header');
                var $productHeader = $('#order-refund-partial-dollar-details .order-refund-partial-products-panel .order-refund-breakdown > .list-group-item').first();

                if ($dollarHeader.length && $productHeader.length) {
                    var headerHeight = $productHeader.outerHeight();
                    $dollarHeader.css({
                        'min-height': headerHeight + 'px',
                        'height': headerHeight + 'px'
                    });
                }

                $('#order-refund-partial-dollar-details .order-refund-partial-dollar-row').each(function (index) {
                    var $dollarRow = $(this);
                    var $productRow = $('#order-refund-partial-dollar-details .order-refund-partial-dollar-product-row').eq(index);

                    if ($productRow.length) {
                        var rowHeight = $productRow.outerHeight();
                        $dollarRow.css({
                            'min-height': rowHeight + 'px',
                            'height': rowHeight + 'px'
                        });
                    }
                });
            }

            function parseDollarRefundAmount(value) {
                var parsed = parseFloat(String(value || '').replace(/[^0-9.]/g, ''));

                return isNaN(parsed) ? 0 : parsed;
            }

            // Allow only digits and one decimal point (max 2 decimal places) in dollar refund fields.
            function sanitizePartialDollarRefundAmountInput(value) {
                var cleaned = String(value || '').replace(/[^0-9.]/g, '');
                var parts = cleaned.split('.');

                if (parts.length > 1) {
                    cleaned = parts.shift() + '.' + parts.join('').replace(/\./g, '');
                    var decimalParts = cleaned.split('.');
                    if (decimalParts[1] && decimalParts[1].length > 2) {
                        cleaned = decimalParts[0] + '.' + decimalParts[1].substring(0, 2);
                    }
                }

                return cleaned;
            }

            // On blur: format valid amounts like 40 → 40.00
            function formatPartialDollarRefundAmountOnBlur(value) {
                var sanitized = sanitizePartialDollarRefundAmountInput(value);

                if (sanitized === '' || sanitized === '.') {
                    return '';
                }

                var amount = parseFloat(sanitized);
                if (isNaN(amount)) {
                    return '';
                }

                return amount.toFixed(2);
            }

            function getPartialRefundSelectionData() {
                var items = [];
                var itemRefundTotal = 0;
                var itemTaxTotal = 0;

                $('.order-refund-partial-qty-row').each(function () {
                    var $qtyRow = $(this);
                    var itemId = $qtyRow.data('item-id');
                    var selectedQty = parseInt($qtyRow.find('.partial-refund-qty-select').val(), 10) || 0;
                    var maxQty = parseInt($qtyRow.data('max-qty'), 10) || 1;
                    var unitAmount = parseFloat($qtyRow.data('unit-amount')) || 0;
                    var lineTaxTotal = parseFloat($qtyRow.data('item-tax-total')) || 0;
                    var productTitle = $qtyRow.data('product-title') || '';

                    if (selectedQty > 0) {
                        var lineRefund = unitAmount * selectedQty;
                        var lineTax = (lineTaxTotal / maxQty) * selectedQty;
                        itemRefundTotal += lineRefund;
                        itemTaxTotal += lineTax;
                        items.push({
                            item_id: itemId,
                            qty: selectedQty,
                            label: selectedQty + ' x ' + productTitle,
                            amount: lineRefund,
                            tax: lineTax
                        });
                    }
                });

                return {
                    items: items,
                    itemRefundTotal: itemRefundTotal,
                    itemTaxTotal: itemTaxTotal,
                    grandTotal: itemRefundTotal + itemTaxTotal
                };
            }

            function escapeRefundHtml(text) {
                return $('<div>').text(text || '').html();
            }

            function populatePartialRefundConfirmModal() {
                var data = getPartialRefundSelectionData();
                var refundCancelLabel = @json(__('Refund & Cancel:'));
                var partialTaxLabel = @json(__('Refund: Partial Tax On Items:'));
                var totalLabel = @json(__('Total Order Refund Amount:'));
                var html = '';

                data.items.forEach(function (item) {
                    html += '<div class="order-refund-partial-confirm-detail-row">'
                        + '<strong class="detail-label">' + refundCancelLabel + ' ' + escapeRefundHtml(item.label) + ':</strong>'
                        + '<strong class="detail-value">' + formatRefundMoney(item.amount) + '</strong>'
                        + '</div>';
                });

                html += '<div class="order-refund-partial-confirm-detail-row">'
                    + '<strong class="detail-label">' + partialTaxLabel + '</strong>'
                    + '<strong class="detail-value">' + formatRefundMoney(data.itemTaxTotal) + '</strong>'
                    + '</div>';

                html += '<div class="order-refund-partial-confirm-detail-row is-total">'
                    + '<strong class="detail-label">' + totalLabel + '</strong>'
                    + '<strong class="detail-value">' + formatRefundMoney(data.grandTotal) + '</strong>'
                    + '</div>';

                $('#partial_refund_confirm_details').html(html);
                $('#partial_refund_items_input').val(JSON.stringify(data.items));
            }

            function updatePartialRefundSummary() {
                var data = getPartialRefundSelectionData();
                var selectedLabels = data.items.map(function (item) {
                    return item.label;
                });

                $('.order-refund-partial-qty-row').each(function () {
                    var $qtyRow = $(this);
                    var itemId = $qtyRow.data('item-id');
                    var $productRow = $('.order-refund-partial-product-row[data-item-id="' + itemId + '"]');
                    var selectedQty = parseInt($qtyRow.find('.partial-refund-qty-select').val(), 10) || 0;

                    if (selectedQty > 0) {
                        $qtyRow.addClass('is-selected');
                        $productRow.addClass('is-selected');
                    } else {
                        $qtyRow.removeClass('is-selected');
                        $productRow.removeClass('is-selected');
                    }
                });

                if (selectedLabels.length > 0) {
                    $('#partial_refund_items_label').text(selectedLabels.join(', '));
                    $('#complete-partial-refund-btn').prop('disabled', false);
                } else {
                    $('#partial_refund_items_label').text(@json(__('0 items selected')));
                    $('#complete-partial-refund-btn').prop('disabled', true);
                }

                $('#partial_refund_item_total').text(formatRefundMoney(data.itemRefundTotal));
                $('#partial_refund_tax_total').text(formatRefundMoney(data.itemTaxTotal));
                $('#partial_refund_grand_total').text(formatRefundMoney(data.grandTotal));

                syncPartialRefundRowHeights();
            }

            function resetPartialRefundSelections() {
                $('.partial-refund-qty-select').val('0');
                $('.order-refund-partial-qty-row, .order-refund-partial-product-row').removeClass('is-selected');
                updatePartialRefundSummary();
            }

            function getPartialDollarRefundSelectionData() {
                var items = [];
                var itemRefundTotal = 0;
                var itemTaxTotal = 0;
                var hasValidationError = false;

                $('#order-refund-partial-dollar-details .order-refund-partial-dollar-row').each(function () {
                    var $dollarRow = $(this);
                    var itemId = $dollarRow.data('item-id');
                    var lineTotal = parseFloat($dollarRow.data('line-total')) || 0;
                    var lineTaxTotal = parseFloat($dollarRow.data('item-tax-total')) || 0;
                    var lineTotalOriginal = parseFloat($dollarRow.data('line-total-original')) || 0;
                    var productTitle = $dollarRow.data('product-title') || '';
                    var itemQty = parseInt($dollarRow.data('item-qty'), 10) || 1;
                    var refundAmount = parseDollarRefundAmount($dollarRow.find('.partial-dollar-refund-amount-input').val());
                    var $input = $dollarRow.find('.partial-dollar-refund-amount-input');

                    $dollarRow.removeClass('has-error');
                    $input.removeClass('is-invalid');

                    if (refundAmount > 0 && refundAmount > (lineTotal + 0.001)) {
                        hasValidationError = true;
                        $dollarRow.addClass('has-error');
                        $input.addClass('is-invalid');
                    }

                    if (refundAmount > 0 && refundAmount <= (lineTotal + 0.001)) {
                        var lineTax = lineTotalOriginal > 0 ? ((refundAmount / lineTotalOriginal) * lineTaxTotal) : 0;
                        itemRefundTotal += refundAmount;
                        itemTaxTotal += lineTax;
                        items.push({
                            item_id: itemId,
                            amount: refundAmount,
                            label: productTitle + ' (' + formatRefundMoney(refundAmount) + ')',
                            confirmLabel: itemQty + ' x ' + productTitle,
                            tax: lineTax
                        });
                    }
                });

                return {
                    items: items,
                    itemRefundTotal: itemRefundTotal,
                    itemTaxTotal: itemTaxTotal,
                    grandTotal: itemRefundTotal + itemTaxTotal,
                    hasValidationError: hasValidationError
                };
            }

            function populatePartialDollarRefundConfirmModal() {
                var data = getPartialDollarRefundSelectionData();
                var refundAmountLabel = @json(__('Refund Amount for'));
                var partialTaxLabel = @json(__('Refund: Partial Tax On Items:'));
                var totalLabel = @json(__('Total Order Refund Amount:'));
                var html = '';

                data.items.forEach(function (item) {
                    html += '<div class="order-refund-partial-confirm-detail-row">'
                        + '<strong class="detail-label">' + refundAmountLabel + ' ' + escapeRefundHtml(item.confirmLabel) + ':</strong>'
                        + '<strong class="detail-value">' + formatRefundMoney(item.amount) + '</strong>'
                        + '</div>';
                });

                html += '<div class="order-refund-partial-confirm-detail-row">'
                    + '<strong class="detail-label">' + partialTaxLabel + '</strong>'
                    + '<strong class="detail-value">' + formatRefundMoney(data.itemTaxTotal) + '</strong>'
                    + '</div>';

                html += '<div class="order-refund-partial-confirm-detail-row is-total">'
                    + '<strong class="detail-label">' + totalLabel + '</strong>'
                    + '<strong class="detail-value">' + formatRefundMoney(data.grandTotal) + '</strong>'
                    + '</div>';

                $('#partial_dollar_refund_confirm_details').html(html);
                $('#partial_dollar_refund_items_input').val(JSON.stringify(data.items.map(function (item) {
                    return {
                        item_id: item.item_id,
                        amount: item.amount
                    };
                })));
            }

            function updatePartialDollarRefundSummary() {
                var data = getPartialDollarRefundSelectionData();
                var selectedLabels = data.items.map(function (item) {
                    return item.label;
                });

                $('#order-refund-partial-dollar-details .order-refund-partial-dollar-row').each(function () {
                    var $dollarRow = $(this);
                    var itemId = $dollarRow.data('item-id');
                    var $productRow = $('#order-refund-partial-dollar-details .order-refund-partial-dollar-product-row[data-item-id="' + itemId + '"]');
                    var refundAmount = parseDollarRefundAmount($dollarRow.find('.partial-dollar-refund-amount-input').val());

                    if (refundAmount > 0) {
                        $dollarRow.addClass('is-selected');
                        $productRow.addClass('is-selected');
                    } else {
                        $dollarRow.removeClass('is-selected');
                        $productRow.removeClass('is-selected');
                    }
                });

                if (data.hasValidationError) {
                    $('#partial_dollar_refund_error_banner').addClass('is-visible');
                    $('#complete-partial-dollar-refund-btn').prop('disabled', true);
                } else {
                    $('#partial_dollar_refund_error_banner').removeClass('is-visible');

                    if (selectedLabels.length > 0) {
                        $('#partial_dollar_refund_items_label').text(selectedLabels.join(', '));
                        $('#complete-partial-dollar-refund-btn').prop('disabled', false);
                    } else {
                        $('#partial_dollar_refund_items_label').text(@json(__('0 items selected')));
                        $('#complete-partial-dollar-refund-btn').prop('disabled', true);
                    }
                }

                $('#partial_dollar_refund_item_total').text(formatRefundMoney(data.itemRefundTotal));
                $('#partial_dollar_refund_tax_total').text(formatRefundMoney(data.itemTaxTotal));
                $('#partial_dollar_refund_grand_total').text(formatRefundMoney(data.grandTotal));

                syncPartialDollarRefundRowHeights();
            }

            function resetPartialDollarRefundSelections() {
                $('.partial-dollar-refund-amount-input').val('');
                $('#order-refund-partial-dollar-details .order-refund-partial-dollar-row, #order-refund-partial-dollar-details .order-refund-partial-dollar-product-row')
                    .removeClass('is-selected has-error');
                $('.partial-dollar-refund-amount-input').removeClass('is-invalid');
                $('#partial_dollar_refund_error_banner').removeClass('is-visible');
                updatePartialDollarRefundSummary();
            }

            function updateRefundSectionTitle() {
                var selected = $('#order_refund_type_select').val();
                $('#order-refund-section-title').text(
                    selected ? (refundTitleBase + ': ' + orderInvoiceNo) : refundTitleBase
                );
            }

            function updateRefundViewState() {
                var selected = $('#order_refund_type_select').val();
                updateRefundSectionTitle();

                if (selected === 'full') {
                    $('#order-refund-full-details').show();
                    $('#order-refund-partial-details').hide();
                    $('#order-refund-partial-dollar-details').hide();
                    $('#order-refund-simple-actions').hide();
                } else if (selected === 'partial') {
                    $('#order-refund-full-details').hide();
                    $('#order-refund-partial-details').show();
                    $('#order-refund-partial-dollar-details').hide();
                    $('#order-refund-simple-actions').hide();
                    updatePartialRefundSummary();
                    setTimeout(syncPartialRefundRowHeights, 50);
                } else if (selected === 'partial_dollar') {
                    $('#order-refund-full-details').hide();
                    $('#order-refund-partial-details').hide();
                    $('#order-refund-partial-dollar-details').show();
                    $('#order-refund-simple-actions').hide();
                    updatePartialDollarRefundSummary();
                    setTimeout(syncPartialDollarRefundRowHeights, 50);
                } else {
                    $('#order-refund-full-details').hide();
                    $('#order-refund-partial-details').hide();
                    $('#order-refund-partial-dollar-details').hide();
                    $('#order-refund-simple-actions').show();
                    resetPartialRefundSelections();
                    resetPartialDollarRefundSelections();
                }
            }

            function resetRefundCancellationView() {
                $orderRefundView.hide();
                $orderDetailsView.show();
                $orderHeaderTitle.text(defaultHeaderTitle);
                $('#order_refund_type_select').val('');
                resetPartialRefundSelections();
                resetPartialDollarRefundSelections();
                updateRefundViewState();
            }

            $('#show-order-refund-view-btn, #show-cash-check-refund-view-btn').on('click', function () {
                $orderDetailsView.hide();
                $orderRefundView.show();
                $orderHeaderTitle.text(refundHeaderTitle);
                $('#order_refund_type_select').val('');
                updateRefundViewState();
            });

            $('#order_refund_type_select').on('change', updateRefundViewState);

            $(document).on('change', '.partial-refund-qty-select', updatePartialRefundSummary);

            $(document).on('keypress', '.partial-dollar-refund-amount-input', function (e) {
                // Block letters/symbols; allow digits, one decimal, and control keys.
                if (e.ctrlKey || e.metaKey || e.which === 0 || e.which === 8) {
                    return;
                }

                var char = String.fromCharCode(e.which);
                if (!/[0-9.]/.test(char)) {
                    e.preventDefault();
                    return;
                }

                if (char === '.' && String($(this).val() || '').indexOf('.') !== -1) {
                    e.preventDefault();
                }
            });

            $(document).on('input', '.partial-dollar-refund-amount-input', function () {
                var $input = $(this);
                var sanitized = sanitizePartialDollarRefundAmountInput($input.val());

                if ($input.val() !== sanitized) {
                    $input.val(sanitized);
                }

                updatePartialDollarRefundSummary();
            });

            $(document).on('blur', '.partial-dollar-refund-amount-input', function () {
                var $input = $(this);
                var formatted = formatPartialDollarRefundAmountOnBlur($input.val());

                if ($input.val() !== formatted) {
                    $input.val(formatted);
                }

                updatePartialDollarRefundSummary();
            });

            $('#complete-partial-refund-btn').on('click', function () {
                var data = getPartialRefundSelectionData();

                if (!data.items.length) {
                    return;
                }

                populatePartialRefundConfirmModal();
                $('#partialRefundConfirmModal').modal('show');
            });

            $('#complete-partial-dollar-refund-btn').on('click', function () {
                var data = getPartialDollarRefundSelectionData();

                if (!data.items.length || data.hasValidationError) {
                    return;
                }

                populatePartialDollarRefundConfirmModal();
                $('#partialDollarRefundConfirmModal').modal('show');
            });

            $('#cancel-order-refund-process-btn, #cancel-order-refund-process-btn-full, #cancel-order-refund-process-btn-partial, #cancel-order-refund-process-btn-partial-dollar').on('click', resetRefundCancellationView);

            @if (request()->query('refund') && !$refundSuccess && !$partialRefundSuccess && !$partialDollarRefundSuccess && $info->payment_status == 1 && (optional($info->getway)->name !== 'cash' || !empty($isCashCheckCaptured)))
                $orderDetailsView.hide();
                $orderRefundView.show();
                $orderHeaderTitle.text(refundHeaderTitle);
                $('#order_refund_type_select').val('full');
                updateRefundViewState();
            @endif

            @if ($partialDollarRefundSuccess)
                resetRefundCancellationView();
                $('#partialDollarRefundSuccessModal').modal('show');
            @elseif ($partialRefundSuccess)
                resetRefundCancellationView();
                $('#partialRefundSuccessModal').modal('show');
            @elseif ($refundSuccess)
                resetRefundCancellationView();
                $('#refundSuccessModal').modal('show');
            @endif
        });
    </script>

    @if ($info->order_method == 'delivery' && !empty($info->shippingwithinfo))
        @if (!empty($info->shippingwithinfo->lat) && !empty($info->shippingwithinfo->long))
            <script async defer
                src="https://maps.googleapis.com/maps/api/js?key={{ get_option('order_settings', true)->google_api ?? '' }}&libraries=places&sensor=false&callback=initialise">
            </script>
            <script type="text/javascript">
                "use strict";
                var resturent_lat = {{ tenant('lat') ?? 0.0 }};
                var resturent_long = {{ tenant('long') ?? 0.0 }};
                var customer_lat = {{ $info->shippingwithinfo->lat ?? 0.0 }};
                var customer_long = {{ $info->shippingwithinfo->long ?? 0.0 }};
                var resturent_icon = '{{ asset('uploads/resturent.png') }}';
                var user_icon = '{{ asset('uploads/userpin.png') }}';

                var customer_name = '{{ $info->user->name ?? 'customer' }}';
                var resturent_name = '{{ tenant('id') }}';
                var mainUrl = "{{ url('/') }}";


                function initialise() {
                    var map;
                    var resturent = new google.maps.LatLng(resturent_lat, resturent_long);
                    var customer = new google.maps.LatLng(customer_lat, customer_long);
                    var option = {
                        zoom: 10,
                        center: resturent,
                    };
                    map = new google.maps.Map(document.getElementById('map'), option);
                    var display = new google.maps.DirectionsRenderer({
                        polylineOptions: {
                            strokeColor: "rgba(255, 0, 0, 0.5)"
                        }
                    });
                    var services = new google.maps.DirectionsService();
                    display.setMap(map);
                    calculateroute();

                    function calculateroute() {
                        var request = {
                            origin: resturent,
                            destination: customer,
                            travelMode: 'DRIVING'
                        };
                        services.route(request, function(result, status) {

                            if (status == 'OK') {
                                display.setDirections(result);
                                display.setOptions({
                                    suppressMarkers: true
                                });
                                var leg = result.routes[0].legs[0];
                                makeMarker(leg.start_location, resturent_icon, resturent_name);
                                makeMarker(leg.end_location, user_icon, customer_name);
                            }
                        });
                    }

                    function makeMarker(position, icon, title) {
                        new google.maps.Marker({
                            position: position,
                            map: map,
                            icon: icon,
                            title: title
                        });
                    }
                }
            </script>
        @endif
    @endif

	<script type="text/javascript">
		$(document).ready(function() {
			
	
			$("#mainSelect").on("change", function() {
				$("#tacking_number").val('');
			    $("#shipping_service").val('');
				$('#chooseTracking').val('');

				var selectedValue = $(this).val();
				if (selectedValue === "1") {
				 $("#hiddenChooseTracking").show();
				 $("#hide_tacking_number").hide();
				 $("#chooseTracking").attr("required", true);
				} else {
				 $("#tacking_number").val('');
			     $("#shipping_service").val('');

				 $("#hiddenChooseTracking").hide();
				 $("#hide_tacking_number").hide();
				 $("#hide_shipping_service").hide();
	
				 $("#chooseTracking").attr("required", false);
				 $("#tacking_number").attr("required", false);
				 $("#shipping_service").attr("required", false);
				}
			});
	
			$("#chooseTracking").on("change", function() {
				$("#tacking_number").val('');
			    $("#shipping_service").val('');

				var selectedValue = $(this).val();
				if (selectedValue === "FedEx" || selectedValue === "UPS" || selectedValue === "US Postal Service") {
				   $("#hide_tacking_number").show();
				   $("#hide_shipping_service").hide();
	
				   $("#tacking_number").attr("required", true);
				   $("#shipping_service").attr("required", false);
	
				} else if(selectedValue === "Other") {
				   $("#hide_tacking_number").show();
				   $("#hide_shipping_service").show();
	
				   $("#tacking_number").attr("required", true);
				   $("#shipping_service").attr("required", true);
	
				} else {
					$("#hide_tacking_number").hide();
					$("#hide_shipping_service").hide();
	
					$("#tacking_number").attr("required", false);
					$("#shipping_service").attr("required", false);
				}
			});
	});
	</script>

@endpush
