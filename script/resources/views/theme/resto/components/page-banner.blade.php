@php
    // Fetch Store Banner image and URL from Store Settings
    $storeBanner = get_option('banner_logo') ?: '';
    $storeBannerUrl = get_option('banner_url') ?: '';

    // Link is fetched and prepared, but kept disabled by default as requested
    $enableBannerLink = $enable_link ?? false;

    $hasBannerImage = !empty($storeBanner);
    $finalBannerUrl = '';
    if ($hasBannerImage) {
        $finalBannerUrl = \Illuminate\Support\Str::startsWith($storeBanner, ['http://', 'https://', '//']) 
            ? $storeBanner 
            : asset($storeBanner);
    }

    $isHome = $is_home ?? false;
@endphp

@if($isHome)
    @if($hasBannerImage)
        <div class="store-banner-area w-100 overflow-hidden" style="background-color: #f8f9fa;">
            <div class="container-fluid p-0">
                <div class="w-100 text-center">
                    @if($enableBannerLink && !empty($storeBannerUrl))
                        <a href="{{ $storeBannerUrl }}" class="d-block w-100">
                            <img src="{{ $finalBannerUrl }}" alt="{{ tenant('store_name') ?? 'Store Banner' }}" class="d-block w-100" style="width: 100%; height: auto; max-height: 420px; min-height: 180px; object-fit: cover; object-position: center;">
                        </a>
                    @else
                        <img src="{{ $finalBannerUrl }}" alt="{{ tenant('store_name') ?? 'Store Banner' }}" class="d-block w-100" style="width: 100%; height: auto; max-height: 420px; min-height: 180px; object-fit: cover; object-position: center;">
                    @endif
                </div>
            </div>
        </div>
    @else
        <div class="store-banner-area w-100 overflow-hidden" style="background-color: #528CD7; min-height: 140px; display: flex; align-items: center; justify-content: center;">
            <div class="container text-center py-4">
                <h2 style="color: #ffffff; font-weight: 700; margin-bottom: 0;">{{ tenant('store_name') ?? config('app.name') }}</h2>
            </div>
        </div>
    @endif
@else
    <!-- Inner Pages (Cart, etc.) Breadcrumbs Area -->
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
@endif
