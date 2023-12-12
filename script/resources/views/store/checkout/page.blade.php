@extends('layouts.checkout')
@section('content')
<!-- Start Breadcrumbs Area -->
		<div class="breadcrumbs" >
			<div class="container">
				<div class="row">
					<div class="col-lg-12 col-md-12 col-12">
						<div class="breadcrumbs-content">
						    @php
							 $club_info = tenant_club_info();
							@endphp
							<h1 class="page-title">{{ $club_info['club_name'] }} Store</h1>    
							<p>{{$info->title}}</p>
							<p>{{ $meta->page_excerpt ?? '' }}</p>
						</div>
						{{-- <ul class="breadcrumb-nav">
							<li><a href="{{ url('/') }}"><i class="icofont-home"></i> {{ __('Home') }}</a></li>
							<li>{{ $info->title }}</li>
						</ul> --}}
					</div>
				</div>
			</div>
		</div>
		<!--/ End Breadcrumbs Area -->
		
		
		<!-- Shopping Cart -->
		@if(!in_array($info->slug,['terms-and-conditions','privacy-policy','return-policy']))
		<style>
		div#cart-anchor-clr-page ul, div#cart-anchor-clr-page ol {
			list-style: auto;
		}

		div#cart-anchor-clr-page p,div#cart-anchor-clr-page ul:not(.list-unstyled),div#cart-anchor-clr-page ol {
			line-height: 28px;
			font-family: "Nunito", "Segoe UI", arial;
            color: #838181;
			display: block;
			margin-block-start: 1em;
			margin-block-end: 1em;
			margin-inline-start: 0px;
			margin-inline-end: 0px;
		}
		div#cart-anchor-clr-page p{
			/*padding-inline-start: 40px;*/
		}
		</style>
		@endif
		<div class="shopping-cart section" id="cart-anchor-clr-page">
			<div class="container">
				<div class="row">
					<div class="col-sm-12">
						@php 
						   $club_info = tenant_club_info();
                           $content = $meta->page_content ?? '';
						   $lastupdated = Carbon\Carbon::parse(tenant('created_at'))->format('d-m-Y');
						   if(isset($club_info['club_url'])){
							$club_url = $club_info['club_url'];
							$club_url = '<a href="'.$club_url.'" target="_blank">'.$club_url.'</a>';
						   }else{
							$club_url = 'https://staging3.booostr.co/all-booster-clubs/';
							$club_url = '<a href="'.$club_url.'" target="_blank">'.$club_url.'</a>';
						   }
						   $address = explode(',',$club_info['address']);
                           $store_country = trim($address[count($address)-1]);

                           $club_email = '<a href="mailto:'.$club_info['club_email'].'">'.$club_info['club_email'].'</a>';


						   $content = str_replace('[Date]',$lastupdated,$content);
						   $content = str_replace(array('[store_name]','[club_name]'),$club_info['club_name'].' Store',$content);
						   $content = str_replace(array('[store_url]','[club_profile_url]'),$club_url,$content);
						   $content = str_replace(array('[store_email]','[club_email]'),$club_email,$content);
						   $content = str_replace('[store_jurisdiction]',$store_country,$content);
						   $content = str_replace('[club_manager_first_and_last_name]',$club_info['co_profile_manager']??'',$content);
						   $content = str_replace('[club_address]',$club_info['address'],$content);
						   
                         @endphp
						{!! $content !!}
					</div>
				</div>
			</div>
		</div>
		<!--/ End Shopping Cart -->
@endsection
@push('js')
<script src="{{ asset('admin/js/sweetalert2.all.min.js') }}"></script>
<script src="{{ asset('admin/js/form.js') }}"></script>
@endpush