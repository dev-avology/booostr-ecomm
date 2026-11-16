@extends('theme.resto.layouts.app')
@section('content')
<!-- Start Forgot Password Section -->
<section class="login section py-5" style="background-color: #f8fafc; min-height: 80vh;">
	<div class="container">
		<div class="row justify-content-center">
			<div class="col-lg-6 col-md-8 col-12" style="max-width: 550px;">
				<div class="form-head bg-white" style="border: 1px solid #edf2f7; border-radius: 14px; box-shadow: 0 10px 30px rgba(0,0,0,0.06); padding: 48px 45px;">
					<h3 class="title text-center" style="font-size: 26px; font-weight: 700; color: #1e293b; letter-spacing: -0.5px; margin-bottom: 20px;">
						{{ __('Reset Your Password') }}
					</h3>

					<!-- Alert Notifications Area -->
					<div id="forgetAlertArea">
						@if(Session::has('msg'))
							<div class="alert alert-{{ Session::get('type', 'danger') === 'danger' ? 'danger' : (Session::get('type') === 'success' ? 'success' : 'info') }}" role="alert" style="margin-bottom: 24px; border-radius: 8px; font-size: 14px; line-height: 1.55;">
								{!! Session::get('msg') !!}
							</div>
						@endif
						@if(Session::has('error'))
							<div class="alert alert-danger" role="alert" style="margin-bottom: 24px; border-radius: 8px; font-size: 14px; line-height: 1.55;">
								{{ Session::get('error') }}
							</div>
						@endif
						@if(Session::has('success'))
							<div class="alert alert-success" role="alert" style="margin-bottom: 24px; border-radius: 8px; font-size: 14px; line-height: 1.55;">
								{{ Session::get('success') }}
							</div>
						@endif
						@if(Session::has('status'))
							<div class="alert alert-success" role="alert" style="margin-bottom: 24px; border-radius: 8px; font-size: 14px; line-height: 1.55;">
								{{ Session::get('status') }}
							</div>
						@endif
						@if(Session::has('message'))
							<div class="alert alert-info" role="alert" style="margin-bottom: 24px; border-radius: 8px; font-size: 14px; line-height: 1.55;">
								{{ Session::get('message') }}
							</div>
						@endif
					</div>

					@if(Session::has('booostr_register_action'))
						<!-- State 3: User exists in Booostr Central API - Direct to Registration -->
						<div class="booostr-action-container text-center mt-3 mb-2">
							<a href="{{ Session::get('booostr_register_action') }}" class="btn w-100 mb-3" style="height: 52px; background: #528CD7; color: #ffffff; font-size: 15.5px; font-weight: 600; border-radius: 8px; display: inline-flex; align-items: center; justify-content: center; text-decoration: none; box-shadow: 0 4px 14px rgba(82, 140, 215, 0.3); transition: all 0.25s ease;">
								{{ __('Create Your Account') }}
							</a>
							<p style="font-size: 13.5px; color: #64748b; margin-top: 15px;">
								{{ __('Need to use a different email?') }} 
								<a href="{{ route('customer.forget.password') }}" style="color: #528CD7; font-weight: 600; text-decoration: none;">
									{{ __('Try another email') }}
								</a>
							</p>
						</div>
					@else
						<!-- Helper Text (Dynamic: switches to Helper Text 2 if email was not found) -->
						<p id="forgetHelperText" class="text-center" style="font-size: 14.5px; line-height: 1.6; color: #64748b; margin-bottom: 28px;">
							@if(Session::has('email_not_found'))
								{{ __('It is possible you used a separate email to create an account for our site. If so, please try submitting a different email that may have been used to create your account. If that email exists in the system, you will receive a password reset link at that email address. Click that link to reset your password.') }}
							@else
								{{ __('Please enter the email used to create your account for our site. If that email exists in the system, you will receive a password reset link at that email address. Click that link to reset your password.') }}
							@endif
						</p>

						<!-- Form: Enter Username or Email -->
						{{-- Force HTTPS action URL to prevent HTTP→HTTPS redirect losing session flash data --}}
						<form action="{{ str_replace('http://', 'https://', route('customer.forget.password.post')) }}" method="post" id="forgetPasswordForm">
							@csrf

							<div class="form-group mb-4">
								<input class="custom-input" type="text" id="forgetUserInput" name="username" value="{{ old('username') }}" required placeholder="{{ __('Your username or email address') }}" autocomplete="username" />
								@error('username')
								<span class="text-danger small d-block mt-1"><strong>{{ $message }}</strong></span>
								@enderror
							</div>

							<!-- Submit Button (Disabled until input filled) -->
							<div class="forget-button-wrapper">
								<button type="submit" id="forgetSubmitBtn" class="btn w-100" disabled>
									{{ __('Send Reset Link') }}
								</button>
							</div>
						</form>
					@endif

					<!-- Navigation Back Links -->
					<div class="text-center mt-4 pt-2">
						<p style="font-size: 14px; color: #64748b; margin-bottom: 8px;">
							{{ __('Remember your password?') }} 
							<a href="{{ url('/customer/login') }}" style="color: #528CD7; font-weight: 600; text-decoration: none;">
								{{ __('Login here') }}
							</a>
						</p>
						<p style="font-size: 14px; color: #64748b; margin-bottom: 0;">
							{{ __("Don't have an account with Booostr?") }} 
							<a href="{{ url('/customer/register') }}" style="color: #528CD7; font-weight: 600; text-decoration: none;">
								{{ __('Sign up today!') }}
							</a>
						</p>
					</div>

				</div>
			</div>
		</div>
	</div>
