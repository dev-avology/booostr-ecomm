@extends('theme.resto.customer.app')

@section('customer_content')
{{-- 
    =================================================================================
    Site Builder Standard: Customer Dashboard Home View
    Author / Context: Foodsify & Booostr Central Auth Integration
    Features:
      - 2-Column Responsive Dashboard Layout
      - Purchases & Pending Orders Summary Cards with direct navigation links
      - Recent Orders Table with Invoice links, Status & Payment badges
      - User welcome greeting with Booostr meta username resolution
    =================================================================================
--}}
@php
    $userMeta = !empty(Auth::user()->meta) ? json_decode(Auth::user()->meta, true) : [];
    $displayName = !empty($userMeta['username']) ? $userMeta['username'] : Auth::user()->name;
@endphp

<!-- Start Summary Cards (Site Builder Purchases Architecture) -->
<div class="details-lists mb-4">
	<div class="row g-3">
		<div class="col-lg-6 col-md-6 col-12">
			<!-- Total Orders / Purchases Card -->
			<div class="single-list primary" style="position: relative; padding: 35px 25px 35px 115px !important; border-radius: 8px; margin-bottom: 20px;">
				<div class="list-icon">
					<i class="icofont-shopping-cart"></i>
				</div>
				<div class="d-flex align-items-center justify-content-between flex-wrap" style="gap: 10px;">
					<div>
						<h3 style="color: #ffffff !important; font-size: 32px; font-weight: 700; margin: 0; line-height: 1.1;">
							{{ $total_orders }}
						</h3>
						<span style="color: rgba(255, 255, 255, 0.9) !important; font-size: 14px; font-weight: 500; display: block; margin-top: 4px;">
							{{ __('Purchases / Total Orders') }}
						</span>
					</div>
					<a href="{{ url('/customer/orders') }}" class="btn btn-sm bg-white text-success fw-bold shadow-sm" style="border-radius: 20px; padding: 6px 16px; font-size: 12px; white-space: nowrap; text-decoration: none;">
						{{ __('View All') }} &rarr;
					</a>
				</div>
			</div>
		</div>
		<div class="col-lg-6 col-md-6 col-12">
			<!-- Pending Orders Card -->
			<div class="single-list dark" style="position: relative; padding: 35px 25px 35px 115px !important; border-radius: 8px; margin-bottom: 20px;">
				<div class="list-icon">
					<i class="icofont-sand-clock"></i>
				</div>
				<div class="d-flex align-items-center justify-content-between flex-wrap" style="gap: 10px;">
					<div>
						<h3 style="color: #ffffff !important; font-size: 32px; font-weight: 700; margin: 0; line-height: 1.1;">
							{{ $total_pending_orders }}
						</h3>
						<span style="color: rgba(255, 255, 255, 0.9) !important; font-size: 14px; font-weight: 500; display: block; margin-top: 4px;">
							{{ __('Pending Orders') }}
						</span>
					</div>
					<a href="{{ url('/customer/orders') }}" class="btn btn-sm bg-white text-dark fw-bold shadow-sm" style="border-radius: 20px; padding: 6px 16px; font-size: 12px; white-space: nowrap; text-decoration: none;">
						{{ __('Check Status') }} &rarr;
					</a>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- End Summary Cards -->

