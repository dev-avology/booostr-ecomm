@extends('theme.resto.customer.app')

@section('customer_content')
{{-- 
    =================================================================================
    Site Builder Standard: Modern Customer Dashboard
    Features:
      - Industry-standard, clean card design (No redundant sidebar links)
      - Welcome Header with quick account status
      - 2 Balanced Metric Stat Cards (Total Purchases, Pending Orders)
      - Full-Width Recent Purchases Table with accurate status & payment badges
      - Full-Width Account Overview with direct profile and security actions
      - Fully responsive across desktop, tablet, and mobile
    =================================================================================
--}}
@php
    $user = Auth::user();
    $userMeta = !empty($user->meta) ? json_decode($user->meta, true) : [];
    $displayName = !empty($userMeta['username']) ? $userMeta['username'] : $user->name;
    $phone = !empty($user->phone) ? $user->phone : (!empty($userMeta['phone']) ? $userMeta['phone'] : '');

    $pendingLink = url('/customer/orders');
    if (!empty($pendingStatusIds) && count($pendingStatusIds) === 1) {
        $pendingLink = url('/customer/orders') . '?status=' . $pendingStatusIds[0];
    }

    $currency = get_option('currency_data', true);
    $currencyIcon = $currency->currency_icon ?? '$';
    $currencyPosition = $currency->currency_position ?? 'left';
@endphp

<!-- 1. Welcome Greeting Banner -->
<div class="dash-welcome-banner mb-4 p-4 bg-white rounded border d-flex align-items-center justify-content-between flex-wrap gap-3 shadow-sm">
    <div class="d-flex align-items-center gap-3">
        <div class="dash-user-avatar">
            {{ strtoupper(substr($displayName, 0, 1)) }}
        </div>
        <div>
            <h3 class="fs-5 fw-bold mb-1 text-dark">{{ __('Welcome back') }}, {{ $displayName }}!</h3>
            <p class="text-muted small mb-0">{{ __('Here is a summary of your recent purchases and account status.') }}</p>
        </div>
    </div>
    <div class="d-flex align-items-center gap-2">
        <span class="badge dash-status-pill">
            <i class="icofont-check-circled me-1 text-success"></i> {{ __('Active Customer') }}
        </span>
    </div>
</div>

<!-- 2. Metric Stat Cards Row (2 Balanced Cards) -->
<div class="dash-stats-row mb-4">
    <div class="row g-3">
        {{-- Card 1: Total Orders --}}
        <div class="col-md-6 col-12">
            <div class="dash-stat-box bg-white p-4 rounded border shadow-sm h-100 d-flex flex-column justify-content-between">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <span class="dash-stat-label text-muted small fw-semibold text-uppercase">{{ __('Total Purchases') }}</span>
                    <div class="dash-icon-pill dash-icon-blue">
                        <i class="icofont-shopping-cart"></i>
                    </div>
                </div>
                <div>
                    <h3 class="dash-stat-val mb-1 text-dark">{{ $total_orders }}</h3>
                    <p class="text-muted small mb-3">{{ __('All-time completed & placed orders') }}</p>
                </div>
                <div class="pt-2 border-top">
                    <a href="{{ url('/customer/orders') }}" class="dash-card-action">
                        {{ __('View purchase history') }} <i class="icofont-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>

        {{-- Card 2: Pending Orders --}}
        <div class="col-md-6 col-12">
            <div class="dash-stat-box bg-white p-4 rounded border shadow-sm h-100 d-flex flex-column justify-content-between">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <span class="dash-stat-label text-muted small fw-semibold text-uppercase">{{ __('Pending Orders') }}</span>
                    <div class="dash-icon-pill dash-icon-amber">
                        <i class="icofont-sand-clock"></i>
                    </div>
                </div>
                <div>
                    <h3 class="dash-stat-val mb-1 text-dark">{{ $total_pending_orders }}</h3>
                    <p class="text-muted small mb-3">{{ __('Orders currently awaiting fulfillment') }}</p>
                </div>
                <div class="pt-2 border-top">
                    <a href="{{ $pendingLink }}" class="dash-card-action text-amber">
                        {{ __('Review pending status') }} <i class="icofont-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 3. Recent Purchases (Full Width Prominent Section) -->
