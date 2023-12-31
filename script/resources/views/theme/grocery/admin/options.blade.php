@extends('layouts.backend.app')
@section('head')
@include('layouts.backend.partials.headersection',['title'=>'Theme Settings'])
@endsection

@section('style')
<link rel="stylesheet" href="{{ asset('admin/assets/css/summernote/summernote-bs4.css') }}">
<link rel="stylesheet" href="{{ asset('admin/plugins/dropzone/dropzone.css') }}">
@endsection
@section('content')
<div class="row">
	<div class="col-12 col-sm-12 col-lg-12">
		<div class="card">
			<div class="card-header">
				<h4>{{ __('Theme Settings') }}</h4>
			</div>
			<div class="card-body">
				<div class="row">
					<div class="col-12 col-sm-12 col-md-4">
						<ul class="nav nav-pills flex-column" id="myTab4" role="tablist">
							<li class="nav-item">
								<a class="nav-link active"  data-toggle="tab" href="#home_page" role="tab" aria-controls="home" aria-selected="true">{{ __('Home Page') }}</a>
							</li>
							<li class="nav-item">
								<a class="nav-link"  data-toggle="tab" href="#wishlist_page" role="tab" aria-controls="profile" aria-selected="false">{{ __('Wishlist Page') }}</a>
							</li>
							<li class="nav-item">
								<a class="nav-link"  data-toggle="tab" href="#checkout_page" role="tab" aria-controls="profile" aria-selected="false">{{ __('Checkout Page') }}</a>
							</li>
							<li class="nav-item">
								<a class="nav-link"  data-toggle="tab" href="#settings_page" role="tab" aria-controls="profile" aria-selected="false">{{ __('Template primary settings') }}</a>
							</li>
							<li class="nav-item">
								<a class="nav-link"  data-toggle="tab" href="#mailchimp" role="tab" aria-controls="profile" aria-selected="false">{{ __('Mailchimp API Settings (Newsletter)') }}</a>
							</li>
							
						</ul>
					</div>
					<div class="col-12 col-sm-12 col-md-8">
						<div class="tab-content no-padding" id="myTab2Content">
							<div class="tab-pane fade show active" id="home_page" role="tabpanel" aria-labelledby="home_page">
								<form method="post" class="ajaxform" action="{{ route('seller.themeoption.update','home_page') }}">
									@csrf
									@php
									$home_page_data=get_option('home_page',true);
									$home_page_data=$home_page_data->meta ?? '';
									@endphp
								<h6>{{ __('Hero Area') }}</h6>
								<hr>
								<div class="form-group">
									<label>{{ __('Hero Title :') }}</label>
									<input type="text"  value="{{ $home_page_data->hero_title ?? '' }}" name="option[hero_title]" class="form-control">
								</div>
								<div class="form-group">
									<label>{{ __('Hero Description :') }}</label>
									<input type="text" value="{{ $home_page_data->hero_des ?? '' }}" name="option[hero_des]" class="form-control">
								</div>
							
								<div class="form-group">
									<label>{{ __('Background Image') }}</label>
									{{mediasection([
										'preview_class'=>'hero_img',
										'input_id'=>'hero_img',
										'input_class'=>'hero_img',
										'input_name'=>'option[hero_img]',
										'value'=>$home_page_data->hero_img ?? '',
										'preview'=>$home_page_data->hero_img ?? 'admin/img/img/placeholder.png'
									])}}
								</div>


								<div class="form-group">
									<button class="btn btn-primary basicbtn">{{ __('Update') }}</button>
								</div>
								</form>
							</div>
							<div class="tab-pane fade" id="wishlist_page" role="tabpanel" aria-labelledby="profile-tab4">
								@php
								$wishlist_page=get_option('wishlist_page',true);
								$wishlist_page=$wishlist_page->meta ?? '';
								@endphp
								<form method="post" class="ajaxform" action="{{ route('seller.themeoption.update','wishlist_page') }}">
									@csrf
								
								<div class="form-group">
									<label>{{ __('Title :') }}</label>
									<input type="text" value="{{ $wishlist_page->wishlist_page_title ?? '' }}" name="option[wishlist_page_title]" class="form-control">
								</div>

								<div class="form-group">
									<label>{{ __('Description :') }}</label>
									<textarea name="option[wishlist_page_description]" class="form-control">{{ $wishlist_page->wishlist_page_description ?? '' }}</textarea>
								</div>
								<div class="form-group">
									<label>{{ __('Page Banner') }}</label>
									{{mediasection([
										'preview_class'=>'wishlist_page_icon',
										'input_id'=>'wishlist_page_icon',
										'input_class'=>'wishlist_page_image',
										'input_name'=>'option[wishlist_page_banner]',
										'value'=>$wishlist_page->wishlist_page_banner ?? '',
										'preview'=>$wishlist_page->wishlist_page_banner ?? 'admin/img/img/placeholder.png'
									])}}
								</div>
								<div class="form-group">
									<button class="btn btn-primary basicbtn">{{ __('Update') }}</button>
								</div>
							  </form>
							</div>
							<div class="tab-pane fade" id="checkout_page" role="tabpanel" aria-labelledby="profile-tab4">
								@php
								$checkout_page=get_option('checkout_page',true);
								$checkout_page=$checkout_page->meta ?? '';
								@endphp
								<form method="post" class="ajaxform" action="{{ route('seller.themeoption.update','checkout_page') }}">
									@csrf
								
								<div class="form-group">
									<label>{{ __('Title :') }}</label>
									<input type="text" value="{{ $checkout_page->cart_page_title ?? '' }}" name="option[cart_page_title]" class="form-control">
								</div>

								<div class="form-group">
									<label>{{ __('Description :') }}</label>
									<textarea name="option[cart_page_description]" class="form-control">{{ $checkout_page->cart_page_description ?? '' }}</textarea>
								</div>
								<div class="form-group">
									<label>{{ __('Page Banner') }}</label>
									{{mediasection([
										'preview_class'=>'checkout_page_icon',
										'input_id'=>'checkout_page_icon',
										'input_class'=>'checkout_page_image',
										'input_name'=>'option[checkout_page_banner]',
										'value'=>$checkout_page->checkout_page_banner ?? '',
										'preview'=>$checkout_page->checkout_page_banner ?? 'admin/img/img/placeholder.png'
									])}}
								</div>
								<div class="form-group">
									<label>{{ __('Checkout Form Title :') }}</label>
									<input type="text" value="{{ $checkout_page->checkout_form_title ?? '' }}" name="option[checkout_form_title]" class="form-control">
								</div>

								<div class="form-group">
									<label>{{ __('Checkout Description :') }}</label>
									<textarea name="option[checkout_form_description]" class="form-control">{{ $checkout_page->checkout_form_description ?? '' }}</textarea>
								</div>

								<div class="form-group">
									<button class="btn btn-primary basicbtn">{{ __('Update') }}</button>
								</div>
							  </form>
							</div>
							<div class="tab-pane fade" id="settings_page" role="tabpanel" aria-labelledby="profile-tab4">
								@php
								$site_settings=get_option('site_settings',true);
								$site_settings=$site_settings->meta ?? '';
								@endphp
								<form method="post" class="ajaxform" action="{{ route('seller.themeoption.update','site_settings') }}">
									@csrf
								<h6>{{ __('Template primary settings') }}</h6>
								<div class="form-group">
									<label>{{ __('Header Contact Title :') }}</label>
									<input type="text"  name="option[header_contact_title]" class="form-control" value="{{ $site_settings->header_contact_title ?? '' }}">
								</div>
								<div class="form-group">
									<label>{{ __('Header Contact Number:') }}</label>
									<input type="text"  name="option[header_contact_number]" class="form-control" value="{{ $site_settings->header_contact_number ?? '' }}">
								</div>

								<div class="form-group">
									<label>{{ __('Topbar') }}</label>
									<a href="javascript:void(0)" data-html='<ul class="list-main"><li><i class="icofont-location-pin"></i> Store location</li><li><i class="icofont-ui-alarm"></i> <a href="#">Daily deal</a></li><li><i class="icofont-ui-user"></i> <a href="#">Open Account</a></li><li><i class="icofont-ui-power"></i><a href="#">Login</a></li></ul>' class="text-right float-right exampleclick" data-target=".topbarhtml">{{ __('Example') }}</a>
									<textarea name="option[topbar]" class="form-control topbarhtml">{{ $site_settings->topbar ?? '' }}</textarea>
								</div>

								<hr>
								<h6>{{ __('Footer Section') }}</h6>
								<div class="form-group">
									<label>{{ __('Footer Column 1:') }}</label>
									<a href="javascript:void(0)" data-html='<div class="footer-logo"><a href="#"><img src="img/logo.png" alt="#"></a></div><ul class="f-contact-inner"><li><i class="icofont-phone"></i>+880 1234 56789</li><li><i class="icofont-email"></i>Support@yourmail.com</li><li><i class="icofont-map"></i>9870,St Vincent Place, United States</li></ul><ul class="footer-social"><li class="sn"><a href="#">Follow Us</a></li><li><a href="#"><i class="icofont-facebook"></i></a></li><li><a href="#"><i class="icofont-vimeo"></i></a></li><li><a href="#"><i class="icofont-twitter"></i></a></li><li><a href="#"><i class="icofont-tumblr"></i></a></li></ul>' class="text-right float-right exampleclick" data-target=".footer_column1">{{ __('Example') }}</a>

									<textarea name="option[footer_column1]" class="form-control footer_column1">{{ $site_settings->footer_column1 ?? '' }}</textarea>
								</div>
								<div class="form-group">
									<label>{{ __('Footer Column 2:') }}</label>
									<a href="javascript:void(0)" data-html='<h2 class="widget-title">Information</h2><ul class="footer-links-inner"><li><a href="#">Marketing Strategy</a></li><li><a href="#">Interior Design</a></li><li><a href="#">Digital Services</a></li><li><a href="#">Product Selling</a></li><li><a href="#">Product Design</a></li><li><a href="#">Social Marketing</a></li></ul>' class="text-right float-right exampleclick" data-target=".footer_column2">{{ __('Example') }}</a>
									<textarea name="option[footer_column2]" class="form-control footer_column2">{{ $site_settings->footer_column2 ?? '' }}</textarea>
								</div>
								<div class="form-group">
									<label>{{ __('Footer Column 3:') }}</label>
									<a href="javascript:void(0)" data-html='<h2 class="widget-title">Popular Brands </h2><ul class="footer-links-inner"><li><a href="#">Marketing Strategy</a></li><li><a href="#">Interior Design</a></li><li><a href="#">Digital Services</a></li><li><a href="#">Product Selling</a></li><li><a href="#">Product Design</a></li><li><a href="#">Social Marketing</a></li></ul>' class="text-right float-right exampleclick" data-target=".footer_column3">{{ __('Example') }}</a>

									<textarea name="option[footer_column3]" class="form-control footer_column3">{{ $site_settings->footer_column3 ?? '' }}</textarea>
								</div>
								<div class="form-group">
									<label>{{ __('Footer Column 4:') }}</label>
									<a href="javascript:void(0)" data-html='<h2 class="widget-title">Newsletter</h2><p>Don’t miss out thousands of great deals & promotions.</p>' class="text-right float-right exampleclick" data-target=".footer_column4">{{ __('Example') }}</a>

									<textarea name="option[footer_column4]" class="form-control footer_column4">{{ $site_settings->footer_column4 ?? '' }}</textarea>
								</div>

								<hr>
								<h6>{{ __('Bottom Section') }}</h6>
								<div class="form-group">
									<label>{{ __('Bottom Left Column:') }}</label>
									<a href="javascript:void(0)" data-html='<p class="copyright-text">© 2021. GRshop. Developed By <a>AMCoders</a></p>' class="text-right float-right exampleclick" data-target=".bottom_left_column">{{ __('Example') }}</a>
									<textarea name="option[bottom_left_column]" class="form-control bottom_left_column">{{ $site_settings->bottom_left_column ?? '' }}</textarea>
								</div>
								
								<div class="form-group">
									<label>{{ __('Bottom Right Column:') }}</label>
									<a href="javascript:void(0)" data-html='<div class="pm-img"><img src="pament logo src" alt="#"></div>' class="text-right float-right exampleclick" data-target=".bottom_right_column">{{ __('Example') }}</a>
									<textarea name="option[bottom_right_column]" class="form-control bottom_right_column">{{ $site_settings->bottom_right_column ?? '' }}</textarea>
								</div>

								

								@php
								
								$cart_sidebar=$site_settings->cart_sidebar ?? 'yes';
								$preloader=$site_settings->preloader ?? 'yes';
								$bottom_bar=$site_settings->bottom_bar ?? 'yes';
								$newsletter_status=$site_settings->newsletter_status ?? 'yes';
								@endphp
								
								
								<div class="form-group">
									<label>{{ __('Cart Sidebar :') }}</label>
									<select class="form-control" name="option[cart_sidebar]">
										<option value="yes" @if($cart_sidebar == 'yes') selected="" @endif>{{ __('Enable') }}</option>
										<option value="no" @if($cart_sidebar == 'no') selected="" @endif>{{ __('Disable') }}</option>
									</select>
								</div>
								
								<div class="form-group">
									<label>{{ __('Mobile bottom menubar :') }}</label>
									<select class="form-control" name="option[bottom_bar]">
										<option value="yes" @if($bottom_bar == 'yes') selected="" @endif>{{ __('Enable') }}</option>
										<option value="no" @if($bottom_bar == 'no') selected="" @endif>{{ __('Disable') }}</option>
									</select>
								</div>
								<div class="form-group">
									<label>{{ __('Newsletter form :') }}</label>
									<select class="form-control" name="option[newsletter_status]">
										<option value="yes" @if($newsletter_status == 'yes') selected="" @endif>{{ __('Enable') }}</option>
										<option value="no" @if($newsletter_status == 'no') selected="" @endif>{{ __('Disable') }}</option>
									</select>
								</div>
								
								<div class="form-group">
									<button class="btn btn-primary basicbtn">{{ __('Update') }}</button>
								</div>
							  </form>
							</div>

							<div class="tab-pane fade" id="mailchimp" role="tabpanel" aria-labelledby="profile-tab4">
								@php
								$mailchimp=get_option('mailchimp',true);
								$mailchimp=$mailchimp->meta ?? '';
								
								@endphp
								<form method="post" class="ajaxform" action="{{ route('seller.themeoption.update','mailchimp') }}">
									@csrf
								<h6>{{ __('Mailchimp Api Settings') }}</h6>
								<div class="form-group">
									<label>{{ __('MAILCHIMP API KEY :') }}</label>
									<input type="text"  name="option[mailchimp_api_key]" class="form-control" value="{{ $mailchimp->mailchimp_api_key ?? '' }}" required="">
								</div>
								<div class="form-group">
									<label>{{ __('MAILCHIMP LIST ID :') }}</label>
									<input type="text"  name="option[mailchimp_list_id]" class="form-control" value="{{ $mailchimp->mailchimp_list_id ?? '' }}" required="">
								</div>
								

								
								
								
								<div class="form-group">
									<button class="btn btn-primary basicbtn">{{ __('Update') }}</button>
								</div>
							  </form>
							</div>

						</div>


					</div>
				</div>
			</div>
		</div>
	</div>
</div>
{{ mediasingle() }} 
@endsection

@push('script')
<script src="{{ asset('admin/assets/js/summernote-bs4.js') }}"></script>
<script src="{{ asset('admin/assets/js/summernote.js') }}"></script>
<!-- JS Libraies -->
<script src="{{ asset('admin/plugins/dropzone/dropzone.min.js') }}"></script>

<!-- Page Specific JS File -->
<script src="{{ asset('admin/plugins/dropzone/components-multiple-upload.js') }}"></script>
<script src="{{ asset('admin/js/media.js') }}"></script>
<script>
	"use strict";

	$('.exampleclick').on('click',function(){
		var html=$(this).data('html');
		var target=$(this).data('target');

		$(target).val(html);
	});
</script>
@endpush