@extends('theme.resto.layouts.app')
@section('content')

@php
    $cleanExcerpt = !empty($info->excerpt->value) ? trim(strip_tags($info->excerpt->value)) : '';
    $hasVariations = count($info->optionwithcategories ?? []) > 0;
    $isOutOfStock = false;
    if (!$hasVariations) {
        if (($info->price->stock_status ?? 1) == 0 || (($info->price->stock_manage ?? 0) == 1 && ($info->price->qty ?? 0) <= 0)) {
            $isOutOfStock = true;
        }
    }
    $productSku = !$hasVariations ? ($info->price->sku ?? '') : '';
@endphp
@include('theme.resto.components.page-banner', [
    'title' => $info->title,
    'description' => \Illuminate\Support\Str::limit($cleanExcerpt, 120),
    'breadcrumbs' => [
        ['url' => url('/'), 'label' => __('Home'), 'icon' => 'icofont-home'],
        ['url' => url('/products'), 'label' => __('Products'), 'icon' => 'icofont-box'],
        ['url' => '', 'label' => $info->title, 'icon' => 'icofont-tag'],
    ]
])

		<!-- Shop Single -->
		<section class="shop single section">
			<div class="container">
				<div class="top-content">
					<div class="row">
						<!-- Left: Product Images -->
						<div class="col-lg-6 col-12">
							<div class="product-details-image">
								<div class="main-preview-image">
									<div class="tab-content product-image" id="pills-tabContent">
										@foreach($galleries ?? [] as $key => $row)
										<div class="tab-pane fade {{ $key == 0 ? 'show active' : '' }}" id="pills-home{{ $key }}" role="tabpanel" aria-labelledby="pills-home-tab{{ $key }}">
											<div class="single-product-image">
												<img src="{{ asset($row) }}" alt="{{ $info->title }}">
											</div>
										</div>
										@endforeach
									</div>
								</div>
								@if(count($galleries ?? []) > 1)
								<ul class="nav-pills nav flex-nowrap product-thumbs" id="pills-tab" role="tablist">
									@foreach($galleries ?? [] as $key => $row)
									<li class="single-thumbs" role="presentation">
										<a class="{{ $key == 0 ? 'active' : '' }}" id="pills-home-tab{{ $key }}" data-bs-toggle="pill" href="#pills-home{{ $key }}" role="tab" aria-controls="pills-home" aria-selected="true">
											<img src="{{ asset($row) }}" alt="{{ $info->title }}">
										</a>
									</li>
									@endforeach
								</ul>
								@endif
							</div>
						</div>

						<!-- Right: Product Info -->
						<div class="col-lg-6 col-12">
							<div class="single-item-des">
								<div class="short">
									<!-- Title -->
									<h2 style="font-size: 24px; font-weight: 700; color: #1e293b; margin-bottom: 12px; line-height: 1.3;">{{ $info->title }}</h2>

									<!-- Price -->
									<p class="price price_area" style="font-size: 22px; font-weight: 700; color: var(--primary, #0A8CDE); margin-bottom: 14px;">
										@if(!$hasVariations && isset($info->price->price))
											<span class="discount">{{ booostr_price($info->price->price) }}</span>
											@if(!empty($info->price->old_price) && $info->price->old_price > 0)
												<s style="font-size: 16px; color: #94a3b8; font-weight: 400; margin-left: 8px;">{{ booostr_price($info->price->old_price) }}</s>
											@endif
										@endif
									</p>

									<!-- Short Description -->
									@if(!empty($cleanExcerpt))
									<div class="product-short-description" style="margin-bottom: 18px; padding-bottom: 18px; border-bottom: 1px solid #e2e8f0;">
										<div id="short-desc-text" style="color: #475569; font-size: 14px; line-height: 1.6; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; transition: all 0.3s ease;">
											{{ $cleanExcerpt }}
										</div>
										@if(!empty($info->description->value ?? '') || strlen($cleanExcerpt) > 140)
										<div style="margin-top: 6px;">
											<a href="#product-full-description" class="scroll-to-desc" style="color: var(--primary, #0A8CDE); font-size: 13px; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 4px;">
												<span>{{ __('Full details') }}</span> <i class="icofont-arrow-down"></i>
											</a>
										</div>
										@endif
									</div>
									@endif

									<!-- Stock Status -->
									<div class="product-meta-row stock_status @if($hasVariations) none @endif" style="margin-bottom: 10px;">
										<p style="margin: 0; font-size: 14px; color: #475569;">
											<strong>{{ __('Availability') }}:</strong>
											<span class="stock_status_display">
												@if(!$hasVariations)
													@if(!$isOutOfStock)
														<span class="badge" style="background-color: var(--primary, #0A8CDE) !important; color: #ffffff !important; font-size: 12px; font-weight: 600; padding: 4px 10px; border-radius: 4px;">{{ __('In Stock') }}</span>
													@else
														<span class="badge" style="background-color: #94a3b8 !important; color: #ffffff !important; font-size: 12px; font-weight: 600; padding: 4px 10px; border-radius: 4px;">{{ __('Out of Stock') }}</span>
													@endif
												@endif
											</span>
										</p>
									</div>

									<!-- SKU -->
									@if(!$hasVariations && !empty($productSku) && $productSku !== '0')
									<div class="product-meta-row sku_display" style="margin-bottom: 10px;">
										<p style="margin: 0; font-size: 14px; color: #475569;">
											<strong>{{ __('SKU') }}:</strong> <span class="sku_value">{{ $productSku }}</span>
										</p>
									</div>
									@elseif($hasVariations)
									<div class="product-meta-row sku_display" style="margin-bottom: 10px; display: none;">
										<p style="margin: 0; font-size: 14px; color: #475569;">
											<strong>{{ __('SKU') }}:</strong> <span class="sku_value"></span>
										</p>
									</div>
									@endif

									<!-- Category -->
									@if(count($info->category ?? []) > 0)
									<div class="product-meta-row" style="margin-bottom: 10px;">
										<p style="margin: 0; font-size: 14px; color: #475569;">
											<strong>{{ __('Category') }}:</strong>
											@foreach($info->category ?? [] as $row)
												<a href="{{ url('/category', $row->slug) }}" class="categories" data-id="{{ $row->id }}" style="color: var(--primary, #0A8CDE);">{{ $row->name }}</a>{{ !$loop->last ? ',' : '' }}
											@endforeach
										</p>
									</div>
									@endif

									<!-- Brand -->
									@if(count($info->brands ?? []) > 0)
									<div class="product-meta-row" style="margin-bottom: 10px;">
										<p style="margin: 0; font-size: 14px; color: #475569;">
											<strong>{{ __('Brand') }}:</strong>
											@foreach($info->brands ?? [] as $row)
												<a href="{{ url('/brand', $row->slug) }}" style="color: var(--primary, #0A8CDE);">{{ $row->name }}</a>{{ !$loop->last ? ',' : '' }}
											@endforeach
										</p>
									</div>
									@endif

									<!-- Tags -->
									@if(count($info->tags ?? []) > 0)
									<div class="product-meta-row" style="margin-bottom: 18px; padding-bottom: 18px; border-bottom: 1px solid #e2e8f0;">
										<p style="margin: 0; font-size: 14px; color: #475569;">
											<strong>{{ __('Tags') }}:</strong>
											@foreach($info->tags ?? [] as $row)
												<a href="{{ url('/tag', $row->slug) }}" style="color: #64748b;">{{ $row->name }}</a>{{ !$loop->last ? ',' : '' }}
											@endforeach
										</p>
									</div>
									@endif
								</div>

								<!-- Variation & Cart Form -->
								<form class="product_option_form" method="post" action="{{ route('add.tocart') }}">
									@csrf
									<input type="hidden" name="id" value="{{ $info->id }}">
									<input type="hidden" name="variation_id" id="selected_variation_id" value="">
									<script>
										window.booostr_variation_prices = @json($info->prices ?? []);
									</script>
									@if(!$hasVariations)
									<input
										class="none pricesvariationshide"
										data-stockstatus="{{ $info->price->stock_status }}"
										data-stockmanage="{{ $info->price->stock_manage }}"
										data-sku="{{ $info->price->sku }}"
										data-qty="{{ $info->price->qty }}"
										data-oldprice="{{ $info->price->old_price }}"
										data-price="{{ $info->price->price }}"
										type="radio"
										checked
									>
									@endif

									<!-- Variations -->
									@include('theme.resto.components.variations', ['info' => $info ?? ''])

									<!-- Quantity + Add to Cart -->
									<div class="product-buy" style="margin-top: 24px; clear: both; display: flex; flex-wrap: wrap; align-items: flex-end; gap: 16px;">
										<div class="quantity" style="margin: 0;">
											<h6 style="margin-bottom: 8px; font-size: 14px; font-weight: 600; color: #1e293b;">{{ __('Quantity') }} :</h6>
											<div class="quantity-container product-quantity">
												<div class="sp-quantity single-page @if($isOutOfStock) disabled-quantity @endif">
													<button type="button" class="inline arrow sp-minus fff" @if($isOutOfStock) disabled @endif><a class="ddd minus" data-multi="-1">-</a></button>
													<div class="inline sp-input">
														<input type="text" class="quntity-input input_qty" name="qty" value="1" @if(!$hasVariations && $info->price && $info->price->stock_manage == 1) data-max="{{ $info->price->qty }}" @endif @if($isOutOfStock) disabled @endif>
													</div>
													<button type="button" class="inline arrow sp-plus fff" @if($isOutOfStock) disabled @endif><a class="ddd cart_increase plus" data-multi="1">+</a></button>
												</div>
											</div>
										</div>
										<div class="add-to-cart button d-flex align-items-center flex-wrap" style="gap: 10px; margin: 0;">
											<button type="submit" class="btn add_to_cart_form_btn" @if($isOutOfStock) disabled style="background: #94a3b8 !important; border-color: #94a3b8 !important; cursor: not-allowed;" @else style="background: var(--primary, #0A8CDE) !important; border-color: var(--primary, #0A8CDE) !important; color: #ffffff !important;" @endif>
												{{ $isOutOfStock ? __('Out of Stock') : __('Add to cart') }}
											</button>
											@php
												$isItemInWishlist = Cart::instance('wishlist')->content()->where('id', $info->id)->isNotEmpty();
											@endphp
											<a href="javascript:void(0)" data-id="{{ $info->id }}" class="btn-detail-wishlist add_to_wishlist {{ $isItemInWishlist ? 'in-wishlist activewishlist' : '' }}" title="{{ $isItemInWishlist ? __('Remove from Wishlist') : __('Add to Wishlist') }}">
												<i class="icofont-heart"></i>
												<span class="wishlist-btn-text">{{ $isItemInWishlist ? __('In Wishlist') : __('Add to Wishlist') }}</span>
											</a>
										</div>
									</div>
								</form>

								<!-- Social Share -->
								<div class="product-share" style="margin-top: 20px; padding-top: 18px; border-top: 1px solid #e2e8f0;">
									<p style="margin: 0; font-size: 14px; color: #475569; display: flex; align-items: center; gap: 12px;">
										<strong>{{ __('Share') }}:</strong>
										<a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}" target="_blank" rel="noopener noreferrer" style="color: #475569; font-size: 18px; transition: color 0.2s;" onmouseover="this.style.color='var(--primary, #0A8CDE)'" onmouseout="this.style.color='#475569'"><i class="icofont-facebook"></i></a>
										<a href="https://twitter.com/intent/tweet?url={{ urlencode(url()->current()) }}&text={{ urlencode($info->title) }}" target="_blank" rel="noopener noreferrer" style="color: #475569; font-size: 18px; transition: color 0.2s;" onmouseover="this.style.color='var(--primary, #0A8CDE)'" onmouseout="this.style.color='#475569'"><i class="icofont-twitter"></i></a>
										<a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(url()->current()) }}" target="_blank" rel="noopener noreferrer" style="color: #475569; font-size: 18px; transition: color 0.2s;" onmouseover="this.style.color='var(--primary, #0A8CDE)'" onmouseout="this.style.color='#475569'"><i class="icofont-linkedin"></i></a>
									</p>
								</div>
							</div>
						</div>
					</div>
				</div>

				<!-- Description Section (Full Width) -->
				@php
					$hasFullDescription = !empty($info->description->value ?? '');
					$displayBottomDesc = $hasFullDescription ? $info->description->value : (!empty($cleanExcerpt) && strlen($cleanExcerpt) > 140 ? $cleanExcerpt : '');
				@endphp
				@if(!empty($displayBottomDesc))
				<div class="product-info" id="product-full-description" style="margin-top: 40px; scroll-margin-top: 100px;">
					<div class="row">
						<div class="col-12">
							<div class="description-section" style="border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden; background: #ffffff; box-shadow: 0 1px 3px rgba(0,0,0,0.03);">
								<div style="background: #f8fafc; padding: 14px 20px; border-bottom: 2px solid var(--primary, #0A8CDE);">
									<h5 style="margin: 0; font-size: 16px; font-weight: 600; color: #1e293b;"><i class="icofont-document-folder" style="color: var(--primary, #0A8CDE); margin-right: 8px;"></i>{{ __('Product Description') }}</h5>
								</div>
								<div style="padding: 24px;">
									<div class="single-des" style="color: #475569; font-size: 14px; line-height: 1.8;">
										@if($hasFullDescription)
											{!! content_format($displayBottomDesc) !!}
										@else
											<p style="color: #475569; font-size: 14px; line-height: 1.8; margin: 0;">{{ $displayBottomDesc }}</p>
										@endif
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				@endif
			</div>
		</section>
		<!--/ End Shop Single -->
		<input type="hidden" id="term_id" value="{{ $info->id }}">
@endsection
@push('js')
<script src="{{ asset('admin/js/sweetalert2.all.min.js') }}"></script>
<script src="{{ asset('admin/js/form.js') }}"></script>
<script src="{{ asset('theme/jquery.unveil.js') }}"></script>
<script src="{{ asset('theme/resto/js/product-details.js?v='.(tenant('cache_version') ?: '2.9')) }}"></script>
@endpush
