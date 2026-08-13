@extends('layouts.checkout')
@section('content')

<style>
a.cart-summary > span {
    display: inline;
    float: left;
    width: 100%;
}

p span.title {
    font-size: 12px;
}

.attr-variation-style {
    background: #eeebec;
    padding: 2px;
    border-radius: 2px;
    margin: 0px 2px;
}

/* Add coupon code CSS */
.coupon-main-div {
    padding-left: 12px;
}

.apply-c-code .coupon-input-button {
    display: flex;
    flex-direction: row;
    padding: 6px 0px;
}

.apply-c-code .coupon-input-button .coupon-btn {
    background-color: #00c0ff;
    color: white;
    padding: 10px 15px;
    border: none;
    border-radius: 3px;
    cursor: pointer;
    font-size: 13px;
    width: auto;
    height: 37px;
    padding-top: 9px;
    border-radius: 6px !important;
}
.shop.checkout .single-widget.get-button .btn {
    height: 55px;
    width: 100%;
    line-height: 55px;
    text-align: center;
    border-radius: 0;
    text-transform: uppercase;
    color: #fff;
    padding: 0 20px;
    max-width: max-content;
    margin-left: auto;
    display: block;
    margin-top: 40px;
}
.apply-c-code .coupon-input-button .c-input {
    width: 224px;
    height: 42px;
    margin-top: 7px;
}

.apply-c-code h5 {
    margin-left: 23px;
    font-size: 14px;
}

.coupon-main-div a {
    color: #00c0ff;
    font-weight: bold;
}

p#show_coupon_error {
    color: red;
    background: transparent;
}

.submit-btn:disabled {
    background-color: #4a4a4a; 
    color: #ffffff; 
    cursor: not-allowed;
    opacity: 0.7;
}
.checkout-main span.price,
.checkout-main span.price span {
    display: block;
}

.checkout-main span.price span.old-price {
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
    margin-left: -20px;
    margin-bottom: -8px;
}

.alert.alert-success.success-sec {
    margin: 0 10px;
    padding-left: 30px;
    background-color: #E4FFEE;
}
form#payment-form {
    padding: 0 15px;
}
form#payment-form > .row {
    row-gap: 20px;
}

/* Checkout form layout */
.checkout-form {
    display: flex;
    flex-direction: column;
}

.shipping-method-button-container {
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
    width: 100%;
    margin-top: 20px;
    display:block;
}

input[type="checkbox"] {
    width: 20px;
    height: 20px;
    position: relative;
    -webkit-appearance: none;
    border: 1px solid #2222224a;
    cursor: pointer;
    border-radius: 2px;
    flex-shrink: 0;
    appearance: none;
}

input[type="checkbox"]::before {
    content: "";
    position: absolute;
    left: 7px;
    top: 2px;
    width: 5px;
    height: 10px;
    border: solid white;
    border-width: 0 2px 2px 0;
    -webkit-transform: rotate(45deg);
    -ms-transform: rotate(45deg);
    transform: rotate(45deg);
    opacity: 0;
}

input[type="checkbox"]:checked::before {
    opacity: 1;
}

input[type="checkbox"]:checked {
    background: #0278d2;
}
.shop.checkout .form .create-account {
    display: flex;
    gap: 5px;
}
.shipping-method-button-container .checkout-consent label span {
    font-size: 12px;
}
.shipping-method-button-container .checkout-consent label span a {
    color: #00BAFD;
}
.shipping_method_area,
.single-widget.get-button {
    flex: 1;
    min-width: 0; /* Prevents overflow issues */
}

.shipping_method_area {
    margin-right: 10px; /* Space between shipping and button */
}

.single-widget.get-button {
    text-align: right;
}

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

.coupon-input-button input#couponInput {
    width: auto;
}


/* Responsive adjustments */
@media (max-width: 991px) {
    .shipping-method-button-container {
        flex-direction: column;
        align-items: stretch;
    }

    .shipping_method_area,
    .single-widget.get-button {
        width: 100%;
        text-align: left;
    }

    .single-widget.get-button {
        margin-top: 10px;
    }
    
    form#payment-form > .row > .col-lg-4 {
        padding: 0;
        width: 100%;
        flex-basis: 100%;
    }
    .shop.checkout .single-widget.get-button .btn{
        font-size: 14px;
    }
}

