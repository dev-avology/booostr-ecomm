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
         /* Desktop Navbar Links: White on Booostr Blue */
         @media (min-width: 992px) {
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
         }

         /* Responsive Mobile & Tablet Navbar Dropdown (Screen <= 991px) */
         @media (max-width: 991px) {
            .nav-inner {
               position: relative !important;
            }
            .navbar {
               position: static !important;
            }
            .navbar-collapse {
               position: absolute !important;
               top: calc(100% + 8px) !important;
               left: 0 !important;
               right: 0 !important;
               width: 100% !important;
               max-width: 100% !important;
               background-color: #ffffff !important;
               border-radius: 12px !important;
               box-shadow: 0 12px 30px rgba(0, 0, 0, 0.15) !important;
               border: 1px solid #e2e8f0 !important;
               padding: 10px 12px !important;
               z-index: 99999 !important;
               overflow-y: auto !important;
               max-height: 80vh !important;
            }
            .navbar-collapse .navbar-nav {
               margin: 0 !important;
               padding: 0 !important;
               width: 100% !important;
               display: flex !important;
               flex-direction: column !important;
               gap: 3px !important;
            }
            .navbar-collapse .navbar-nav .nav-item {
               margin: 0 !important;
               width: 100% !important;
            }
            .navbar-collapse .navbar-nav .nav-item a {
               color: #1e293b !important;
               font-size: 15px !important;
               font-weight: 600 !important;
               padding: 11px 16px !important;
               display: flex !important;
               align-items: center !important;
               justify-content: space-between !important;
               border-radius: 8px !important;
               border-bottom: 1px solid #f1f5f9 !important;
               text-decoration: none !important;
               transition: all 0.2s ease !important;
               background: transparent !important;
            }
            .navbar-collapse .navbar-nav .nav-item:last-child a {
               border-bottom: none !important;
            }
            .navbar-collapse .navbar-nav .nav-item a:hover,
            .navbar-collapse .navbar-nav .nav-item a.active {
               color: var(--booostr-blue, #0A8CDE) !important;
               background-color: var(--booostr-blue-light, #e8f4fc) !important;
            }
            .navbar-collapse .navbar-nav .nav-item a::before {
               display: none !important;
            }
            .navbar-collapse .navbar-nav .nav-item a .span-dot {
               display: none !important;
            }
            .navbar-collapse .navbar-nav .nav-item .drop-icon i {
               color: #64748b !important;
            }
            .navbar-collapse .navbar-nav .nav-item .sub-menu {
               position: static !important;
               box-shadow: none !important;
               border-top: none !important;
               padding-left: 15px !important;
               background: #f8fafc !important;
               border-radius: 6px !important;
               margin: 4px 0 !important;
            }
            .navbar-collapse .navbar-nav .nav-item .sub-menu .nav-item a {
               font-size: 14px !important;
               padding: 8px 12px !important;
               color: #334155 !important;
            }
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
         .shopping-cart .total-amount .right.cart-summary-card {
            background: #ffffff !important;
            background-color: #ffffff !important;
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
         .section__title .main__title,
         .main__title {
            color: #1e293b !important;
         }

         /* Sidebar Headings & Widgets: Consistent Dark Black (#1e293b) */
         .category-sidebar .single-widget .wd-title,
         .sidebar-accordion-header {
            color: #1e293b !important;
         }
         .category-sidebar .single-widget .wd-title:hover,
         .sidebar-accordion-header:hover {
            color: var(--booostr-blue, #0A8CDE) !important;
         }
         .category-sidebar .single-widget .wd-title::before {
            background: var(--booostr-blue, #0A8CDE) !important;
         }

         /* Category & Brand Sidebar Items and Modern Neutral Count Badges (Option 1) */
         .categories-widget ul li a {
            color: #334155 !important;
         }
         .categories-widget ul li a span {
            background-color: #e2e8f0 !important;
            color: #475569 !important;
            font-weight: 600 !important;
            transition: all 0.2s ease-in-out !important;
         }
         .categories-widget ul li a:hover {
            background-color: var(--booostr-blue, #0A8CDE) !important;
            color: #ffffff !important;
         }
         .categories-widget ul li a:hover span,
         .categories-widget ul li a.active span,
         .categories-widget ul li .active span {
            background-color: rgba(255, 255, 255, 0.28) !important;
            color: #ffffff !important;
         }
         .categories-widget ul li a.active,
         .categories-widget ul li .active {
            background-color: var(--booostr-blue, #0A8CDE) !important;
            color: #ffffff !important;
         }

         /* Mobile Bottom Navigation Bar: Booostr Brand Colors & Perfectly Divided */
         @media only screen and (max-width: 767px) {
            .mobile-cart-show {
               position: fixed !important;
               bottom: 0 !important;
               left: 0 !important;
               right: 0 !important;
               width: 100% !important;
               max-width: 100vw !important;
               background: #ffffff !important;
               border-top: 1px solid #e2e8f0 !important;
               box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.05) !important;
               padding: 8px 0 !important;
               margin: 0 !important;
               box-sizing: border-box !important;
               z-index: 9999 !important;
               display: block !important;
            }
            .mobile-cart-show ul {
               display: flex !important;
               align-items: center !important;
               justify-content: space-around !important;
               width: 100% !important;
               margin: 0 !important;
               padding: 0 !important;
               list-style: none !important;
               box-sizing: border-box !important;
            }
            .mobile-cart-show ul li {
               flex: 1 1 0 !important;
               width: 25% !important;
               max-width: 25% !important;
               text-align: center !important;
               margin: 0 !important;
               padding: 0 !important;
               display: flex !important;
               justify-content: center !important;
               align-items: center !important;
               box-sizing: border-box !important;
            }
            .single-cart-show {
               width: 100% !important;
               text-align: center !important;
               display: flex !important;
               justify-content: center !important;
            }
            .single-cart-show a {
               color: #64748b !important;
               font-size: 11px !important;
               font-weight: 500 !important;
               display: flex !important;
               flex-direction: column !important;
               align-items: center !important;
               justify-content: center !important;
               gap: 3px !important;
               width: 100% !important;
               text-decoration: none !important;
               transition: color 0.2s ease !important;
            }
            .single-cart-show a i {
               background: #e8f4fc !important;
               color: var(--primary, #0A8CDE) !important;
               border-radius: 50% !important;
               width: 32px !important;
               height: 32px !important;
               line-height: 32px !important;
               font-size: 16px !important;
               margin-right: 0 !important;
               transition: all 0.2s ease !important;
               display: inline-flex !important;
               align-items: center !important;
               justify-content: center !important;
            }
            .single-cart-show a:hover i,
            .single-cart-show.active a i,
            .mobile-cart-show ul li.active .single-cart-show a i {
               background: var(--primary, #0A8CDE) !important;
               color: #ffffff !important;
            }
            .single-cart-show a:hover,
            .single-cart-show.active a,
            .mobile-cart-show ul li.active .single-cart-show a {
               color: var(--primary, #0A8CDE) !important;
               font-weight: 600 !important;
            }
         }
         /* Wishlist Button: Clear Toggle & Vibrant Active States */
         .product-overlay .btn.wishlist {
            transition: all 0.25s ease !important;
         }
         .single-popular-product .btn.wishlist.in-wishlist,
         .single-popular-product .btn.wishlist.activewishlist,
         .product-overlay .btn.wishlist.in-wishlist,
         .product-overlay .btn.wishlist.activewishlist {
            background: #ef4444 !important;
            background-color: #ef4444 !important;
            color: #ffffff !important;
            border-color: #ef4444 !important;
            box-shadow: 0 4px 10px rgba(239, 68, 68, 0.4) !important;
         }
         .single-popular-product .btn.wishlist.in-wishlist:hover,
         .single-popular-product .btn.wishlist.activewishlist:hover,
         .product-overlay .btn.wishlist.in-wishlist:hover,
         .product-overlay .btn.wishlist.activewishlist:hover {
            background: #dc2626 !important;
            background-color: #dc2626 !important;
            color: #ffffff !important;
            border-color: #dc2626 !important;
            transform: scale(1.08) !important;
         }
         .single-popular-product .btn.wishlist.in-wishlist i,
         .single-popular-product .btn.wishlist.activewishlist i,
         .product-overlay .btn.wishlist.in-wishlist i,
         .product-overlay .btn.wishlist.activewishlist i {
            color: #ffffff !important;
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
         .single-item-des .add-to-cart .btn.add_to_cart_form_btn::before,
         .single-item-des .add-to-cart .btn.add_to_cart_form_btn::after {
            display: none !important;
            content: none !important;
         }
         .single-item-des .add-to-cart .btn.add_to_cart_form_btn[disabled] {
            background: #94a3b8 !important;
            background-color: #94a3b8 !important;
            border-color: #94a3b8 !important;
            color: #ffffff !important;
            cursor: not-allowed !important;
         }

         /* Product Details Wishlist Button */
         .btn-detail-wishlist {
            height: 48px;
            padding: 0 20px;
            border-radius: 6px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-size: 14.5px;
            font-weight: 600;
            text-decoration: none !important;
            cursor: pointer;
            transition: all 0.2s ease-in-out;
            background: #ffffff !important;
            background-color: #ffffff !important;
            border: 1.5px solid #cbd5e1 !important;
            color: #475569 !important;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
         }
         .btn-detail-wishlist i {
            font-size: 18px;
            color: #64748b;
            transition: all 0.2s ease-in-out;
         }
         .btn-detail-wishlist:hover {
            background: #fef2f2 !important;
            background-color: #fef2f2 !important;
            border-color: #fca5a5 !important;
            color: #ef4444 !important;
            transform: translateY(-1px);
            box-shadow: 0 3px 8px rgba(239, 68, 68, 0.15);
         }
         .btn-detail-wishlist:hover i {
            color: #ef4444 !important;
            transform: scale(1.15);
         }

         /* Active State (When added to wishlist) */
         .btn-detail-wishlist.in-wishlist,
         .btn-detail-wishlist.activewishlist {
            background: #fef2f2 !important;
            background-color: #fef2f2 !important;
            border-color: #fca5a5 !important;
            color: #dc2626 !important;
            box-shadow: 0 2px 6px rgba(239, 68, 68, 0.12) !important;
         }
         .btn-detail-wishlist.in-wishlist i,
         .btn-detail-wishlist.activewishlist i {
            color: #ef4444 !important;
         }
         .btn-detail-wishlist.in-wishlist:hover,
         .btn-detail-wishlist.activewishlist:hover {
            background: #fee2e2 !important;
            background-color: #fee2e2 !important;
            border-color: #f87171 !important;
            color: #b91c1c !important;
         }

         /* Variation Chips & Selects */
         .variation-chips-wrapper {
            gap: 8px;
         }
         .variation-chip {
            display: inline-block;
         }
         .variation-chip-label {
            min-width: 48px;
            height: 38px;
            padding: 0 16px;
            border-radius: 6px;
            border: 1.5px solid #cbd5e1;
            background: #ffffff;
            color: #334155;
            font-size: 14px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease-in-out;
            margin: 0;
            user-select: none;
         }
         .variation-chip-label:hover {
            border-color: var(--primary, #0A8CDE);
            color: var(--primary, #0A8CDE);
            background: #f0f9ff;
         }
         .variation-radio-input:checked + .variation-chip-label {
            background: var(--primary, #0A8CDE) !important;
            background-color: var(--primary, #0A8CDE) !important;
            border-color: var(--primary, #0A8CDE) !important;
            color: #ffffff !important;
            box-shadow: 0 2px 6px rgba(10, 140, 222, 0.3);
         }
         .variation-select-input:focus {
            border-color: var(--primary, #0A8CDE) !important;
            outline: none;
            box-shadow: 0 0 0 3px rgba(10, 140, 222, 0.15);
         }

         /* Variation Dropdown & NiceSelect Styling */
         .product-weight.variation-group {
            position: relative !important;
            z-index: 10 !important;
            clear: both !important;
            width: 100% !important;
            display: block !important;
         }
         .product-weight.variation-group:focus-within,
         .product-weight.variation-group:hover,
         .product-weight.variation-group.active-group {
            z-index: 1000 !important;
         }
         .variation-select-wrapper {
            position: relative !important;
            max-width: 440px !important;
            width: 100% !important;
            margin-bottom: 8px !important;
            z-index: 10 !important;
         }
         .variation-select-wrapper:focus-within,
         .variation-select-wrapper.active-wrapper {
            z-index: 1000 !important;
         }
         .variation-select-wrapper .nice-select {
            width: 100% !important;
            float: none !important;
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
            height: 44px !important;
            line-height: 44px !important;
            padding: 0 16px !important;
            border: 1.5px solid #cbd5e1 !important;
            border-radius: 6px !important;
            font-size: 14.5px !important;
            font-weight: 500 !important;
            color: #334155 !important;
            background: #ffffff !important;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03) !important;
         }
         .variation-select-wrapper .nice-select:hover {
            border-color: var(--primary, #0A8CDE) !important;
         }
         .variation-select-wrapper .nice-select.open {
            border-color: var(--primary, #0A8CDE) !important;
            box-shadow: 0 0 0 3px rgba(10, 140, 222, 0.15) !important;
            z-index: 10000 !important;
         }
         .variation-select-wrapper .nice-select .current {
            display: block !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
            white-space: nowrap !important;
            padding-right: 20px !important;
         }
         .variation-select-wrapper .nice-select .list {
            width: 100% !important;
            min-width: 100% !important;
            max-height: 250px !important;
            overflow-y: auto !important;
            border: 1.5px solid #e2e8f0 !important;
            border-radius: 8px !important;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.12) !important;
            background: #ffffff !important;
            background-color: #ffffff !important;
            opacity: 1 !important;
            z-index: 999999 !important;
            padding: 4px 0 !important;
            margin-top: 4px !important;
         }
         .variation-select-wrapper .nice-select .option {
            padding: 10px 16px !important;
            min-height: 38px !important;
            line-height: 20px !important;
            font-size: 14px !important;
            font-weight: 500 !important;
            color: #334155 !important;
            display: flex !important;
            align-items: center !important;
            white-space: normal !important;
            word-break: break-word !important;
            background: #ffffff !important;
            transition: background 0.15s ease !important;
         }
         .variation-select-wrapper .nice-select .option:hover,
         .variation-select-wrapper .nice-select .option.focus {
            background: #f0f9ff !important;
            color: var(--primary, #0A8CDE) !important;
         }
         .variation-select-wrapper .nice-select .option.selected {
            background: #e0f2fe !important;
            color: var(--primary, #0A8CDE) !important;
            font-weight: 600 !important;
         }

         /* Product Details Layout & Responsive Actions */
         .single-item-des .product-buy {
            position: relative !important;
            z-index: 1 !important;
            margin-top: 24px !important;
            display: flex !important;
            flex-wrap: wrap !important;
            align-items: flex-end !important;
            gap: 14px !important;
            clear: both !important;
         }
         .single-item-des .product-buy .quantity {
            margin: 0 !important;
            display: block !important;
            position: relative !important;
            z-index: 1 !important;
         }
         .single-item-des .product-buy .quantity h6 {
            margin-bottom: 8px !important;
         }
         .single-item-des .product-buy .sp-quantity {
            position: relative !important;
            z-index: 1 !important;
         }
         .single-item-des .product-buy .sp-quantity button,
         .single-item-des .product-buy .sp-quantity button.fff,
         .single-item-des .product-buy .sp-quantity button.sp-minus,
         .single-item-des .product-buy .sp-quantity button.sp-plus {
            z-index: 2 !important;
         }
         .single-item-des .add-to-cart {
            margin-top: 0 !important;
            display: flex !important;
            align-items: center !important;
            flex-wrap: wrap !important;
            gap: 10px !important;
            position: relative !important;
            z-index: 1 !important;
         }
         .single-item-des .product-weight {
            clear: both !important;
            width: 100% !important;
            display: block !important;
         }
         @media (max-width: 576px) {
            .single-item-des .product-buy {
               flex-direction: column !important;
               align-items: stretch !important;
               gap: 16px !important;
            }
            .single-item-des .quantity {
               width: 100% !important;
            }
            .single-item-des .add-to-cart {
               width: 100% !important;
            }
            .single-item-des .add-to-cart .btn.add_to_cart_form_btn {
               flex: 1 1 auto !important;
               width: auto !important;
            }
            .variation-select-wrapper {
               max-width: 100% !important;
            }
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
<input type="hidden" id="wishlist_ids" value="{{ json_encode(Cart::instance('wishlist')->content()->pluck('id')->map(function($id){ return (int)$id; })->values()->all()) }}">
<script>
    window.booostr_wishlist_ids = {!! json_encode(Cart::instance('wishlist')->content()->pluck('id')->map(function($id){ return (int)$id; })->values()->all()) !!};
</script>
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