<div class="row">
	<div class="col-lg-7 col-md-12 col-12 mb-4">
		<!-- Start Activity Log / Recent Orders -->
		<div class="activity-log dashboard-block p-3 bg-white rounded shadow-sm">
			<div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
				<h3 class="block-title mb-0 fs-5"><i class="icofont-history me-1 text-primary"></i> {{ __('Recent Purchases') }}</h3>
				<a href="{{ url('/customer/orders') }}" class="text-decoration-underline small text-primary">{{ __('View Purchase History') }}</a>
			</div>

			<div class="my-items table-responsive">
				<div class="item-list-title bg-light p-2 rounded mb-2 d-none d-md-block">
					<div class="row align-items-center text-muted small font-weight-bold">
						<div class="col-md-4">
							<p class="mb-0">{{ __('Order') }}</p>
						</div>
						<div class="col-md-3">
							<p class="mb-0">{{ __('Status') }}</p>
						</div>
						<div class="col-md-3">
							<p class="mb-0">{{ __('Payment') }}</p>
						</div>
						<div class="col-md-2">
							<p class="mb-0">{{ __('Date') }}</p>
						</div>
					</div>
				</div>
				@forelse($orders ?? [] as $row)
				<div class="single-item-list border-bottom py-2">
					<div class="row align-items-center">
						<div class="col-md-4 col-12">
							<div class="single-item-inner image-top" data-title="Order No">
								<div class="item-image">
									<div class="content">
										<h4 class="title fs-6 mb-0">
											<a class="text-primary font-weight-bold" href="{{ url('/customer/order/'.$row->id) }}">
												#{{ $row->invoice_no }}
											</a>
										</h4>
										<span class="price render_currency text-success fw-bold">{{ number_format($row->total,2) }}</span>
									</div>
								</div>
							</div>
						</div>

						<div class="col-md-3 col-6 my-1 my-md-0">
							<div class="single-item-inner conditions" data-title="Order Status">
								<span class="badge" style="background-color: {{ $row->orderstatus->slug ?? '#007bff' }}">{{ $row->orderstatus->name ?? __('Processed') }}</span>
							</div>
						</div>

						<div class="col-md-3 col-6 my-1 my-md-0">
							<div class="single-item-inner conditions" data-title="Payment Status">
								@php
								if($row->payment_status == 1){
									$payment_status = __('Paid');
									$payment_badge = 'badge-success bg-success text-white';
								} elseif($row->payment_status == 2){
									$payment_status = __('Pending');
									$payment_badge = 'badge-warning bg-warning text-dark';
								} else{
									$payment_status = __('Payment Fail');
									$payment_badge = 'badge-danger bg-danger text-white';
								}
								@endphp 
								<span class="badge {{ $payment_badge }}"> 
									{{ $payment_status }}
								</span>
							</div>
						</div>

						<div class="col-md-2 col-12 text-md-start text-muted small">
							<div class="single-item-inner category" data-title="Placed At">
								<span>{{ $row->created_at->diffForHumans() }}</span>
							</div>
						</div>
					</div>
				</div>
				@empty
				<div class="text-center py-4 text-muted">
					<i class="icofont-shopping-cart fs-1 d-block mb-2 text-muted"></i>
					<p class="mb-0">{{ __('No purchases found yet.') }}</p>
				</div>
				@endforelse
			</div>
		</div>
		<!-- End Activity Log -->
	</div>

	<div class="col-lg-5 col-md-12 col-12">
		<!-- Start Welcome Card -->
		<div class="recent-items dashboard-block p-4 bg-white rounded shadow-sm">
			<h3 class="block-title fs-5 mb-3 border-bottom pb-2">
				<i class="icofont-user-alt-5 me-1 text-primary"></i> {{ __('Welcome') }}
			</h3>
			<p class="mb-3">
				{{ __('Hello') }} <strong>{{ $displayName }}</strong> 
				<span class="text-muted small">({{ __('not') }} <strong>{{ $displayName }}</strong>? 
				<a class="text-danger" href="javascript:void(0)" onclick="event.preventDefault(); document.getElementById('customer-sidebar-logout-form').submit();">{{ __('Logout') }}</a>)</span>
			</p>

			<p class="text-muted" style="line-height: 1.6;">
				{{ __('From your account dashboard you can view your') }} 
				<a class="text-primary font-weight-bold" href="{{ url('/customer/orders') }}">{{ __('recent purchases') }}</a>, 
				{{ __('manage your') }} 
				<a class="text-primary font-weight-bold" href="{{ url('/customer/edit-profile') }}">{{ __('shipping addresses') }}</a>, 
				{{ __('and update your') }} 
				<a class="text-primary font-weight-bold" href="{{ url('/customer/change-password') }}">{{ __('account password') }}</a>.
			</p>
		</div>
		<!-- End Welcome Card -->
	</div>
</div>
@endsection
