@extends('layouts.checkout')
@section('content')

<style>

a.cart-summary > span {
    display: inline;
    float: left;
    width: 100%;
}

p span.title{
    font-size: 12px;
}

.attr-variation-style{
    background: #eeebec;
    padding: 2px;
    border-radius: 2px;
    margin: 0px 2px;
}

/* add coupon code css */

.coupon-main-div {
    padding-left: 22px;
}

.apply-c-code .coupon-input-button {
  display: flex;
  flex-direction: row;
  padding: 6px 0px;
}

.apply-c-code .coupon-input-button .coupon-btn {
    background-color: #00c0ff;
    color: white;
    padding: -17px;
    /* margin: 7px 1px 27px 42px; */
    border: none;
    border-radius: 3px;
    cursor: pointer;
    font-size: 13px;
    width: 87px;
    height: 37px;
    padding-top: 9px;
    border-radius: 6px!important;
}

.apply-c-code .coupon-input-button .c-input {
    width: 224px;
    height: 42px;
    margin-top: 7px;
}

.apply-c-code h5{
    margin-left: 23px;
    font-size: 14px;
}

.coupon-main-div a{
    color:#00c0ff;
    font-weight: bold;
}

p#show_coupon_error {
    color: red;
    background: transparent;
}

.checkout-main span.price,.checkout-main span.price span{
    display: block;
}
.checkout-main span.price span.old-price{
    text-decoration: line-through;
}
span.learn-more {
    float: inline-end;
    color: deepskyblue;
    display: inline-block;
    position: relative;
    bottom: -18px;
    font-size: 10px;
}

input#cover_fee_checkbox {
    margin-left: -18px;
}
.alert.alert-success.success-sec {
    margin: 0 10px;
    padding-left: 28px;
    background-color: #E4FFEE;
}
.shipping_method_area,
.single-widget.get-button {
    display: inline-block;
    vertical-align: middle;
}

.shipping_method_area {
    width: 60%;
}

.single-widget.get-button {
    width: 38%;
    text-align: right;
}

/* Button styling */
.single-widget.get-button .button button {
    background-color: #00baff;
    border: none;
    padding: 10px 25px;
    color: #fff;
    font-weight: 600;
    border-radius: 5px;
    transition: 0.3s;
}

