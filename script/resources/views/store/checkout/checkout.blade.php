@extends('layouts.checkout')
@section('content')
<!-- Topbar Area -->
<div class="topbar-area">
	<div class="container">
		<div class="row">
			<div class="col-lg-12 col-md-12 col-12">
				<!-- Topbar Right -->
				<div class="topbar-right">
					<ul class="topbar-right-inner">
						<!-- Topbar Language -->
						@if(tenant('customer_modules') == 'on')
						<li class="accounts-top-btn"><a href="{{ !Auth::check() ? '#' : url('/customer/dashboard') }}"><i class="icofont-user-male"></i><span>{{ !Auth::check() ? __('My Account') : Auth::user()->name }}</span></a>
							@if(!Auth::check())
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
				</div>
			</div>
		</div>
	</div>
</div>
<!-- Start Checkout -->
<section class="shop checkout section checkout-main">
	<div class="checkout-container">
		<h1 class="page-title">{{ $page_data->cart_page_title ?? 'Checkout' }}</h1>
		@if(Cart::instance('default')->count() != 0)
		<form class="form orderform" id="payment-form" method="post" action="{{ route('checkout.makeorder') }}">
			@csrf
			<div class="row">
				<div class="col-lg-8 col-12 col-65 container">
					<div class="checkout-form  pb-3">
						<h3 class="mt-3 mb-1">Billing Address</h3>
						<em>Please create your account to check your order status quickly</em>
						<!-- Form -->

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
									<input type="text" name="name" value="{{ Auth::check() ? Auth::user()->name : '' }}" placeholder="" required="required">
								</div>
							</div>

							<div class="col-lg-6 col-md-6 col-12">
								<div class="form-group">
									<label><i class="fa fa-envelope"></i>{{ __('Email Address') }}<span>*</span></label>
									<input value="{{ Auth::check() ? Auth::user()->email : '' }}" type="email" name="email" placeholder="" required="required">
								</div>
							</div>
							<div class="col-lg-6 col-md-6 col-12">
								<div class="form-group">
									<label><i class="fa fa-address-card-o"></i>{{ __('Phone Number') }}<span>*</span></label>
									<input type="number" name="phone" value="{{ Auth::check() ? Auth::user()->phone : '' }}" placeholder="" required="required" maxlength="20">
								</div>
							</div>
							<div class="col-lg-6 col-md-6 col-12 delivery_address_area">
								<div class="form-group">
									<label><i class="fa fa-address-card-o"></i> {{ __('Address') }} <span>*</span></label>
									<input type="text" class="location_input" id="location_input" name="address" placeholder="" required="required" value="{{ $meta->address ?? '' }}">
								</div>
							</div>
							<div class="col-lg-6 col-md-6 col-12 delivery_address_city">
								<div class="form-group">
									<label><i class="fa fa-institution"></i> {{ __('City') }} <span>*</span></label>
									<input type="text" class="location_input" id="location_city" name="city" placeholder="" required="required" value="{{ $meta->city ?? '' }}">
								</div>
							</div>
							<div class="col-lg-6 col-md-6 col-12 delivery_address_state">
								<div class="form-group">
									<label> {{ __('State') }} <span>*</span></label>
									<input type="text" class="location_input" id="location_state" name="state" placeholder="" required="required" value="{{ $meta->state ?? '' }}">
								</div>
							</div>

							<div class="col-lg-6 col-md-6 col-12 post_code_area">
								<div class="form-group">
									<label>{{ __('Postal Code') }}<span>*</span></label>
									<input type="text" name="post_code" placeholder="" value="{{ $meta->post_code ?? '' }}" required="required">
								</div>
							</div>

							@if($order_settings->shipping_amount_type == 'distance')
							<div class="col-lg-12 col-md-12 col-12 map_area">
								<div class="form-group">
									<p class="text-danger alert_area"></p>
									<div class="map-canvas h-300" id="map-canvas">

									</div>

								</div>
							</div>
							@endif

							@if(Auth::check() == false)
							<div class="col-lg-6 col-md-6 col-12">
								<div class="form-group create-account">
									<input id="create_account" type="checkbox" value="1">
									<label for="create_account">{{ __('Create an account?') }}</label>
								</div>
								<div class="form-group  password_area none">
									<input type="password" name="password" placeholder="Password">
								</div>
							</div>
							@endif
						</div>
					</div>

					<!-- Shopping Cart -->
					<div class="shopping-cart section">
						<div class="">
							<div class="row">
								<div class="col-12">
									<!-- Total Amount -->
									<div class="card">
										<div class="card-body">
											<div class="px-4">
												<div class="form-row">
													<label for="card-element">
														{{ __('Credit or debit card') }}
													</label>
													<div id="card-element">
														<!-- A Stripe Element will be inserted here. -->
													</div>
													<!-- Used to display form errors. -->
													<div id="card-errors" role="alert"></div>
													<button type="submit" class="btn btn-primary btn-lg w-100 mt-4" id="submit_btn">{{ __('Submit Payment') }}</button>
												</div>

											</div>
										</div>
									</div>
									<!--/ End Total Amount -->
								</div>
							</div>
						</div>
						<input type="hidden" id="publishable_key" value="{{ $payment_data['publishable_key'] }}">
						<input type="hidden" id="stripesecret_key" value="{{ $payment_data['secret_key'] }}">
					</div>
					<!--/ End Shopping Cart -->

				</div>
				<div class="col-lg-4 col-12 col-35">
					<div class="order-details container carts-right">
						<!-- Order Widget -->
						<div class="single-widget">

							<div class="">
								<h2>{{ __('CART  SUMMARY') }}<span class="price" style="color:black"><i class="fa fa-shopping-cart"></i> <b>{{Cart::instance('default')->countItems()}}</b></span></h2>
								@foreach(Cart::instance('default')->content() as $item)
								<p><a href="#"> <img src="{{$item->options->preview}}" alt="img">{{$item->name}}</a> <span class="price">{{ get_option('currency_data',true)->currency_icon }}{{$item->price}}</span></p>
								@endforeach
								<hr>
							</div>
							@if($pickup_order == 'on')
							<div class="order-type-section">
								<input type="radio" name="order_method" id="is_pickup" class="order_method {{ $pickup_order == 'off' ? 'none' : '' }}" value="pickup" @if($order_method=='pickup' ) checked="" @endif>
								<label for="is_pickup">{{ __('pickup') }}</label>

								<input type="radio" name="order_method" id="is_pickup1" class="order_method" value="delivery" @if($order_method=='delivery' ) checked="" @endif>
								<label for="is_pickup1">{{ __('delivery') }}</label>

							</div>
							@else
							<input type="hidden" name="order_method" class="order_method none" value="delivery">
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
									<li>(+) {{ __('Delivery fee') }}<span class="shipping_fee">0.00</span></li>

									<li class="last">{{ __('Total') }}<span class="cart_total">0.00</span></li>
								</ul>
							</div>
						</div>
						@if($order_settings->shipping_amount_type != 'distance')
						<div class="single-widget shipping_method_area">
							<h2>{{ __('Shipping Method') }}</h2>
							<div class="content">
								<div class="checkbox shipping_render_area">
									@foreach($shipping_methods as $shipping_method)
									<label class="checkbox-inline shipping_method" for="shipping{{$shipping_method->id}}">
										<input name="shipping_method" class="shipping_item" value="{{$shipping_method->id}}" data-price="{{$shipping_method->slug}}" id="shipping{{$shipping_method->id}}" type="radio"> {{$shipping_method->name}}
									</label>
									@endforeach
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
									<input type="hidden" id="my_lat" name="my_lat" value="{{ $meta->lat ?? '' }}">
									<input type="hidden" id="my_long" name="my_long" value="{{ $meta->long ?? '' }}">
									<button type="submit" class="btn submit_btn submitbtn">{{ __('Proceed to checkout') }}</a>
								</div>
							</div>
						</div>
						<!--/ End Button Widget -->
					</div>
				</div>
			</div>
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
<input type="hidden" id="total" value="{{ Cart::instance('default')->total() }}">

