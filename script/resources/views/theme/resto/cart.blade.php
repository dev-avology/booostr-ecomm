@extends('theme.resto.layouts.app')
@section('content')
@include('theme.resto.components.page-banner', [
    'title' => __('Cart'),
    'description' => __('Review your selected items and proceed to checkout.'),
    'breadcrumbs' => [
        ['url' => url('/'), 'label' => __('Home'), 'icon' => 'icofont-home'],
        ['url' => '', 'label' => __('Cart'), 'icon' => 'icofont-cart'],
    ]
])

@php
    $cartCount = Cart::instance('default')->count();
@endphp

<!-- Shopping Cart Section -->
<div class="shopping-cart section">
    <div class="container">
        <!-- Empty State View -->
        <div class="cart-empty-view text-center py-5" style="{{ $cartCount == 0 ? '' : 'display: none;' }}">
            <div class="empty-cart-card">
                <div class="empty-cart-icon-wrap">
                    <i class="icofont-shopping-cart"></i>
                </div>
                <h3 class="empty-cart-title">{{ __('Your Cart is Empty') }}</h3>
                <p class="empty-cart-desc">{{ __('Looks like you haven\'t added any items to your cart yet. Discover our latest products and start shopping!') }}</p>
                <div class="empty-cart-btn-wrap">
                    <a href="{{ url('/products') }}" class="btn btn-empty-cart">
                        <i class="icofont-shopping-bag mr-1"></i> {{ __('Start Shopping') }}
                    </a>
                </div>
            </div>
        </div>

        <!-- Filled State View -->
        <div class="cart-filled-view" style="{{ $cartCount > 0 ? '' : 'display: none;' }}">
            <div class="row">
                <div class="col-12">
                    <!-- Shopping Summary Table -->
                    <div class="cart-table-wrapper">
                        <table class="table shopping-summery">
                            <thead>
                                <tr class="main-hading">
                                    <th>{{ __('PRODUCT') }}</th>
                                    <th>{{ __('NAME') }}</th>
                                    <th class="text-center">{{ __('UNIT PRICE') }}</th>
                                    <th class="text-center">{{ __('QUANTITY') }}</th>
                                    <th class="text-center">{{ __('TOTAL') }}</th> 
                                    <th class="text-center">{{ __('REMOVE') }}</th>
                                </tr>
                            </thead>
                            <tbody class="cart_page">
                                
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-12">
                    <div class="total-amount">
                        <div class="row align-items-start justify-content-between">
                            <!-- Left: Continue Shopping Button (Desktop left, Mobile under summary) -->
                            <div class="col-lg-6 col-md-5 col-12 order-2 order-md-1 mt-4 mt-md-0">
                                <a href="{{ url('/products') }}" class="btn-continue-shopping">
                                    <i class="icofont-arrow-left"></i> {{ __('Continue Shopping') }}
                                </a>
                            </div>

                            <!-- Right: Order Summary Card -->
                            <div class="col-lg-5 col-md-7 col-12 order-1 order-md-2 ms-auto">
                                <div class="right cart-summary-card">
                                    <h4 class="summary-card-title">{{ __('Order Summary') }}</h4>

                                    <div class="summary-row d-flex justify-content-between align-items-center mb-3">
                                        <span class="summary-label">{{ __('Cart Subtotal') }}</span>
                                        <span class="summary-value cart_subtotal">{{ booostr_price(Cart::instance('default')->subtotal()) }}</span>
                                    </div>

                                    <!-- Shipping & Tax Calculated in Next Steps Notice -->
                                    <div class="shipping-tax-notice">
                                        <i class="icofont-info-circle mr-1"></i>
                                        <span>{{ __('Shipping & Tax calculated in next steps') }}</span>
                                    </div>

                                    <hr class="summary-divider">

                                    <div class="summary-row d-flex justify-content-between align-items-baseline mb-4">
                                        <span class="summary-total-label">{{ __('Total') }}</span>
                                        <span class="summary-total-value cart_total">{{ booostr_price(Cart::instance('default')->total()) }}</span>
                                    </div>

                                    <div class="summary-actions">
                                        <a href="{{ url('/checkout') }}" class="btn btn-cart-checkout">
                                            <i class="icofont-lock mr-2"></i> {{ __('Proceed to Checkout') }}
                                        </a>
                                        <div class="secure-checkout-badge text-center mt-3">
                                            <i class="icofont-shield-alt text-success mr-1"></i> {{ __('Guaranteed Safe & Secure Checkout') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--/ End Shopping Cart -->
@endsection

@push('css')
<style>
/* Shopping Cart Section & Layout */
.shopping-cart.section {
    background: #f8fafc;
    padding: 60px 0 80px 0;
}

/* Empty State Card */
.cart-empty-view {
    padding: 40px 0;
}
.empty-cart-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    padding: 60px 30px;
    max-width: 580px;
    margin: 0 auto;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
}
.empty-cart-icon-wrap {
    width: 88px;
    height: 88px;
    background: #e8f4fc;
    color: #0A8CDE;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 38px;
    margin-bottom: 24px;
}
.empty-cart-title {
    font-size: 24px;
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 12px;
}
.empty-cart-desc {
    color: #64748b;
    font-size: 15px;
    line-height: 1.6;
    margin-bottom: 28px;
    max-width: 440px;
    margin-left: auto;
    margin-right: auto;
}
.btn-empty-cart {
    background: #0A8CDE !important;
    background-color: #0A8CDE !important;
    color: #ffffff !important;
    padding: 13px 32px;
    border-radius: 8px;
    font-size: 15px;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.2s ease-in-out;
    box-shadow: 0 4px 14px rgba(10, 140, 222, 0.3);
}
.btn-empty-cart:hover {
    background: #0870b2 !important;
    background-color: #0870b2 !important;
    transform: translateY(-2px);
    box-shadow: 0 6px 18px rgba(8, 112, 178, 0.4);
}

/* Cart Table Wrapper */
.cart-table-wrapper {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.02);
}
.table.shopping-summery {
    margin-bottom: 0;
    border-collapse: collapse;
}
.table.shopping-summery thead {
    background: #f8fafc !important;
    border-bottom: 1px solid #e2e8f0;
}
.table.shopping-summery thead tr th {
    background: transparent !important;
    color: #475569 !important;
    font-size: 13px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    padding: 16px 20px;
    border: none;
    vertical-align: middle;
}
.table.shopping-summery tbody td {
    padding: 20px;
    vertical-align: middle;
    border-top: 1px solid #f1f5f9;
}
.table.shopping-summery tbody .image img {
    width: 72px;
    height: 72px;
    object-fit: cover;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
    padding: 4px;
    background: #ffffff;
    box-shadow: none;
}
.table.shopping-summery tbody .product-name a {
    color: #1e293b;
    font-weight: 600;
    font-size: 15px;
    transition: color 0.2s ease;
}
.table.shopping-summery tbody .product-name a:hover {
    color: #0A8CDE;
}
.table.shopping-summery tbody .price span {
    color: #1e293b;
    font-weight: 600;
    font-size: 15px;
}
.table.shopping-summery tbody .total-amount span {
    color: #1e293b;
    font-weight: 700;
    font-size: 16px;
}
.table.shopping-summery tbody .action .remove-icon {
    color: #94a3b8;
    font-size: 18px;
    transition: color 0.2s ease;
}
.table.shopping-summery tbody .action a:hover .remove-icon {
    color: #ef4444;
}

