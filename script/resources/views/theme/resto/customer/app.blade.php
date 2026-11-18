@extends('theme.resto.layouts.app')
@section('content')
{{-- 
    =================================================================================
    Page Banner (Site Builder standard component matching product & cart page)
    Static title & description mapped per dashboard section without table queries
    =================================================================================
--}}
@php
    $segment = Request::segment(2);
    $dashboardTitle = __('Dashboard');
    $dashboardDesc = __('Manage your orders, profile details, and account settings.');

    if ($segment == 'orders' || $segment == 'purchase-history') {
        $dashboardTitle = __('Purchase History');
        $dashboardDesc = __('Track, review, and view invoices for all your past purchases.');
    } elseif ($segment == 'edit-profile' || $segment == 'settings') {
        $dashboardTitle = __('Edit Profile');
        $dashboardDesc = __('Update your personal information, contact info, and delivery address.');
    } elseif ($segment == 'change-password') {
        $dashboardTitle = __('Change Password');
        $dashboardDesc = __('Keep your account secure with a strong and updated password.');
    } elseif ($segment == 'reviews') {
        $dashboardTitle = __('Reviews');
        $dashboardDesc = __('View and manage your product reviews and ratings.');
    }

    $dashboardCrumbs = [
        ['url' => url('/'), 'label' => __('Home'), 'icon' => 'icofont-home']
    ];

    if ($segment == 'dashboard' || empty($segment)) {
        $dashboardCrumbs[] = ['url' => '', 'label' => __('Dashboard'), 'icon' => 'icofont-dashboard-web'];
    } else {
        $dashboardCrumbs[] = ['url' => url('/customer/dashboard'), 'label' => __('Dashboard'), 'icon' => 'icofont-dashboard-web'];
        $dashboardCrumbs[] = ['url' => '', 'label' => $dashboardTitle, 'icon' => 'icofont-navigation-menu'];
    }
@endphp

@include('theme.resto.components.page-banner', [
    'title' => $dashboardTitle,
    'description' => $dashboardDesc,
    'breadcrumbs' => $dashboardCrumbs,
])

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

							{{-- 3. Change Password (Customers only) --}}
							@if(Auth::user()->role_id != 3)
							<li>
								<a class="{{ request()->is('customer/change-password*') ? 'active' : '' }}" href="{{ url('/customer/change-password') }}">
									<i class="icofont-key me-2"></i> {{ __('Change Password') }}
								</a>
							</li>
							@endif

							{{-- 4. Purchase History --}}
							<li>
								<a class="{{ request()->is('customer/orders*') || request()->is('customer/purchase-history*') || request()->is('customer/order/*') ? 'active' : '' }}" href="{{ url('/customer/orders') }}">
									<i class="icofont-shopping-cart me-2"></i> {{ __('Purchase History') }}
								</a>
							</li>
						</ul>
						<div class="dashboard-logout-wrapper mt-3">
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

@push('css')
<style>
/* Eliminate theme green ::before pseudo element hover across customer dashboard buttons */
.dashboard .button .btn::before,
.dashboard .button .btn::after,
.dashboard .button .btn:hover::before,
.dashboard-sidebar .btn::before,
.dashboard-sidebar .btn::after,
.dashboard-sidebar .btn:hover::before,
.dashboard-logout-wrapper .btn::before,
.dashboard-logout-wrapper .btn::after,
.dashboard-logout-wrapper .btn:hover::before {
	display: none !important;
	content: none !important;
	width: 0 !important;
	height: 0 !important;
	background: transparent !important;
	background-color: transparent !important;
	opacity: 0 !important;
	visibility: hidden !important;
}

/* Sidebar Logout Button - Clean Navy styling, never green */
.dashboard-sidebar .dashboard-menu .dashboard-logout-wrapper .btn {
	background-color: #1f2d5a !important;
	color: #ffffff !important;
	border: 1px solid #1f2d5a !important;
	border-radius: 8px !important;
	padding: 11px 20px !important;
	font-size: 14px !important;
	font-weight: 600 !important;
	transition: background-color 0.2s ease, border-color 0.2s ease, box-shadow 0.2s ease !important;
}
.dashboard-sidebar .dashboard-menu .dashboard-logout-wrapper .btn:hover {
	background-color: #162142 !important;
	border-color: #162142 !important;
	color: #ffffff !important;
	box-shadow: 0 4px 12px rgba(31, 45, 90, 0.25) !important;
}
</style>
@endpush

@push('js')
<script src="{{ asset('admin/js/sweetalert2.all.min.js') }}"></script>
<script src="{{ asset('admin/js/form.js') }}"></script>
@endpush