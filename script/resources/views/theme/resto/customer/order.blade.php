@extends('theme.resto.layouts.app')
@push('css')
<style>
	/* Clean Order View Styles */
	.order-card-header {
		padding: 24px 30px 20px !important;
		border-bottom: 1px solid #eee !important;
		margin-bottom: 24px !important;
	}

	.order-view-title-clean {
		font-size: 18px !important;
		font-weight: 500 !important;
		color: #081828 !important;
		margin: 0 !important;
		line-height: 1.3 !important;
	}

	.order-view-title-clean b {
		font-weight: 500 !important;
	}

	.order-view-title-clean span {
		background-image: none !important;
		background: none !important;
		color: var(--primary, #0A8CDE) !important;
		font-weight: 700 !important;
		display: inline-block !important;
	}

	.btn-back-history {
		display: inline-flex !important;
		align-items: center !important;
		gap: 6px !important;
		padding: 6px 14px !important;
		border-radius: 6px !important;
		font-size: 13px !important;
		font-weight: 500 !important;
		color: var(--primary, #0A8CDE) !important;
		background: #F0F5FD !important;
		border: 1px solid #D6E4F8 !important;
		text-decoration: none !important;
		transition: all 0.2s ease !important;
		margin-bottom: 0 !important;
	}

	.btn-back-history:hover {
		background: var(--primary, #0A8CDE) !important;
		color: #ffffff !important;
		border-color: var(--primary, #0A8CDE) !important;
	}

	.rating-main {
		display: none !important;
	}

	/* Order Status Badges */
	.order-status-badge {
		font-size: 12.5px !important;
		font-weight: 600 !important;
		padding: 6px 12px !important;
		border-radius: 20px !important;
		display: inline-block !important;
	}

	.order-badge-pending {
		background-color: #fffbeb !important;
		color: #b45309 !important;
		border: 1px solid #fde68a !important;
	}

	.order-badge-processing {
		background-color: #eff6ff !important;
		color: #1d4ed8 !important;
		border: 1px solid #bfdbfe !important;
	}

	.order-badge-completed {
		background-color: #ecfdf5 !important;
		color: #047857 !important;
		border: 1px solid #a7f3d0 !important;
	}

	.order-badge-cancelled {
		background-color: #fef2f2 !important;
		color: #b91c1c !important;
		border: 1px solid #fecaca !important;
	}

	/* Payment Status Badges */
	.payment-status-badge {
		font-size: 12.5px !important;
		font-weight: 600 !important;
		padding: 6px 12px !important;
		border-radius: 20px !important;
		display: inline-flex !important;
		align-items: center !important;
	}

	.payment-badge-paid {
		background-color: #ecfdf5 !important;
		color: #047857 !important;
		border: 1px solid #a7f3d0 !important;
	}

	.payment-badge-refunded {
		background-color: #fef2f2 !important;
		color: #dc2626 !important;
		border: 1px solid #fecaca !important;
	}

	.payment-badge-processing {
		background-color: #eff6ff !important;
		color: #1d4ed8 !important;
		border: 1px solid #bfdbfe !important;
	}

	.payment-badge-pending {
		background-color: #fffbeb !important;
		color: #b45309 !important;
		border: 1px solid #fde68a !important;
	}

	.payment-badge-cancelled {
		background-color: #f1f5f9 !important;
		color: #64748b !important;
		border: 1px solid #cbd5e1 !important;
	}

	.payment-badge-incomplete {
		background-color: #fff7ed !important;
		color: #c2410c !important;
		border: 1px solid #ffedd5 !important;
	}

	/* Total Amount Block */
	.total-am-top .total-amount {
		background: var(--primary, #0A8CDE) !important;
		border-radius: 8px !important;
		color: #ffffff !important;
		box-shadow: 0 4px 12px rgba(10, 140, 222, 0.25) !important;
	}

	.total-am-top .total-amount span {
		color: #ffffff !important;
	}

	/* Order Summary key-value rows: flex layout matching Your Order Details */
	.customer-order-details.order-sum .order-d-list li {
		display: flex !important;
		justify-content: space-between !important;
		align-items: center !important;
		padding: 10px 0 !important;
		margin-bottom: 0 !important;
		border-bottom: 1px solid #f1f5f9 !important;
		line-height: 1.5 !important;
	}

	.customer-order-details.order-sum .order-d-list li:last-child {
		border-bottom: none !important;
		padding-bottom: 0 !important;
	}

	.customer-order-details.order-sum .order-d-list b {
		display: inline-block !important;
		color: #081828 !important;
		font-size: 14px !important;
		font-weight: 500 !important;
		margin: 0 !important;
	}

	.customer-order-details.order-sum .order-d-list span {
		display: inline-block !important;
		color: #475569 !important;
		font-size: 14px !important;
		font-weight: 500 !important;
		text-align: right !important;
		margin: 0 !important;
	}

	/* Item List Row & Content Layout */
	.order-view-list .item-image {
		display: flex !important;
		align-items: flex-start !important;
		gap: 14px !important;
	}
	.order-view-list .item-image img {
		width: 52px !important;
		height: 52px !important;
		object-fit: cover !important;
		border-radius: 6px !important;
		border: 1px solid #e2e8f0 !important;
		flex-shrink: 0 !important;
	}
	.order-view-list .item-image .content {
		flex: 1 !important;
		min-width: 0 !important;
		top: 0 !important;
	}
	.order-view-list .item-image .content .title a {
		font-size: 14.5px !important;
		font-weight: 600 !important;
		color: #0f172a !important;
		line-height: 1.4 !important;
	}
	.order-view-list .item-image .content .title a:hover {
		color: var(--primary, #0A8CDE) !important;
	}
	.order-view-list .order-item-variations {
		margin-top: 4px !important;
	}
	.order-view-list .variation-item-row {
		font-size: 12.5px !important;
		line-height: 1.4 !important;
		color: #64748b !important;
		margin-top: 2px !important;
	}
	.order-view-list .variation-item-row strong {
		color: #334155 !important;
		font-weight: 600 !important;
		text-transform: capitalize !important;
	}
</style>
@endpush
@section('content')
{{-- 
    =================================================================================
    Page Banner (Site Builder standard component matching product & cart page)
    Static title & description passed directly without table queries
    =================================================================================
--}}
@include('theme.resto.components.page-banner', [
    'title' => __('Order') . ' #' . $info->invoice_no,
    'description' => __('View order details, delivery address, items, and download your invoice.'),
    'breadcrumbs' => [
        ['url' => url('/'), 'label' => __('Home'), 'icon' => 'icofont-home'],
        ['url' => url('/customer/dashboard'), 'label' => __('Dashboard'), 'icon' => 'icofont-dashboard-web'],
        ['url' => '', 'label' => '#' . $info->invoice_no, 'icon' => 'icofont-file-document'],
    ]
])
@php
$ordermeta = json_decode($info->ordermeta->value ?? '');
$currencyIcon = '$';
$currencyPosition = 'left';
$cover_fee = isset($ordermeta->cover_fee) ? (float) $ordermeta->cover_fee : 0;

// Order Status styling
$statusName = $info->orderstatus->name ?? __('Pending');
$statusSlug = $info->orderstatus->slug ?? '';
$statusLower = strtolower($statusName);
$orderStatusClass = 'order-badge-pending';

if (strpos($statusLower, 'complete') !== false || strpos($statusLower, 'deliver') !== false) {
    $orderStatusClass = 'order-badge-completed';
} elseif (strpos($statusLower, 'cancel') !== false || strpos($statusLower, 'fail') !== false) {
    $orderStatusClass = 'order-badge-cancelled';
} elseif (strpos($statusLower, 'process') !== false) {
    $orderStatusClass = 'order-badge-processing';
}

// Payment Status styling
if ($info->payment_status == 1) {
    $paymentText = __('Paid');
    $paymentClass = 'payment-badge-paid';
    $paymentIcon = 'icofont-check-circled';
} elseif ($info->payment_status == 5) {
    $paymentText = __('Refunded');
    $paymentClass = 'payment-badge-refunded';
    $paymentIcon = 'icofont-reply';
} elseif ($info->payment_status == 4) {
    $paymentText = __('Processing');
    $paymentClass = 'payment-badge-processing';
    $paymentIcon = 'icofont-refresh';
} elseif ($info->payment_status == 2) {
    $paymentText = __('Pending');
    $paymentClass = 'payment-badge-pending';
    $paymentIcon = 'icofont-clock-time';
} elseif ($info->payment_status == 3) {
    $paymentText = __('Incomplete');
    $paymentClass = 'payment-badge-incomplete';
    $paymentIcon = 'icofont-exclamation-circle';
} elseif ($info->payment_status == 0) {
    $paymentText = __('Cancelled');
    $paymentClass = 'payment-badge-cancelled';
    $paymentIcon = 'icofont-close-circled';
} else {
    $paymentText = __('Pending');
    $paymentClass = 'payment-badge-pending';
    $paymentIcon = 'icofont-clock-time';
}
@endphp
<!-- Start Dashboard Section -->
<section class="dashboard order-detail section">
	<div class="container">
		<div class="row">
			<div class="col-lg-3 col-md-4 col-12">
				<!-- Start Dashboard Sidebar -->
				<div class="dashboard-sidebar">
					<div class="user-image">
						@php
							$userMeta = !empty(Auth::user()->meta) ? json_decode(Auth::user()->meta, true) : [];
							$displayName = !empty($userMeta['username']) ? $userMeta['username'] : Auth::user()->name;
						@endphp
						<h3>{{ $displayName }}</h3>
					</div>
					<div class="dashboard-menu">
						<ul>
							{{-- 1. Dashboard Home --}}
							<li>
								<a class="{{ request()->is('customer/dashboard*') ? 'active' : '' }}" href="{{ url('/customer/dashboard') }}">
									<i class="icofont-dashboard-web me-2"></i> {{ __('Dashboard') }}
								</a>
							</li>

							{{-- 2. Edit Profile --}}
							<li>
								<a class="{{ (request()->is('customer/edit-profile*') || request()->is('customer/settings*')) && !request()->is('customer/change-password*') ? 'active' : '' }}" href="{{ url('/customer/edit-profile') }}">
									<i class="icofont-ui-user me-2"></i> {{ __('Edit Profile') }}
								</a>
							</li>

							{{-- 3. Change Password --}}
							<li>
								<a class="{{ request()->is('customer/change-password*') ? 'active' : '' }}" href="{{ url('/customer/change-password') }}">
									<i class="icofont-key me-2"></i> {{ __('Change Password') }}
								</a>
							</li>

							{{-- 4. Purchase History --}}
							<li>
								<a class="{{ request()->is('customer/orders*') || request()->is('customer/purchase-history*') || request()->is('customer/order/*') ? 'active' : '' }}" href="{{ url('/customer/orders') }}">
									<i class="icofont-shopping-cart me-2"></i> {{ __('Purchase History') }}
								</a>
							</li>
						</ul>
						<div class="button mt-3">
							<a class="btn alt-btn w-100" href="javascript:void(0)" onclick="event.preventDefault(); document.getElementById('customer-sidebar-logout-form').submit();">
								<i class="icofont-logout me-1"></i> {{ __('Logout') }}
							</a>
							<form id="customer-sidebar-logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
								@csrf
							</form>
						</div>
					</div>
				</div>
				<!-- Start Dashboard Sidebar -->
			</div>
			<div class="col-lg-6 col-md-9 col-12">
				<div class="main-content">
					<div class="dashboard-block mt-0">
						<div class="order-card-header">
							<div class="mb-3">
								<a href="{{ url('/customer/orders') }}" class="btn-back-history">
									<i class="icofont-arrow-left me-1"></i> {{ __('Back to Purchase History') }}
								</a>
							</div>
							<div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
								<h3 class="order-view-title-clean mb-0">
									<b>{{ __('Order Number') }} <span>#{{ $info->invoice_no }}</span></b>
								</h3>
								<span class="badge order-status-badge {{ $orderStatusClass }}">
									{{ $statusName }}
								</span>
							</div>
						</div>
						<!-- Start Items Area -->
						<div class="my-items">
							<!-- Start Item List Title -->
							<div class="item-list-title">
								<div class="row align-items-center">
									<div class="col-lg-5 col-md-5 col-12">
										<p>{{ __('Item summary') }}</p>
									</div>
									<div class="col-lg-2 col-md-2 col-12">
										<p>{{ __('QTY') }}</p>
									</div>
									<div class="col-lg-2 col-md-2 col-12">
										<p>{{ __('Price') }}</p>
									</div>
									<div class="col-lg-3 col-md-3 col-12 align-right">
										<p>{{ __('Total Price') }}</p>
									</div>
								</div>
							</div>
							<!-- End List Title -->
							@foreach($info->orderitemswithpreview ?? [] as $row)
							@php
							$variations=json_decode($row->info);
							$options=$variations->options ?? [];
							@endphp
							<!-- Start Single List -->
							<div class="single-item-list  order-view-list">
								<div class="row align-items-center">
									<div class="col-lg-5 col-md-5 col-12">
										<div class="item-image">
											<img src="{{ asset($row->termwithpreview->preview->value ?? 'uploads/default.png') }}" alt="{{ $row->term->title ?? '' }}"/>
											<div class="content">
												<h3 class="title"><a href="{{ url('/product/'.$row->term->slug ?? '') }}">{{ $row->term->title ?? '' }}</a> </h3>

												@if(!empty($options) && (is_array($options) || is_object($options)) && count((array)$options) > 0)
													<div class="order-item-variations">
														@foreach ($options as $key => $item)
															@php
																$optValues = [];
																if (is_array($item) || is_object($item)) {
																	foreach ($item as $r) {
																		$valName = is_object($r) ? ($r->name ?? '') : (is_array($r) ? ($r['name'] ?? '') : (string)$r);
																		if (!empty($valName)) {
																			$optValues[] = $valName;
																		}
																	}
																} elseif (is_scalar($item)) {
																	$optValues[] = (string)$item;
																}
															@endphp
															@if(count($optValues) > 0)
																<div class="variation-item-row">
																	<strong>{{ $key }}:</strong> {{ implode(', ', $optValues) }}
																</div>
															@endif
														@endforeach
													</div>
												@endif
												<div class="rating-main" style="display: none !important;">
													@php
													$stars=0;
													$comment ='';
													if(array_key_exists($row->term_id,$reviews)){
														$stars=$reviews[$row->term_id]['rating'];
														$comment=$reviews[$row->term_id]['comment'];
													}
													@endphp
													<a href="javascript:void(0)" data-star="{{ $stars }}" data-comment="{{ $comment }}" data-action="{{ url('/customer/order-make-rating/'.$info->id.'/'.$row->term_id.'/'.$row->id) }}" class="text-secondary make_review" data-bs-toggle="modal" data-bs-target="#exampleModal">
														<ul class="rating">
															@for($i=1; $i <= 5; $i++)  
																<li><i class="icofont-star {{ $i <= $stars ? 'star' : '' }} "></i></li>
															@endfor
															
															
														</ul>
													</a>
												</div>
											</div>
										</div>
									</div>
									<div class="col-lg-2 col-md-2 col-4">
										<p class="it-quantity">x {{ $row->qty }}</p>
									</div>
									<div class="col-lg-2 col-md-2 col-4">
										<p class="it-price">{{ $currencyIcon }}{{ number_format($row->amount, 2) }}</p>
									</div>
									<div class="col-lg-3 col-md-3 col-4 align-right">
										<p class="total-price">{{ $currencyIcon }}{{ number_format($row->amount * $row->qty, 2) }}</p>
									</div>
								</div>
							</div>
							<!-- End Single List -->
							@endforeach

						</div>
						<!-- End Items Area -->
					</div>
					<!-- Customer details -->
					<div class="customer-order-details dashboard-block mt-30">
						<h3 class="orderd-title">{{ __('Your') }} <span>{{ __('Order details') }}</span></h3>
						<ul class="order-d-list">
							<li><b>{{ __('Customer Name') }}</b> <span>{{ $ordermeta->name ?? '' }}</span></li>
							@if(!empty($ordermeta->email))
							<li><b>{{ __('Customer Email') }}</b> <span>{{ $ordermeta->email }}</span></li>
							@endif
							<li><b>{{ __('Phone Number') }}</b> <span>{{ $ordermeta->phone ?? '' }}</span></li>

							<li><b>{{ __('Order Status') }}</b> <span class="badge order-status-badge {{ $orderStatusClass }}">{{ $statusName }}</span></li>
							<li><b>{{ __('Payment Status') }}</b> <span class="badge payment-status-badge {{ $paymentClass }}"><i class="{{ $paymentIcon }} me-1"></i>{{ $paymentText }}</span></li>
							<li><b>{{ __('Total') }}</b> <span>{{ $currencyIcon }}{{ number_format($info->total ?? 0, 2) }}</span></li>
							@if(!empty($ordermeta->note))
							<li><b>{{ __('Note') }} :</b> <span>{{ $ordermeta->note }}</span></li>
							@endif
						</ul>
					</div>
					<!-- End Customer details -->
				</div>
			</div>
			<div class="col-lg-3 col-12">
				<!-- Order Summary -->
				<div class="customer-order-details order-sum dashboard-block">
					<h3 class="orderd-title">{{ __('Order') }} <span>{{ __('summary') }}</span></h3>
					<ul class="order-d-list">
						<li><b>{{ __('Order Created') }}</b> <span>{{ $info->created_at->format('M d, Y') }}</span></li>
						@if(!empty($info->shippingwithinfo))
						@if(!empty($info->shippingwithinfo->rider))
						<li><b>{{ __('Order Security Code') }} </b> <span><b>({{ $info->shippingwithinfo->tracking_no ?? '' }})</b></span></li>
						@endif
						@endif
						<li><b>{{ __('Type') }} </b> <span>{{ ucwords(str_replace('_',' ',$info->order_method ?? '')) }}</span></li>

						@php
							$orderSubtotal = 0;
							foreach($info->orderitemswithpreview ?? [] as $itemRow) {
								$orderSubtotal += ($itemRow->amount * $itemRow->qty);
							}
							$shippingFee = !empty($info->shippingwithinfo->shipping_price) ? (float) $info->shippingwithinfo->shipping_price : 0;
						@endphp

						<li><b>{{ __('Subtotal') }}</b> <span>{{ $currencyIcon }}{{ number_format($orderSubtotal, 2) }}</span></li>

						@if($info->discount > 0)
						<li><b>{{ __('Discount') }}</b> <span>-{{ $currencyIcon }}{{ number_format($info->discount, 2) }}</span></li>
						@endif

						<li><b>{{ __('Tax') }}</b> <span>{{ $currencyIcon }}{{ number_format($info->tax, 2) }}</span></li>

						@if($shippingFee > 0)
						<li><b>{{ __('Shipping Fee') }}</b> <span>{{ $currencyIcon }}{{ number_format($shippingFee, 2) }}</span></li>
						@endif

						@if($cover_fee > 0)
						<li><b>{{ __('Supporter Covered Fees') }}</b> <span>{{ $currencyIcon }}{{ number_format($cover_fee, 2) }}</span></li>
						@endif

					</ul>
				</div>
				<!-- End Order Summary -->

				<!-- Total Amount -->
				<div class="customer-order-details order-sum total-am-top dashboard-block">

					<h4 class="total-amount">{{ __('Total Amount') }} <span>{{ $currencyIcon }}{{ number_format($info->total, 2) }}</span></h4>

				</div>
				<!-- End Total Amount -->
			</div>
		</div>
	</div>
</section>
<!-- End Dashboard Section -->

<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel">{{ __('Leave a feedback') }}</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<form method="post" class="form review_form ajaxform_with_reload">
				@csrf
			<div class="modal-body">
				<div class="row">
					<div class="col-sm-12">
						<label for="recipient-name" class="col-form-label">{{ __('Rating') }}</label>
						<div class="product-availity">
							<select name="rating" class="rating_option">
								<option value="5">{{ __('5 Star') }}</option>
								<option value="4">{{ __('4 Star') }}</option>
								<option value="3">{{ __('3 Star') }}</option>
								<option value="2">{{ __('2 Star') }}</option>
								<option value="1">{{ __('1 Star') }}</option>
							</select>
						</div>
					</div>
					<div class="col-sm-12">
						<label for="feedback">{{ __('Feedback') }}</label>
						<textarea id="feedback" maxlength="200" class="form-control feedback" name="feedback"></textarea>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
				<button type="submit" class="btn btn-primary basicbtn">{{ __('Leave') }}</button>
			</div>
			</form>
		</div>
	</div>
</div>
@endsection
@push('js')
<script src="{{ asset('admin/js/sweetalert2.all.min.js') }}"></script>
<script src="{{ asset('admin/js/form.js') }}"></script>
@if(!empty($info->shippingwithinfo))
@if(!empty($info->shippingwithinfo->lat) && !empty($info->shippingwithinfo->long))

@php
$order_settings=get_option('order_settings',true);
@endphp

<script async defer src="https://maps.googleapis.com/maps/api/js?key={{ $order_settings->google_api ?? '' }}&libraries=places&sensor=false&callback=initialise"></script>
<script>
	"use strict";
  //if order type home delivery
  var resturent_lat = {{ tenant('lat') }};
  var resturent_long  = {{ tenant('long') }};
  var customer_lat = {{ $info->shippingwithinfo->lat ?? '' }};
  var customer_long = {{ $info->shippingwithinfo->long ?? '' }};
  var resturent_icon= '{{ asset('uploads/resturent.png') }}';
  var user_icon= '{{ asset('uploads/userpin.png') }}';
  var customer_name= '{{ Auth::user()->name }}';
  var resturent_name= '{{ tenant('store_name') }}';
  var mainUrl= "{{ url('/') }}";
</script>
<script src="{{ asset('admin/js/orderview.js') }}"></script>


@endif
@endif
@endpush