.single-widget.get-button .button button:hover {
    background-color: #0099cc;
}
 .shop.checkout .single-widget.get-button .btn {
    top: 50px;
}
/* Responsive (for mobile screens) */
@media (max-width: 991px) {
    .shipping_method_area,
    .single-widget.get-button {
        width: 100%;
        display: block;
        text-align: left;
    }

    .single-widget.get-button {
        margin-top: 10px;
    }
}
    .StripeElement {
        box-sizing: border-box;
        height: auto;
        padding: 10px 12px;
        border: 1px solid #ccc;
        border-radius: 4px;
        background-color: white;
        box-shadow: 0 1px 3px 0 #e6ebf1;
        -webkit-transition: box-shadow 150ms ease;
        transition: box-shadow 150ms ease;
    }

    .StripeElement--focus {
        box-shadow: 0 1px 3px 0 #cfd7df;
    }

    .StripeElement--invalid {
        border-color: #fa755a;
    }

    .StripeElement--webkit-autofill {
        background-color: #fefde5 !important;
    }

    #card-errors {
        color: #fa755a;
        margin-top: 10px;
        font-size: 14px;
    }

    .payment-form-container {
       
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
 .single-widget ul {
    margin-top: 30px;
}
.single-widget.get-button {
    display: inline-block;
    vertical-align: middle;
}
    .payment-form-container h3 {
        margin-bottom: 15px;
        font-size: 18px;
        font-weight: bold;
    }

    .btn-primary {
        background-color: #00c0ff;
        border: none;
        border-radius: 6px;
        padding: 10px 20px;
        font-size: 14px;
        cursor: pointer;
    }

    .btn-primary:hover {
        background-color: #0096cc;
    }

    .spinner-container {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(255, 255, 255, 0.5);
        display: none;
        justify-content: center;
        align-items: center;
        z-index: 999;
    }

    .custom-spinner {
        border: 3px solid transparent;
        border-top: 3px solid #007bff;
        border-radius: 50%;
        width: 40px;
        height: 40px;
        animation: spin 2s linear infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .cover-fee {
        background-color: #E4FFEE;
        padding: 5px 10px;
    }
    
    ul.order-summary {
    margin-left: 20px;
    }
    ul.order-summary{
    list-style: none;
    padding: 0;
    margin: 0;
    color: #333;
    font-size: 14px;
    font-weight: 400;
    font-family: 'Open Sans', sans-serif;
     padding-left: 20px;
    padding-right: 20px;
    }
    
    .order-summary li {
        padding: 10px;
    }
    .payment-pg h3 {
    color: #00c0ff;
    font-size: 26px;
    font-weight: 600;
    font-family: 'Arvo', serif;
}
.order-summary ul {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .order-summary ul li {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 0;
        font-size: 14px;
        border-bottom: 1px solid #e0e0e0;
        margin-bottom: 0;
    }

    .order-summary ul li:last-child {
        border-bottom: none;
        font-weight: bold;
        font-size: 18px;
        padding: 16px 0 0;
        border-top: 2px solid #e0e0e0;
        color: #333;
    }

    .order-summary ul li .cart_subtotal,
    .order-summary ul li .cart_discount,
    .order-summary ul li .cart_tax,
    .order-summary ul li .shipping_fee,
    .order-summary ul li .cover_fee_amount,
    .order-summary ul li .cart_total {
        font-weight: 600;
        color: #000;
        min-width: 60px;
        text-align: right;
    }
    
    span.cart_subtotal {
    display: inline-block;
    float: right;
    }
        span.cart_discount {
    display: inline-block;
    float: right;
    }
        span.cart_tax {
    display: inline-block;
    float: right;
    }
        span.shipping_fee {
    display: inline-block;
    float: right;
    }
        span.cover_fee_amount {
    display: inline-block;
    float: right;
    }
        span.cart_total {
    display: inline-block;
    float: right;
    }


    .order-summary .cover-fee {
        background-color: #E4FFEE !important;
      
        padding: 12px 16px !important;
        border-radius: 0 4px 4px 0;
        margin-bottom: 0;
    }

    .order-summary .cover-fee span {
        color: #155724;
        font-weight: 500;
    }


</style>

<!-- Spinner -->
<div id="page-loader" class="spinner-container">
    <div class="custom-spinner"></div>
</div>

<!-- Topbar -->
<div class="topbar-area">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 col-md-7 col-12">
                <div class="topbar-left">
                    <ul class="topbar-left-inner">
                        @if (!empty(tenant()->logo))
                            <li><a href="{{ url('/') }}"><img src="{{ env('WP_URL') }}{{ tenant()->logo }}" style="max-width: 80px;" /></a></li>
                        @else
                            <li><a href="{{ url('/') }}">{{ ucfirst(str_replace(['-', '_'], ' ', tenant()->id)) }}</a></li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Payment Section -->
<section class="shop checkout section checkout-main">
    <div class="checkout-container">
        <h1 class="page-title">{{ $page_data->payment_page_title ?? 'Complete Your Payment' }}</h1>

        <div class="row pb-5 breadcrumb">
            <div class="col-lg-12">
                @php 
                    $club_info = tenant_club_info();
                    $club_url = session()->has('redirect_url') ? session()->get('redirect_url') : $club_info['club_url'];
                    $club_cart_url = session()->has('redirect_url') ? str_replace('shop', '', $club_url) . 'cart' : $club_info['club_url'] . '?tab=cart';
                @endphp
                <a href="{{ $club_url }}">{{ $club_info['club_name'] }}</a> >>  
                <a href="{{ $club_cart_url }}">Cart</a> >>  
                <a href="{{ route('direct.new_checkout', [
                    'cartid' => session('cartid', 'default'),
                    'redirect_url' => base64_encode(session('redirect_url', url('/')))
                ]) }}">Checkout</a> >> Payment
            </div>
        </div>

        <div class="row">
            <!-- Payment Form Left -->
            <div class="col-lg-8 col-12 col-65 container">
                <div class="payment-form-container">
                    <h3>Payment Method</h3>

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    @if (Session::has('error'))
                        <div class="alert alert-danger">
                            <ul>
                                <li>{{ Session::get('error') }}</li>
                            </ul>
                        </div>
                    @endif

                    <form id="payment-form" method="post" action="{{ route('checkout.processPayment', $order->id) }}">
                        @csrf
                        <div id="payment_method_area"></div>
                        <input type="hidden" id="publishable_key" value="{{ $publishable_key }}">
                        <div id="card-errors" role="alert"></div>
                        <button type="submit" class="btn btn-primary mt-3" id="submit_btn">Pay Now</button>
                    </form>
                </div>
            </div>

            <!-- Order Summary Right -->
            <div class="col-lg-4 col-12 col-35">
                <div class="order-details container carts-right">
                    <div class="single-widget">

                        <h2>{{ __('ORDER SUMMARY') }}<span class="price" style="color:black"><i class="fa fa-shopping-cart"></i>
                            <b>{{ count($order->orderitems) }}</b></span></h2>

                        {{-- Order Items --}}
                        @foreach ($order->orderitems as $item)
                  
                            <p id="{{ $item->id }}">
                                <a href="#" class="cart-summary" style="display:flex; align-items:center; text-decoration:none;">
                                    
                                
                                         @if($item->term && $item->term->media())
                                            <img src=" {{ asset($item->term->media->value ?? 'uploads/default.png') }}" alt="img">
                                          

                                        @else
                                            <img src="{{ asset('uploads/default.jpg') }}" alt="img">
                                        @endif

                                    <span class="title">{{ $item->term->title ?? 'Product Name' }}</span>

                                    {{-- Options like in checkout --}}
                                    @php
                                    if(count($item->options ?? [])){
                                        echo "<span class='product-description' style='font-size:9px; display:flow;'>";
                                        foreach($item->options as $option => $selected_option){
                                            $product_options = $selected_option->varitionOptions ?? [];
                                            foreach($selected_option->varitions ?? [] as $sel_val){
                                                $cur_opt_name = collect($product_options)->filter(function($x) use($sel_val){
                                                    return $x->id == $sel_val->pivot->productoption_id;
                                                });
                                                echo "<strong class='attr-variation-style'>".$sel_val->name."</strong>";
                                            }
                                        }
                                        echo "</span>";
                                    }
                                    @endphp
                                </a>
                                <span class="d-qty">{{ $item->qty }}</span>
                                <span class="price">${{ number_format($item->amount * $item->qty, 2) }}</span>
                            </p>
                        @endforeach

                        <hr>

                        {{-- Totals --}}
                   <ul class="order-summary">
                            <li>
                                <span>{{ __('Subtotal') }}</span>
                                <span class="cart_subtotal">${{ number_format($subtotal ?? 0, 2) }}</span>
                            </li>
                            <li>
                                <span>(-) {{ __('Discount') }}</span>
                                <span class="cart_discount">${{ number_format($discount ?? 0, 2) }}</span>
                            </li>
                            <li>
                                <span>(+) {{ __('Tax') }}</span>
                                <span class="cart_tax">${{ number_format($tax ?? 0, 2) }}</span>
                            </li>
                            <li class="shipping-item">
                                <span>(+) {{ __('Delivery fee') }}</span>
                                <span class="shipping_fee">${{ number_format($delivery_fee ?? 0, 2) }}</span>
                            </li>
                            <li class="cover-fee alert-success">
                                <span>(+) {{ __('Covered Fees - thank you') }}</span>
                                <span class="cover_fee_amount">${{ number_format($cover_fee ?? 0, 2) }}</span>
                            </li>
                            <li class="last">
                                <span>{{ __('Total') }}</span>
                                <span class="cart_total">${{ number_format($total ?? 0, 2) }}</span>
                            </li>
                        </ul>

                    </div>
                </div>
            </div>
            <!-- End Order Summary Right -->

        </div>
    </div>