/* Digital item adjustments */
.single-widget.get-button {
    text-align: right;
    flex: 0 0 auto; /* Prevents it from stretching */
}

@if($order_type == 'Digital')
.shipping-method-button-container {
    justify-content: flex-end !important;
    min-height: 50px; /* Ensures container has height when shipping is hidden */
}
@endif

@if($order_type == 'Digital')
.shipping-item,.shipping_method_area,.shipping_same_as_billing{
    display:none !important;
}    
@endif

</style>

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
                                $club_url = session()->has('redirect_url') ? session()->get('redirect_url'): $club_info['club_url'];
                                $club_cart_url = session()->has('redirect_url') ? str_replace('shop','',$club_url).'cart' : $club_info['club_url'].'?tab=cart';
                            @endphp
                          <a href="{{$club_url}}"> {{$club_info['club_name']}} </a>  &nbsp;&nbsp;>>&nbsp;&nbsp;  <a href="{{$club_cart_url}}">Cart</a>  &nbsp;&nbsp;>>&nbsp;&nbsp;  Checkout
        
                        </div>
                    </div>
    
                @if (Cart::instance('default')->count() != 0)
                    <form class="form orderform" id="payment-form" method="post" action="{{ route('checkout.newmakeorder') }}">
                        @csrf
                        
                        <div class="row">
                            <div class="col-lg-8 col-12 col-65 container">
                        <div class="checkout-form pb-3">
                            <h3 class="mt-3 mb-1">Billing Address</h3>
                            <em>Enter your payment method billing information below</em>
                            <!-- Form -->
                            <div class="row mt-3" id="error-msg"></div>
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
                                            placeholder="" class="required" data-msg="{{__('Billing Full Name')}}" @if(!empty($customer['name'])) @endif>
                                    </div>
                                </div>
                        
                                <div class="col-lg-6 col-md-6 col-12">
                                    <div class="form-group">
                                        <label><i class="fa fa-envelope"></i>{{ __('Email Address') }}<span>*</span></label>
                                        <input value="{{ $customer['email'] }}" id="billing-email"
                                            data-shippingf="shipping-email" type="email" name="email"
                                            placeholder="" class="required" data-msg="{{__('Billing Email')}}" required @if(!empty($customer['email'])) @endif>
                                    </div>
                                </div>
                        
                                <div class="col-lg-6 col-md-6 col-12">
                                    <div class="form-group">
                                        <label><i class="fa fa-phone"></i>{{ __('Phone Number') }}<span>*</span></label>
                                        <input type="number" id="billing-phone" name="phone"
                                            data-shippingf="shipping-phone"
                                            value="{{ str_replace('-', '', $customer['phone']) }}" placeholder=""
                                            maxlength="20" class="required" data-msg="{{__('Billing Phone Number')}}" @if(!empty($customer['phone'])) @endif>
                                    </div>
                                </div>
                        
                                <div class="col-lg-12 col-md-12 col-12 delivery_address_area">
                                    <div class="form-group">
                                        <label><i class="fa fa-map-marker"></i> {{ __('Address') }}<span>*</span></label>
                                        <input type="text" class="location_input required" id="location_input"
                                            data-shippingf="location_input1" name="billing[address]" placeholder=""
                                            value="{{ $customer['address'] }}" data-msg="{{__('Billing Address')}}" @if(!empty($customer['address'])) @endif>
                                    </div>
                                </div>
                        
                                <div class="col-lg-6 col-md-6 col-12 delivery_address_city">
                                    <div class="form-group">
                                        <label><i class="fa fa-building"></i> {{ __('City') }}<span>*</span></label>
                                        <input type="text" class="location_input required" id="location_city"
                                            data-shippingf="location_city1" name="billing[city]" placeholder=""
                                            value="{{ $customer['city'] }}" data-msg="{{__('Billing City')}}" @if(!empty($customer['city'])) @endif>
                                    </div>
                                </div>
                        
                                <div class="col-lg-6 col-md-6 col-12 delivery_address_state">
                                    <div class="form-group">
                                        <label><i class="fa fa-map"></i>{{ __('State') }}<span>*</span></label>
                                        <select class="location_input nice-select add-coupon-check required" id="location_state"
                                            data-shippingf="location_state1" name="billing[state]" data-msg="{{__('Billing State')}}" @if(!empty($customer['state'])) @endif>
                                            @foreach ($states_data as $key => $val)
                                                <option @if ($key == $customer['state']) selected @endif
                                                    value="{{ $key }}">{{ $val }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                        
                                <div class="col-lg-6 col-md-6 col-12 delivery_address_country">
                                    <div class="form-group">
                                        <label><i class="fa fa-globe"></i>{{ __('Country') }}<span>*</span></label>
                                        <select id="billing-country" name="billing[country]"
                                            data-shippingf="billing-country1" class="nice-select required" data-msg="{{__('Billing Country')}}">
                                            <option value="USA">United State</option>
                                        </select>
                                    </div>
                                </div>
                        
                                <div class="col-lg-6 col-md-6 col-12 post_code_area">
                                    <div class="form-group">
                                        <label><i class="fa fa-envelope"></i>{{ __('Zip Code') }}<span>*</span></label>
                                        <input type="text" id="post_code" name="billing[post_code]"
                                            data-shippingf="post_code1" placeholder=""
                                            value="{{ $customer['zip'] }}" class="required" data-msg="{{__('Billing Postal Code')}}" @if(!empty($customer['zip'])) @endif>
                                    </div>
                                </div>
                        
                                <div class="col-lg-12 col-md-12 col-12">
                                    <div class="form-group create-account shipping_same_as_billing">
                                        <input id="shipping_address" name="shipping_same_as_billing" type="checkbox" value="1" checked>
                                        <label for="shipping_address">{{ __('Shipping address same as billing') }}</label>
                                    </div>
                                </div>
                        
                                <!-- Shipping Address Fields - Hidden by default -->
                                <div class="row mt-3 shipping_address_area none" style="display:none">
                                    <div class="col-lg-6 col-md-6 col-12">
                                        <div class="form-group">
                                            <label><i class="fa fa-user"></i>{{ __('Full Name') }}<span>*</span></label>
                                            <input type="text" id="shipping-name" name="shipping[name]"
                                                value="{{ $customer['name'] }}" placeholder="" class="required" data-msg="{{__('Shipping Full Name')}}">
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-12">
                                        <div class="form-group">
                                            <label><i class="fa fa-phone"></i>{{ __('Phone Number') }}<span>*</span></label>
                                            <input type="number" id="shipping-phone" name="shipping[phone]"
                                                value="{{ str_replace('-', '', $customer['phone']) }}" placeholder=""
                                                maxlength="20" class="required" data-msg="{{__('Shipping Phone Number')}}">
                                        </div>
                                    </div>
                                    <div class="col-lg-12 col-md-12 col-12 delivery_address_area">
                                        <div class="form-group">
                                            <label><i class="fa fa-map-marker"></i> {{ __('Address') }}<span>*</span></label>
                                            <input type="text" class="location_input required" id="location_input1"
                                                name="shipping[address]" placeholder=""
                                                value="{{ $customer['address'] }}" data-msg="{{__('Shipping Address')}}">
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-12 delivery_address_city">
                                        <div class="form-group">
                                            <label><i class="fa fa-building"></i> {{ __('City') }}<span>*</span></label>
                                            <input type="text" class="location_input required" id="location_city1"
                                                name="shipping[city]" placeholder=""
                                                value="{{ $customer['city'] }}" data-msg="{{__('Shipping City')}}">
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-12 delivery_address_state">
                                        <div class="form-group">
                                            <label><i class="fa fa-map"></i>{{ __('State') }}<span>*</span></label>
                                            <select class="location_input add-coupon-check2 nice-select required" id="location_state1"
                                                name="shipping[state]" data-msg="{{__('Shipping State')}}">
                                                @foreach ($states_data as $key => $val)
                                                    <option @if ($key == $customer['state']) selected @endif
                                                        value="{{ $key }}">{{ $val }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-12 delivery_address_country">
                                        <div class="form-group">
                                            <label><i class="fa fa-globe"></i>{{ __('Country') }}<span>*</span></label>
                                            <select id="shipping-country" name="shipping[country]"
                                                class="nice-select required" data-msg="{{__('Shipping Country')}}">
                                                <option value="USA">United State</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-12 post_code_area">
                                        <div class="form-group">
                                            <label><i class="fa fa-envelope"></i>{{ __('Zip Code') }}<span>*</span></label>
                                            <input type="text" id="post_code1" class="required post_code_class" name="shipping[post_code]"
                                                placeholder="" value="{{ $customer['zip'] }}" data-msg="{{__('Shipping Postal Code')}}">
                                        </div>
                                    </div>
                                </div>
                        
                                <!-- Shipping Method and Button Container -->
                                <div class="shipping-method-button-container" style="justify-content: flex-end;">
                                   @if ($order_settings->shipping_amount_type != 'distance' && !empty($shipping_options) && count($shipping_options) > 0)
                                        <div class="single-widget shipping_method_area">
                                            <h3 class="mb-1">{{ __('Shipping Method') }}</h3>
                                    
                                            <div class="content">
                                                <div class="checkbox shipping_render_area">
                                                    @foreach ($shipping_options as $method)
                                                        <label class="checkbox-inline shipping_method d-block mb-3"
                                                               for="shipping{{ $method['key'] }}">
                                    
                                                            <input type="radio"
                                                                   name="shipping_method"
                                                                   class="shipping_item me-2"
                                                                   id="shipping{{ $method['key'] }}"
                                                                   value="{{ $method['key'] }}"
                                                                   data-price="{{ $method['price'] }}"
                                                                   data-shippinginfo='@json($method["info"])'
                                                                   {{ $loop->first ? 'checked' : '' }}>
                                    
                                                            <span>{{ $method['label'] }}</span>
                                    
                                                            @if ($method['key'] === 'inperson_pickup' && ($allow_inperson_pickup ?? 0) == 1)
                                                                <a href="javascript:void(0)"
                                                                   class="text-primary small"
                                                                   data-bs-toggle="modal"
                                                                   data-bs-target="#inpersonPickupModal"
                                                                   data-pickup-details='@json($method["info"]["details"] ?? [])'>
                                                                    (details)
                                                                </a>
                                                            @endif
                                                        </label>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    @php 
                                    $club_info = tenant_club_info(); 
                             
                                    @endphp
                            <div class="checkout-consent mt-3">
                                <label class="d-flex align-items-start gap-2">
                                    <input type="checkbox" name="sms_consent" value="1">
                                    <span>
                                        I would like to receive recurring messages including notifications, outreach and order updates from
                                        <strong>{{ $club_info['club_name'] }}</strong>.
                                        By checking the box, you agree to receive {{ $club_info['club_name'] }} text message updates to the phone number provided above.
                                        Consent is not a requirement for purchase. Message and data rates may apply.
                                        Text HELP for help and STOP to cancel. See our
                                        <a href="#" target="_blank">SMS Terms of Service</a> and
                                        <a href="#" target="_blank">Privacy Policy</a>.
                                    </span>
                                </label>
                            </div>
                            <div class="single-widget get-button">
                                <div class="content">
                                    <div class="button">
                                        <input type="hidden" id="shipping_fee" name="shipping_fee">
                                        <input type="hidden" id="total_price" name="total_price">
                                        <button type="submit" class="btn submit_btn submitbtn" id="submit_btn" >{{ __('Continue to Payment') }}</button>
                                    </div>
                                </div>
                            </div>
                            

                        </div>
                            </div>
                        </div>
                                
                                
                            </div>
                            <div class="col-lg-4 col-12 col-35">
                                <div class="order-details container carts-right">
                                    <!-- Order Widget -->
                                    <div class="single-widget">

                                        <div class="">
                                            <h2>{{ __('CART SUMMARY') }}<span class="price" style="color:black"><i
                                                        class="fa fa-shopping-cart"></i>
                                                    <b>{{ Cart::count() }}</b></span></h2>
                                            @foreach (Cart::instance('default')->content() as $item)
                                                <p id="{{$item->rowId}}"><a href="#" class="cart-summary"> <img src="{{ $item->options->preview }}"
                                                            alt="img"><span class="title">{{ $item->name }}
                                                           
						@php if(count($item->options['options'])){
                          echo "<span class='product-description' style='font-size: 9px;display: flow;'>";
						 foreach($item->options['options'] as $option => $selected_option){
                            $product_options = $selected_option->varitionOptions;
                            foreach($selected_option->varitions as $sel_val){
                                $cur_opt_name = $product_options->filter(function ($x) use ($sel_val) {
                                return $x->id == $sel_val->pivot->productoption_id;
                            });
    						   echo "<strong class='attr-variation-style'>".$sel_val->name."</strong>";
                            }
                           						
						 }						
						  echo "</span>";	 						
						 } @endphp


											</span></a><span class="d-qty">{{$item->qty}} </span> 
												@php
                                                    $itemTicketFee = 0;
                                                
                                                    $productKind = DB::table('termmetas')
                                                        ->where('term_id', $item->id)
                                                        ->where('key', 'product_kind')
                                                        ->value('value');
                                                
                                                    $ticketFee = DB::table('termmetas')
                                                        ->where('term_id', $item->id)
                                                        ->where('key', 'ticket_fee')
                                                        ->value('value');
                                                
                                                    if (trim((string) $productKind) === 'event_ticket') {
                                                        $itemTicketFee = 0;
                                                    }
                                                
                                                    $itemDisplayPrice = ($item->price * $item->qty) + $itemTicketFee;
                                                @endphp
                                                
                                                <span class="price">
                                                    <span>{{ get_option('currency_data', true)->currency_icon }}{{ number_format($itemDisplayPrice, 2) }}</span>
                                                </span>
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
                                            <div class="coupon-main-div">
                                                <div class="apply-c-code">
                                                    <p id="show_coupon_error" style="display:none;"></p>
                                                    <div class="coupon-input-button">
                                                        <input type="text" class="c-input" placeholder="Enter coupon" id="couponInput">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                        <button type="button" class="btn btn-primary coupon-btn" id="applyCouponBtn">Apply</button>

                                                        <button style="display:none;" type="button" class="btn btn-danger coupon-btn" id="removeCouponBtn">Remove</button>
                                                    </div>
                                                
                                                </div>
                                            </div>
                                            
                                            <div class="alert alert-success success-sec" role="alert">
                                               <input type="checkbox" id="cover_fee_checkbox" checked value="cover_fee" name="cover_fee_checkbox" />   Yes! I would like to help {{ ucfirst(str_replace(['-', '_'], ' ', tenant()->id)) }} lower their costs by covering the processing fees for this transaction. <span class="learn-more"><a data-bs-toggle="modal" data-bs-target="#transactionModal">Learn More</a> </span>
                                            </div>
                                            
                                            <ul>
                                                <li>{{ __('Subtotal') }}
                                                    <span class="cart_subtotal">
                                                        0.00
                                                    </span>
                                                </li>
                                                <li>(-) {{ __('Discount') }}
                                                    <span class="cart_discount">
                                                        0.00
                                                    </span>
                                                </li>

                                                <li>(+) {{ __('Tax') }}
                                                    <span class="cart_tax">
                                                        0.00
                                                    </span>
                                                </li>
                                                <li class="shipping-item">(+) {{ __('Delivery fee') }}<span class="shipping_fee">0.00</span>
                                                
                                                </li>

                                                <li class="cover-fee alert-success" style="margin: 0 10px;padding: 5px 22px;background-color: #E4FFEE;">(+) {{ __('Covered Fees - thank you') }}<span class="cover_fee_amount">0.00</span>
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
                                 
                                    <!--/ End Order Widget -->

                                    <!--/ End Order Widget -->

                                    
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="wpuid" value="{{ $customer['wpuid'] }}">
                        @if(isset($customer['guest']) && $customer['guest'] != '')
                          <input type="hidden" name="guest" value="1">
                        @endif
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
        <input type="hidden" id="credit_card_fee" value="{{ credit_card_fee(Cart::instance('default')->total() + $shipping_price) }}">
        <input type="hidden" id="booster_platform_fee" value="{{ booster_club_chagre(Cart::instance('default')->total() + $shipping_price) }}">
        <input type="hidden" id="total" value="{{ Cart::instance('default')->total() }}">
        <input type="hidden" id="discount" value="{{ Cart::instance('default')->discount() }}">
        <input type="hidden" id="ticket_fee_total" value="{{ $ticket_fee_total ?? 0 }}">
        <input type="hidden" id="totalWeight" value="{{ Cart::instance('default')->weight() }}">
        <input type="hidden" id="totalItem" value="{{ Cart::instance('default')->count() }}">

        <input type="hidden" id="latitude" value="{{ tenant('lat') }}">
        <input type="hidden" id="longitude" value="{{ tenant('long') }}">
        <input type="hidden" id="city" value="{{ $invoice_data->store_legal_city ?? '' }}">
        <input type="hidden" id="cover_fee" value="{{ $cover_fee }}">
        
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
        
<!-- Modal -->
<div class="modal fade" id="transactionModal" tabindex="-1" aria-labelledby="transactionModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-md modal-dialog-centered">
    <div class="modal-content">

      <div class="modal-header border-0" style="background-color: #9bffd0;">
        <h5 class="modal-title text-success fw-bold w-100 text-center" id="transactionModalLabel" style="font-size: 16px;">
          Covering Transaction Fees Makes A Bigger Impact
        </h5>
        <button type="button" class="btn-close ms-auto me-1" data-bs-dismiss="modal" aria-label="Close"></button>

      </div>

      <div class="modal-body">
        <div class="row align-items-start">
          <div class="col-md-4 text-center mb-3 mb-md-0">
            <img src="{{ asset('checkout/img/big_jar.png') }}" alt="Money Jar" class="img-fluid" style="max-width:170px;">
          </div>
          <div class="col-md-8" style="font-size: 13px;">
            <p>
              Organizations like {{ ucfirst(str_replace(['-', '_'], ' ', tenant()->id)) }} work extremely hard to fundraise and make sure every dollar is spent in the best possible way. 
              Boooostr’s Platform and connected software tools lower existing technology barriers to allow small nonprofits to grow and make a bigger impact.
            </p>
            <p>
              <strong style="display: block;padding-top: 10px;">Covering transaction fees is always optional.</strong> 
              Every time you choose to cover fees, you help ensure {{ ucfirst(str_replace(['-', '_'], ' ', tenant()->id)) }} can continue to access the online tools through Boooostr they need AND help maximize every dollar raised to achieve their funding goals and mission quicker.
            </p>
          </div>
        </div>
      </div>

      <div class="modal-footer border-0 justify-content-center">
        <button type="button" class="btn btn-link text-muted text-decoration-underline" data-bs-dismiss="modal" style="font-size: small;">
          close window
        </button>
      </div>
    </div>
  </div>
</div>

<!-- In-Person Pick Up Modal - Simple layout like Image 1 -->
<div class="modal fade" id="inpersonPickupModal" tabindex="-1" aria-labelledby="inpersonPickupModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content pickup-simple-modal">

      <div class="modal-header pickup-simple-header">
        <h5 class="modal-title" id="inpersonPickupModalLabel">In-Person Pick Up Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body pickup-simple-body">
        <p class="pickup-paragraph" id="pickup_schedule_text"></p>
        <p class="pickup-paragraph" id="pickup_contact_text"></p>

        <div class="pickup-address-wrap">
            <div class="pickup-address-title">Pick Up Address:</div>
            <div class="pickup-address" id="pickup_address_text"></div>
        </div>
    </div>


    </div>
  </div>
</div>
    @endsection
    @push('js')
	<style>
        /* Center the fixed spinner */
        .spinner-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 120%;
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
        

/* Modal pickup */
.pickup-simple-modal{
  border-radius: 8px;
  overflow: hidden;
}

.pickup-simple-header{
  background: #fff;
  border-bottom: 0;
  padding: 18px 22px;
}

.pickup-simple-header .modal-title{
  font-size: 22px;
  font-weight: 700;
  color: #111;
}

.pickup-simple-body{
  padding: 14px 22px 22px 22px;
  font-size: 13px;
  color: #666;
  line-height: 1.55;
}

.pickup-paragraph{
  margin: 8px 0 12px 0;
}

.pickup-note{
  margin-top: 14px;
}

.pickup-address-wrap{
  margin-top: 18px;
}

.pickup-address-title{
  font-weight: 700;
  color: #111;
  margin-bottom: 6px;
}

.pickup-address{
  color: #666;
}
    </style>


     {{-- <script>
       $(document).ready(function(){
            $('.apply-c-code').hide();
            $("#applyCouponCode").click(function(){
                $('.apply-c-code').show();
                $('#hide-coupon-div').html('&nbsp;&nbsp;<i style="color: #00c0ff;">Hide</i>');
            });

            $("#hide-coupon-div").click(function(){
                $('.apply-c-code');
                console.log($('.apply-c-code'));
                // $('#hide-coupon-div').html('');
            });
       });
     </script> --}}
     
        <script type="text/javascript">
            "use strict";
            var get_stripe_paymentIntent_url = "{{ route('checkout.get_stripe_paymentIntent') }}";
            var subtotal = parseFloat($('#subtotal').val());
            
            var tax = parseFloat($('#tax').val());
            
            var total = parseFloat($('#total').val());
            var price = {{ $shipping_price }};
            var credit_card_fee = parseFloat($('#credit_card_fee').val());
            var booster_platform_fee = parseFloat($('#booster_platform_fee').val());
            var cover_fee = parseFloat($('#cover_fee').val());
            var new_total = subtotal;
            var apply_tax_url = "{{ route('checkout.applyTax') }}";
            var store_info = {!! Tenant('club_info') !!};
            var currency_icon = "{{ get_option('currency_data', true)->currency_icon }}";
            var discount = parseFloat($('#discount').val());
        </script>
        @if ($source_code == 'off')
            <script type="text/javascript" src="{{ asset('theme/disable-source-code.js') }}"></script>
        @endif
        <script type="text/javascript" src="{{ asset('checkout/js/new-checkout.js') }}?v=1.0.0"></script>
    @endpush
    @push('js')
        <script src="https://js.stripe.com/v3/"></script>
         <!--<script src="{{ asset('checkout/js/new-stripe.js') }}"></script> -->
        <script>
        $(function () {
            const shippingArea = $('.shipping_address_area');
            const shippingCheckbox = $('#shipping_address');
            const submitBtn = $('#submit_btn');

            // Toggle shipping address visibility
            shippingCheckbox.on('change', function () {
                shippingArea.slideToggle('fast', !$(this).is(':checked'));
            });

            // Set initial state (checked → hidden)
            if (shippingCheckbox.is(':checked')) shippingArea.hide();

            // Handle form submission
            $('#payment-form').on('submit', function () {
                submitBtn
                .prop('disabled', true)
                .text('PLEASE WAIT...')
                .css({
                    backgroundColor: '#4a4a4a',
                    color: 'white',
                    cursor: 'not-allowed'
                });
            });
        });

        </script>
    <script>
$(document).ready(function () {
  $('#inpersonPickupModal').on('show.bs.modal', function (event) {
    const button = $(event.relatedTarget);

    const details = button.data('pickup-details') || {};

    if (!details || Object.keys(details).length === 0) {
      $('#pickup_schedule_text').html('<em>Schedule not configured yet.</em>');
      $('#pickup_contact_text').html('<em>Contact information not available.</em>');
      $('#pickup_address_text').html('<em>Address not configured.</em>');
      return;
    }

    const a1 = details.address_line1 || '';
    const a2 = details.address_line2 || '';
    const city = details.city || '';
    const state = details.state || '';
    const zip = details.zip || '';

    let addressHtml = '';
    if (a1) addressHtml += `${a1}<br>`;
    if (a2) addressHtml += `${a2}<br>`;
    addressHtml += `${city}${city && (state || zip) ? ', ' : ''}${state} ${zip}`.trim();

    $('#pickup_address_text').html(addressHtml || 'Address not configured.');

    const instructionsRaw = (details.instructions || '').toString().trim();

    if (!instructionsRaw) {
      $('#pickup_schedule_text').html('<em>Pick up instructions not available.</em>');
      $('#pickup_contact_text').html('');
      return;
    }


    const instructions = instructionsRaw
      .replace(/\r\n/g, '\n')
      .replace(/\n{3,}/g, '\n\n');


    const scheduleMatch = instructions.match(/In-Person Pick Up is available[\s\S]*?(except holidays\.)/i);
    const contactMatch  = instructions.match(/Please email[\s\S]*?(items\.)/i);

    const scheduleText = scheduleMatch ? scheduleMatch[0].trim() : instructions.split('\n\n')[0].trim();
    const contactText  = contactMatch ? contactMatch[0].trim() : (instructions.split('\n\n')[1] || '').trim();

    $('#pickup_schedule_text').html(scheduleText);
    $('#pickup_contact_text').html(contactText);
  });
});

</script>

    @endpush
