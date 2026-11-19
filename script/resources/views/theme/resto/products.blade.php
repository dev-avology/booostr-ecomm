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
											<h6 class="text-center mt-2 none zero_product">{{ __('0 Product available') }}</h6>
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
<script src="{{ asset('theme/resto/js/products.js') }}"></script>
@endpush