</section>


<input type="hidden" id="subtotal_hidden" value="{{ $order->subtotal ?? 0 }}">
<input type="hidden" id="discount_hidden" value="{{ $order->discount ?? 0 }}">
<input type="hidden" id="tax_hidden" value="{{ $order->tax ?? 0 }}">
<input type="hidden" id="shipping_hidden" value="{{ $order->shipping->shipping_price ?? 0 }}">
<input type="hidden" id="cover_fee_hidden" value="{{ $order->cover_fee ?? 0 }}">
<input type="hidden" id="total_hidden" value="{{ $order->total ?? 0 }}">
<input type="hidden" id="publishable_key" value="{{ $publishable_key }}">
<input type="hidden" id="client_secret" value="{{$client_secret}}">
<input type="hidden" id="order_success_url" value="{{ route('checkout.success')}}?order_id={{ $order->id }}">
<input type="hidden" id="process_order_url" value="{{ route('checkout.processPayment', $order->id) }}">


<!-- Footer -->
<footer class="container">
    <div class="row">
        <div class="col-lg-12 container" style="text-align: center;">
            <a href="{{ route('store.page', ['slug' => 'terms-and-conditions']) }}" target="_blank">Terms and Conditions</a> |
            <a href="{{ route('store.page', ['slug' => 'privacy-policy']) }}" target="_blank">Privacy Policy</a> |
            <a href="{{ route('store.page', ['slug' => 'return-policy']) }}" target="_blank">Return Policy</a>
        </div>
    </div>
</footer>

@push('js')
    <script src="https://js.stripe.com/v3/"></script>
    <script src="{{ asset('checkout/js/new-stripe.js') }}"></script>
@endpush

@endsection