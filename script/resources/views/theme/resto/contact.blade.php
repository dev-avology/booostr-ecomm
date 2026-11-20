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
                <div class="volunteer-form-container" id="contactFormCard" style="width: 100%; max-width: 800px; margin: 0 auto; background: #ffffff; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.06); border: 1px solid #e2e8f0; overflow: hidden; padding: 35px 30px 25px 30px; transition: all 0.3s ease;">
                    <div class="contact-card-heading" id="contactCardHeading" style="margin-bottom: 25px; border-bottom: 2px solid #f1f5f9; padding-bottom: 15px;">
                        <h3 style="font-size: 22px; font-weight: 700; color: #1e293b; margin: 0;">{{ __('Send Us a Message') }}</h3>
                        <p style="color: #64748b; font-size: 14px; margin-top: 6px; margin-bottom: 0;">{{ __('Please fill out the form below and our team will get back to you shortly.') }}</p>
                    </div>
                    <iframe id="fornstupframework{{ $form_id }}" class="fornstupframework" style="overflow-y: hidden; width: 100%; min-height: 380px; display: block; border: 0;" frameborder="0" border="0" cellspacing="0" scrolling="no" onload="AdjustIframeHeightOnLoad()" src="{{ $formBaseUrl ?? 'https://booostr.co' }}/?em_embed_form={{ $form_id }}&em_item={{ $club_id }}"></iframe>
                    <script>
                    function AdjustIframeHeightOnLoad() {}

                    (function() {
                        var maxFormHeight = 0;
                        var isSubmitted = false;

                        function getIframe() {
                            return document.getElementById("fornstupframework{{ $form_id }}") 
                                || document.getElementById("fornstupframeworkcalform{{ $form_id }}") 
                                || document.querySelector("iframe.fornstupframework");
                        }

                        function markFormSubmitted(newHeight) {
                            isSubmitted = true;
                            var heading = document.getElementById("contactCardHeading");
                            if (heading) {
                                heading.style.display = "none";
                            }
                            var card = document.getElementById("contactFormCard");
                            if (card) {
                                card.style.padding = "25px 20px 25px 20px";
                                card.style.maxWidth = "700px";
                            }
                            var iframe = getIframe();
                            if (iframe) {
                                var targetH = newHeight ? Math.max(newHeight + 25, 530) : 530;
                                iframe.style.height = targetH + "px";
                                iframe.style.minHeight = targetH + "px";
                            }
                        }

                        window.addEventListener("message", function(e) {
                            if (!e.origin || !/https:\/\/(?:[a-z0-9-]+\.)?booostr\.co$/i.test(e.origin)) return;

                            var data = e.data;
                            if (typeof data === "string") {
                                try {
                                    data = JSON.parse(data);
                                } catch (err) {
                                    var parsedNum = parseInt(data, 10);
                                    if (!isNaN(parsedNum)) {
                                        data = { iframeHeight: parsedNum };
                                    }
                                }
                            }

                            // If Booostr signals form submission completion (e.g. SCROLL_TO_CHECKOUT_FORM event fired on success)
                            if (data && (data.type === "SCROLL_TO_CHECKOUT_FORM" || data.status === "success" || data.type === "SUBMIT_SUCCESS" || data.formSubmitted || data.submitted)) {
                                markFormSubmitted(data.iframeHeight ? parseInt(data.iframeHeight, 10) : null);
                                return;
                            }

                            var h = null;
                            if (data && data.iframeHeight) {
                                h = parseInt(data.iframeHeight, 10);
                            } else if (typeof data === "number") {
                                h = parseInt(data, 10);
                            }

                            var iframe = getIframe();
                            if (iframe && h && !isNaN(h)) {
                                if (h > maxFormHeight && !isSubmitted) {
                                    maxFormHeight = h;
                                }

                                // If height drops significantly (e.g. from ~750px form down to ~350-500px thank-you screen)
                                if (maxFormHeight > 550 && h < (maxFormHeight - 150)) {
                                    markFormSubmitted(h);
                                    return;
                                }

                                if (isSubmitted) {
                                    var targetH = Math.max(h + 25, 530);
                                    iframe.style.height = targetH + "px";
                                    iframe.style.minHeight = targetH + "px";
                                } else {
                                    iframe.style.height = h + "px";
                                }
                            }
                        });
                    })();
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
