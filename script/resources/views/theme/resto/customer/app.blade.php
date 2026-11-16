@extends('theme.resto.layouts.app')
@section('content')
<!-- Start Breadcrumbs Area -->
<div class="breadcrumbs" style="background-image:url('images/bread-bg.jpg')">
	<div class="container">
		<div class="row">
			<div class="col-lg-6 col-md-8 col-12">
				<div class="breadcrumbs-content">
					<h1 class="page-title">{{ ucwords(Request::segment(2)) }}</h1>
					
				</div>
				<ul class="breadcrumb-nav">
					<li><a href="{{ url('/') }}"><i class="icofont-home"></i> {{ __('Home') }}</a></li>
					<li><i class="icofont-fast-food"></i> {{ Request::segment(2) }}</li>
				</ul>
			</div>
		</div>
	</div>
</div>
<!--/ End Breadcrumbs Area -->

<!-- Start Dashboard Section -->
<section class="dashboard section">
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

							{{-- 5. Reviews (Preserved for store items rating) --}}
							@if(tenant('review_status') == 'on' || tenant('customer_modules') == 'on')
							<li>
								<a class="{{ request()->is('customer/reviews*') ? 'active' : '' }}" href="{{ url('/customer/reviews') }}">
									<i class="icofont-star me-2"></i> {{ __('Reviews') }}
								</a>
							</li>
							@endif
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
			<div class="col-lg-9 col-md-8 col-12">
				<div class="main-content">
					@if (Auth::user()->role_id == 3)
					<div class="row">
						<div class="col-lg-12">
						<div class="customer-button-area">
							<a href="{{ url('/seller/dashboard') }}">Go To Your Panel</a>
						</div>
						</div>
					</div>

					@elseif(Auth::user()->role_id == 5)
					<div class="row">
						<div class="col-lg-12">
						<div class="customer-button-area">
							<a href="{{ url('/rider/dashboard') }}">Go To Your Panel</a>
						</div>
						</div>
					</div>
					@endif
					@yield('customer_content')
					
				</div>
			</div>
		</div>
	</div>
</section>

@if(tenant('push_notification') == 'on' && env('FMC_SERVER_API_KEY') != null)
<!-- End Dashboard Section -->
<div class="notification-button-area notification_button">
	<button type="button" class="notification_buttons"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24"><path fill="none" d="M0 0h24v24H0z"/><path d="M20 17h2v2H2v-2h2v-7a8 8 0 1 1 16 0v7zM9 21h6v2H9v-2z"/></svg></button>
</div>
@endif
@endsection
@push('js')
<script src="{{ asset('admin/js/sweetalert2.all.min.js') }}"></script>
<script src="{{ asset('admin/js/form.js') }}"></script>
@endpush