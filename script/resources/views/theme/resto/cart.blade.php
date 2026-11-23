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
                        <div class="row align-items-start">
                            <!-- Left: Coupon Code & Continue Shopping -->
                            <div class="col-lg-7 col-md-6 col-12 mb-4 mb-md-0">
                                <div class="coupon-section-box">
                                    <h5 class="coupon-box-title">{{ __('Have a Coupon Code?') }}</h5>
                                    <div class="coupon">
                                        <form method="post" action="{{ route('makediscount') }}" class="ajaxform_with_reload d-flex">
                                            @csrf
                                            <input name="coupon" required="" placeholder="{{ __('Enter coupon code') }}" class="coupon-input">
                                            <button class="btn btn-coupon-apply basicbtn" type="submit">{{ __('Apply') }}</button>
                                        </form>
                                    </div>
                                    <div class="continue-shopping-wrap mt-4">
                                        <a href="{{ url('/products') }}" class="btn-continue-shopping">
                                            <i class="icofont-arrow-left"></i> {{ __('Continue Shopping') }}
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- Right: Order Summary Card -->
                            <div class="col-lg-5 col-md-6 col-12">
                                <div class="right cart-summary-card">
                                    <h4 class="summary-card-title">{{ __('Order Summary') }}</h4>

                                    <div class="summary-row d-flex justify-content-between align-items-center mb-3">
                                        <span class="summary-label">{{ __('Cart Subtotal') }}</span>
                                        <span class="summary-value cart_subtotal">{{ Cart::instance('default')->subtotal() }}</span>
                                    </div>

                                    <!-- Shipping & Tax Calculated in Next Steps Notice -->
                                    <div class="shipping-tax-notice">
                                        <i class="icofont-info-circle mr-1"></i>
                                        <span>{{ __('Shipping & Tax calculated in next steps') }}</span>
                                    </div>

                                    <hr class="summary-divider">

                                    <div class="summary-row d-flex justify-content-between align-items-baseline mb-4">
                                        <span class="summary-total-label">{{ __('Total') }}</span>
                                        <span class="summary-total-value cart_total">{{ Cart::instance('default')->total() }}</span>
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
    overflow: hidden;
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

/* Coupon Box */
.coupon-section-box {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 24px 28px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.02);
}
.coupon-box-title {
    font-size: 16px;
    font-weight: 600;
    color: #1e293b;
    margin-bottom: 14px;
}
.coupon-input {
    flex: 1;
    height: 48px;
    border: 1px solid #cbd5e1 !important;
    border-radius: 8px 0 0 8px !important;
    padding: 0 16px;
    font-size: 14px;
    color: #1e293b;
    outline: none;
    transition: border-color 0.2s ease;
}
.coupon-input:focus {
    border-color: #0A8CDE !important;
}
.btn-coupon-apply {
    background: #0A8CDE !important;
    background-color: #0A8CDE !important;
    color: #ffffff !important;
    border: 1px solid #0A8CDE !important;
    border-radius: 0 8px 8px 0 !important;
    padding: 0 24px;
    font-weight: 600;
    font-size: 14px;
    height: 48px;
    transition: all 0.2s ease;
}
.btn-coupon-apply:hover {
    background: #0870b2 !important;
    background-color: #0870b2 !important;
    border-color: #0870b2 !important;
}
.btn-continue-shopping {
    color: #475569;
    font-weight: 500;
    font-size: 14.5px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: color 0.2s ease;
}
.btn-continue-shopping:hover {
    color: #0A8CDE;
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
</style>
@endpush

@push('js')
<script src="{{ asset('admin/js/sweetalert2.all.min.js') }}"></script>
<script src="{{ asset('admin/js/form.js') }}"></script>
@endpush
