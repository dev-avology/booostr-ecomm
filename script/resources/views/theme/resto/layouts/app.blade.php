<!DOCTYPE html>
  <html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
   <head>
      <meta charset="utf-8">
      <meta http-equiv="x-ua-compatible" content="ie=edge">
      {{-- generate meta tags --}}
      {!! SEOMeta::generate() !!}
      {!! OpenGraph::generate() !!}
      {!! Twitter::generate() !!}
      {!! JsonLd::generate() !!}
      {!! JsonLdMulti::generate() !!}
      {!! SEO::generate(true) !!}

      <meta name="csrf-token" content="{{ csrf_token() }}">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <link rel="shortcut icon" type="image/x-icon" href="{{ asset('uploads/'.tenant('uid').'/favicon.ico') }}">
      <!-- Web Font -->
      <link rel="preconnect" href="https://fonts.googleapis.com">
      <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
      <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
      <link rel="stylesheet" href="{{ asset('theme/resto/css/bootstrap.min.css') }}">
      <link rel="stylesheet" href="{{ asset('theme/resto/css/icofont.css') }}">
      <link rel="stylesheet" href="{{ asset('theme/resto/css/nice-select.css') }}">
      <link rel="stylesheet" href="{{ asset('theme/resto/css/animate.css') }}">
      <link rel="stylesheet" href="{{ asset('theme/resto/css/tiny-slider.css') }}">
      <link rel="stylesheet" href="{{ asset('theme/resto/css/glightbox.min.css') }}">
      <link rel="stylesheet" href="{{ asset('theme/resto/css/perfect-scrollbar.css') }}">
      <!-- Theme Styles -->
      <link rel="stylesheet" href="{{ asset('theme/resto/css/reset.css?v='.(tenant('cache_version') ?: '2.5')) }}">
      <link rel="stylesheet" href="{{ asset('theme/resto/style.css?v='.(tenant('cache_version') ?: '2.5')) }}">
      <link rel="stylesheet" href="{{ asset('theme/resto/css/responsive.css?v='.(tenant('cache_version') ?: '2.5')) }}">
      <link rel="stylesheet" href="{{ asset('theme/helper.css?v='.(tenant('cache_version') ?: '2.5')) }}">
      <style>
         :root {
            --booostr-blue: #0A8CDE;
            --booostr-blue-hover: #0870b2;
            --booostr-blue-light: #e8f4fc;
            --primary: #0A8CDE;
         }
         body, button, input, select, textarea, h1, h2, h3, h4, h5, h6, p, a, span {
            font-family: 'Roboto', sans-serif !important;
         }
         /* Header & Navbar: Standard Booostr Blue */
         .header.navbar-area,
         .navbar-area,
         .navbar-area.sticky {
            background: var(--primary, #0A8CDE) !important;
            background-color: var(--primary, #0A8CDE) !important;
         }
         /* Header & Footer Club Logo: Crisp White Edge Glow - Keeps 100% original colors, no background box */
         .navbar-brand img,
         #clubNavbarLogo,
         .footer .footer-logo img {
            filter: drop-shadow(0 0 1px #ffffff) drop-shadow(0 0 3px rgba(255, 255, 255, 0.85)) !important;
            -webkit-filter: drop-shadow(0 0 1px #ffffff) drop-shadow(0 0 3px rgba(255, 255, 255, 0.85)) !important;
         }
         .navbar-nav .nav-item a {
            color: #ffffff !important;
         }
         .navbar-nav .nav-item a.active,
         .navbar-nav .nav-item:hover a,
         .navbar-nav .nav-item a:hover {
            color: #ffffff !important;
            opacity: 1 !important;
         }
         .header .navbar .nav-item a:hover .span-dot,
         .header .navbar .nav-item a.active .span-dot {
            opacity: 1 !important;
            visibility: visible !important;
         }
         .cart-list-top a:hover {
            color: #ffffff !important;
            border-color: #ffffff !important;
            background: rgba(255, 255, 255, 0.2) !important;
         }

         /* Footer: Standard Booostr Blue */
         .footer {
            background: var(--primary, #0A8CDE) !important;
            background-color: var(--primary, #0A8CDE) !important;
         }
         .footer .footer-top .inner-content {
            border-bottom: 1px solid rgba(255, 255, 255, 0.2) !important;
         }
         .footer .footer-bottom .inner-content {
            border-top: 1px solid rgba(255, 255, 255, 0.2) !important;
         }
         .footer .single-footer h3 {
            color: #ffffff !important;
         }
         .footer .single-footer h3::before {
            background-color: rgba(255, 255, 255, 0.7) !important;
         }
         .footer .single-footer.f-link ul li a {
            color: rgba(255, 255, 255, 0.85) !important;
         }
         .footer .single-footer.f-link ul li a:hover {
            color: #ffffff !important;
            padding-left: 5px;
         }
         .footer .copyright p {
            color: rgba(255, 255, 255, 0.85) !important;
         }
         .footer-terms-link {
            color: rgba(255, 255, 255, 0.85) !important;
         }
         .footer-terms-link:hover {
            color: #ffffff !important;
            text-decoration: underline !important;
         }

         /* Scroll-To-Top Button: Standard Booostr Blue */
         .scroll-top {
            background: var(--primary, #0A8CDE) !important;
            background-color: var(--primary, #0A8CDE) !important;
            color: #ffffff !important;
            opacity: 1 !important;
            box-shadow: 0 4px 14px rgba(10, 140, 222, 0.35) !important;
         }
         .scroll-top:hover {
            background: var(--booostr-blue-hover, #0870b2) !important;
            background-color: var(--booostr-blue-hover, #0870b2) !important;
            color: #ffffff !important;
            box-shadow: 0 6px 18px rgba(8, 112, 178, 0.45) !important;
            transform: translate3d(0, -4px, 0) !important;
         }

         /* Customer Sidebar Logout Button & Action Buttons */
         .dashboard-sidebar .dashboard-menu .dashboard-logout-wrapper .btn,
         .dashboard-sidebar .button .btn.alt-btn {
            background: var(--primary, #0A8CDE) !important;
            background-color: var(--primary, #0A8CDE) !important;
            border-color: var(--primary, #0A8CDE) !important;
            color: #ffffff !important;
            box-shadow: none !important;
         }
         .dashboard-sidebar .dashboard-menu .dashboard-logout-wrapper .btn:hover,
         .dashboard-sidebar .button .btn.alt-btn:hover {
            background: var(--booostr-blue-hover, #0870b2) !important;
            background-color: var(--booostr-blue-hover, #0870b2) !important;
            border-color: var(--booostr-blue-hover, #0870b2) !important;
            color: #ffffff !important;
            box-shadow: 0 4px 12px rgba(10, 140, 222, 0.3) !important;
         }

         /* Outline Buttons (e.g. View Full History) */
         .btn-outline-primary {
            color: var(--primary, #0A8CDE) !important;
            border-color: var(--primary, #0A8CDE) !important;
         }
         .btn-outline-primary:hover,
         .btn-outline-primary:focus,
         .btn-outline-primary:active {
            background: var(--primary, #0A8CDE) !important;
            background-color: var(--primary, #0A8CDE) !important;
            border-color: var(--primary, #0A8CDE) !important;
            color: #ffffff !important;
            box-shadow: 0 4px 12px rgba(10, 140, 222, 0.25) !important;
         }

         /* Shopping Cart Order Summary Box */
         .shopping-cart .right,
         .shopping-cart .total-amount .right {
            background: var(--primary, #0A8CDE) !important;
            background-color: var(--primary, #0A8CDE) !important;
         }

         /* Global Theme Overrides: All legacy green elements mapped to Booostr Blue */
         .cart-sidebar {
            background: var(--booostr-blue, #0A8CDE) !important;
         }
         .cart-sidebar:hover {
            background: var(--booostr-blue-hover, #0870b2) !important;
         }
         .sub__title {
            color: var(--booostr-blue, #0A8CDE) !important;
         }
         .food-tab-main .nav li button.active {
            background-color: var(--booostr-blue, #0A8CDE) !important;
            border-color: var(--booostr-blue, #0A8CDE) !important;
            color: #ffffff !important;
         }
         .food-tab-main .nav li button:hover:not(.active) {
            color: var(--booostr-blue, #0A8CDE) !important;
            border-color: var(--booostr-blue, #0A8CDE) !important;
         }
         .btn.primary,
         .btn.primary.animate {
            background: var(--booostr-blue, #0A8CDE) !important;
            background-color: var(--booostr-blue, #0A8CDE) !important;
            border-color: var(--booostr-blue, #0A8CDE) !important;
            color: #ffffff !important;
         }
         .btn.primary:hover,
         .btn.primary.animate:hover {
            background: var(--booostr-blue-hover, #0870b2) !important;
            background-color: var(--booostr-blue-hover, #0870b2) !important;
            border-color: var(--booostr-blue-hover, #0870b2) !important;
            color: #ffffff !important;
         }
         /* Pagination Booostr Blue Theme */
         .pagination .pagination-list li.active a,
         .pagination .pagination-list li.active span,
         .pagination .pagination-list li:hover a,
         .pagination .pagination-list li:hover span,
         .pagination .pagination-list li a.active,
         .pagination-list li.active a,
         .pagination-list li.active span {
            background: var(--primary, #0A8CDE) !important;
            background-color: var(--primary, #0A8CDE) !important;
            border-color: var(--primary, #0A8CDE) !important;
            color: #ffffff !important;
         }
         .pagination .pagination-list li a:focus {
            color: var(--primary, #0A8CDE) !important;
         }
         /* Breadcrumbs: Booostr Blue hover on links & icons */
         .breadcrumbs .breadcrumb-nav li a:hover,
         .breadcrumbs .breadcrumb-nav li a:hover i,
         .breadcrumb-nav li a:hover,
         .breadcrumb-nav li a:hover i {
            color: var(--primary, #0A8CDE) !important;
         }
         /* General hover & primary button sweep */
         .button .btn:hover::before,
         .button .btn::before {
            background-color: var(--primary, #0A8CDE) !important;
         }
         /* Product Details Gallery: Industry Standard */
         .product-details-image .main-preview-image {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            overflow: hidden;
            height: 480px;
            display: flex;
            align-items: center;
            justify-content: center;
         }
         .product-details-image .main-preview-image .product-image,
         .product-details-image .main-preview-image .tab-pane {
            width: 100%;
            height: 100%;
         }
         .product-details-image .product-image .single-product-image {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 16px;
         }
         .product-details-image .product-image .single-product-image img {
            max-width: 100%;
            max-height: 100%;
            width: auto;
            height: auto;
            object-fit: contain;
            object-position: center;
            margin: 0 auto;
            display: block;
         }
         .product-details-image .product-thumbs {
            display: flex !important;
            flex-wrap: nowrap !important;
            gap: 10px;
            overflow-x: auto;
            padding: 12px 2px 6px 2px;
            margin: 0;
            list-style: none;
            scrollbar-width: thin;
            scrollbar-color: #cbd5e1 transparent;
         }
         .product-details-image .product-thumbs::-webkit-scrollbar {
            height: 5px;
         }
         .product-details-image .product-thumbs::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
         }
         .product-details-image .product-thumbs .single-thumbs {
            flex: 0 0 72px;
            width: 72px;
            height: 72px;
            margin: 0;
         }
         .product-details-image .product-thumbs .single-thumbs a {
            display: block;
            width: 72px;
            height: 72px;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            overflow: hidden;
            background: #f8fafc;
            padding: 2px;
            transition: all 0.2s ease;
         }
         .product-details-image .product-thumbs .single-thumbs a:hover {
            border-color: var(--primary, #0A8CDE);
         }
         .product-details-image .product-thumbs .single-thumbs a.active {
            border-color: var(--primary, #0A8CDE) !important;
            box-shadow: 0 0 0 2px rgba(10, 140, 222, 0.25);
         }
         .product-details-image .product-thumbs .single-thumbs a img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
            border-radius: 5px;
            border: none;
         }
         @media (max-width: 767px) {
            .product-details-image .main-preview-image {
               height: 340px;
            }
            .product-details-image .product-thumbs .single-thumbs {
               flex: 0 0 60px;
               width: 60px;
               height: 60px;
            }
            .product-details-image .product-thumbs .single-thumbs a {
               width: 60px;
               height: 60px;
            }
         }
         /* Product Details Add to Cart button: strictly Booostr Blue with NO hover effect */
         .single-item-des .add-to-cart .btn,
         .single-item-des .add-to-cart .btn.add_to_cart_form_btn {
            background: var(--primary, #0A8CDE) !important;
            background-color: var(--primary, #0A8CDE) !important;
            border-color: var(--primary, #0A8CDE) !important;
            color: #ffffff !important;
            border-radius: 6px !important;
            padding: 12px 28px !important;
            font-weight: 600 !important;
            box-shadow: none !important;
            transition: none !important;
         }
         .single-item-des .add-to-cart .btn:hover,
         .single-item-des .add-to-cart .btn:focus,
         .single-item-des .add-to-cart .btn:active,
         .single-item-des .add-to-cart .btn.add_to_cart_form_btn:hover,
         .single-item-des .add-to-cart .btn.add_to_cart_form_btn:focus,
         .single-item-des .add-to-cart .btn.add_to_cart_form_btn:active {
            background: var(--primary, #0A8CDE) !important;
            background-color: var(--primary, #0A8CDE) !important;
            border-color: var(--primary, #0A8CDE) !important;
            color: #ffffff !important;
            box-shadow: none !important;
            transform: none !important;
         }
         .single-item-des .add-to-cart .btn::before,
         .single-item-des .add-to-cart .btn::after,
         .single-item-des .add-to-cart .btn.add_to_cart_form_btn::before,
         .single-item-des .add-to-cart .btn.add_to_cart_form_btn::after {
            display: none !important;
            content: none !important;
         }
         .single-item-des .add-to-cart .btn[disabled],
         .single-item-des .add-to-cart .btn.add_to_cart_form_btn[disabled] {
            background: #94a3b8 !important;
            background-color: #94a3b8 !important;
            border-color: #94a3b8 !important;
            color: #ffffff !important;
            cursor: not-allowed !important;
         }
      </style>
      @stack('css')
      {{ load_header() }}
   </head>
   <body>
     @php
     $autoload_data=getautoloadquery();
     $average_times=optionfromcache('average_times');
    
     $cart_count=Cart::instance('default')->count();
     $wishlist_count=Cart::instance('wishlist')->count();
     @endphp
      <!--[if lte IE 9]>
      <p class="browserupgrade">
         You are using an <strong>outdated</strong> browser. Please
         <a href="https://browsehappy.com/">upgrade your browser</a> to improve
         your experience and security.
      </p>
     <![endif]-->
      @if(isset($autoload_data['site_settings']))
      @php
      $site_settings=json_decode($autoload_data['site_settings']);
      $site_settings=$site_settings->meta ?? '';
      $preloader=$site_settings->preloader ?? 'yes';
      @endphp

      @if($preloader == 'yes')
      <div class="preloader">
         <div class="preloader-inner">
            <div class="preloader-icon"><span></span><span></span></div>
         </div>
      </div>
      @endif

      @endif

     @include('theme.resto.layouts.header',['autoload_data'=>$autoload_data,'cart_count'=>$cart_count,'wishlist_count'=>$wishlist_count,'average_times'=>$average_times,'site_settings'=>$site_settings ?? ''])
     @yield('content')
     @include('theme.resto.layouts.footer',['site_settings'=>$site_settings ?? ''])

      @if(isset($autoload_data['whatsapp_settings']))
     @php
     $whatsapp= json_decode($autoload_data['whatsapp_settings'])
     @endphp
     @if($whatsapp->whatsapp_status == 'on')
       @include('components.whatsapp',['whatsapp'=>$whatsapp])
     @endif
     @endif
<!--  scroll-top -->
<a href="#" class="scroll-top btn-hover"><i class="icofont-long-arrow-up"></i></a>
<input type="hidden" id="callback_url" value="{{ url('/databack') }}">  
<input type="hidden" id="cart_link" value="{{ route('add.tocart') }}" />
<input type="hidden" id="base_url" value="{{ url('/') }}" />
<input type="hidden" id="click_sound" value="{{ asset('uploads/click.wav') }}">
<input type="hidden" id="cart_sound" value="{{ asset('uploads/cart.wav') }}">
<input type="hidden" id="cart_increment" value="{{ url('/cart-qty') }}">
<input type="hidden" id="pos_product_varidation" value="{{ url('/product-varidation') }}">
<input type="hidden" id="cart_content" value="{{ Cart::content() }}">
<input type="hidden" class="total_amount" value="{{ str_replace(',','',Cart::total()) }}">
<input type="hidden" id="preloader" value="{{ asset('uploads/preload.webp') }}">
<input type="hidden" id="currency_settings" value="{{ $autoload_data['currency_data'] ?? '' }}">
<!--  JS Files  -->
<script src="{{ asset('theme/resto/js/jquery.min.js') }}"></script>
<script src="{{ asset('theme/resto/js/jquery-ui.min.js') }}"></script>
<script src="{{ asset('theme/resto/js/bootstrap.min.js') }}"></script>
<script src="{{ asset('theme/resto/js/nice-select.js') }}"></script>
<script src="{{ asset('theme/resto/js/wow.min.js') }}"></script>
<script src="{{ asset('theme/resto/js/tiny-slider.js') }}"></script>
<script src="{{ asset('theme/resto/js/glightbox.min.js') }}"></script>
<script src="{{ asset('theme/resto/js/perfect-scrollbar.min.js') }}"></script>
<script src="{{ asset('theme/resto/js/main.js') }}"></script>
<script src="{{ asset('theme/helper.js?v='.(tenant('cache_version') ?: '2.5')) }}"></script>
<script src="{{ asset('theme/resto/js/theme-helper.js?v='.(tenant('cache_version') ?: '2.5')) }}"></script>

@stack('js')
{{ load_footer() }}
  </body>
</html>