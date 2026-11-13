@php
    // Use store banner from Store Settings (banner_logo)
    $storeBanner = $banner ?? (get_option('banner_logo') ?: '');
    $hasBannerImage = !empty($storeBanner);
    $finalBannerUrl = '';
    if ($hasBannerImage) {
        $finalBannerUrl = \Illuminate\Support\Str::startsWith($storeBanner, ['http://', 'https://', '//']) 
            ? $storeBanner 
            : asset($storeBanner);
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

