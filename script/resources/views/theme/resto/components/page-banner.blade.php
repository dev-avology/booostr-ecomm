@php
    $storeBanner = get_option('banner_logo') ?: '';
    $pageBanner = $banner ?? ($page_data->cart_page_banner ?? '');

    // Ignore placeholder dummy food banners from seeders
    if (!empty($pageBanner) && (str_contains($pageBanner, 'dummy') || str_contains($pageBanner, '61cde8e0371023012211640884448'))) {
        $pageBanner = '';
    }

    $finalBanner = !empty($storeBanner) ? $storeBanner : $pageBanner;
    $hasBannerImage = !empty($finalBanner);
    $finalBannerUrl = '';
    if ($hasBannerImage) {
        $finalBannerUrl = \Illuminate\Support\Str::startsWith($finalBanner, ['http://', 'https://', '//']) 
            ? $finalBanner 
            : asset($finalBanner);
    }
@endphp

<!-- Start Breadcrumbs Area -->
<div class="breadcrumbs" style="{{ $hasBannerImage ? "background-image: url('{$finalBannerUrl}'); background-size: cover; background-position: center; background-repeat: no-repeat;" : 'background-color: #528CD7 !important; background-image: none !important;' }}">
    <div class="container">
        <div class="row">
            <div class="col-lg-6 col-md-8 col-12">
                <div class="breadcrumbs-content">
                    <h1 class="page-title">{{ $title ?? __('Cart') }}</h1>
                    @if(!empty($description))
                        <p>{{ $description }}</p>
                    @endif
                </div>
                <ul class="breadcrumb-nav">
                    @foreach($breadcrumbs ?? [['url' => url('/'), 'label' => __('Home')], ['url' => '', 'label' => $title ?? __('Cart')]] as $crumb)
                        @if(!empty($crumb['url']))
                            <li><a href="{{ $crumb['url'] }}"><i class="{{ $crumb['icon'] ?? 'icofont-home' }}"></i> {{ $crumb['label'] }}</a></li>
                        @else
                            <li>{{ $crumb['label'] }}</li>
                        @endif
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>
<!--/ End Breadcrumbs Area -->