<div class="dash-section-card bg-white rounded border shadow-sm mb-4">
    <div class="dash-section-header p-4 border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div>
            <h3 class="fs-5 fw-bold mb-1 text-dark">
                <i class="icofont-history text-primary me-2"></i>{{ __('Recent Purchases') }}
            </h3>
            <p class="text-muted small mb-0">{{ __('Your latest orders and live fulfillment status.') }}</p>
        </div>
        <div>
            <a href="{{ url('/customer/orders') }}" class="btn btn-sm btn-outline-primary fw-semibold px-3 py-2">
                {{ __('View Full History') }} <i class="icofont-arrow-right ms-1"></i>
            </a>
        </div>
    </div>

    @if(($orders ?? collect())->count() > 0)
    <div class="table-responsive">
        <table class="table align-middle mb-0 dash-modern-table">
            <thead>
                <tr>
                    <th scope="col" style="min-width: 140px;">{{ __('Order No') }}</th>
                    <th scope="col" style="min-width: 140px;">{{ __('Placed At') }}</th>
                    <th scope="col" class="text-center" style="min-width: 120px;">{{ __('Order Status') }}</th>
                    <th scope="col" class="text-center" style="min-width: 130px;">{{ __('Payment Status') }}</th>
                    <th scope="col" class="text-end" style="min-width: 100px;">{{ __('Action') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($orders as $row)
                @php
                    // Total item count
                    $totalQty = $row->orderitems->sum('qty');
                    if (!$totalQty || $totalQty < 1) {
                        $totalQty = $row->orderitems_count ?? 1;
                    }

                    // Order Status styling
                    $statusName = $row->orderstatus->name ?? __('Pending');
                    $statusLower = strtolower($statusName);
                    $orderStatusClass = 'order-badge-pending';

                    if (strpos($statusLower, 'complete') !== false || strpos($statusLower, 'deliver') !== false) {
                        $orderStatusClass = 'order-badge-completed';
                    } elseif (strpos($statusLower, 'cancel') !== false || strpos($statusLower, 'fail') !== false) {
                        $orderStatusClass = 'order-badge-cancelled';
                    } elseif (strpos($statusLower, 'process') !== false) {
                        $orderStatusClass = 'order-badge-processing';
                    }

                    // Payment Status styling (strict 1-to-1 mapping matching orders & order detail)
                    if ($row->payment_status == 1) {
                        $paymentText = __('Paid');
                        $paymentClass = 'payment-badge-paid';
                        $paymentIcon = 'icofont-check-circled';
                    } elseif ($row->payment_status == 5) {
                        $paymentText = __('Refunded');
                        $paymentClass = 'payment-badge-refunded';
                        $paymentIcon = 'icofont-reply';
                    } elseif ($row->payment_status == 4) {
                        $paymentText = __('Processing');
                        $paymentClass = 'payment-badge-processing';
                        $paymentIcon = 'icofont-refresh';
                    } elseif ($row->payment_status == 2) {
                        $paymentText = __('Pending');
                        $paymentClass = 'payment-badge-pending';
                        $paymentIcon = 'icofont-clock-time';
                    } elseif ($row->payment_status == 3) {
                        $paymentText = __('Incomplete');
                        $paymentClass = 'payment-badge-incomplete';
                        $paymentIcon = 'icofont-exclamation-circle';
                    } elseif ($row->payment_status == 0) {
                        $paymentText = __('Cancelled');
                        $paymentClass = 'payment-badge-cancelled';
                        $paymentIcon = 'icofont-close-circled';
                    } else {
                        $paymentText = __('Pending');
                        $paymentClass = 'payment-badge-pending';
                        $paymentIcon = 'icofont-clock-time';
                    }
                @endphp
                <tr class="dash-table-row">
                    <td>
                        <a href="{{ url('/customer/order/'.$row->id) }}" class="dash-order-no fw-bold text-decoration-none">
                            #{{ $row->invoice_no }}
                        </a>
                        <div class="dash-order-meta mt-1">
                            <span class="fw-bold text-dark">{{ $currencyPosition === 'right' ? number_format($row->total, 2) . ' ' . $currencyIcon : $currencyIcon . number_format($row->total, 2) }}</span>
                            <span class="text-muted small ms-1">· {{ $totalQty }} {{ $totalQty == 1 ? __('item') : __('items') }}</span>
                        </div>
                    </td>
                    <td>
                        <div class="fw-medium text-dark">{{ $row->created_at->format('M d, Y') }}</div>
                        <div class="text-muted small">{{ $row->created_at->diffForHumans() }}</div>
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
                        <a href="{{ url('/customer/order/'.$row->id) }}" class="btn btn-sm btn-outline-primary dash-action-btn">
                            <i class="icofont-eye me-1"></i>{{ __('Details') }}
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div class="dash-empty-state text-center py-5">
        <div class="dash-empty-icon mb-3">
            <i class="icofont-shopping-cart text-muted"></i>
        </div>
        <h5 class="fw-bold text-dark mb-1">{{ __('No Purchases Found') }}</h5>
        <p class="text-muted small mb-4">{{ __('You have not placed any orders yet. Discover items in the catalog today!') }}</p>
        <a href="{{ url('/products') }}" class="btn btn-primary px-4 py-2">
            {{ __('Browse Products') }}
        </a>
    </div>
    @endif
</div>

<!-- 4. Account Overview (Full Width Standard Card) -->
<div class="dash-section-card bg-white p-4 rounded border shadow-sm">
    <div class="d-flex align-items-center justify-content-between pb-3 mb-4 border-bottom flex-wrap gap-2">
        <div>
            <h4 class="fs-6 fw-bold mb-1 text-dark">
                <i class="icofont-id-card text-primary me-2"></i>{{ __('Account Information') }}
            </h4>
            <p class="text-muted small mb-0">{{ __('Your profile details and primary contact credentials.') }}</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ url('/customer/edit-profile') }}" class="btn btn-sm btn-outline-primary fw-semibold px-3 py-1">
                <i class="icofont-edit me-1"></i>{{ __('Edit Profile') }}
            </a>
            @if(Auth::user()->role_id != 3)
            <a href="{{ url('/customer/change-password') }}" class="btn btn-sm btn-outline-secondary fw-semibold px-3 py-1">
                <i class="icofont-key me-1"></i>{{ __('Change Password') }}
            </a>
            @endif
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-3 col-md-6 col-12">
            <div class="dash-info-card">
                <span class="dash-info-label">{{ __('Full Name') }}</span>
                <span class="dash-info-value">{{ $user->name }}</span>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-12">
            <div class="dash-info-card">
                <span class="dash-info-label">{{ __('Username') }}</span>
                <span class="dash-info-value">{{ $displayName }}</span>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-12">
            <div class="dash-info-card">
                <span class="dash-info-label">{{ __('Email Address') }}</span>
                <span class="dash-info-value text-truncate d-block" title="{{ $user->email }}">{{ $user->email }}</span>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-12">
            <div class="dash-info-card">
                <span class="dash-info-label">{{ __('Phone Number') }}</span>
                <span class="dash-info-value">{{ !empty($phone) ? $phone : __('Not provided') }}</span>
            </div>
        </div>
    </div>