/* Continue Shopping Action */
.btn-continue-shopping {
    color: #475569;
    font-weight: 600;
    font-size: 14.5px;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 22px;
    border-radius: 8px;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    transition: all 0.2s ease-in-out;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
    text-decoration: none !important;
}
.btn-continue-shopping:hover {
    color: #0A8CDE;
    border-color: #0A8CDE;
    background: #f8fafc;
    transform: translateX(-3px);
    box-shadow: 0 3px 8px rgba(10, 140, 222, 0.12);
}

/* Order Summary Card */
.cart-summary-card {
    background: #ffffff !important;
    background-color: #ffffff !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 12px !important;
    padding: 24px 28px !important;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.04) !important;
}
.summary-card-title {
    font-size: 18px;
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 20px;
    padding-bottom: 12px;
    border-bottom: 1px solid #f1f5f9;
}
.summary-label {
    color: #64748b;
    font-size: 15px;
    font-weight: 500;
}
.summary-value {
    color: #1e293b;
    font-size: 16px;
    font-weight: 600;
}
.shipping-tax-notice {
    background: #f8fafc;
    border: 1px dashed #cbd5e1;
    border-radius: 8px;
    padding: 10px 14px;
    color: #64748b;
    font-size: 13px;
    display: flex;
    align-items: center;
    gap: 8px;
    margin: 16px 0;
}
.summary-divider {
    border-top: 1px solid #e2e8f0;
    margin: 16px 0;
}
.summary-total-label {
    color: #1e293b;
    font-size: 18px;
    font-weight: 700;
}
.summary-total-value {
    color: #0A8CDE;
    font-size: 24px;
    font-weight: 800;
}
.btn-cart-checkout {
    background: #0A8CDE !important;
    background-color: #0A8CDE !important;
    color: #ffffff !important;
    width: 100%;
    height: 50px;
    border-radius: 8px !important;
    font-size: 16px;
    font-weight: 600;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    transition: all 0.2s ease-in-out;
    box-shadow: 0 4px 14px rgba(10, 140, 222, 0.3) !important;
    border: none !important;
}
.btn-cart-checkout:hover {
    background: #0870b2 !important;
    background-color: #0870b2 !important;
    color: #ffffff !important;
    transform: translateY(-2px);
    box-shadow: 0 6px 18px rgba(8, 112, 178, 0.4) !important;
}
.secure-checkout-badge {
    color: #64748b;
    font-size: 12.5px;
    font-weight: 500;
}

