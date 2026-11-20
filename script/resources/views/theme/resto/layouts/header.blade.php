@php
$invoice_data=json_decode($autoload_data['invoice_data'] ?? '');
$languages=json_decode($autoload_data['languages']);
$default_language=$autoload_data['default_language'] ?? '';
$current_url=url()->current();

// Club / Store details matching Admin Panel
$clubInfo = function_exists('tenant_club_info') ? (tenant_club_info() ?: []) : [];
$clubName = trim((string) ($clubInfo['club_name'] ?? (tenant('id') ?? '')));
$clubLogo = function_exists('tenant_club_logo') ? tenant_club_logo() : null;
@endphp

 @if(url('/checkout') !=  $current_url &&  Request::is('customer/*') !=  $current_url)

<!-- Cart Sidebar -->
@php
$cart_sidebar=$site_settings->cart_sidebar ?? 'yes';
$bottom_bar=$site_settings->bottom_bar ?? 'yes';
@endphp
@if($cart_sidebar == 'yes')
      <div class="cart-sidebar">
         <div class="item-line">
            <div class="cart-line"><i class="icofont-cart-alt"></i></div>
            <div class="cart-line item-name"><span class="cart_count">{{ $cart_count }}</span><span>{{ __('Items') }}</span></div>
         </div>
         <div class="prrice-tag"><span class="cart_subtotal render_currency">{{ Cart::instance('default')->subtotal() }}</span></div>
      </div>
      <!-- End Cart Sidebar -->
      @endif
    @if($bottom_bar == 'yes') 
      <!-- Mobile Cart Show -->
      <div class="mobile-cart-show">
         <ul>
            <li>
               <div class="single-cart-show"><a href="{{ url('/') }}"><i class="icofont-home"></i><span>{{ __('Home') }}</span></a></div>
            </li>
            <li>
               <div class="single-cart-show"><a href="{{ url('/wishlist') }}"><i class="icofont-heart"></i><span>{{ __('Wishlist') }}</span></a></div>
            </li>
            <li>
               <div class="single-cart-show"><a href="{{ url('/cart') }}"><i class="icofont-cart"></i><span>{{ __('Cart') }}</span></a></div>
            </li>
            <li>
               <div class="single-cart-show"><a href="{{ url('/customer/dashboard') }}"><i class="icofont-ui-user"></i><span>{{ __('Account') }}</span></a></div>
            </li>
         </ul>
      </div>
      <!-- End Mobile Cart Show -->
    @endif
         
               
      <!-- Shopping Item -->
      <div class="shopping-item" id="booostr-hover-cart">
         <div class="dropdown-cart-header">
            <span class="cart-header-title">{{ __('Shopping Cart') }} (<span class="cart_count">{{ $cart_count }}</span>)</span>
            <div class="close-button"><a href="javascript:void(0)" aria-label="Close cart"><i class="icofont-close"></i></a></div>
         </div>
         <div class="shopping-item-inner">
            <div class="cart-single-inner">
               <ul id="shopping" class="shopping-list">
                  
                  
               </ul>
            </div>
            <div class="cart-single-inner bottom">
               <div class="total">
                  <span>{{ __('Total') }}</span>
                  <span class="total-amount cart_subtotal">{{ booostr_price(Cart::instance('default')->subtotal()) }}</span>
               </div>
               <a href="{{ url('/cart') }}" class="btn animate btn-view-cart">{{ __('View Cart') }}</a>
               <a href="{{ url('/checkout') }}" class="btn primary animate btn-checkout">{{ __('Checkout') }}</a>
            </div>
         </div>
      </div>
      <!--/ End Shopping Item -->

      <style>
         /* Scoped Styles for Right Side Hover Cart: #booostr-hover-cart ONLY */
         #booostr-hover-cart {
            position: fixed !important;
            top: 0 !important;
            right: 0 !important;
            width: 420px !important;
            max-width: 100vw !important;
            height: 100vh !important;
            max-height: 100vh !important;
            background: #ffffff !important;
            padding: 22px 24px !important;
            display: flex !important;
            flex-direction: column !important;
            box-shadow: -6px 0 30px rgba(0, 0, 0, 0.14) !important;
            border-top: none !important;
            z-index: 999999 !important;
            box-sizing: border-box !important;
            scrollbar-width: thin !important;
            scrollbar-color: #cbd5e1 transparent !important;
         }

         #booostr-hover-cart *::-webkit-scrollbar {
            width: 5px !important;
         }
         #booostr-hover-cart *::-webkit-scrollbar-track {
            background: transparent !important;
         }
         #booostr-hover-cart *::-webkit-scrollbar-thumb {
            background: #cbd5e1 !important;
            border-radius: 4px !important;
         }
         #booostr-hover-cart *::-webkit-scrollbar-thumb:hover {
            background: #94a3b8 !important;
         }

         @media (max-width: 480px) {
            #booostr-hover-cart {
               width: 100vw !important;
               padding: 16px 18px !important;
            }
         }

         #booostr-hover-cart .dropdown-cart-header {
            display: flex !important;
            justify-content: space-between !important;
            align-items: center !important;
            margin-bottom: 14px !important;
            padding-bottom: 14px !important;
            border-bottom: 1px solid #f1f5f9 !important;
            flex-shrink: 0 !important;
         }

         #booostr-hover-cart .dropdown-cart-header .cart-header-title {
            font-size: 17px !important;
            font-weight: 700 !important;
            color: #0f172a !important;
            letter-spacing: -0.2px !important;
         }

         #booostr-hover-cart .close-button a {
            background: transparent !important;
            background-color: transparent !important;
            border-radius: 6px !important;
            color: #64748b !important;
            width: 32px !important;
            height: 32px !important;
            line-height: 1 !important;
            padding: 0 !important;
            font-size: 20px !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            box-shadow: none !important;
            transition: all 0.2s ease !important;
         }
         #booostr-hover-cart .close-button a:hover {
            background: #f1f5f9 !important;
            background-color: #f1f5f9 !important;
            color: #0f172a !important;
         }
         #booostr-hover-cart .close-button a i {
            color: inherit !important;
         }

         #booostr-hover-cart .shopping-item-inner {
            display: flex !important;
            flex-direction: column !important;
            flex: 1 !important;
            min-height: 0 !important;
            overflow: hidden !important;
         }

         #booostr-hover-cart .shopping-item-inner > .cart-single-inner:first-child {
            flex: 1 !important;
            overflow-y: auto !important;
            padding-right: 6px !important;
            margin-bottom: 8px !important;
         }

         #booostr-hover-cart .shopping-list {
            list-style: none !important;
            padding: 0 !important;
            margin: 0 !important;
         }

         #booostr-hover-cart .shopping-list li.cart_item {
            padding: 16px 0 !important;
            border-bottom: 1px solid #f1f5f9 !important;
            margin-bottom: 0 !important;
            overflow: visible !important;
         }
         #booostr-hover-cart .shopping-list li.cart_item:last-child {
            border-bottom: none !important;
         }

         /* Cart Item Modern Card Layout */
         #booostr-hover-cart .cart-single-main {
            display: flex !important;
            gap: 14px !important;
            align-items: flex-start !important;
            width: 100% !important;
         }

         #booostr-hover-cart .cart-item-img-wrap {
            width: 72px !important;
            height: 72px !important;
            min-width: 72px !important;
            border-radius: 8px !important;
            overflow: hidden !important;
            background: #f8fafc !important;
            border: 1px solid #e2e8f0 !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            flex-shrink: 0 !important;
         }
         #booostr-hover-cart .cart-item-img-wrap a.cart-img {
            width: 100% !important;
            height: 100% !important;
            display: block !important;
            border: none !important;
            border-radius: 0 !important;
            float: none !important;
         }
         #booostr-hover-cart .cart-item-img-wrap img {
            width: 100% !important;
            height: 100% !important;
            object-fit: cover !important;
            border: none !important;
            border-radius: 0 !important;
         }

         #booostr-hover-cart .cart-single-item {
            flex: 1 !important;
            min-width: 0 !important;
            display: flex !important;
            flex-direction: column !important;
         }

         #booostr-hover-cart .cart-item-header {
            display: flex !important;
            justify-content: space-between !important;
            align-items: flex-start !important;
            gap: 10px !important;
            margin-bottom: 8px !important;
            width: 100% !important;
         }

         #booostr-hover-cart .cart-item-title,
         #booostr-hover-cart .shopping-list h4 {
            font-size: 14px !important;
            font-weight: 600 !important;
            color: #0f172a !important;
            margin: 0 !important;
            line-height: 1.35 !important;
            flex: 1 !important;
         }
         #booostr-hover-cart .cart-item-title a,
         #booostr-hover-cart .shopping-list h4 a {
            color: #0f172a !important;
            text-decoration: none !important;
            display: -webkit-box !important;
            -webkit-line-clamp: 2 !important;
            -webkit-box-orient: vertical !important;
            overflow: hidden !important;
            transition: color 0.2s ease !important;
         }
         #booostr-hover-cart .cart-item-title a:hover,
         #booostr-hover-cart .shopping-list h4 a:hover {
            color: var(--booostr-blue, #0A8CDE) !important;
            text-decoration: none !important;
         }

         /* Trash / Remove Button Overrides - Clean Icon, No Grey Box, Red Hover */
         #booostr-hover-cart .shopping-list li a.remove,
         #booostr-hover-cart .shopping-list li .remove,
         #booostr-hover-cart a.remove_cart,
         #booostr-hover-cart .remove_cart {
            position: static !important;
            top: auto !important;
            bottom: auto !important;
            left: auto !important;
            right: auto !important;
            margin: 0 !important;
            margin-top: 0 !important;
            padding: 0 !important;
            width: 30px !important;
            height: 30px !important;
            line-height: 30px !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            background: transparent !important;
            background-color: transparent !important;
            border: none !important;
            border-radius: 6px !important;
            color: #94a3b8 !important;
            font-size: 17px !important;
            cursor: pointer !important;
            box-shadow: none !important;
            transition: all 0.2s ease !important;
            flex-shrink: 0 !important;
            text-decoration: none !important;
         }
         #booostr-hover-cart .shopping-list li a.remove:hover,
         #booostr-hover-cart .shopping-list li .remove:hover,
         #booostr-hover-cart a.remove_cart:hover,
         #booostr-hover-cart .remove_cart:hover {
            background: #fee2e2 !important;
            background-color: #fee2e2 !important;
            color: #ef4444 !important;
            border: none !important;
         }
         #booostr-hover-cart .shopping-list li a.remove i,
         #booostr-hover-cart .remove_cart i {
            color: inherit !important;
            font-size: 17px !important;
            line-height: 1 !important;
         }

         #booostr-hover-cart .cart-item-meta {
            font-size: 12px !important;
            color: #64748b !important;
            margin-bottom: 6px !important;
            line-height: 1.3 !important;
         }

         #booostr-hover-cart .cart-item-footer {
            display: flex !important;
            justify-content: space-between !important;
            align-items: center !important;
            margin-top: 10px !important;
            width: 100% !important;
         }

         /* Modern Quantity Selector Pill Overrides - Clean Layout, No Absolute Stacking, Blue Hover */
         #booostr-hover-cart .sp-quantity {
            position: static !important;
            display: inline-flex !important;
            flex-direction: row !important;
            align-items: center !important;
            justify-content: space-between !important;
            background: #f8fafc !important;
            background-color: #f8fafc !important;
            border-radius: 6px !important;
            border: 1px solid #e2e8f0 !important;
            height: 32px !important;
            width: 98px !important;
            min-width: 98px !important;
            max-width: 98px !important;
            padding: 2px !important;
            overflow: hidden !important;
            box-sizing: border-box !important;
            margin: 0 !important;
            top: auto !important;
         }

         #booostr-hover-cart .sp-quantity button,
         #booostr-hover-cart .sp-quantity button.sp-minus,
         #booostr-hover-cart .sp-quantity button.sp-plus,
         #booostr-hover-cart .sp-quantity button.cart_decrement,
         #booostr-hover-cart .sp-quantity button.cart_increment {
            position: static !important;
            top: auto !important;
            bottom: auto !important;
            left: auto !important;
            right: auto !important;
            width: 26px !important;
            min-width: 26px !important;
            max-width: 26px !important;
            height: 26px !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            background: #ffffff !important;
            background-color: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 4px !important;
            color: #334155 !important;
            cursor: pointer !important;
            font-weight: 700 !important;
            font-size: 14px !important;
            line-height: 1 !important;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05) !important;
            padding: 0 !important;
            margin: 0 !important;
            z-index: 1 !important;
            text-decoration: none !important;
            transition: all 0.15s ease !important;
            box-sizing: border-box !important;
         }

         #booostr-hover-cart .sp-quantity button:hover,
         #booostr-hover-cart .sp-quantity button.cart_decrement:hover,
         #booostr-hover-cart .sp-quantity button.cart_increment:hover {
            background: var(--booostr-blue-light, #e8f4fc) !important;
            background-color: var(--booostr-blue-light, #e8f4fc) !important;
            border-color: var(--booostr-blue, #0A8CDE) !important;
            color: var(--booostr-blue, #0A8CDE) !important;
         }

         #booostr-hover-cart .sp-quantity button a,
         #booostr-hover-cart .sp-quantity button a.ddd {
            color: inherit !important;
            text-decoration: none !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            width: 100% !important;
            height: 100% !important;
            line-height: 1 !important;
            font-size: 14px !important;
            font-weight: 700 !important;
         }
         #booostr-hover-cart .sp-quantity button:hover a,
         #booostr-hover-cart .sp-quantity button:hover a.ddd {
            color: var(--booostr-blue, #0A8CDE) !important;
         }

         #booostr-hover-cart .sp-quantity .inline,
         #booostr-hover-cart .sp-quantity .inline.sp-input {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            width: 38px !important;
            min-width: 38px !important;
            max-width: 38px !important;
            height: 26px !important;
            padding: 0 !important;
            margin: 0 !important;
            position: static !important;
            box-sizing: border-box !important;
         }

         #booostr-hover-cart .sp-quantity .quntity-input,
         #booostr-hover-cart .shopping-list .sp-quantity .quntity-input {
            position: static !important;
            width: 38px !important;
            min-width: 38px !important;
            max-width: 38px !important;
            height: 26px !important;
            line-height: 26px !important;
            padding: 0 !important;
            margin: 0 !important;
            text-align: center !important;
            border: none !important;
            background: transparent !important;
            background-color: transparent !important;
            font-size: 13px !important;
            font-weight: 700 !important;
            color: #0f172a !important;
            box-shadow: none !important;
            outline: none !important;
            box-sizing: border-box !important;
         }

         #booostr-hover-cart .cart-item-price {
            font-size: 16px !important;
            font-weight: 700 !important;
            color: #0f172a !important;
            letter-spacing: -0.2px !important;
            margin-left: auto !important;
         }

         /* Drawer Bottom Total & Action Buttons */
         #booostr-hover-cart .shopping-item-inner > .bottom {
            margin-top: auto !important;
            padding-top: 14px !important;
            border-top: 1px solid #f1f5f9 !important;
            flex-shrink: 0 !important;
            width: 100% !important;
         }

         #booostr-hover-cart .total {
            display: flex !important;
            justify-content: space-between !important;
            align-items: center !important;
            border-top: none !important;
            border-bottom: 1px solid #f1f5f9 !important;
            padding: 0 0 12px 0 !important;
            margin: 0 0 12px 0 !important;
         }
         #booostr-hover-cart .total span:first-child {
            font-size: 14px !important;
            font-weight: 700 !important;
            color: #475569 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.5px !important;
         }
         #booostr-hover-cart .total .total-amount {
            background: transparent !important;
            background-color: transparent !important;
            border-radius: 0 !important;
            padding: 0 !important;
            color: #0f172a !important;
            font-size: 20px !important;
            font-weight: 800 !important;
            display: inline-block !important;
            box-shadow: none !important;
            letter-spacing: -0.3px !important;
         }

         #booostr-hover-cart .bottom a.btn.btn-view-cart,
         #booostr-hover-cart .bottom a.btn:not(.primary) {
            background: #ffffff !important;
            background-color: #ffffff !important;
            border: 1.5px solid var(--booostr-blue, #0A8CDE) !important;
            color: var(--booostr-blue, #0A8CDE) !important;
            border-radius: 8px !important;
            font-weight: 700 !important;
            font-size: 14px !important;
            text-transform: uppercase !important;
            letter-spacing: 0.4px !important;
            text-align: center !important;
            padding: 13px 20px !important;
            display: block !important;
            margin-top: 8px !important;
            box-shadow: none !important;
            transition: all 0.2s ease !important;
         }
         #booostr-hover-cart .bottom a.btn.btn-view-cart:hover,
         #booostr-hover-cart .bottom a.btn:not(.primary):hover {
            background: var(--booostr-blue-light, #e8f4fc) !important;
            background-color: var(--booostr-blue-light, #e8f4fc) !important;
            border-color: var(--booostr-blue-hover, #0870b2) !important;
            color: var(--booostr-blue-hover, #0870b2) !important;
         }

         #booostr-hover-cart .bottom a.btn.btn-checkout,
         #booostr-hover-cart .bottom a.btn.primary {
            background: var(--booostr-blue, #0A8CDE) !important;
            background-color: var(--booostr-blue, #0A8CDE) !important;
            border: 1.5px solid var(--booostr-blue, #0A8CDE) !important;
            color: #ffffff !important;
            border-radius: 8px !important;
            font-weight: 700 !important;
            font-size: 14px !important;
            text-transform: uppercase !important;
            letter-spacing: 0.4px !important;
            text-align: center !important;
            padding: 13px 20px !important;
            display: block !important;
            margin-top: 8px !important;
            box-shadow: 0 4px 12px rgba(10, 140, 222, 0.25) !important;
            transition: all 0.2s ease !important;
         }
         #booostr-hover-cart .bottom a.btn.btn-checkout:hover,
         #booostr-hover-cart .bottom a.btn.primary:hover {
            background: var(--booostr-blue-hover, #0870b2) !important;
            background-color: var(--booostr-blue-hover, #0870b2) !important;
            border-color: var(--booostr-blue-hover, #0870b2) !important;
            color: #ffffff !important;
            box-shadow: 0 6px 16px rgba(8, 112, 178, 0.35) !important;
         }
      </style>

      <script>
         window.booostrFormatPrice = function(amount) {
            if (amount === undefined || amount === null || amount === '') return '$0.00';
            var clean = String(amount).replace(/[^0-9.-]+/g, '');
            var num = parseFloat(clean);
            if (isNaN(num)) return '$0.00';
            return '$' + num.toFixed(2);
         };

         (function() {
            function formatCartSubtotals() {
               var elements = document.querySelectorAll('#booostr-hover-cart .cart_subtotal');
               elements.forEach(function(el) {
                  var txt = el.textContent || el.innerText || '';
                  if (txt && !txt.trim().startsWith('$')) {
                     el.textContent = window.booostrFormatPrice(txt);
                  }
               });
            }

            if (typeof MutationObserver !== 'undefined') {
               var observer = new MutationObserver(function() {
                  formatCartSubtotals();
               });
               document.addEventListener('DOMContentLoaded', function() {
                  var target = document.getElementById('booostr-hover-cart');
                  if (target) {
                     observer.observe(target, { childList: true, subtree: true, characterData: true });
                  }
                  formatCartSubtotals();
               });
            }
         })();
      </script>
      @endif
      
      <!-- Header Area -->
      <header class="header navbar-area">
         <div class="container">
            <div class="row align-items-center">
               <div class="col-12">
                  <div class="nav-inner">
                     <!-- navbar -->
                     <nav class="navbar navbar-expand-lg">
                        <a class="navbar-brand" href="{{ url('/') }}" id="clubBrandContainer" style="display: inline-flex; align-items: center; text-decoration: none; margin-right: 30px; background: transparent !important;">
                           @if(!empty($clubLogo))
                              <img id="clubNavbarLogo" src="{{ $clubLogo }}" alt="{{ $clubName }}" style="max-height: 44px; width: auto; object-fit: contain; display: block; background: transparent !important;" />
                           @elseif(!empty($clubName))
                              <span id="clubNavbarName" style="font-size: 20px; font-weight: 700; color: #ffffff; letter-spacing: -0.3px; display: inline-block;">{{ $clubName }}</span>
                           @endif
                        </a>
                        <!-- Responsive Nav Button -->
                        <button class="navbar-toggler mobile-menu-btn" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation" >
                           <span class="toggler-icon"></span>
                           <span class="toggler-icon"></span>
                           <span class="toggler-icon"></span>
                        </button>
                        <div class="collapse navbar-collapse sub-menu-bar" id="navbarSupportedContent">
                           <ul id="nav" class="navbar-nav ms-auto">
                              {{ThemeMenu('header','theme.resto.components.menu')}}
                           </ul>
                        </div>
                     </nav>
                     <!-- End navbar -->
                     <div class="right-bar d-flex align-items-center">
                        <ul class="cart-list-top mb-0 d-flex align-items-center">
                           <li class="single-bar"><a href="{{ url('/cart') }}" class="icon" title="{{ __('Cart') }}"><i class="icofont-cart-alt"></i><span class="count cart_count">{{ $cart_count }}</span></a></li>

                           <li class="single-bar"><a href="{{ url('/wishlist') }}" class="icon" title="{{ __('Wishlist') }}"><i class="icofont-heart-alt"></i><span class="count wishlist_count">{{ $wishlist_count }}</span></a></li>
                        </ul>

                        @if(Auth::check())
                        @php
                           $headerUser = Auth::user();
                           $headerMeta = !empty($headerUser->meta) ? json_decode($headerUser->meta, true) : [];
                           $headerAvatar = !empty($headerMeta['avatar']) ? $headerMeta['avatar'] : null;
                        @endphp
                        <!-- User Profile Dropdown (Separate from cart-list-top to prevent CSS conflicts) -->
                        <div class="user-custom-dropdown ms-3" style="position: relative;">
                           <a href="javascript:void(0)" class="user-avatar-circle-btn d-flex align-items-center justify-content-center" id="userMenuToggleBtn" title="{{ $headerUser->name }}">
                              @if(!empty($headerAvatar))
                                 <img src="{{ asset($headerAvatar) }}" alt="{{ $headerUser->name }}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
                              @else
                                 <i class="icofont-ui-user"></i>
                              @endif
                           </a>
                           <div class="user-custom-menu shadow" id="userCustomDropdownMenu">
                              <div class="user-dropdown-header d-flex align-items-center gap-2" style="padding: 12px 16px;">
                                 @if(!empty($headerAvatar))
                                    <img src="{{ asset($headerAvatar) }}" alt="{{ $headerUser->name }}" class="rounded-circle border" style="width: 38px; height: 38px; object-fit: cover; flex-shrink: 0;">
                                 @else
                                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; font-weight: 700; font-size: 15px; flex-shrink: 0;">
                                       {{ strtoupper(substr($headerUser->name, 0, 1)) }}
                                    </div>
                                 @endif
                                 <div style="min-width: 0; flex: 1;">
                                    <div class="user-dropdown-name text-truncate" style="font-weight: 600; font-size: 14px; color: #1e293b;">{{ $headerUser->name }}</div>
                                    <div class="user-dropdown-email text-truncate" style="font-size: 12px; color: #64748b;">{{ $headerUser->email }}</div>
                                 </div>
                              </div>
                              <div class="user-dropdown-body">
                                 <a class="custom-dropdown-item" href="{{ url('/customer/dashboard') }}">
                                    <i class="icofont-dashboard-web"></i> <span>{{ __('My Dashboard') }}</span>
                                 </a>
                                 <a class="custom-dropdown-item" href="{{ url('/customer/orders') }}">
                                    <i class="icofont-box"></i> <span>{{ __('Purchase History') }}</span>
                                 </a>
                                 <a class="custom-dropdown-item" href="{{ url('/customer/edit-profile') }}">
                                    <i class="icofont-ui-user"></i> <span>{{ __('Edit Profile') }}</span>
                                 </a>
                                 @if(Auth::user()->role_id != 3)
                                 <a class="custom-dropdown-item" href="{{ url('/customer/change-password') }}">
                                    <i class="icofont-key"></i> <span>{{ __('Change Password') }}</span>
                                 </a>
                                 @endif
                                 <div class="user-dropdown-divider"></div>
                                 <a class="custom-dropdown-item text-danger" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('navbar-logout-form').submit();">
                                    <i class="icofont-logout"></i> <span>{{ __('Logout') }}</span>
                                 </a>
                                 <form id="navbar-logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                    @csrf
                                 </form>
                              </div>
                           </div>
                        </div>
                        @else
                        <!-- Guest Auth Buttons -->
                        <div class="user-auth-links ms-3 d-flex align-items-center">
                           <a href="{{ url('/customer/login') }}" class="nav-auth-btn-login">
                              {{ __('Login') }}
                           </a>
                           <a href="{{ url('/customer/register') }}" class="nav-auth-btn-register ms-2">
                              {{ __('Register') }}
                           </a>
                        </div>
                        @endif
                     </div>
                        <!--/ End Shopping Cart -->
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </header>
      <!-- End Header Area -->

      <script>
      // User Profile Dropdown Toggle (Popper-independent, error-free)
      document.addEventListener('DOMContentLoaded', function() {
         const toggleBtn = document.getElementById('userMenuToggleBtn');
         const menu = document.getElementById('userCustomDropdownMenu');
         const arrow = document.getElementById('userMenuDropdownArrow');

         if (!toggleBtn || !menu) return;

         toggleBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const isOpen = menu.style.display === 'block';
            menu.style.display = isOpen ? 'none' : 'block';
            if (arrow) arrow.style.transform = isOpen ? 'rotate(0deg)' : 'rotate(180deg)';
         });

         // Close on outside click
         document.addEventListener('click', function(e) {
            if (!toggleBtn.contains(e.target) && !menu.contains(e.target)) {
               menu.style.display = 'none';
               if (arrow) arrow.style.transform = 'rotate(0deg)';
            }
         });

         // Close on Escape key
         document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
               menu.style.display = 'none';
               if (arrow) arrow.style.transform = 'rotate(0deg)';
            }
         });
      });
      </script>

      <style>
      .user-avatar-circle-btn {
         width: 30px !important;
         height: 30px !important;
         border: 1px solid rgba(255, 255, 255, 0.65) !important;
         border-radius: 50% !important;
         color: #ffffff !important;
         background: transparent !important;
         text-decoration: none !important;
         cursor: pointer !important;
         display: inline-flex !important;
         align-items: center !important;
         justify-content: center !important;
         transition: all 0.2s ease !important;
      }
      .user-avatar-circle-btn i {
         font-size: 15px !important;
         line-height: 1 !important;
      }
      .user-avatar-circle-btn:hover {
         border-color: #ffffff !important;
         background: rgba(255, 255, 255, 0.2) !important;
         color: #ffffff !important;
      }
      .user-custom-menu {
         display: none;
         position: absolute;
         right: 0;
         top: calc(100% + 12px);
         background: #ffffff;
         border-radius: 10px;
         border: 1px solid #e2e8f0;
         box-shadow: 0 12px 30px rgba(0, 0, 0, 0.12);
         padding: 0;
         width: 220px;
         z-index: 1050;
         overflow: hidden;
         text-align: left;
      }
      .user-dropdown-header {
         padding: 12px 16px;
         background: #f8fafc;
         border-bottom: 1px solid #e2e8f0;
      }
      .user-dropdown-name {
         font-size: 13.5px;
         font-weight: 700;
         color: #1e293b;
         overflow: hidden;
         text-overflow: ellipsis;
         white-space: nowrap;
      }
      .user-dropdown-email {
         font-size: 11.5px;
         color: #64748b;
         margin-top: 2px;
         overflow: hidden;
         text-overflow: ellipsis;
         white-space: nowrap;
      }
      .user-dropdown-body {
         padding: 6px 0;
      }
      .user-dropdown-divider {
         height: 1px;
         background: #e2e8f0;
         margin: 6px 0;
      }
      .custom-dropdown-item {
         display: flex !important;
         align-items: center !important;
         width: 100% !important;
         height: auto !important;
         min-height: 38px !important;
         line-height: 1.5 !important;
         border: none !important;
         border-radius: 0 !important;
         background: transparent !important;
         padding: 8px 16px !important;
         color: #334155 !important;
         font-size: 13.5px !important;
         font-weight: 500 !important;
         text-align: left !important;
         text-decoration: none !important;
         box-sizing: border-box !important;
         transition: background-color 0.15s ease, color 0.15s ease !important;
      }
      .custom-dropdown-item i {
         font-size: 16px !important;
         color: #64748b !important;
         margin-right: 12px !important;
         width: 18px !important;
         text-align: center !important;
         display: inline-block !important;
         line-height: 1 !important;
      }
      .custom-dropdown-item span {
         display: inline-block !important;
         white-space: nowrap !important;
         overflow: hidden !important;
         text-overflow: ellipsis !important;
      }
      .custom-dropdown-item:hover {
         background-color: #f1f5f9 !important;
         color: var(--primary, #0A8CDE) !important;
      }
      .custom-dropdown-item:hover i {
         color: var(--primary, #0A8CDE) !important;
      }
      .custom-dropdown-item.text-danger {
         color: #dc2626 !important;
      }
      .custom-dropdown-item.text-danger i {
         color: #dc2626 !important;
      }
      .custom-dropdown-item.text-danger:hover {
         background-color: #fef2f2 !important;
         color: #ef4444 !important;
      }
      .custom-dropdown-item.text-danger:hover i {
         color: #ef4444 !important;
      }
      .nav-auth-btn-login {
         color: #ffffff !important;
         font-size: 13px !important;
         font-weight: 600 !important;
         text-decoration: none !important;
         padding: 5px 14px !important;
         border: 1px solid rgba(255, 255, 255, 0.5) !important;
         border-radius: 20px !important;
         transition: all 0.2s ease !important;
         line-height: 1.4 !important;
         display: inline-block !important;
      }
      .nav-auth-btn-login:hover {
         background: rgba(255, 255, 255, 0.15) !important;
         color: #ffffff !important;
         border-color: #ffffff !important;
      }
      .nav-auth-btn-register {
         background: #ffffff !important;
         color: var(--primary, #0A8CDE) !important;
         font-size: 13px !important;
         font-weight: 600 !important;
         text-decoration: none !important;
         padding: 5px 14px !important;
         border-radius: 20px !important;
         transition: all 0.2s ease !important;
         box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15) !important;
         line-height: 1.4 !important;
         display: inline-block !important;
         border: 1px solid #ffffff !important;
      }
      .nav-auth-btn-register:hover {
         background: #f1f5f9 !important;
         color: var(--booostr-blue-hover, #0870b2) !important;
         border-color: #f1f5f9 !important;
      }
      </style>

      