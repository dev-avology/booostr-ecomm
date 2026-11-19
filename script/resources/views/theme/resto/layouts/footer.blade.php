@php
// Club / Store details matching Header & Admin Panel
$clubInfo = function_exists('tenant_club_info') ? (tenant_club_info() ?: []) : [];
$clubName = trim((string) ($clubInfo['club_name'] ?? (tenant('id') ?? '')));
$clubLogo = function_exists('tenant_club_logo') ? tenant_club_logo() : null;

// Top 4 categories with active products, sorted by highest product count
try {
    $footerCategories = \App\Models\Category::where('type', 'category')
        ->where('status', 1)
        ->has('products')
        ->withCount('products')
        ->orderBy('products_count', 'desc')
        ->take(4)
        ->get();
} catch (\Exception $e) {
    $footerCategories = collect();
}
@endphp

<!-- Start Footer Area  -->
<footer class="footer">
   <div class="footer-middle">
      <div class="container">
         <div class="bottom-inner">
            <div class="row">
               <!-- Column 1: Store Brand & Info -->
               <div class="col-lg-3 col-md-6 col-12">
                  <div class="single-footer f-about">
                     <div class="footer-logo mb-3">
                        <a href="{{ url('/') }}" style="display: inline-flex; align-items: center; text-decoration: none; background: transparent !important;">
                           @if(!empty($clubLogo))
                              <img src="{{ $clubLogo }}" alt="{{ $clubName }}" style="max-height: 48px; width: auto; object-fit: contain; display: block; background: transparent !important; filter: drop-shadow(0 0 1px #ffffff) drop-shadow(0 0 3px rgba(255, 255, 255, 0.85)); -webkit-filter: drop-shadow(0 0 1px #ffffff) drop-shadow(0 0 3px rgba(255, 255, 255, 0.85));" />
                           @elseif(!empty($clubName))
                              <span style="font-size: 20px; font-weight: 700; color: #ffffff; letter-spacing: -0.3px; display: inline-block;">{{ $clubName }}</span>
                           @endif
                        </a>
                     </div>
                     <p style="color: #cbd5e1; font-size: 14px; line-height: 1.6; margin-bottom: 0;">
                        {{ __('Welcome to our official store. Browse our merchandise and support our club and community.') }}
                     </p>
                  </div>
               </div>

               <!-- Column 2: Shop Categories -->
               <div class="col-lg-3 col-md-6 col-12">
                  <div class="single-footer f-link">
                     <h3>{{ __('Shop Categories') }}</h3>
                     <ul>
                        <li><a href="{{ url('/products') }}">{{ __('All Categories') }}</a></li>
                        @if(!empty($footerCategories) && count($footerCategories) > 0)
                           @foreach($footerCategories as $fCat)
                              @if(!empty($fCat->slug) && !empty($fCat->name))
                                 <li><a href="{{ url('/category/' . $fCat->slug) }}">{{ $fCat->name }}</a></li>
                              @endif
                           @endforeach
                        @endif
                     </ul>
                  </div>
               </div>

               <!-- Column 3: Quick Links -->
               <div class="col-lg-3 col-md-6 col-12">
                  <div class="single-footer f-link">
                     <h3>{{ __('Quick Links') }}</h3>
                     <ul>
                        <li><a href="{{ url('/') }}">{{ __('Home') }}</a></li>
                        <li><a href="{{ url('/products') }}">{{ __('All Products') }}</a></li>
                        <li><a href="{{ url('/contact') }}">{{ __('Contact Us') }}</a></li>
                        <li><a href="{{ url('/cart') }}">{{ __('Shopping Cart') }}</a></li>
                        <li><a href="{{ url('/wishlist') }}">{{ __('Wishlist') }}</a></li>
                     </ul>
                  </div>
               </div>

               <!-- Column 4: Customer Care -->
               <div class="col-lg-3 col-md-6 col-12">
                  <div class="single-footer f-link">
                     <h3>{{ __('Customer Care') }}</h3>
                     <ul>
                        <li><a href="{{ url('/contact') }}">{{ __('Need Help? Contact Us') }}</a></li>
                        <li><a href="{{ url('/page/terms-and-conditions') }}">{{ __('Terms and Conditions') }}</a></li>
                     </ul>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>

   <!-- Footer Bottom Bar -->
   <div class="footer-bottom">
      <div class="container">
         <div class="inner-content">
            <div class="row align-items-center">
               <div class="col-lg-6 col-md-6 col-12 text-md-start text-center">
                  <div class="copyright">
                     <p style="margin: 0; color: rgba(255, 255, 255, 0.85); font-size: 14px;">
                        © {{ date('Y') }} {{ !empty($clubName) ? $clubName : (tenant('id') ?? 'Booostr') }}. {{ __('All rights reserved.') }}
                     </p>
                  </div>
               </div>
               <div class="col-lg-6 col-md-6 col-12 text-md-end text-center mt-2 mt-md-0">
                  <div class="footer-legal-links d-flex align-items-center justify-content-md-end justify-content-center" style="gap: 16px;">
                     <a href="{{ url('/page/terms-and-conditions') }}" class="footer-terms-link">{{ __('Terms and Conditions') }}</a>
                     <span style="color: rgba(255,255,255,0.25);">|</span>
                     <span style="color: rgba(255, 255, 255, 0.85); font-size: 13px;">{{ __('Powered by') }} <a href="https://booostr.co" target="_blank" rel="noopener noreferrer" style="color: #ffffff; text-decoration: underline; font-weight: 700;">Booostr</a></span>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</footer>
<!-- End Footer Area  -->

<style>
   .footer-terms-link {
      color: rgba(255, 255, 255, 0.85) !important;
      text-decoration: none !important;
      font-size: 13px !important;
      font-weight: 500 !important;
      transition: color 0.2s ease !important;
   }
   .footer-terms-link:hover {
      color: #ffffff !important;
      text-decoration: underline !important;
   }
</style>

<!-- scroll-top -->
@if(($site_settings->scroll_to_top ?? 'yes') == 'yes')
<a href="#" class="scroll-top btn-hover"><i class="icofont-long-arrow-up"></i></a>
@endif

