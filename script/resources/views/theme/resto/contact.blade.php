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
                <div class="volunteer-form-container" style="width: 100%; max-width: 800px; margin: 0 auto; background: #ffffff; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.06); border: 1px solid #e2e8f0; overflow: hidden; padding: 20px;">
                    <iframe id="fornstupframework{{ $form_id }}" class="fornstupframework" style="overflow-y: hidden;width: 100%;" frameborder="0" border="0" cellspacing="0" scrolling="no" onload="AdjustIframeHeightOnLoad()" src="{{ $formBaseUrl ?? 'https://booostr.co' }}/?em_embed_form={{ $form_id }}&em_item={{ $club_id }}"></iframe>
                    <script>
                    function AdjustIframeHeightOnLoad() {}
                    window.addEventListener("message", function(e) {
                        if (e.origin !== "https://booostr.co" && e.origin !== "https://app.booostr.co" && e.origin !== "https://staging5.booostr.co") return;
                        if (e.data && e.data.iframeHeight) {
                            var i = document.getElementById("fornstupframeworkcalform{{ $form_id }}") || document.getElementById("fornstupframework{{ $form_id }}");
                            if (i) {
                                i.style.height = e.data.iframeHeight + "px";
                            }
                        }
                    });
                    </script>
                </div>
            @else
                <div class="contact-fallback-card text-center" style="max-width: 600px; margin: 40px auto; background: #ffffff; padding: 50px 30px; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.06); border: 1px solid #e2e8f0;">
                    <div style="width: 64px; height: 64px; border-radius: 50%; background: #f1f5f9; color: #64748b; display: inline-flex; align-items: center; justify-content: center; font-size: 28px; margin-bottom: 20px;">
                        <i class="icofont-info-circle"></i>
                    </div>
                    <h3 style="font-size: 22px; font-weight: 700; color: #1e293b; margin-bottom: 10px;">{{ __('Contact Form Unavailable') }}</h3>
                    <p style="color: #64748b; font-size: 15px; margin: 0; line-height: 1.6;">
                        {{ __('The online contact form is currently not available.') }}
                    </p>
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
