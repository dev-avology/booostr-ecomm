@extends('theme.resto.layouts.app')
@section('content')
<!-- start Registration section -->
<section class="login section py-5" style="background-color: #f8fafc; min-height: 80vh;">
	<div class="container">
		<div class="row justify-content-center">
			<div class="col-lg-6 col-md-8 col-12">
				<div class="form-head bg-white p-4 p-md-5" style="border: 1px solid #edf2f7; border-radius: 14px; box-shadow: 0 10px 30px rgba(0,0,0,0.06);">
					<h3 class="title text-center mb-4" style="font-size: 26px; font-weight: 700; color: #1e293b; letter-spacing: -0.5px;">{{ __('Create Your Account') }}</h3>

					@if(Session::has('error') || Session::has('msg'))
						<div class="alert alert-danger" role="alert" style="margin-bottom: 20px; border-radius: 8px;">
							{{ Session::get('error') ?: Session::get('msg') }}
						</div>
					@endif
					@if(Session::has('success'))
						<div class="alert alert-success" role="alert" style="margin-bottom: 20px; border-radius: 8px;">
							{{ Session::get('success') }}
						</div>
					@endif

					<!-- Booostr SSO Split Button matching reference -->
					<div class="mb-4">
						<div class="sso-btn-wrapper">
							<a href="javascript:void(0)" class="booostr-sso-btn" onclick="return false;" title="In progress • Working on it">
								<div class="sso-logo-box">
									<img src="{{ asset('uploads/booostr-logo.png') }}" onerror="this.onerror=null; this.src='https://app.booostr.co/wp-content/themes/booostr-app/assets/images/Header-logo.png';" alt="Booostr">
								</div>
								<div class="sso-text-box">
									<span>{{ __('Sign up with your Booostr User Account') }}</span>
								</div>
							</a>
							<div class="sso-hover-tooltip">
								<i class="icofont-info-circle"></i> {{ __('In progress • Working on it') }}
							</div>
						</div>
						<div class="or-separator" style="display: flex; align-items: center; text-align: center; margin: 24px 0 20px;">
							<hr style="flex: 1; border: none; border-top: 1px solid #e2e8f0; margin: 0;">
							<span style="padding: 0 14px; color: #94a3b8; font-size: 12px; font-weight: 700; letter-spacing: 1px;">OR</span>
							<hr style="flex: 1; border: none; border-top: 1px solid #e2e8f0; margin: 0;">
						</div>
					</div>

					<form action="{{ route('customer.register') }}" method="post" id="registrationForm">
						@csrf
						<div class="row">
							<div class="col-md-6 col-12">
								<div class="form-group mb-3">
									<label class="form-label" style="font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 6px; display: block;">{{ __('First Name') }}</label>
									<input class="custom-input" value="{{ old('fname') }}" type="text" name="fname" required placeholder="First Name"/>
									@error('fname')
									<span class="text-danger small d-block mt-1"><strong>{{ $message }}</strong></span>
									@enderror
								</div>
							</div>
							<div class="col-md-6 col-12">
								<div class="form-group mb-3">
									<label class="form-label" style="font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 6px; display: block;">{{ __('Last Name') }}</label>
									<input class="custom-input" value="{{ old('lname') }}" type="text" name="lname" required placeholder="Last Name"/>
									@error('lname')
									<span class="text-danger small d-block mt-1"><strong>{{ $message }}</strong></span>
									@enderror
								</div>
							</div>
						</div>

						<div class="form-group mb-3">
							<label class="form-label" style="font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 6px; display: block;">{{ __('Username') }}</label>
							<input class="custom-input" value="{{ old('username') }}" type="text" name="username" required placeholder="Username"/>
							@error('username')
							<span class="text-danger small d-block mt-1"><strong>{{ $message }}</strong></span>
							@enderror
						</div>

						<div class="form-group mb-3">
							<label class="form-label" style="font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 6px; display: block;">{{ __('Email') }}</label>
							<input class="custom-input" value="{{ old('email') }}" type="email" name="email" required placeholder="Email"/>
							@error('email')
							<span class="text-danger small d-block mt-1"><strong>{{ $message }}</strong></span>
							@enderror
						</div>

						<div class="form-group mb-3">
							<label class="form-label" style="font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 6px; display: block;">{{ __('Create password') }}</label>
							<div class="password-wrapper" style="position: relative;">
								<input class="custom-input" name="password" type="password" id="registerPassword" required placeholder="Create password" style="padding-right: 44px;"/>
								<span class="password-toggle-icon" onclick="togglePasswordVisibility('registerPassword', this)" title="Show / Hide Password" style="position: absolute; right: 14px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #94a3b8; font-size: 18px; z-index: 2;">
									<i class="icofont-eye"></i>
								</span>
							</div>
							@error('password')
							<span class="text-danger small d-block mt-1"><strong>{{ $message }}</strong></span>
							@enderror
						</div>

						<div class="form-group mb-3">
							<label class="form-label" style="font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 6px; display: block;">{{ __('Confirm Password') }}</label>
							<div class="password-wrapper" style="position: relative;">
								<input class="custom-input" required name="password_confirmation" type="password" id="registerPasswordConfirm" placeholder="Confirm Password" style="padding-right: 44px;"/>
								<span class="password-toggle-icon" onclick="togglePasswordVisibility('registerPasswordConfirm', this)" title="Show / Hide Password" style="position: absolute; right: 14px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #94a3b8; font-size: 18px; z-index: 2;">
									<i class="icofont-eye"></i>
								</span>
							</div>
							@error('password_confirmation')
							<span class="text-danger small d-block mt-1"><strong>{{ $message }}</strong></span>
							@enderror
						</div>

						<div class="form-group mb-3">
							<label class="form-label" style="font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 6px; display: block;">{{ __('Age') }}</label>
							<select name="age" class="custom-input" required style="width: 100%; height: 48px; border-radius: 8px; padding: 0 16px; font-size: 14px; color: #334155; background-color: #fff;">
								<option value="" disabled {{ old('age') ? '' : 'selected' }}>{{ __('Choose Age') }}</option>
								<option value="child" {{ old('age') == 'child' ? 'selected' : '' }}>{{ __('Above 13 - Under 19') }}</option>
								<option value="adult" {{ old('age') == 'adult' ? 'selected' : '' }}>{{ __('Above 19') }}</option>
							</select>
							@error('age')
							<span class="text-danger small d-block mt-1"><strong>{{ $message }}</strong></span>
							@enderror
						</div>

						<!-- Terms and condition checkbox properly aligned as single row -->
						<div class="agree-terms-container">
							<input type="checkbox" name="agree_terms" id="agree_terms" required {{ old('agree_terms') ? 'checked' : '' }}>
							<label for="agree_terms">
								{{ __("I've read and agree with") }} 
								<a href="{{ url('/page/terms-and-conditions') }}" target="_blank">{{ __('Terms and condition') }}</a> 
								{{ __('and') }} 
								<a href="{{ url('/page/privacy-policy') }}" target="_blank">{{ __('privacy policy') }}</a>
							</label>
						</div>
						@error('agree_terms')
						<span class="text-danger small d-block mb-3"><strong>{{ $message }}</strong></span>
						@enderror

						<!-- reCAPTCHA Widget Left-Aligned -->
						<div class="recaptcha-wrapper">
							<div class="g-recaptcha" data-sitekey="{{ config('services.recaptcha.site_key') ?: env('RECAPTCHA_SITE_KEY') }}"></div>
						</div>
						@error('g-recaptcha-response')
						<span class="text-danger small d-block mt-1 mb-2"><strong>{{ $message }}</strong></span>
						@enderror
						
						<!-- Register Submit Button (disabled until terms checked) -->
						<div class="button mt-4">
							<button type="submit" id="registerSubmitBtn" class="btn w-100">
								{{ __('Register') }}
							</button>
						</div>

						<p class="outer-link mt-4 text-center" style="font-size: 14px; color: #64748b;">
							{{ __('Already have an account?') }} 
							<a href="{{ url('/customer/login') }}" class="login-link">
								{{ __('Login here') }}
							</a>
						</p>
					</form>
				</div>
			</div>
		</div>
	</div>
