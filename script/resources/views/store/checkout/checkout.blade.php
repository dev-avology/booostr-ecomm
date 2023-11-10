@extends('layouts.checkout')
@section('content')


 <!-- Spinner container -->
 <div id="page-loader" class="spinner-container" style="display:none;">
	<!-- Custom Spinner -->
	<div class="custom-spinner"></div>
 </div>

    <!-- Topbar Area -->
    <div class="topbar-area">
		

        <div class="container">
            <div class="row">
                <div class="row align-items-center">

                    <div class="col-lg-6 col-md-7 col-12">
                        <!-- Topbar Left -->
                        <div class="topbar-left">
                            <ul class="topbar-left-inner">
                                @if (!empty(tenant()->logo))
                                    <li><a href="#"><img src="{{ env('WP_URL') }}{{ tenant()->logo }}"
                                                style="max-width: 80px;" /></a></li>
                                @else
                                    <li><a
                                            href="{{ url('/') }}">{{ ucfirst(str_replace(['-', '_'], ' ', tenant()->id)) }}</a>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </div>

                    <div class="col-lg-6 col-md-5 col-12">
                        <!-- Topbar Right -->
                        {{-- <div class="topbar-right">
					<ul class="topbar-right-inner">
						<!-- Topbar Language -->
						@if (tenant('customer_modules') == 'on')
						<li class="accounts-top-btn"><a href="{{ !Auth::check() ? '#' : url('/customer/dashboard') }}"><i class="icofont-user-male"></i><span>{{ !Auth::check() ? __('My Account') : Auth::user()->name }}</span></a>
							@if (!Auth::check())
							<!-- Topbar Accounts Form -->
							<div class="accounts-signin-top-form">
								<form action="{{ route('login') }}" method="post" class="accounts-signin-inner">
									@csrf
									<div class="row">
										<div class="col-12">
											<div class="form-group">
												<label><i class="icofont-ui-user"></i> {{ __('Email') }}</label>
												<input type="email" name="email" required="required" placeholder="Enter Email">
											</div>
										</div>
										<div class="col-12">
											<div class="form-group">
												<label><i class="icofont-ssl-security"></i> {{ __('Password') }}</label>
												<input type="password" name="password" required="">
											</div>
										</div>
										<div class="col-12">
											<div class="accounts-signin-btn">
												<button type="submit" class="theme-btn">{{ __('Sign in') }}</button>
											</div>
										</div>
									</div>
								</form>
							</div>
							<!-- End Topbar Accounts Form -->
							@endif
						</li>
						@endif
					</ul>
				</div> --}}
                    </div>
                </div>
            </div>
        </div>
        <!-- Start Checkout -->

        <section class="shop checkout section checkout-main">
            <div class="checkout-container">
                <h1 class="page-title">{{ $page_data->cart_page_title ?? 'Checkout' }}</h1>


                    <div class="row pb-5 breadcrumb">
                        <div class="col-lg-12">
                            @php 
                            $club_info = tenant_club_info();
                            @endphp
                          <a href="{{$club_info['club_url']}}"> {{$club_info['club_name']}} </a>  &nbsp;&nbsp;>>&nbsp;&nbsp;  <a href="{{$club_info['club_url']}}?tab=cart">Cart</a>  &nbsp;&nbsp;>>&nbsp;&nbsp;  Checkout
        
                        </div>
                    </div>
    
                @if (Cart::instance('default')->count() != 0)
                    <form class="form orderform" id="payment-form" method="post"
                        action="{{ route('checkout.makeorder') }}">
                        @csrf
                        <div class="row">
                            <div class="col-lg-8 col-12 col-65 container">
                                <div class="checkout-form  pb-3">
                                    <h3 class="mt-3 mb-1">Billing Address</h3>
                                    <em>Enter your payment method billing information below</em>
                                    <!-- Form -->
									<div class="row mt-3" id="error-msg">
									</div>
                                    <div class="row mt-3">
                                        <div class="col-lg-12 col-md-12 col-12">
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
                                            @if (Session::has('alert'))
                                                <div class="alert alert-danger">
                                                    <ul>
                                                        <li>{{ Session::get('alert') }}</li>
                                                    </ul>
                                                </div>
                                            @endif
                                        </div>

                                        <div class="col-lg-6 col-md-6 col-12">
                                            <div class="form-group">
                                                <label><i class="fa fa-user"></i>{{ __('Full Name') }}<span>*</span></label>
                                                <input type="text" name="name" id="billing-name"
                                                    data-shippingf="shipping-name" value="{{ $customer['name'] }}"
                                                    placeholder=""  class="required" data-msg="{{__('Billing Full Name')}}" @if(!empty($customer['name'])) @endif>
                                            </div>
                                        </div>

                                        <div class="col-lg-6 col-md-6 col-12">
                                            <div class="form-group">
                                                <label><i
                                                        class="fa fa-envelope"></i>{{ __('Email Address') }}<span>*</span></label>
                                                <input value="{{ $customer['email'] }}" id="billing-email"
                                                    data-shippingf="shipping-email" type="email" name="email"
                                                    placeholder="" class="required" data-msg="{{__('Billing Email')}}" required @if(!empty($customer['email'])) @endif>
                                            </div>
                                            
                                        </div>
                                        <div class="col-lg-6 col-md-6 col-12">
                                            <div class="form-group">
                                                <label><i
                                                        class="fa fa-phone"></i>{{ __('Phone Number') }}<span>*</span></label>
                                                <input type="number" id="billing-phone" name="phone"
                                                    data-shippingf="shipping-phone"
                                                    value="{{ str_replace('-', '', $customer['phone']) }}" placeholder=""
                                                     maxlength="20" class="required" data-msg="{{__('Billing Phone Number')}}" @if(!empty($customer['phone'])) @endif>
                                            </div>
                                        </div>
                                        <div class="col-lg-12 col-md-12 col-12 delivery_address_area">
                                            <div class="form-group">
                                                <label><i class="fa fa-map-marker"></i> {{ __('Address') }}
                                                    <span>*</span></label>
                                                <input type="text" class="location_input required" id="location_input"
                                                    data-shippingf="location_input1" name="billing[address]" placeholder=""
                                                     value="{{ $customer['address'] }}" data-msg="{{__('Billing Address')}}" @if(!empty($customer['address'])) @endif>
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-md-6 col-12 delivery_address_city">
                                            <div class="form-group">
                                                <label><i class="fa fa-building"></i> {{ __('City') }}
                                                    <span>*</span></label>
                                                <input type="text" class="location_input required" id="location_city"
                                                    data-shippingf="location_city1" name="billing[city]" placeholder=""
                                                     value="{{ $customer['city'] }}" data-msg="{{__('Billing City')}}" @if(!empty($customer['city'])) @endif>
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-md-6 col-12 delivery_address_state">
                                            <div class="form-group">
                                                <label> <i class="fa fa-map"></i>{{ __('State') }} <span>*</span></label>


                                                <select class="location_input nice-select required" id="location_state"
                                                        data-shippingf="location_state1" name="billing[state]" data-msg="{{__('Billing State')}}" @if(!empty($customer['state'])) @endif>
                                                    @foreach ($states_data as $key => $val)
                                                        <option @if ($key == $customer['state']) selected @endif
                                                            value="{{ $key }}">{{ $val }}</option>
                                                    @endforeach
                                                </select>
                                                {{-- <input type="text" class="location_input" id="location_state" name="billing[state]" placeholder="" required="required" value="{{ $customer['state'] }}"> --}}
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-md-6 col-12 delivery_address_country">
                                            <div class="form-group">
                                                <label> <i class="fa fa-globe"></i>
                                                    {{ __('Country') }} <span>*</span></label>
                                                <select id="billing-country" name="billing[country]"
                                                    data-shippingf="billing-country1" class="nice-select required" data-msg="{{__('Billing Country')}}">
                                                    <option value="USA">United State</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-md-6 col-12 post_code_area">
                                            <div class="form-group">
                                                <label><i class="fa fa-envelope"></i>
                                                    {{ __('Zip Code') }}<span>*</span></label>
                                                <input type="text" id="post_code" name="billing[post_code]"
                                                    data-shippingf="post_code1" placeholder=""
                                                    value="{{ $customer['zip'] }}"  class="required" data-msg="{{__('Billing Postal Code')}}" @if(!empty($customer['zip'])) @endif>
                                            </div>
                                        </div>

                                        <div class="col-lg-12 col-md-12 col-12">
                                            <div class="form-group create-account">
                                                <input id="shipping_address" name="shipping_same_as_billing"
                                                    type="checkbox" value="1" checked>
                                                <label
                                                    for="shipping_address">{{ __('Shipping address same as billing') }}</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-3 shipping_address_area none" style="display:none">
                                        <div class="col-lg-6 col-md-6 col-12">
                                            <div class="form-group">
                                                <label><i
                                                        class="fa fa-user"></i>{{ __('Full Name') }}<span>*</span></label>
                                                <input type="text" id="shipping-name" name="shipping[name]"
                                                    value="{{ $customer['name'] }}" placeholder=""  class="required" data-msg="{{__('Shipping Full Name')}}">
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-md-6 col-12">
                                            <div class="form-group">
                                                <label><i
                                                        class="fa fa-phone"></i>{{ __('Phone Number') }}<span>*</span></label>
                                                <input type="number" id="shipping-phone" name="shipping[phone]"
                                                    value="{{ str_replace('-', '', $customer['phone']) }}" placeholder=""
                                                     maxlength="20" class="required" data-msg="{{__('Shipping Phone Number')}}">
                                            </div>
                                        </div>
                                        <div class="col-lg-12 col-md-12 col-12 delivery_address_area">
                                            <div class="form-group">
                                                <label><i class="fa fa-map-marker"></i> {{ __('Address') }}
                                                    <span>*</span></label>
                                                <input type="text" class="location_input required" id="location_input1"
                                                    name="shipping[address]" placeholder="" 
                                                    value="{{ $customer['address'] }}" data-msg="{{__('Shipping Address')}}">
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-md-6 col-12 delivery_address_city">
                                            <div class="form-group">
                                                <label><i class="fa fa-building"></i> {{ __('City') }}
                                                    <span>*</span></label>
                                                <input type="text" class="location_input required" id="location_city1"
                                                    name="shipping[city]" placeholder="" 
                                                    value="{{ $customer['city'] }}" data-msg="{{__('Shipping City')}}">
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-md-6 col-12 delivery_address_state">
                                            <div class="form-group">
                                                <label> <i class="fa fa-map"></i>{{ __('State') }} <span>*</span></label>
                                                <select class="location_input nice-select required" id="location_state1"
                                                    name="shipping[state]" data-msg="{{__('Shipping State')}}">
                                                    @foreach ($states_data as $key => $val)
                                                        <option @if ($key == $customer['state']) selected @endif
                                                            value="{{ $key }}">{{ $val }}</option>
                                                    @endforeach
                                                </select>
                                                {{-- <input type="text" class="location_input" id="location_state1" name="shipping[state]" placeholder="" required="required" value="{{ $customer['state'] }}"> --}}
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-md-6 col-12 delivery_address_country">
                                            <div class="form-group">
                                                <label> <i class="fa fa-globe"></i>{{ __('Country') }} <span>*</span></label>
                                                <select id="shipping-country" name="shipping[country]"
                                                    class="nice-select required" data-msg="{{__('Shipping Country')}}">
                                                    <option value="USA">United State</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-md-6 col-12 post_code_area">
                                            <div class="form-group">
                                                <label><i class="fa fa-envelope"></i>{{ __('Zip Code') }}<span>*</span></label>
                                                <input type="text" id="post_code1" class="required" name="shipping[post_code]"
                                                    placeholder="" value="{{ $customer['zip'] }}"  data-msg="{{__('Shipping Postal Code')}}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Shopping Cart -->
                                <div class="checkout-form  pb-3">
                                    <div class="form-row">
                                        <h3>{{ __('Payment Method') }}</h3>
                                        <div id="card-element">

                                            <label for="fname">The following payment types are accepted. Please enter your payment information below.</label>
                                            <div class="icon-container">
                                                <i class="fa fa-cc-visa" style="color:navy;"></i>
                                                <i class="fa fa-cc-amex" style="color:blue;"></i>
                                                <i class="fa fa-cc-mastercard" style="color:red;"></i>
                                                <i class="fa fa-cc-discover" style="color:orange;"></i>
                                            </div>
                                            <label for="cardnumber">Credit card number</label>
                                            <div id="cardnumber"></div>
                                            <div class="row">
                                                <div class="col-50">
                                                    <label for="cardexpiry">Exp Month</label>
                                                    <div id="cardexpiry"></div>
                                                </div>
                                                <div class="col-25">
                                                    <label for="cardcvv">CVV</label>
                                                    <div id="cardcvv"></div>
                                                </div>
                                                <div class="col-25">
                                                    <label for="cardpostal">ZIP</label>
                                                    <div id="cardpostal"></div>
                                                </div>
                                            </div>

                                        </div>
                                        <!-- Used to display form errors. -->
                                        <div id="card-errors" role="alert"></div>
                                    </div>
                                    <input type="hidden" id="publishable_key"
                                        value="{{ $payment_data['publishable_key'] }}">
                                </div>
                                <!--/ End Shopping Cart -->

                            </div>
                            <div class="col-lg-4 col-12 col-35">
                                <div class="order-details container carts-right">
                                    <!-- Order Widget -->
                                    <div class="single-widget">

                                        <div class="">
                                            <h2>{{ __('CART SUMMARY') }}<span class="price" style="color:black"><i
                                                        class="fa fa-shopping-cart"></i>
                                                    <b>{{ Cart::instance('default')->countItems() }}</b></span></h2>
                                            @foreach (Cart::instance('default')->content() as $item)
                                                <p><a href="#"> <img src="{{ $item->options->preview }}"
                                                            alt="img">{{ $item->name }}</a> <span
                                                        class="price">{{ get_option('currency_data', true)->currency_icon }}{{ number_format($item->price, 2) }}</span>
                                                </p>
                                            @endforeach
                                            <hr>
                                        </div>
                                        @if ($pickup_order == 'on')
                                            <div class="order-type-section">
                                                <input type="radio" name="order_method" id="is_pickup"
                                                    class="order_method {{ $pickup_order == 'off' ? 'none' : '' }}"
                                                    value="pickup" @if ($order_method == 'pickup') checked="" @endif>
                                                <label for="is_pickup">{{ __('pickup') }}</label>

                                                <input type="radio" name="order_method" id="is_pickup1"
                                                    class="order_method" value="delivery"
                                                    @if ($order_method == 'delivery') checked="" @endif>
                                                <label for="is_pickup1">{{ __('delivery') }}</label>

                                            </div>
                                        @else
                                            <input type="hidden" name="order_method" class="order_method none"
                                                value="delivery">
                                        @endif

                                        <div class="content">
                                            <ul>
                                                <li>{{ __('Subtotal') }}
                                                    <span class="cart_subtotal">
                                                        0.00
                                                    </span>
                                                </li>
                                                <li>(+) {{ __('Tax') }}
                                                    <span class="cart_tax">
                                                        0.00
                                                    </span>
                                                </li>
                                                <li>(+) {{ __('Delivery fee') }}<span class="shipping_fee">0.00</span>
                                                </li>

                                                <li class="last">{{ __('Total') }}<span
                                                        class="cart_total">0.00</span></li>

                                                {{-- <li>{{ __('Credit Card Fee') }}
										<span class="cart_credit_card_fee">
											0.00
										</span>
									</li>
									<li> {{ __('Booostr Platform Fee') }}
										<span class="cart_booster_platform_fee">
											0.00
										</span>
									</li>
									<li class="last">{{ __('Grand Total') }}<span class="cart_grand_total">0.00</span></li> --}}

                                            </ul>
                                        </div>
                                    </div>
                                    @if ($order_settings->shipping_amount_type != 'distance')
                                        <div class="single-widget shipping_method_area">
                                            <h2>{{ __('Shipping Method') }}</h2>
                                            <div class="content">
                                                <div class="checkbox shipping_render_area">

                                                    <label class="checkbox-inline shipping_method"
                                                        for="shipping{{ $shipping_methods['method_type'] }}">
                                                        <input name="shipping_method" class="shipping_item"
                                                            value="{{ $shipping_methods['method_type'] }}"
                                                            data-price="{{ $shipping_methods['base_pricing'] }}"
                                                            data-shippingInfo='{!! json_encode($shipping_methods) !!}'
                                                            id="shipping{{ $shipping_methods['method_type'] }}"
                                                            type="radio"> {{ $shipping_methods['label'] }}
                                                    </label>

                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    <!--/ End Order Widget -->

                                    <!--/ End Order Widget -->

                                    <!-- Button Widget -->
                                    <div class="single-widget get-button">
                                        <div class="content">
                                            <div class="button">
                                                <input type="hidden" id="shipping_fee" name="shipping_fee">
                                                <input type="hidden" id="total_price" name="total_price">
                                                <button type="submit" class="btn submit_btn submitbtn"
                                                    id="submit_btn">{{ __('Place Order') }}</button>
                                            </div>
                                        </div>
                                    </div>
                                    <!--/ End Button Widget -->
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="wpuid" value="{{ $customer['wpuid'] }}">

                    </form>
                @else
                    <div class="alert alert-danger" role="alert">
                        {{ __('No Cart Item Available For Checkout') }}
                    </div>
                @endif
            </div>
        </section>
        <!--/ End Checkout -->
        <input type="hidden" id="subtotal" value="{{ Cart::instance('default')->subtotal() }}">
        <input type="hidden" id="tax" value="{{ Cart::instance('default')->tax() }}">
        <input type="hidden" id="credit_card_fee"
            value="{{ credit_card_fee(Cart::instance('default')->total() + $shipping_price) }}">
        <input type="hidden" id="booster_platform_fee"
            value="{{ booster_club_chagre(Cart::instance('default')->total() + $shipping_price) }}">
        <input type="hidden" id="total" value="{{ Cart::instance('default')->total() }}">

        <input type="hidden" id="totalWeight" value="{{ Cart::instance('default')->weight() }}">
        <input type="hidden" id="totalItem" value="{{ Cart::instance('default')->count() }}">


        <input type="hidden" id="latitude" value="{{ tenant('lat') }}">
        <input type="hidden" id="longitude" value="{{ tenant('long') }}">
        <input type="hidden" id="city" value="{{ $invoice_data->store_legal_city ?? '' }}">

        <footer class="container">
            <div class="row">
                <div class="col-lg-12 container" style="text-align:center;">

                    <a href="{{ route('store.page', ['slug' => 'terms-and-conditions']) }}" target="_blank"> Terms and
                        conditions</a> |
                    <a href="{{ route('store.page', ['slug' => 'privacy-policy']) }}" target="_blank">Privacy Policy</a> |
                    <a href="{{ route('store.page', ['slug' => 'return-policy']) }}" target="_blank">Return Policy</a>

                </div>
            </div>
        </footer>


    @endsection
    @push('js')
	<style>
        /* Center the fixed spinner */
        .spinner-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(255, 255, 255, 0.5); /* Transparent white background */
            display: flex;
            justify-content: center;
            align-items: center;
			z-index: 999;
        }

        /* Style the spinner */
        .custom-spinner {
            border: 3px solid transparent;
            border-top: 3px solid #007bff; /* Change the color here */
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 2s linear infinite;
        }

        /* Spinner animation */
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
        <script type="text/javascript">
            "use strict";

            var subtotal = parseFloat($('#subtotal').val());
            var tax = parseFloat($('#tax').val());
            var total = parseFloat($('#total').val());
            var price = {{ $shipping_price }};
            var credit_card_fee = parseFloat($('#credit_card_fee').val());
            var booster_platform_fee = parseFloat($('#booster_platform_fee').val());

            var new_total = subtotal;
            var apply_tax_url = "{{ route('checkout.applyTax') }}";
            var store_info = {!! Tenant('club_info') !!};
            var currency_icon = "{{ get_option('currency_data', true)->currency_icon }}";
        </script>
        @if ($source_code == 'off')
            <script type="text/javascript" src="{{ asset('theme/disable-source-code.js') }}"></script>
        @endif
        @if (1)
            <script async defer
                src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCmimJcxCmMIgBR0G0UKmQAgfr7RSS8pDg&libraries=places&radius=5&location={{ tenant('lat') }}%2C{{ tenant('long') }}&callback=initialize">
            </script>
            <script type="text/javascript">
                "use strict";
                if ($('#my_lat').val() != null) {
                    localStorage.setItem('lat', $('#my_lat').val());
                }
                if ($('#my_long').val() != null) {
                    localStorage.setItem('long', $('#my_long').val());
                }
                if ($('#location_input').val() != null) {
                    localStorage.setItem('location', $('#location_input').val());
                }
                if (localStorage.getItem('location') != null) {
                    var locs = localStorage.getItem('location');
                } else {
                    var locs = "";
                }
                $('#location_input').val(locs);
                if (localStorage.getItem('lat') !== null) {
                    var lati = localStorage.getItem('lat');
                    $('#my_lat').val(lati)
                } else {
                    var lati = "{{ tenant('lat') }}";
                }
                if (localStorage.getItem('long') !== null) {
                    var longlat = localStorage.getItem('long');
                    $('#my_long').val(longlat)
                } else {
                    var longlat = "{{ tenant('long') }}";
                }

                const maxRange = "{{ $order_settings->google_api_range ?? 0 }}";
                const resturentlocation = "{{ $invoice_data->store_legal_address ?? '' }}";
                const feePerkilo = "{{ $order_settings->delivery_fee ?? 0 }}";
                var mapOptions;
                var map;
                var marker;
                var searchBox;
                var city;
            </script>
            <script type="text/javascript" src="{{ asset('checkout/js/google-api.js') }}"></script>
        @endif

        <script type="text/javascript" src="{{ asset('checkout/js/checkout.js') }}"></script>
    @endpush
    @push('js')
        <script src="https://js.stripe.com/v3/"></script>
        <script src="{{ asset('checkout/js/stripe.js') }}"></script>
    @endpush
