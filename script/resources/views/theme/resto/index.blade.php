@extends('theme.resto.layouts.app')
@php
$page_data = $page_data ?? null;
@endphp
@section('content')
@include('theme.resto.components.page-banner', ['is_home' => true])
      
      <!-- Start Food Tab Area -->
      <section class="food-tab-main section">
         <div class="container">
            <div class="row">
               <div class="col-lg-6 offset-lg-3 col-md-8 offset-md-2 col-12">
                  <div class="section__title text-center">
                     <span class="sub__title wow fadeInUp" data-wow-delay=".2s">{{ $page_data->products_area_short_title ?? 'For you' }}</span>
                     <h2 class="main__title wow fadeInUp" data-wow-delay=".4s"><span></span>{{ $page_data->products_area_title ?? 'Latest Products' }}</h2>
                     <p class="section__text">{{ $page_data->products_area_description ?? '' }}</p>
                  </div>
               </div>
            </div>
            <div class="row">
               <div class="col-12">
                  <ul class="nav nav-tabs menutab" id="myTab" role="tablist">
                     <li class="nav-item" role="presentation">
                        <button class="nav-link active"  id="meat-tab" data-bs-toggle="tab" data-bs-target="#meat" type="button" role="tab" aria-controls="meat" aria-selected="true"> {{ __('All') }}</button>
                     </li>
                      
                     
                     <li class="nav-item content-preloader" role="presentation">
                        <button class="content-placeholder"  type="button" data-height="40px" data-width="100%"> </button>
                     </li>
                      <li class="nav-item content-preloader" role="presentation">
                        <button class="content-placeholder"  type="button" data-height="40px" data-width="100%"> </button>
                     </li>
                     <li class="nav-item content-preloader" role="presentation">
                        <button class="content-placeholder"  type="button" data-height="40px" data-width="100%"> </button>
                     </li>
                     <li class="nav-item content-preloader" role="presentation">
                        <button class="content-placeholder"  type="button" data-height="40px" data-width="100%"> </button>
                     </li>
                     <li class="nav-item content-preloader" role="presentation">
                        <button class="content-placeholder"  type="button" data-height="40px" data-width="100%"> </button>
                     </li>
                  </ul>
                  <div class="tab-content" id="myTabContent">
                     <div class="tab-pane fade show active" id="meat" role="tabpanel" aria-labelledby="meat-tab">
                        <div class="row latest_products">
                           <div class="col-lg-3 col-md-6 col-6 content-preloader">
                              <div class="single-popular-product  content-placeholder" data-height="200px" data-width="100%"  >
                              </div>
                           </div>
                           <div class="col-lg-3 col-md-6 col-6 content-preloader">
                              <div class="single-popular-product  content-placeholder" data-height="200px" data-width="100%"  >
                              </div>
                           </div>
                           <div class="col-lg-3 col-md-6 col-6 content-preloader">
                              <div class="single-popular-product  content-placeholder" data-height="200px" data-width="100%"  >
                              </div>
                           </div>
                           <div class="col-lg-3 col-md-6 col-6 content-preloader">
                              <div class="single-popular-product  content-placeholder" data-height="200px" data-width="100%"  >
                              </div>
                           </div>
                           <div class="col-lg-3 col-md-6 col-6 content-preloader">
                              <div class="single-popular-product  content-placeholder" data-height="200px" data-width="100%"  >
                              </div>
                           </div>
                           <div class="col-lg-3 col-md-6 col-6 content-preloader">
                              <div class="single-popular-product  content-placeholder" data-height="200px" data-width="100%"  >
                              </div>
                           </div>
                           <div class="col-lg-3 col-md-6 col-6 content-preloader">
                              <div class="single-popular-product  content-placeholder" data-height="200px" data-width="100%"  >
                              </div>
                           </div>
                           <div class="col-lg-3 col-md-6 col-6 content-preloader">
                              <div class="single-popular-product  content-placeholder" data-height="200px" data-width="100%"  >
                              </div>
                           </div>
                          
                           
                        </div>
                     </div>
                     <div class="tab-pane fade" id="filteredtab" role="tabpanel" aria-labelledby="filteredtab-tab" >
                         <div class="row filtered_product_tab">
                        
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </section>
      <!-- /End Food Tab Area -->

      <!-- /End Count Down Area -->
@endsection
@push('js')
<script src="{{ asset('admin/js/sweetalert2.all.min.js') }}"></script>
<script src="{{ asset('admin/js/form.js') }}"></script>
<script src="{{ asset('theme/jquery.unveil.js') }}"></script>
<script src="{{ asset('theme/resto/js/home.js?v='.tenant('cache_version')) }}"></script>
@endpush