@php
$invoice_data=json_decode($autoload_data['invoice_data'] ?? '');
$languages=json_decode($autoload_data['languages']);
$default_language=$autoload_data['default_language'] ?? '';
$current_url=url()->current();
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
      <!-- Topbar Area -->
      @if(Auth::check())
      <div class="topbar-area">
         <div class="container">
            <div class="row">
               <div class="col-12">
                  <!-- Topbar Right -->
                  <div class="topbar-right">
                      <div class="topbar-language-section">
                        <li class="topbar-language">
                           <form class="change_lang_form" action="{{ url('/locale/lang') }}">
                              <select name="locale" class="trans_lang">
                                 @php
                                 $local=Session::get('locale');
                                 @endphp
                              @foreach($languages ?? [] as $key => $row)
                              <option value="{{ $row->code }}" @if($local == $row->code) selected @endif>{{ $row->name }}</option>
                              @endforeach
                           </select>
                          </form>
                        </li>
                      </div>
                     <ul class="topbar-right-inner">
                        <!-- Topbar Language -->
                        @if(tenant('customer_modules') == 'on')
                        @if(Auth::check() && Auth::user()->role_id == 3)
                        <li class="accounts-top-btn"><a href="{{ url('/seller/dashboard') }}"><i class="icofont-dashboard-web"></i><span>{{ __('Admin Dashboard') }}</span></a></li>
                        @elseif(Auth::check() && Auth::user()->role_id == 4)
                        <li class="accounts-top-btn"><a href="{{ url('/customer/dashboard') }}"><i class="icofont-user-male"></i><span>{{ Auth::user()->name }}</span></a></li>
                        @endif
                        @endif
                     </ul>
                     
                  </div>
               </div>
            </div>
         </div>
      </div>
      @endif
      <!-- End Topbar Area -->
      
      <!-- Header Area -->
      <header class="header navbar-area">
         <div class="container">
            <div class="row align-items-center">
               <div class="col-12">
                  <div class="nav-inner">
                     <!-- navbar -->
                     <nav class="navbar navbar-expand-lg">
                        <a class="navbar-brand" href="{{ url('/') }}" id="clubBrandContainer" style="display: inline-flex; align-items: center; text-decoration: none; margin-right: 30px;">
                           <img id="clubNavbarLogo" src="" alt="" style="max-height: 48px; width: auto; object-fit: contain; display: none;" />
                           <span id="clubNavbarName" style="font-size: 20px; font-weight: 700; color: #ffffff; letter-spacing: -0.3px; display: none;"></span>
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
                        <ul class="cart-list-top mb-0">
                           <li class="single-bar"><a href="{{ url('/cart') }}" class="icon" title="{{ __('Cart') }}"><i class="icofont-cart-alt"></i><span class="count cart_count">{{ $cart_count }}</span></a></li>

                           <li class="single-bar"><a href="{{ url('/wishlist') }}" class="icon" title="{{ __('Wishlist') }}"><i class="icofont-heart-alt"></i><span class="count wishlist_count">{{ $wishlist_count }}</span></a></li>
                        </ul>

                        <!-- User Auth Section -->
                        <div class="nav-auth-section ms-3">
                           @if(Auth::check())
                              <div class="user-custom-dropdown" style="position: relative;">
                                 <button type="button" class="user-auth-toggle d-flex align-items-center text-white" id="userMenuToggleBtn" style="background: none; border: none; padding: 0; cursor: pointer; text-decoration: none; font-size: 14px; font-weight: 500;">
                                    <span class="user-avatar-circle" style="width: 32px; height: 32px; background: rgba(255,255,255,0.2); border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; margin-right: 8px;">
                                       <i class="icofont-ui-user" style="font-size: 16px; color: #ffffff;"></i>
                                    </span>
                                    <span class="user-display-name d-none d-md-inline" style="max-width: 130px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                       {{ Auth::user()->name }}
                                    </span>
                                    <i class="icofont-rounded-down ms-1" style="font-size: 13px; color: rgba(255,255,255,0.7); transition: transform 0.2s ease;" id="userMenuDropdownArrow"></i>
                                 </button>
                                 <div class="user-custom-menu shadow" id="userCustomDropdownMenu" style="display: none; position: absolute; right: 0; top: calc(100% + 12px); background: #ffffff; border-radius: 8px; border: 1px solid #edf2f7; box-shadow: 0 10px 25px rgba(0,0,0,0.12); padding: 8px 0; min-width: 190px; z-index: 1050;">
                                    @if(Auth::user()->role_id == 3)
                                       <a class="custom-dropdown-item py-2 px-3 d-flex align-items-center" href="{{ url('/seller/dashboard') }}" style="color: #334155; font-size: 14px; text-decoration: none; transition: background 0.15s ease;">
                                          <i class="icofont-dashboard-web me-2" style="font-size: 16px; color: #64748b;"></i> {{ __('Admin Dashboard') }}
                                       </a>
                                    @else
                                       <a class="custom-dropdown-item py-2 px-3 d-flex align-items-center" href="{{ url('/customer/dashboard') }}" style="color: #334155; font-size: 14px; text-decoration: none; transition: background 0.15s ease;">
                                          <i class="icofont-dashboard-web me-2" style="font-size: 16px; color: #64748b;"></i> {{ __('My Dashboard') }}
                                       </a>
                                       <a class="custom-dropdown-item py-2 px-3 d-flex align-items-center" href="{{ url('/customer/orders') }}" style="color: #334155; font-size: 14px; text-decoration: none; transition: background 0.15s ease;">
                                          <i class="icofont-box me-2" style="font-size: 16px; color: #64748b;"></i> {{ __('My Orders') }}
                                       </a>
                                       <a class="custom-dropdown-item py-2 px-3 d-flex align-items-center" href="{{ url('/customer/settings') }}" style="color: #334155; font-size: 14px; text-decoration: none; transition: background 0.15s ease;">
                                          <i class="icofont-ui-settings me-2" style="font-size: 16px; color: #64748b;"></i> {{ __('Settings') }}
                                       </a>
                                    @endif
                                    <div style="height: 1px; background: #e2e8f0; margin: 6px 0;"></div>
                                    <a class="custom-dropdown-item py-2 px-3 d-flex align-items-center text-danger" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('navbar-logout-form').submit();" style="font-size: 14px; text-decoration: none; transition: background 0.15s ease;">
                                       <i class="icofont-logout me-2" style="font-size: 16px;"></i> {{ __('Logout') }}
                                    </a>
                                    <form id="navbar-logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                       @csrf
                                    </form>
                                 </div>
                              </div>
                           @else
                              <div class="user-auth-links d-flex align-items-center">
                                 <a href="{{ url('/customer/login') }}" class="nav-auth-link" style="color: #ffffff; font-size: 14px; font-weight: 600; text-decoration: none; padding: 4px 8px; transition: opacity 0.2s ease;">
                                    {{ __('Login') }}
                                 </a>
                                 <span style="color: rgba(255,255,255,0.4); margin: 0 2px;">/</span>
                                 <a href="{{ url('/customer/register') }}" class="nav-auth-link" style="color: #ffffff; font-size: 14px; font-weight: 600; text-decoration: none; padding: 4px 8px; transition: opacity 0.2s ease;">
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

      <!-- Lazy-load Club Logo directly from booostr.site API without blocking page load -->
      <script>
      (function() {
         const clubId = {!! json_encode((string) (tenant('id') ?? '')) !!};
         if (!clubId) return;

         function renderClubData(data) {
            if (!data) return;
            const logoImg = document.getElementById('clubNavbarLogo');
            const nameSpan = document.getElementById('clubNavbarName');

            // Strictly display only if actual logo returned (never show dummy default)
            if (data.logo && typeof data.logo === 'string' && data.logo.trim() !== '') {
               if (logoImg) {
                  logoImg.src = data.logo;
                  logoImg.alt = data.name || 'Club Logo';
                  logoImg.style.display = 'inline-block';
               }
               if (nameSpan) nameSpan.style.display = 'none';
            } else if (data.name && typeof data.name === 'string' && data.name.trim() !== '') {
               if (nameSpan) {
                  nameSpan.textContent = data.name;
                  nameSpan.style.display = 'inline-block';
               }
               if (logoImg) logoImg.style.display = 'none';
            }
         }

         // 1. Instant display from sessionStorage if already available
         try {
            const cached = sessionStorage.getItem('booostr_club_info_' + clubId);
            if (cached) {
               renderClubData(JSON.parse(cached));
            }
         } catch (e) {}

         // 2. Direct live fetch (Non-blocking async request directly to booostr.site API)
         fetch('https://booostr.site/api/club-info/' + encodeURIComponent(clubId), {
            headers: { 'Accept': 'application/json' }
         })
         .then(function(res) {
            return res.json();
         })
         .then(function(res) {
            if (res && res.success && res.data) {
               renderClubData(res.data);
               try {
                  sessionStorage.setItem('booostr_club_info_' + clubId, JSON.stringify(res.data));
               } catch (e) {}
            }
         })
         .catch(function() {});
      })();

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
      .custom-dropdown-item:hover {
         background-color: #f8fafc !important;
         color: #528CD7 !important;
      }
      .custom-dropdown-item:hover i {
         color: #528CD7 !important;
      }
      .custom-dropdown-item.text-danger:hover {
         background-color: #fef2f2 !important;
         color: #ef4444 !important;
      }
      .custom-dropdown-item.text-danger:hover i {
         color: #ef4444 !important;
      }
      </style>

      