</div>

<style>
/* ==========================================================================
   Industry Standard Modern Dashboard CSS
   Scoped specifically for customer dashboard
   ========================================================================== */

/* 1. Welcome Banner */
.dash-user-avatar {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    background: linear-gradient(135deg, #528CD7 0%, #3a6db8 100%);
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    font-weight: 700;
    flex-shrink: 0;
    box-shadow: 0 4px 10px rgba(82, 140, 215, 0.25);
}

.dash-status-pill {
    background-color: #F0FDF4;
    color: #15803D;
    border: 1px solid #BBF7D0;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 12.5px;
    font-weight: 600;
}

/* 2. Stat Metric Boxes */
.dash-stat-box {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.dash-stat-box:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.06) !important;
}

.dash-stat-val {
    font-size: 32px;
    font-weight: 700;
    letter-spacing: -0.5px;
    line-height: 1.1;
}

.dash-icon-pill {
    width: 44px;
    height: 44px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
}

.dash-icon-blue {
    background-color: #EFF6FF;
    color: #528CD7;
}

.dash-icon-amber {
    background-color: #FFFBEB;
    color: #D97706;
}

.dash-icon-green {
    background-color: #ECFDF5;
    color: #059669;
}

.dash-card-action {
    font-size: 13px;
    font-weight: 600;
    color: #528CD7;
    text-decoration: none !important;
    display: inline-flex;
    align-items: center;
    transition: color 0.15s ease;
}

.dash-card-action:hover {
    color: #2b5fa3;
    text-decoration: underline !important;
}

.dash-card-action.text-amber {
    color: #D97706 !important;
}

.dash-card-action.text-amber:hover {
    color: #92400e !important;
}

.dash-card-action.text-green {
    color: #059669 !important;
}

.dash-card-action.text-green:hover {
    color: #047857 !important;
}

