@extends('layouts.checkout')
@section('content')

<style>
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
    
    .order-summary li {
        padding: 10px;
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
                <a href="{{ $club_url }}">{{ $club_info['club_name'] }}</a>   >>  
                <a href="{{ $club_cart_url }}">Cart</a>   >>  
                <a href="{{ route('direct.new_checkout', [
    'cartid' => session('cartid', 'default'),
    'redirect_url' => base64_encode(session('redirect_url', url('/')))
]) }}">Checkout</a>
   >>   Payment
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8 col-12 col-65 container">
                <div class="payment-form-container">
                    <h3>Payment Method</h3>
                    <p>Order ID: {{ $order->id }}</p>
                    <p>Total: {{ get_option('currency_data', true)->currency_icon }}{{ number_format($order->total, 2) }}</p>

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
                        <button type="submit" class="btn btn-primary mt-3">Pay Now</button>
                    </form>
                </div>
            </div>

            <div class="col-lg-4 col-12 col-35">
                <div class="order-details container carts-right">
                <div class="single-widget">
                    <h2>{{ __('Order Summary') }}</h2>
                    
                    <ul class="order-summary">
                        <li>{{ __('Subtotal') }}
                            <span class="cart_subtotal">${{ number_format($subtotal, 2) }}</span>
                        </li>
                        <li>(-) {{ __('Discount') }}
                            <span class="cart_discount">${{ number_format($discount, 2) }}</span>
                        </li>
                        <li>(+) {{ __('Tax') }}
                            <span class="cart_tax">${{ number_format($tax, 2) }}</span>
                        </li>
                        <li class="shipping-item">(+) {{ __('Delivery fee') }}
                            <span class="shipping_fee">${{ number_format($delivery_fee, 2) }}</span>
                        </li>
                        <li class="cover-fee alert-success">(+) {{ __('Covered Fees - thank you') }}
                            <span class="cover_fee_amount">${{ number_format($cover_fee, 2) }}</span>
                        </li>
                        <li class="last">{{ __('Total') }}
                            <span class="cart_total">${{ number_format($total, 2) }}</span>
                        </li>
                </ul>

                </div>
            </div>

            </div>
        </div>
    </div>
</section>

<input type="hidden" id="subtotal_hidden" value="{{ $order->subtotal ?? 0 }}">
<input type="hidden" id="discount_hidden" value="{{ $order->discount ?? 0 }}">
<input type="hidden" id="tax_hidden" value="{{ $order->tax ?? 0 }}">
<input type="hidden" id="shipping_hidden" value="{{ $order->shipping->shipping_price ?? 0 }}">
<input type="hidden" id="cover_fee_hidden" value="{{ $order->cover_fee ?? 0 }}">
<input type="hidden" id="total_hidden" value="{{ $order->total ?? 0 }}">


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
    <script type="text/javascript">
        "use strict";
        document.addEventListener("DOMContentLoaded", function () {
            console.log('Script loaded');

            // Format numbers to 2 decimal places
            function fmt(v) {
                return Number(v || 0).toFixed(2);
            }

            // Update display with values from hidden inputs
            const subtotal = document.getElementById('subtotal_hidden')?.value || 0;
            const discount = document.getElementById('discount_hidden')?.value || 0;
            const tax = document.getElementById('tax_hidden')?.value || 0;
            const shipping = document.getElementById('shipping_hidden')?.value || 0;
            const cover = document.getElementById('cover_fee_hidden')?.value || 0;
            const total = document.getElementById('total_hidden')?.value || 0;

            console.log({ subtotal, discount, tax, shipping, cover, total }); // Debug values

            // Stripe initialization
       const stripe = Stripe("{{ $publishable_key }}");
    const elements = stripe.elements({
      clientSecret: '{{$client_secret}}',
      appearance: { /* appearance config */ }
    });

    elements.create('payment', {
      layout: { type: 'tabs', defaultCollapsed: false }
    }).mount('#payment_method_area');



    const form = document.getElementById('payment-form');
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        document.getElementById('page-loader').style.display = 'flex';


    const { error, paymentIntent } = await stripe.confirmPayment({
      elements,
      confirmParams: {
        // optional return_url for redirects that some methods require
        return_url: '{{ route("checkout.success") }}?order_id={{ $order->id }}',
      },
      redirect: 'if_required' // avoids automatic redirect when not needed
    });


    });
});
    </script>
@endpush
@endsection