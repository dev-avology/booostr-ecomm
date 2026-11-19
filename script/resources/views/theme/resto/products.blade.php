@extends('theme.resto.layouts.app')
@section('content')
@include('theme.resto.components.page-banner', [
    'title' => __('Products'),
    'description' => __('Explore our complete collection of products and merchandise.'),
    'breadcrumbs' => [
        ['url' => url('/'), 'label' => __('Home'), 'icon' => 'icofont-home'],
        ['url' => '', 'label' => __('Products'), 'icon' => 'icofont-fast-food'],
    ]
])
		
		<!-- Start Items Listing Grid -->
		<section class="category-page section">
			<div class="container">
				<div class="row">
					<div class="col-lg-9 col-12">
						<div class="category-grid-list">
							<div class="row">
								<div class="col-12">
									<div class="category-grid-topbar">
										<div class="row align-items-center">
											<div class="col-12">
												<h3 class="title">{{ __('Showing') }} <span class="from_products">0</span>- <span class="to_products">-</span> {{ __('of') }} <span class="total_products"></span> {{ __('Products found') }}</h3>
											</div>
										</div>
									</div>
									<div class="tab-content" id="nav-tabContent">
										<div class="tab-pane fade show active" id="nav-grid" role="tabpanel">
											<div class="text-center py-5 none zero_product" style="display: none; padding: 50px 20px;">
												<div style="font-size: 50px; color: #cbd5e1; margin-bottom: 15px;"><i class="icofont-box"></i></div>
												<h5 style="color: #475569; font-weight: 600; margin-bottom: 8px;">{{ __('No Products Found') }}</h5>
												<p style="color: #94a3b8; font-size: 14px; margin-bottom: 20px;">{{ __('There are currently no products available in this category.') }}</p>
												<a href="{{ url('/') }}" class="btn" style="background: var(--primary, #0A8CDE); color: #fff; padding: 9px 22px; border-radius: 4px; font-weight: 500; display: inline-block;">{{ __('Back to Home') }}</a>
											</div>
											<div class="row latest_products">
												
												
											</div>
										</div>

										<div class="row">
											<div class="col-12">
												<div class="pagination left">
													<ul class="pagination-list">

													</ul>
												</div>
											</div>
										</div>
									</div>
									
								</div>
							</div>
						</div>
					</div>
					
					<div class="col-lg-3 col-12">
						<div class="category-sidebar">
							<div class="single-widget categories-widget food category_area">
								<h3 class="wd-title">{{ __('Categories') }}</h3>
								<ul class="custom product_menu">
									<li class="menu_preload content-preloader  content-placeholder" data-height="40px"  data-width="100%"></li>
									<li class="menu_preload content-preloader  content-placeholder" data-height="40px"  data-width="100%"></li>
									<li class="menu_preload content-preloader  content-placeholder" data-height="40px"  data-width="100%"></li>
									<li class="menu_preload content-preloader  content-placeholder" data-height="40px"  data-width="100%"></li>
									<li class="menu_preload content-preloader  content-placeholder" data-height="40px"  data-width="100%"></li>
								</ul>
							</div>
							<div class="single-widget categories-widget food none brand_area">
								<h3 class="wd-title">{{ __('Brands') }}</h3>
								<ul class="custom product_brands">
								</ul>
							</div>
							
							<!-- Start Single Widget -->
							<div class="single-widget hot-product featured_area none">
								<h3 class="wd-title featured_title"></h3>
								<ul class="all-products featured_products">
									
									
								</ul>
							</div>
							<!-- End Single Widget -->
						</div>
					</div>
				</div>
			</div>
		</section>
		<!-- End Items Listing Grid -->
<input type="hidden" id="cat" value="{{ $categoryid ?? '' }}">

@endsection
@push('js')
<script src="{{ asset('admin/js/sweetalert2.all.min.js') }}"></script>
<script src="{{ asset('admin/js/form.js') }}"></script>
<script src="{{ asset('theme/jquery.unveil.js') }}"></script>
<script src="{{ asset('theme/resto/js/products.js?v='.(tenant('cache_version') ?: '2.5')) }}"></script>
@endpush