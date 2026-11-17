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
               <div class="single-cart-show"><a href="{{ Auth::check() && Auth::user()->role_id == 3 ? url('/seller/dashboard') : url('/customer/dashboard') }}"><i class="{{ Auth::check() && Auth::user()->role_id == 3 ? 'icofont-dashboard-web' : 'icofont-ui-user' }}"></i><span>{{ Auth::check() && Auth::user()->role_id == 3 ? __('Admin') : __('Account') }}</span></a></div>
            </li>
         </ul>
      </div>
      <!-- End Mobile Cart Show -->
    @endif
         
               
      <!-- Shopping Item -->
      <div class="shopping-item">
         <div class="dropdown-cart-header">
            <div class="close-button"><a href="#"><i class="icofont-close"></i></a></div>
            <span><span class="cart_count">{{ $cart_count }}</span> {{ __('Items') }}</span>
         </div>
         <div class="shopping-item-inner">
            <div class="cart-single-inner">
               <ul id="shopping" class="shopping-list">
                  
                  
               </ul>
            </div>
            <div class="cart-single-inner bottom">
               <div class="total">
                  <span>{{ __('Total') }}</span>
                  <span class="total-amount cart_subtotal">{{ Cart::instance('default')->subtotal() }}</span>
               </div>
               <a href="{{ url('/cart') }}" class="btn animate">{{ __('View Cart') }}</a>
               <a href="{{ url('/checkout') }}" class="btn primary animate">{{ __('Checkout') }}</a>
            </div>
         </div>
      </div>
      <!--/ End Shopping Item -->
      @endif
      
      <!-- Header Area -->
      <header class="header navbar-area">
         <div class="container">
            <div class="row align-items-center">
               <div class="col-12">
                  <div class="nav-inner">
                     <!-- navbar -->
                     <nav class="navbar navbar-expand-lg">
                        <a class="navbar-brand" href="{{ url('/') }}" id="clubBrandContainer" style="display: inline-flex; align-items: center; text-decoration: none; margin-right: 30px;">
                           @if(!empty($clubLogo))
                              <span class="club-navbar-logo-badge" style="background: #ffffff; padding: 4px 10px; border-radius: 8px; display: inline-flex; align-items: center; justify-content: center; box-shadow: 0 2px 8px rgba(0,0,0,0.15); min-height: 44px;">
                                 <img id="clubNavbarLogo" src="{{ $clubLogo }}" alt="{{ $clubName }}" style="max-height: 38px; width: auto; object-fit: contain; display: block;" />
                              </span>
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
                        <!-- User Profile Dropdown (Separate from cart-list-top to prevent CSS conflicts) -->
                        <div class="user-custom-dropdown ms-3" style="position: relative;">
                           <a href="javascript:void(0)" class="user-avatar-circle-btn d-flex align-items-center justify-content-center" id="userMenuToggleBtn" title="{{ Auth::user()->name }}">
                              <i class="icofont-ui-user"></i>
                           </a>
                           <div class="user-custom-menu shadow" id="userCustomDropdownMenu">
                              <div class="user-dropdown-header">
                                 <div class="user-dropdown-name">{{ Auth::user()->name }}</div>
                                 <div class="user-dropdown-email">{{ Auth::user()->email }}</div>
                              </div>
                              <div class="user-dropdown-body">
                                 @if(Auth::user()->role_id == 3)
                                    <a class="custom-dropdown-item" href="{{ url('/seller/dashboard') }}">
                                       <i class="icofont-dashboard-web"></i> <span>{{ __('Admin Dashboard') }}</span>
                                    </a>
                                 @else
                                    <a class="custom-dropdown-item" href="{{ url('/customer/dashboard') }}">
                                       <i class="icofont-dashboard-web"></i> <span>{{ __('My Dashboard') }}</span>
                                    </a>
                                    <a class="custom-dropdown-item" href="{{ url('/customer/orders') }}">
                                       <i class="icofont-box"></i> <span>{{ __('My Orders') }}</span>
                                    </a>
                                    <a class="custom-dropdown-item" href="{{ url('/customer/settings') }}">
                                       <i class="icofont-ui-settings"></i> <span>{{ __('Settings') }}</span>
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
         color: #528CD7 !important;
      }
      .custom-dropdown-item:hover i {
         color: #528CD7 !important;
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
         background: #528CD7 !important;
         color: #ffffff !important;
         font-size: 13px !important;
         font-weight: 600 !important;
         text-decoration: none !important;
         padding: 5px 14px !important;
         border-radius: 20px !important;
         transition: all 0.2s ease !important;
         box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15) !important;
         line-height: 1.4 !important;
         display: inline-block !important;
         border: 1px solid #528CD7 !important;
      }
      .nav-auth-btn-register:hover {
         background: #467fcb !important;
         color: #ffffff !important;
         border-color: #467fcb !important;
      }
      </style>

      