</section>
<!-- End Registration section -->

<style>
/* Custom scoped styling for Registration Form */
.login.section {
	padding: 50px 0;
}

.login .form-head {
	background: #ffffff;
	border: 1px solid #edf2f7;
	border-radius: 14px;
	box-shadow: 0 10px 30px rgba(0,0,0,0.06);
	padding: 40px 45px;
}

.login .form-head .title {
	font-size: 26px;
	font-weight: 700;
	color: #1e293b;
	letter-spacing: -0.5px;
	margin-bottom: 24px;
}

/* Clearly visible inputs with crisp borders */
.login .form-head .custom-input {
	width: 100% !important;
	height: 48px !important;
	min-height: 48px !important;
	padding: 10px 16px !important;
	color: #1e293b !important;
	background-color: #ffffff !important;
	border: 1.5px solid #cbd5e1 !important; /* Clearly visible, high-contrast border */
	border-radius: 8px !important;
	font-size: 14px !important;
	line-height: 1.5 !important;
	outline: none !important;
	box-shadow: none !important;
	transition: border-color 0.2s ease, box-shadow 0.2s ease !important;
}

.login .form-head .custom-input:hover {
	border-color: #94a3b8 !important;
}

.login .form-head .custom-input:focus {
	border-color: #528CD7 !important;
	box-shadow: 0 0 0 3px rgba(82, 140, 215, 0.2) !important;
	background-color: #ffffff !important;
}