/* Hide floating cart widget on cart page */
.cart-sidebar {
    display: none !important;
}

/* Responsive adjustments */
@media (max-width: 767px) {
    /* Hide the orange table header pseudo elements completely */
    .shopping-cart .table td::before,
    .single-item-inner::before {
        display: none !important;
        content: none !important;
    }
    .shopping-cart .table thead {
        display: none !important;
    }
    .cart-table-wrapper {
        border: none !important;
        background: transparent !important;
        box-shadow: none !important;
        overflow: visible !important;
    }
    .table.shopping-summery {
        display: block !important;
        width: 100% !important;
    }
    .table.shopping-summery tbody {
        display: block !important;
        width: 100% !important;
    }
    .table.shopping-summery tbody tr.cart_item {
        display: flex !important;
        flex-wrap: wrap !important;
        position: relative !important;
        padding: 16px !important;
        margin-bottom: 14px !important;
        background: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 12px !important;
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.03) !important;
        width: 100% !important;
    }
    .table.shopping-summery tbody td {
        border: none !important;
        padding: 0 !important;
        background: transparent !important;
        margin: 0 !important;
        width: auto !important;
        height: auto !important;
    }
    .table.shopping-summery tbody td.image {
        flex: 0 0 68px !important;
        width: 68px !important;
        height: 68px !important;
        margin-right: 12px !important;
    }
    .table.shopping-summery tbody td.image img {
        width: 68px !important;
        height: 68px !important;
        object-fit: contain !important;
        border-radius: 8px !important;
        border: 1px solid #e2e8f0 !important;
        padding: 4px !important;
    }
    .table.shopping-summery tbody td.product-desc {
        flex: 1 1 calc(100% - 110px) !important;
        padding-right: 28px !important;
    }
    .table.shopping-summery tbody td.product-desc .product-name a {
        font-size: 15px !important;
        font-weight: 600 !important;
        color: #1e293b !important;
        line-height: 1.35 !important;
        display: -webkit-box !important;
        -webkit-line-clamp: 2 !important;
        -webkit-box-orient: vertical !important;
        overflow: hidden !important;
    }
    .table.shopping-summery tbody td.product-desc .product-des {
        font-size: 12px !important;
        color: #64748b !important;
        margin-top: 2px !important;
    }
    .table.shopping-summery tbody td.action {
        position: absolute !important;
        right: 12px !important;
        top: 14px !important;
        text-align: right !important;
    }
    .table.shopping-summery tbody td.action .remove-icon {
        font-size: 18px !important;
        color: #94a3b8 !important;
        padding: 4px !important;
    }
    .table.shopping-summery tbody td.price {
        flex: 0 0 100% !important;
        margin-left: 80px !important;
        margin-top: 2px !important;
        text-align: left !important;
        font-size: 13.5px !important;
        color: #64748b !important;
    }
    .table.shopping-summery tbody td.price::before {
        display: inline !important;
        content: "Price: " !important;
        background: transparent !important;
        color: #64748b !important;
        font-weight: 500 !important;
        position: static !important;
        padding: 0 !important;
        width: auto !important;
        height: auto !important;
    }
    .table.shopping-summery tbody td.price span {
        font-weight: 600 !important;
        color: #1e293b !important;
    }
    .table.shopping-summery tbody td.qty {
        flex: 0 0 auto !important;
        margin-left: 80px !important;
        margin-top: 8px !important;
        display: block !important;
    }
    .table.shopping-summery tbody td.qty .sp-quantity {
        position: relative !important;
        display: inline-flex !important;
        flex-direction: row !important;
        align-items: center !important;
        justify-content: space-between !important;
        width: 104px !important;
        min-width: 104px !important;
        max-width: 104px !important;
        height: 34px !important;
        border: 1px solid #cbd5e1 !important;
        border-radius: 6px !important;
        overflow: hidden !important;
        background: #ffffff !important;
        padding: 0 !important;
        margin: 0 !important;
        box-sizing: border-box !important;
    }
    .table.shopping-summery tbody td.qty .sp-quantity button,
    .table.shopping-summery tbody td.qty .sp-quantity button.arrow,
    .table.shopping-summery tbody td.qty .sp-quantity button.sp-minus,
    .table.shopping-summery tbody td.qty .sp-quantity button.sp-plus {
        position: static !important;
        width: 30px !important;
        min-width: 30px !important;
        max-width: 30px !important;
        height: 100% !important;
        border: none !important;
        background: #f1f5f9 !important;
        color: #475569 !important;
        font-size: 15px !important;
        font-weight: 700 !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        cursor: pointer !important;
        transition: all 0.2s ease !important;
        padding: 0 !important;
        margin: 0 !important;
        line-height: 1 !important;
        box-sizing: border-box !important;
        z-index: 1 !important;
    }
    .table.shopping-summery tbody td.qty .sp-quantity button:hover {
        background: var(--booostr-blue-light, #e8f4fc) !important;
        color: var(--booostr-blue, #0A8CDE) !important;
    }
    .table.shopping-summery tbody td.qty .sp-quantity button a,
    .table.shopping-summery tbody td.qty .sp-quantity button a.ddd {
        color: inherit !important;
        font-size: 15px !important;
        font-weight: 700 !important;
        line-height: 1 !important;
        text-decoration: none !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        width: 100% !important;
        height: 100% !important;
    }
    .table.shopping-summery tbody td.qty .sp-quantity .inline.sp-input {
        width: 44px !important;
        min-width: 44px !important;
        max-width: 44px !important;
        height: 100% !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        padding: 0 !important;
        margin: 0 !important;
        position: static !important;
        box-sizing: border-box !important;
    }
    .table.shopping-summery tbody td.qty .sp-quantity .quntity-input {
        width: 100% !important;
        height: 100% !important;
        border: none !important;
        text-align: center !important;
        font-size: 13.5px !important;
        font-weight: 700 !important;
        color: #1e293b !important;
        padding: 0 !important;
        margin: 0 !important;
        background: transparent !important;
        outline: none !important;
        box-shadow: none !important;
        box-sizing: border-box !important;
    }
    .table.shopping-summery tbody td.total-amount {
        flex: 1 !important;
        text-align: right !important;
        align-self: center !important;
        margin-top: 8px !important;
    }
    .table.shopping-summery tbody td.total-amount::before {
        display: inline !important;
        content: "Total: " !important;
        background: transparent !important;
        color: #64748b !important;
        font-weight: 500 !important;
        position: static !important;
        padding: 0 !important;
        font-size: 13px !important;
        width: auto !important;
        height: auto !important;
    }
    .table.shopping-summery tbody td.total-amount span {
        font-size: 17px !important;
        font-weight: 800 !important;
        color: #0A8CDE !important;
    }
    .cart-summary-card {
        padding: 20px 18px !important;
        margin-top: 6px !important;
    }
    .btn-continue-shopping {
        width: 100%;
        justify-content: center;
        margin-top: 10px;
    }
    .empty-cart-card {
        padding: 40px 20px;
    }
}
</style>
@endpush

@push('js')
<script src="{{ asset('admin/js/sweetalert2.all.min.js') }}"></script>
<script>
window.booostrFormatPrice = function(amount) {
    if (typeof amount === 'string' && amount.trim().startsWith('$')) {
        return amount.trim();
    }
    var clean = (typeof amount === 'string') ? amount.replace(/[^0-9.-]+/g, '') : amount;
    var num = parseFloat(clean);
    if (isNaN(num)) num = 0.00;
    return '$' + num.toFixed(2);
};
window.amount_format = function(amount, type) {
    if (type === 'name') {
        var num = parseFloat(amount);
        return ' USD ' + (isNaN(num) ? '0.00' : num.toFixed(2));
    }
    return window.booostrFormatPrice(amount);
};
</script>
@endpush
