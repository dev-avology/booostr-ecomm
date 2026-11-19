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
            <div class="contact-form" style="max-width: 800px; margin: 0 auto; background: #ffffff; padding: 40px; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.06); border: 1px solid #e2e8f0;">
                <form action="{{ url('contact/send') }}" method="POST" class="ajaxform_with_reset">
                    @csrf
                    @if(!empty($booostrForm))
                        <input type="hidden" name="form_id" value="{{ $booostrForm->id }}">
                    @endif
                    <div class="checkout-form">
                        <div class="checkout-form-inner">
                            <div class="checkout-heading" style="margin-bottom: 25px; border-bottom: 2px solid #f1f5f9; padding-bottom: 15px;">
                                <h3 style="font-size: 22px; font-weight: 700; color: #1e293b; margin: 0;">{{ __('Send Us a Message') }}</h3>
                                <p style="color: #64748b; font-size: 14px; margin-top: 6px; margin-bottom: 0;">{{ __('Please fill out the form below and our team will get back to you shortly.') }}</p>
                            </div>

                            @if(!empty($booostrForm) && !empty($booostrForm->parsed_fields))
                                {{-- Render Booostr Form Builder dynamic fields --}}
                                <div class="row">
                                    @foreach($booostrForm->parsed_fields as $field)
                                        @php
                                            $fieldType = $field['type'] ?? 'text';
                                            $fieldLabel = $field['label'] ?? '';
                                            $fieldName = $field['name'] ?? ('field_' . $loop->index);
                                            $isRequired = !empty($field['required']) && ($field['required'] === true || $field['required'] === 'true' || $field['required'] === 1 || $field['required'] === '1');
                                            $placeholder = $field['placeholder'] ?? '';
                                            $colWidth = in_array($fieldType, ['textarea', 'header', 'paragraph', 'checkbox-group', 'radio-group']) ? 'col-12' : 'col-lg-6 col-md-6 col-12';
                                        @endphp

                                        @if($fieldType === 'header')
                                            <div class="col-12" style="margin-top: 15px; margin-bottom: 10px;">
                                                <h4 style="font-size: 18px; font-weight: 600; color: #1e293b;">{{ $fieldLabel }}</h4>
                                            </div>
                                        @elseif($fieldType === 'paragraph')
                                            <div class="col-12" style="margin-bottom: 15px;">
                                                <p style="color: #64748b; font-size: 14px; margin: 0;">{{ $fieldLabel }}</p>
                                            </div>
                                        @elseif($fieldType === 'textarea')
                                            <div class="{{ $colWidth }}">
                                                <div class="form-group" style="margin-bottom: 20px;">
                                                    <label style="font-size: 14px; font-weight: 600; color: #334155; margin-bottom: 6px; display: block;">{{ $fieldLabel }} @if($isRequired)<span style="color: #ef4444;">*</span>@endif</label>
                                                    <textarea class="form-control" name="{{ $fieldName }}" placeholder="{{ $placeholder }}" style="width: 100%; border: 1px solid #cbd5e1; border-radius: 6px; padding: 12px 14px; min-height: 120px; font-size: 14px;" @if($isRequired) required @endif></textarea>
                                                </div>
                                            </div>
                                        @elseif($fieldType === 'select')
                                            <div class="{{ $colWidth }}">
                                                <div class="form-group" style="margin-bottom: 20px;">
                                                    <label style="font-size: 14px; font-weight: 600; color: #334155; margin-bottom: 6px; display: block;">{{ $fieldLabel }} @if($isRequired)<span style="color: #ef4444;">*</span>@endif</label>
                                                    <select class="form-control" name="{{ $fieldName }}" style="width: 100%; height: 48px; border: 1px solid #cbd5e1; border-radius: 6px; padding: 0 14px; font-size: 14px;" @if($isRequired) required @endif>
                                                        <option value="">{{ $placeholder ?: __('Select an option') }}</option>
                                                        @foreach($field['values'] ?? ($field['options'] ?? []) as $opt)
                                                            <option value="{{ $opt['value'] ?? ($opt['label'] ?? '') }}">{{ $opt['label'] ?? ($opt['value'] ?? '') }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        @elseif($fieldType === 'radio-group')
                                            <div class="{{ $colWidth }}">
                                                <div class="form-group" style="margin-bottom: 20px;">
                                                    <label style="font-size: 14px; font-weight: 600; color: #334155; margin-bottom: 8px; display: block;">{{ $fieldLabel }} @if($isRequired)<span style="color: #ef4444;">*</span>@endif</label>
                                                    <div style="display: flex; flex-wrap: wrap; gap: 15px;">
                                                        @foreach($field['values'] ?? ($field['options'] ?? []) as $opt)
                                                            <label style="font-size: 14px; color: #475569; display: inline-flex; align-items: center; gap: 6px; cursor: pointer; margin: 0;">
                                                                <input type="radio" name="{{ $fieldName }}" value="{{ $opt['value'] ?? ($opt['label'] ?? '') }}" style="width: auto; height: auto; margin: 0;" @if($isRequired) required @endif>
                                                                <span>{{ $opt['label'] ?? ($opt['value'] ?? '') }}</span>
                                                            </label>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        @elseif($fieldType === 'checkbox-group')
                                            <div class="{{ $colWidth }}">
                                                <div class="form-group" style="margin-bottom: 20px;">
                                                    <label style="font-size: 14px; font-weight: 600; color: #334155; margin-bottom: 8px; display: block;">{{ $fieldLabel }} @if($isRequired)<span style="color: #ef4444;">*</span>@endif</label>
                                                    <div style="display: flex; flex-wrap: wrap; gap: 15px;">
                                                        @foreach($field['values'] ?? ($field['options'] ?? []) as $opt)
                                                            <label style="font-size: 14px; color: #475569; display: inline-flex; align-items: center; gap: 6px; cursor: pointer; margin: 0;">
                                                                <input type="checkbox" name="{{ $fieldName }}[]" value="{{ $opt['value'] ?? ($opt['label'] ?? '') }}" style="width: auto; height: auto; margin: 0;">
                                                                <span>{{ $opt['label'] ?? ($opt['value'] ?? '') }}</span>
                                                            </label>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <div class="{{ $colWidth }}">
                                                <div class="form-group" style="margin-bottom: 20px;">
                                                    <label style="font-size: 14px; font-weight: 600; color: #334155; margin-bottom: 6px; display: block;">{{ $fieldLabel }} @if($isRequired)<span style="color: #ef4444;">*</span>@endif</label>
                                                    <input type="{{ in_array($fieldType, ['text', 'email', 'number', 'tel', 'date']) ? $fieldType : 'text' }}" class="form-control" name="{{ $fieldName }}" placeholder="{{ $placeholder }}" style="width: 100%; height: 48px; border: 1px solid #cbd5e1; border-radius: 6px; padding: 0 14px; font-size: 14px;" @if($isRequired) required @endif>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach

                                    <div class="col-12" style="margin-top: 10px;">
                                        <div class="contact-btn">
                                            <button type="submit" class="btn basicbtn" style="background: var(--primary, #0A8CDE); border: none; color: #fff; padding: 13px 36px; border-radius: 6px; font-weight: 600; font-size: 15px; cursor: pointer; transition: background 0.2s ease;">{{ __('Submit') }}</button>
                                        </div>
                                    </div>
                                </div>
                            @else
                                {{-- Default standard fallback contact form --}}
                                <div class="row">
                                    <div class="col-lg-6 col-md-6 col-12">
                                        <div class="form-group" style="margin-bottom: 20px;">
                                            <label style="font-size: 14px; font-weight: 600; color: #334155; margin-bottom: 6px; display: block;">{{ __('Name') }} <span style="color: #ef4444;">*</span></label>
                                            <input type="text" class="form-control" name="name" placeholder="{{ __('Enter your name') }}" required style="width: 100%; height: 48px; border: 1px solid #cbd5e1; border-radius: 6px; padding: 0 14px; font-size: 14px;">
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-12">
                                        <div class="form-group" style="margin-bottom: 20px;">
                                            <label style="font-size: 14px; font-weight: 600; color: #334155; margin-bottom: 6px; display: block;">{{ __('Email Address') }} <span style="color: #ef4444;">*</span></label>
                                            <input type="email" class="form-control" name="email" placeholder="{{ __('Enter your email') }}" required style="width: 100%; height: 48px; border: 1px solid #cbd5e1; border-radius: 6px; padding: 0 14px; font-size: 14px;">
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group" style="margin-bottom: 20px;">
                                            <label style="font-size: 14px; font-weight: 600; color: #334155; margin-bottom: 6px; display: block;">{{ __('Subject') }} <span style="color: #ef4444;">*</span></label>
                                            <input type="text" class="form-control" name="subject" placeholder="{{ __('How can we help you?') }}" required style="width: 100%; height: 48px; border: 1px solid #cbd5e1; border-radius: 6px; padding: 0 14px; font-size: 14px;">
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group" style="margin-bottom: 20px;">
                                            <label style="font-size: 14px; font-weight: 600; color: #334155; margin-bottom: 6px; display: block;">{{ __('Message') }} <span style="color: #ef4444;">*</span></label>
                                            <textarea class="form-control" name="message" placeholder="{{ __('Write your message here...') }}" required maxlength="500" style="width: 100%; border: 1px solid #cbd5e1; border-radius: 6px; padding: 12px 14px; min-height: 130px; font-size: 14px;"></textarea>
                                        </div>
                                    </div>
                                    <div class="col-12" style="margin-top: 10px;">
                                        <div class="contact-btn">
                                            <button type="submit" class="btn basicbtn" style="background: var(--primary, #0A8CDE); border: none; color: #fff; padding: 13px 36px; border-radius: 6px; font-weight: 600; font-size: 15px; cursor: pointer; transition: background 0.2s ease;">{{ __('Send Message') }}</button>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
{{-- contact area end --}}
@endsection

@push('js')
    <script src="{{ asset('admin/js/sweetalert2.all.min.js') }}"></script>
    <script src="{{ asset('admin/js/form.js') }}"></script>
@endpush
