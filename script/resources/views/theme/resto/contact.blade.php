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
    <div class="contact-area">
        <div class="container">
            <div class="contact-form">
                <form action="{{ url('contact/send') }}" method="POST" class="ajaxform_with_reset">
					@csrf
                    <div class="checkout-form m-top-30">
						<div class="checkout-form-inner">
							<div class="checkout-heading">
								<h2>Contact Us</h2>
							</div>
							<!-- Form -->
							<div class="row">
								<div class="col-lg-6 col-md-6 col-12">
									<div class="form-group">
										<label>Name<span>*</span></label>
										<input type="text" name="name" placeholder="" required="required">
									</div>
								</div>
								<div class="col-lg-6 col-md-6 col-12">
									<div class="form-group">
										<label>Email Address<span>*</span></label>
										<input type="email" name="email" placeholder="" required="required">
									</div>
								</div>
                                <div class="col-lg-12 col-md-12 col-12">
									<div class="form-group">
										<label>Subject<span>*</span></label>
										<input type="text" name="subject" placeholder="" required="required">
									</div>
								</div>
                                <div class="col-lg-12 col-md-12 col-12">
                                    <div class="form-group">
                                        <label>Comment</label>
                                        <textarea class="form-control h-150" name="message" maxlength="300"></textarea>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="contact-btn">
                                       <button type="submit" class="btn btn-primary basicbtn">Send Message</button>
                                    </div>
                                </div>
                            </div>
							<!--/ End Form -->
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