<input type="hidden" id="latitude" value="{{ tenant('lat') }}">
<input type="hidden" id="longitude" value="{{ tenant('long') }}">
<input type="hidden" id="city" value="{{ $invoice_data->store_legal_city ?? '' }}">

@endsection
@push('js')
<script type="text/javascript">
	"use strict";

	var subtotal = parseFloat($('#subtotal').val());
	var tax = parseFloat($('#tax').val());
	var total = parseFloat($('#total').val());
	var new_total = subtotal;
</script>
@if($source_code == 'off')
<script type="text/javascript" src="{{ asset('theme/disable-source-code.js') }}"></script>
@endif
@if($order_settings->shipping_amount_type == 'distance')

<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCmimJcxCmMIgBR0G0UKmQAgfr7RSS8pDg&libraries=places&radius=5&location={{ tenant('lat') }}%2C{{ tenant('long') }}&callback=initialize"></script>
<!-- <script async defer src="https://maps.googleapis.com/maps/api/js?key={{ $order_settings->google_api ?? '' }}&libraries=places&radius=5&location={{ tenant('lat') }}%2C{{ tenant('long') }}&callback=initialize"></script> -->
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
		var lati = "{{tenant('lat')}}";
	}
	if (localStorage.getItem('long') !== null) {
		var longlat = localStorage.getItem('long');
		$('#my_long').val(longlat)
	} else {
		var longlat = "{{tenant('long')}}";
	}

	const maxRange = "{{$order_settings->google_api_range ?? 0}}";
	const resturentlocation = "{{ $invoice_data->store_legal_address ?? '' }}";
	const feePerkilo = "{{$order_settings->delivery_fee ?? 0}}";
	var mapOptions;
	var map;
	var marker;
	var searchBox;
	var city;
</script>

<script type="text/javascript" src="{{ asset('theme/resto/js/google-api.js') }}"></script>
@endif

<script type="text/javascript" src="{{ asset('checkout/js/checkout.js') }}"></script>
@endpush
@push('js')
<script src="https://js.stripe.com/v3/"></script>
<script src="{{ asset('checkout/js/stripe.js') }}"></script>

@endpush