/* 3. Modern Table */
.dash-modern-table thead th {
    background-color: #F8FAFC !important;
    color: #64748B !important;
    font-size: 12px !important;
    font-weight: 600 !important;
    text-transform: uppercase !important;
    letter-spacing: 0.5px !important;
    padding: 14px 18px !important;
    border-bottom: 2px solid #E2E8F0 !important;
}

.dash-modern-table tbody td {
    padding: 16px 18px !important;
    vertical-align: middle !important;
    border-bottom: 1px solid #F1F5F9 !important;
    font-size: 14px !important;
}

.dash-table-row:hover {
    background-color: #F8FAFC !important;
}

.dash-order-no {
    color: #528CD7 !important;
    font-size: 15px;
    transition: color 0.15s ease;
}

.dash-order-no:hover {
    color: #3b6fb5 !important;
    text-decoration: underline !important;
}

.dash-action-btn {
    border-color: #528CD7 !important;
    color: #528CD7 !important;
    font-weight: 600 !important;
    padding: 5px 14px !important;
    border-radius: 6px !important;
    transition: all 0.2s ease !important;
}

.dash-action-btn:hover {
    background-color: #528CD7 !important;
    color: #ffffff !important;
}

/* 4. Badges (Aligned with Purchase History) */
.order-status-badge {
    font-size: 12px !important;
    font-weight: 600 !important;
    padding: 5px 12px !important;
    border-radius: 20px !important;
    display: inline-block !important;
}

.order-badge-pending {
    background-color: #FFFBEB !important;
    color: #B45309 !important;
    border: 1px solid #FDE68A !important;
}

.order-badge-processing {
    background-color: #EFF6FF !important;
    color: #1D4ED8 !important;
    border: 1px solid #BFDBFE !important;
}

.order-badge-completed {
    background-color: #ECFDF5 !important;
    color: #047857 !important;
    border: 1px solid #A7F3D0 !important;
}

.order-badge-cancelled {
    background-color: #FEF2F2 !important;
    color: #B91C1C !important;
    border: 1px solid #FECACA !important;
}

.payment-status-badge {
    font-size: 12px !important;
    font-weight: 600 !important;
    padding: 5px 12px !important;
    border-radius: 20px !important;
    display: inline-flex !important;
    align-items: center !important;
}

.payment-badge-paid {
    background-color: #ECFDF5 !important;
    color: #047857 !important;
    border: 1px solid #A7F3D0 !important;
}

.payment-badge-refunded {
    background-color: #FEF2F2 !important;
    color: #DC2626 !important;
    border: 1px solid #FECACA !important;
}

.payment-badge-processing {
    background-color: #EFF6FF !important;
    color: #1D4ED8 !important;
    border: 1px solid #BFDBFE !important;
}

.payment-badge-pending {
    background-color: #FFFBEB !important;
    color: #B45309 !important;
    border: 1px solid #FDE68A !important;
}

.payment-badge-cancelled {
    background-color: #F1F5F9 !important;
    color: #64748B !important;
    border: 1px solid #CBD5E1 !important;
}

.payment-badge-incomplete {
    background-color: #FFF7ED !important;
    color: #C2410C !important;
    border: 1px solid #FFEDD5 !important;
}

/* 5. Account Overview Card */
.dash-info-card {
    background-color: #F8FAFC;
    border: 1px solid #E2E8F0;
    border-radius: 8px;
    padding: 16px 20px;
    height: 100%;
    display: flex;
    flex-direction: column;
    justify-content: center;
    transition: background-color 0.15s ease, border-color 0.15s ease;
}

.dash-info-card:hover {
    background-color: #F1F5F9;
    border-color: #CBD5E1;
}

.dash-info-label {
    color: #64748B;
    font-size: 11.5px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 6px;
    display: block;
}

.dash-info-value {
    color: #0F172A;
    font-size: 15px;
    font-weight: 600;
    word-break: break-word;
}

/* 6. Empty State */
.dash-empty-icon {
    font-size: 48px;
    opacity: 0.4;
}

/* 6. Responsive Adjustments */
@media (max-width: 768px) {
    .dash-welcome-banner {
        flex-direction: column;
        align-items: flex-start !important;
    }

    .dash-modern-table thead {
        display: none;
    }

    .dash-modern-table,
    .dash-modern-table tbody,
    .dash-modern-table tr,
    .dash-modern-table td {
        display: block;
        width: 100%;
    }

    .dash-modern-table tr {
        border-bottom: 1px solid #E2E8F0;
        padding: 12px 0;
    }

    .dash-modern-table td {
        text-align: left !important;
        padding: 6px 16px !important;
        border: none !important;
    }
}
</style>
@endsection