</section>

@push('css')
<style>
/* Consistent Design with Login & Registration pages */
.custom-input {
	width: 100% !important;
	height: 52px !important;
	border: 1px solid #d1d5db !important;
	border-radius: 8px !important;
	padding: 0 18px !important;
	font-size: 15px !important;
	color: #1e293b !important;
	background-color: #ffffff !important;
	outline: none !important;
	transition: all 0.25s ease !important;
	box-sizing: border-box !important;
	display: block !important;
}

.custom-input:focus {
	border-color: #528CD7 !important;
	box-shadow: 0 0 0 3px rgba(82, 140, 215, 0.18) !important;
	background-color: #ffffff !important;
}

.custom-input::placeholder {
	color: #94a3b8 !important;
	font-size: 14.5px !important;
}

.forget-button-wrapper {
	width: 100% !important;
	margin-top: 10px;
}

#forgetSubmitBtn {
	width: 100% !important;
	height: 52px !important;
	background-color: #528CD7 !important;
	border: none !important;
	border-radius: 8px !important;
	color: #ffffff !important;
	font-size: 16px !important;
	font-weight: 600 !important;
	letter-spacing: 0.2px !important;
	cursor: pointer !important;
	transition: all 0.25s ease !important;
	display: inline-flex !important;
	align-items: center !important;
	justify-content: center !important;
	text-decoration: none !important;
	box-shadow: 0 4px 14px rgba(82, 140, 215, 0.25) !important;
	position: relative !important;
}

#forgetSubmitBtn::before,
#forgetSubmitBtn::after {
	display: none !important;
	content: none !important;
}

#forgetSubmitBtn:hover:not(:disabled) {
	background-color: #467fcb !important;
	color: #ffffff !important;
	box-shadow: 0 6px 18px rgba(70, 127, 203, 0.35) !important;
	transform: translateY(-1px);
}

#forgetSubmitBtn:active:not(:disabled) {
	transform: translateY(0);
}

#forgetSubmitBtn:disabled {
	background-color: #a0c4f1 !important;
	color: #ffffff !important;
	cursor: not-allowed !important;
	opacity: 0.8 !important;
	box-shadow: none !important;
	pointer-events: none !important;
}

#forgetSubmitBtn.is-processing {
	background-color: #467fcb !important;
	opacity: 0.9 !important;
	cursor: wait !important;
	pointer-events: none !important;
}

/* Responsive Media Queries */
@media (max-width: 991px) {
	.form-head {
		padding: 38px 30px !important;
	}
}

@media (max-width: 576px) {
	.form-head {
		padding: 28px 20px !important;
		border-radius: 12px !important;
	}
	.title {
		font-size: 22px !important;
		margin-bottom: 16px !important;
	}
	.custom-input {
		height: 48px !important;
		font-size: 14px !important;
	}
	#forgetSubmitBtn {
		height: 48px !important;
		font-size: 15px !important;
	}
	#forgetHelperText {
		font-size: 13.5px !important;
	}
}
</style>
@endpush

@push('js')
<script>
document.addEventListener('DOMContentLoaded', function() {
	const form = document.getElementById('forgetPasswordForm');
	const input = document.getElementById('forgetUserInput');
	const submitBtn = document.getElementById('forgetSubmitBtn');

	if (!form || !input || !submitBtn) return;

	// Dynamic Enable / Disable logic based on user input
	function evaluateInput() {
		if (submitBtn.classList.contains('is-processing')) return;
		const val = input.value ? input.value.trim() : '';
		submitBtn.disabled = (val.length === 0);
	}

	// Listen to real-time input events
	input.addEventListener('input', evaluateInput);
	input.addEventListener('keyup', evaluateInput);
	input.addEventListener('change', evaluateInput);
	input.addEventListener('paste', () => setTimeout(evaluateInput, 50));

	// Initial evaluation on page load (e.g. browser autofill)
	evaluateInput();
	setTimeout(evaluateInput, 200);

	// Form submission processing state
	form.addEventListener('submit', function(e) {
		if (submitBtn.classList.contains('is-processing') || submitBtn.disabled) {
			e.preventDefault();
			return false;
		}

		const val = input.value ? input.value.trim() : '';
		if (val.length === 0) {
			e.preventDefault();
			input.focus();
			return false;
		}

		// Activate processing state: spinner + text + disable
		submitBtn.classList.add('is-processing');
		submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true" style="display: inline-block; width: 1rem; height: 1rem; vertical-align: -0.125em; border: 0.15em solid currentColor; border-right-color: transparent; border-radius: 50%; animation: spinner-border .75s linear infinite; margin-right: 8px;"></span> ' + {!! json_encode(__('Sending Link...')) !!};
		submitBtn.style.pointerEvents = 'none';

		// Slight delay to ensure form submission event dispatches cleanly
		setTimeout(function() {
			submitBtn.disabled = true;
		}, 20);
	});
});
</script>
@endpush
@endsection