.login .form-head .custom-input::placeholder {
	color: #94a3b8 !important;
	font-size: 13.5px !important;
}

.login .form-head .nice-select {
	display: none !important;
}

.login .form-head select.custom-input {
	display: block !important;
	-webkit-appearance: menulist !important;
	-moz-appearance: menulist !important;
	appearance: menulist !important;
}

/* Booostr SSO Split Button matching reference */
.sso-btn-wrapper {
	position: relative;
	width: 100%;
}

.booostr-sso-btn {
	display: flex !important;
	align-items: stretch !important;
	border: 1.5px solid #528CD7 !important;
	border-radius: 8px !important;
	overflow: hidden !important;
	height: 54px !important;
	min-height: 54px !important;
	width: 100% !important;
	text-decoration: none !important;
	cursor: pointer !important;
	transition: all 0.25s ease !important;
	box-shadow: 0 2px 8px rgba(82, 140, 215, 0.15) !important;
}

.booostr-sso-btn:hover {
	opacity: 0.98 !important;
	box-shadow: 0 4px 16px rgba(82, 140, 215, 0.3) !important;
}

.booostr-sso-btn .sso-logo-box {
	background: #ffffff !important;
	width: 125px !important;
	min-width: 125px !important;
	max-width: 125px !important;
	display: flex !important;
	align-items: center !important;
	justify-content: center !important;
	padding: 4px 8px !important;
	border-right: 1.5px solid #528CD7 !important;
}

.booostr-sso-btn .sso-logo-box img {
	height: 32px !important;
	max-height: 34px !important;
	width: auto !important;
	max-width: 100% !important;
	object-fit: contain !important;
	display: block !important;
}

.booostr-sso-btn .sso-text-box {
	background: #528CD7 !important;
	flex: 1 !important;
	display: flex !important;
	align-items: center !important;
	justify-content: center !important;
	color: #ffffff !important;
	font-weight: 600 !important;
	font-size: 15px !important;
	padding: 0 16px !important;
	text-align: center !important;
	letter-spacing: 0.2px !important;
}

/* Tooltip ONLY on Hover */
.sso-hover-tooltip {
	display: none !important;
	position: absolute;
	bottom: calc(100% + 10px);
	left: 50%;
	transform: translateX(-50%);
	background-color: #1e293b;
	color: #ffffff;
	font-size: 13px;
	font-weight: 500;
	padding: 7px 16px;
	border-radius: 6px;
	white-space: nowrap;
	box-shadow: 0 6px 20px rgba(0,0,0,0.2);
	z-index: 1000;
	pointer-events: none;
}

.sso-hover-tooltip::after {
	content: "";
	position: absolute;
	top: 100%;
	left: 50%;
	transform: translateX(-50%);
	border-width: 6px;
	border-style: solid;
	border-color: #1e293b transparent transparent transparent;
}

.sso-btn-wrapper:hover .sso-hover-tooltip {
	display: flex !important;
	align-items: center;
	gap: 6px;
}

.password-toggle-icon:hover {
	color: #475569 !important;
}

/* Terms and Condition Checkbox Row Alignment */
.agree-terms-container {
	display: flex !important;
	align-items: center !important;
	gap: 10px !important;
	margin: 18px 0 16px !important;
	width: 100% !important;
}

.agree-terms-container input[type="checkbox"] {
	width: 18px !important;
	min-width: 18px !important;
	max-width: 18px !important;
	height: 18px !important;
	min-height: 18px !important;
	max-height: 18px !important;
	margin: 0 !important;
	padding: 0 !important;
	cursor: pointer !important;
	accent-color: #528CD7 !important;
	display: inline-block !important;
	flex-shrink: 0 !important;
	border: 1.5px solid #cbd5e1 !important;
	background-color: #ffffff !important;
	border-radius: 4px !important;
}

