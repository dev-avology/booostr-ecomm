@extends('theme.resto.layouts.app')

@section('content')

{{-- 
    =================================================================================
    Page Banner (Site Builder standard component matching product & cart page)
    Static title & description passed directly without table queries
    =================================================================================
--}}
@include('theme.resto.components.page-banner', [
    'title' => __('Contact Us'),
    'description' => __('Have questions, inquiries, or feedback? Get in touch with our team.'),
    'breadcrumbs' => [
        ['url' => url('/'), 'label' => __('Home'), 'icon' => 'icofont-home'],
        ['url' => '', 'label' => __('Contact Us'), 'icon' => 'icofont-envelope'],
    ]
])

{{-- contact area start --}}
<section>
    <div class="contact-area" style="margin: 60px 0 100px 0;">
        <div class="container">
            @if(!empty($form_id) && !empty($club_id))
                <!-- 1. Iframe Embed Code -->
                <div class="volunteer-form-container" style="width: 100%; max-width: 800px; margin: 0 auto; background: #ffffff; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.06); border: 1px solid #e2e8f0; overflow: hidden; padding: 20px;">
                    <iframe 
                        id="fornstupframework" 
                        class="fornstupframework" 
                        src="https://booostr.co/?em_volunteersingup_form={{ $form_id }}&em_item={{ $club_id }}&t={{ time() }}" 
                        style="width: 100%; border: 0; overflow-y: hidden; display: block;" 
                        height="900" 
                        frameborder="0" 
                        scrolling="no">
                    </iframe>
                </div>
                <!-- 2. Auto Dynamic Height Resize Script (Must include) -->
                <script>
                window.addEventListener("message", function(event) {
                    // Sirf Booostr ke domain se aane wale height messages accept karega
                    if (event.origin === "https://app.booostr.co" || event.origin === "https://booostr.co") {
                        var iframe = document.getElementById("fornstupframework");
                        if (iframe && event.data) {
                            iframe.style.height = event.data + "px";
                        }
                    }
                });
                </script>
            @else
                <div class="contact-fallback-card text-center" style="max-width: 650px; margin: 0 auto; background: #ffffff; padding: 50px 30px; border-radius: 16px; box-shadow: 0 4px 25px rgba(0,0,0,0.06); border: 1px solid #e2e8f0;">
                    <div style="width: 64px; height: 64px; border-radius: 50%; background: #e0f2fe; color: #0284c7; display: inline-flex; align-items: center; justify-content: center; font-size: 28px; margin-bottom: 20px;">
                        <i class="icofont-envelope"></i>
                    </div>
                    <h3 style="font-size: 24px; font-weight: 700; color: #1e293b; margin-bottom: 12px;">{{ __('Get In Touch') }}</h3>
                    <p style="color: #64748b; font-size: 15px; line-height: 1.6; margin-bottom: 30px;">
                        {{ __('The online contact form is currently unavailable for this organization. You can reach out directly using the details below:') }}
                    </p>

                    @php
                        $cEmail = $club_info['email'] ?? '';
                        $cPhone = $club_info['phone'] ?? '';
                        $cAddress = $club_info['address'] ?? '';
                    @endphp

                    <div class="row g-3 justify-content-center text-start">
                        @if(!empty($cEmail))
                            <div class="col-12 col-sm-6">
                                <div style="background: #f8fafc; padding: 16px 20px; border-radius: 10px; border: 1px solid #e2e8f0;">
                                    <div style="font-size: 12px; font-weight: 600; color: #64748b; text-transform: uppercase;">{{ __('Email Address') }}</div>
                                    <a href="mailto:{{ $cEmail }}" style="font-size: 14px; font-weight: 600; color: #0284c7; text-decoration: none; word-break: break-all;">{{ $cEmail }}</a>
                                </div>
                            </div>
                        @endif

                        @if(!empty($cPhone))
                            <div class="col-12 col-sm-6">
                                <div style="background: #f8fafc; padding: 16px 20px; border-radius: 10px; border: 1px solid #e2e8f0;">
                                    <div style="font-size: 12px; font-weight: 600; color: #64748b; text-transform: uppercase;">{{ __('Phone') }}</div>
                                    <a href="tel:{{ $cPhone }}" style="font-size: 14px; font-weight: 600; color: #0284c7; text-decoration: none;">{{ $cPhone }}</a>
                                </div>
                            </div>
                        @endif

                        @if(!empty($cAddress))
                            <div class="col-12">
                                <div style="background: #f8fafc; padding: 16px 20px; border-radius: 10px; border: 1px solid #e2e8f0;">
                                    <div style="font-size: 12px; font-weight: 600; color: #64748b; text-transform: uppercase;">{{ __('Location') }}</div>
                                    <div style="font-size: 14px; color: #334155;">{{ $cAddress }}</div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</section>
{{-- contact area end --}}
@endsection

@push('js')
    <script src="{{ asset('admin/js/sweetalert2.all.min.js') }}"></script>
    <script src="{{ asset('admin/js/form.js') }}"></script>
@endpush