.agree-terms-container label {
	font-size: 13.5px !important;
	color: #475569 !important;
	cursor: pointer !important;
	margin: 0 !important;
	line-height: 1.4 !important;
	display: inline-block !important;
}

.agree-terms-container a {
	color: #528CD7 !important;
	font-weight: 600 !important;
	text-decoration: underline !important;
	transition: color 0.2s ease !important;
}

.agree-terms-container a:hover {
	color: #3d71b5 !important;
}

/* reCAPTCHA Left Aligned */
.recaptcha-wrapper {
	display: flex;
	justify-content: flex-start;
	width: 100%;
	margin: 18px 0;
	overflow-x: auto;
}

/* Register Button Styled with #528CD7 */
#registerSubmitBtn {
	background: #528CD7 !important;
	background-color: #528CD7 !important;
	color: #ffffff !important;
	border: none !important;
	height: 48px !important;
	border-radius: 8px !important;
	font-weight: 600 !important;
	font-size: 15px !important;
	letter-spacing: 0.3px !important;
	transition: all 0.25s ease !important;
}

#registerSubmitBtn:hover:not(:disabled) {
	background: #3d71b5 !important;
	background-color: #3d71b5 !important;
	color: #ffffff !important;
	box-shadow: 0 4px 14px rgba(82, 140, 215, 0.35) !important;
}

#registerSubmitBtn:disabled {
	opacity: 0.55 !important;
	cursor: not-allowed !important;
	background: #94b8e8 !important;
	background-color: #94b8e8 !important;
}

.login-link {
	color: #528CD7 !important;
	font-weight: 600 !important;
	text-decoration: none !important;
	transition: color 0.2s ease !important;
}

.login-link:hover {
	color: #3d71b5 !important;
	text-decoration: underline !important;
}

/* Responsive Media Queries */
@media (max-width: 768px) {
	.login.section {
		padding: 30px 0;
	}
	.login .form-head {
		padding: 30px 22px !important;
	}
	.login .form-head .title {
		font-size: 23px;
		margin-bottom: 20px;
	}
}

@media (max-width: 576px) {
	.login.section {
		padding: 20px 0;
	}
	.login .form-head {
		padding: 22px 16px !important;
		border-radius: 10px;
	}
	.login .form-head .title {
		font-size: 21px;
	}
	.booostr-sso-btn {
		height: 48px !important;
		min-height: 48px !important;
	}
	.booostr-sso-btn .sso-logo-box {
		width: 95px !important;
		min-width: 95px !important;
		padding: 4px 6px !important;
	}
	.booostr-sso-btn .sso-logo-box img {
		height: 24px !important;
		max-height: 26px !important;
	}
	.booostr-sso-btn .sso-text-box {
		font-size: 13px !important;
		padding: 6px 10px !important;
		line-height: 1.3 !important;
	}
	.agree-terms-container {
		align-items: flex-start !important;
	}
	.agree-terms-container input[type="checkbox"] {
		margin-top: 3px !important;
	}
	.g-recaptcha {
		transform: scale(0.92);
		-webkit-transform: scale(0.92);
		transform-origin: left center;
		-webkit-transform-origin: left center;
	}
}

@media (max-width: 360px) {
	.g-recaptcha {
		transform: scale(0.82);
		-webkit-transform: scale(0.82);
		transform-origin: left center;
		-webkit-transform-origin: left center;
	}
}
</style>

@push('js')
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
<script>
function togglePasswordVisibility(fieldId, iconElem) {
	const field = document.getElementById(fieldId);
	if (!field) return;
	const icon = iconElem.querySelector('i');
	if (field.type === 'password') {
		field.type = 'text';
		if (icon) {
			icon.classList.remove('icofont-eye');
			icon.classList.add('icofont-eye-blocked');
		}
	} else {
		field.type = 'password';
		if (icon) {
			icon.classList.remove('icofont-eye-blocked');
			icon.classList.add('icofont-eye');
		}
	}
}

document.addEventListener('DOMContentLoaded', function() {
	const agreeCheckbox = document.getElementById('agree_terms');
	const submitBtn = document.getElementById('registerSubmitBtn');

	function updateSubmitButtonState() {
		if (agreeCheckbox && submitBtn) {
			submitBtn.disabled = !agreeCheckbox.checked;
		}
	}

	if (agreeCheckbox) {
		agreeCheckbox.addEventListener('change', updateSubmitButtonState);
		updateSubmitButtonState(); // initialize on page load
	}
});
</script>
@endpush
@endsection
