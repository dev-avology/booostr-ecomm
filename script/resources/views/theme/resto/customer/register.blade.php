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
							<a href="javascript:void(0)" class="booostr-sso-btn" id="triggerBooostrSsoBtn" role="button" tabindex="0">
								<div class="sso-logo-box">
									<img src="{{ asset('uploads/booostr-logo.png') }}" onerror="this.onerror=null; this.src='https://booostr.site/assets/Booostr/booostr-logo.png';" alt="Booostr">
								</div>
								<div class="sso-text-box">
									<span>{{ __('Sign up with your Booostr User Account') }}</span>
								</div>
							</a>
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
									<label class="form-label" style="font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 6px; display: block;">{{ __('First Name') }} <span style="color: #ef4444; font-weight: 700;">*</span></label>
									<input class="custom-input" value="{{ old('fname') }}" type="text" name="fname" required placeholder="First Name"/>
									@error('fname')
									<span class="text-danger small d-block mt-1"><strong>{{ $message }}</strong></span>
									@enderror
								</div>
							</div>
							<div class="col-md-6 col-12">
								<div class="form-group mb-3">
									<label class="form-label" style="font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 6px; display: block;">{{ __('Last Name') }} <span style="color: #ef4444; font-weight: 700;">*</span></label>
									<input class="custom-input" value="{{ old('lname') }}" type="text" name="lname" required placeholder="Last Name"/>
									@error('lname')
									<span class="text-danger small d-block mt-1"><strong>{{ $message }}</strong></span>
									@enderror
								</div>
							</div>
						</div>

						<div class="form-group mb-3">
							<label class="form-label" style="font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 6px; display: block;">{{ __('Username') }} <span style="color: #ef4444; font-weight: 700;">*</span></label>
							<input class="custom-input" value="{{ old('username') }}" type="text" name="username" required placeholder="Username"/>
							@error('username')
							<span class="text-danger small d-block mt-1"><strong>{{ $message }}</strong></span>
							@enderror
						</div>

						<div class="form-group mb-3">
							<label class="form-label" style="font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 6px; display: block;">{{ __('Email') }} <span style="color: #ef4444; font-weight: 700;">*</span></label>
							<input class="custom-input" value="{{ old('email') }}" type="email" name="email" required placeholder="Email"/>
							@error('email')
							<span class="text-danger small d-block mt-1"><strong>{{ $message }}</strong></span>
							@enderror
						</div>

						<div class="form-group mb-3">
							<label class="form-label" style="font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 6px; display: block;">{{ __('Create password') }} <span style="color: #ef4444; font-weight: 700;">*</span></label>
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
							<label class="form-label" style="font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 6px; display: block;">{{ __('Confirm Password') }} <span style="color: #ef4444; font-weight: 700;">*</span></label>
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
							<label class="form-label" style="font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 6px; display: block;">{{ __('Age') }} <span style="color: #ef4444; font-weight: 700;">*</span></label>
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
								<span style="color: #ef4444; font-weight: 700;">*</span>
							</label>
						</div>
						@error('agree_terms')
						<span class="text-danger small d-block mb-3"><strong>{{ $message }}</strong></span>
						@enderror

						<!-- reCAPTCHA Widget Left-Aligned with inline error -->
						<div class="form-group mb-3">
							<div class="recaptcha-wrapper">
								<div class="g-recaptcha" data-sitekey="{{ config('services.recaptcha.site_key') ?: env('RECAPTCHA_SITE_KEY') }}" data-callback="onRecaptchaSuccess" data-expired-callback="onRecaptchaExpired"></div>
							</div>
							<div id="recaptchaInlineError" class="text-danger small mt-1 mb-2 font-weight-bold" style="display: none;">
								<strong>{{ __('Please verify that you are not a robot.') }}</strong>
							</div>
							@error('g-recaptcha-response')
							<span class="text-danger small d-block mt-1 mb-2"><strong>{{ $message }}</strong></span>
							@enderror
						</div>
						
						<!-- Register Submit Button (disabled until terms checked) -->
						<div class="register-button-wrapper mt-4">
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
	border-color: var(--primary, #0A8CDE) !important;
	box-shadow: 0 0 0 3px rgba(10, 140, 222, 0.2) !important;
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
	border: 1.5px solid var(--primary, #0A8CDE) !important;
	border-radius: 8px !important;
	overflow: hidden !important;
	height: 54px !important;
	min-height: 54px !important;
	width: 100% !important;
	text-decoration: none !important;
	cursor: pointer !important;
	transition: all 0.25s ease !important;
	box-shadow: 0 2px 8px rgba(10, 140, 222, 0.15) !important;
}

.booostr-sso-btn:hover {
	opacity: 0.98 !important;
	box-shadow: 0 4px 16px rgba(10, 140, 222, 0.3) !important;
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
	border-right: 1.5px solid var(--primary, #0A8CDE) !important;
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
	background: var(--primary, #0A8CDE) !important;
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
	accent-color: var(--primary, #0A8CDE) !important;
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
	color: var(--primary, #0A8CDE) !important;
	font-weight: 600 !important;
	text-decoration: underline !important;
	transition: color 0.2s ease !important;
}

.agree-terms-container a:hover {
	color: var(--booostr-blue-hover, #0870b2) !important;
}

/* reCAPTCHA Left Aligned */
.recaptcha-wrapper {
	display: flex;
	justify-content: flex-start;
	width: 100%;
	margin: 18px 0;
	overflow-x: auto;
}

/* Register Button Styled with var(--primary, #0A8CDE) - Green hover completely removed */
.register-button-wrapper,
.login .form-head form .register-button-wrapper {
	margin-top: 24px;
	width: 100%;
}

#registerSubmitBtn,
.register-button-wrapper #registerSubmitBtn,
.login .form-head form .register-button-wrapper #registerSubmitBtn,
.login .form-head form .button #registerSubmitBtn {
	background: var(--primary, #0A8CDE) !important;
	background-color: var(--primary, #0A8CDE) !important;
	color: #ffffff !important;
	border: none !important;
	height: 48px !important;
	border-radius: 8px !important;
	font-weight: 600 !important;
	font-size: 15px !important;
	letter-spacing: 0.3px !important;
	transition: background-color 0.2s ease, opacity 0.2s ease, box-shadow 0.2s ease !important;
	position: relative !important;
	overflow: hidden !important;
	box-shadow: none !important;
	display: flex !important;
	align-items: center !important;
	justify-content: center !important;
}

/* Eliminate theme green ::before pseudo element hover */
#registerSubmitBtn::before,
#registerSubmitBtn::after,
.register-button-wrapper #registerSubmitBtn::before,
.register-button-wrapper #registerSubmitBtn::after,
.login .form-head form .button #registerSubmitBtn::before,
.login .form-head form .button #registerSubmitBtn::after,
.button .btn#registerSubmitBtn::before,
.button .btn#registerSubmitBtn:hover::before {
	display: none !important;
	content: none !important;
	width: 0 !important;
	height: 0 !important;
	background: transparent !important;
	background-color: transparent !important;
	opacity: 0 !important;
	visibility: hidden !important;
}

#registerSubmitBtn:hover:not(:disabled):not(.is-processing) {
	background: var(--booostr-blue-hover, #0870b2) !important;
	background-color: var(--booostr-blue-hover, #0870b2) !important;
	color: #ffffff !important;
	box-shadow: 0 4px 12px rgba(10, 140, 222, 0.25) !important;
}

#registerSubmitBtn:focus,
#registerSubmitBtn:active {
	background: var(--booostr-blue-hover, #0870b2) !important;
	background-color: var(--booostr-blue-hover, #0870b2) !important;
	color: #ffffff !important;
	outline: none !important;
	box-shadow: none !important;
}

#registerSubmitBtn:disabled {
	opacity: 0.55 !important;
	cursor: not-allowed !important;
	background: #94b8e8 !important;
	background-color: #94b8e8 !important;
	box-shadow: none !important;
}

#registerSubmitBtn.is-processing {
	opacity: 0.8 !important;
	cursor: wait !important;
	pointer-events: none !important;
	background: var(--primary, #0A8CDE) !important;
	background-color: var(--primary, #0A8CDE) !important;
	box-shadow: none !important;
}

@keyframes spinner-border {
	to { transform: rotate(360deg); }
}

.login-link {
	color: var(--primary, #0A8CDE) !important;
	font-weight: 600 !important;
	text-decoration: none !important;
	transition: color 0.2s ease !important;
}

.login-link:hover {
	color: var(--booostr-blue-hover, #0870b2) !important;
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

// Google reCAPTCHA callbacks for instant client-side validation
window.onRecaptchaSuccess = function(token) {
	const errDiv = document.getElementById('recaptchaInlineError');
	if (errDiv) errDiv.style.display = 'none';
	if (typeof window.checkAllRequiredRegistrationFields === 'function') {
		window.checkAllRequiredRegistrationFields();
	}
};

window.onRecaptchaExpired = function() {
	if (typeof window.checkAllRequiredRegistrationFields === 'function') {
		window.checkAllRequiredRegistrationFields();
	}
};

document.addEventListener('DOMContentLoaded', function() {
	const form = document.getElementById('registrationForm');
	const submitBtn = document.getElementById('registerSubmitBtn');

	function checkAllRequiredFields() {
		if (!form || !submitBtn || submitBtn.classList.contains('is-processing')) return;

		const fname = form.querySelector('input[name="fname"]');
		const lname = form.querySelector('input[name="lname"]');
		const username = form.querySelector('input[name="username"]');
		const email = form.querySelector('input[name="email"]');
		const password = document.getElementById('registerPassword');
		const passwordConfirm = document.getElementById('registerPasswordConfirm');
		const age = form.querySelector('select[name="age"]');
		const agreeTerms = document.getElementById('agree_terms');

		// Check if Google reCAPTCHA has been completed
		const hasRecaptcha = Boolean(
			typeof grecaptcha !== 'undefined' &&
			grecaptcha.getResponse &&
			grecaptcha.getResponse().length > 0
		);

		// Check if age is selected (not empty or default placeholder)
		const isAgeSelected = Boolean(
			age && age.value && age.value.trim() !== '' && age.value !== 'null'
		);

		const allFilled = Boolean(
			fname && fname.value.trim() !== '' &&
			lname && lname.value.trim() !== '' &&
			username && username.value.trim() !== '' &&
			email && email.value.trim() !== '' &&
			password && password.value.trim() !== '' &&
			passwordConfirm && passwordConfirm.value.trim() !== '' &&
			isAgeSelected &&
			agreeTerms && agreeTerms.checked &&
			hasRecaptcha
		);

		submitBtn.disabled = !allFilled;
	}

	window.checkAllRequiredRegistrationFields = checkAllRequiredFields;

	if (form) {
		// Listen to all user input & selection events across all form fields
		form.addEventListener('input', checkAllRequiredFields);
		form.addEventListener('change', checkAllRequiredFields);
		form.addEventListener('keyup', checkAllRequiredFields);
		form.addEventListener('paste', () => setTimeout(checkAllRequiredFields, 50));

		// Hook into jQuery NiceSelect for age dropdown if active in theme
		if (window.jQuery) {
			window.jQuery(form).find('select[name="age"]').on('change', function() {
				checkAllRequiredFields();
			});
		}

		// Initial check on load (handles browser autofill and validation errors with old() values)
		checkAllRequiredFields();
		setTimeout(checkAllRequiredFields, 200);
		setTimeout(checkAllRequiredFields, 600);
	}

	if (form && submitBtn) {
		form.addEventListener('submit', function(e) {
			// Prevent double submission if already processing
			if (submitBtn.classList.contains('is-processing')) {
				e.preventDefault();
				return false;
			}

			// Validate reCAPTCHA client-side before dispatching submission
			const hasRecaptcha = Boolean(
				typeof grecaptcha !== 'undefined' &&
				grecaptcha.getResponse &&
				grecaptcha.getResponse().length > 0
			);

			if (!hasRecaptcha) {
				e.preventDefault();
				const errDiv = document.getElementById('recaptchaInlineError');
				if (errDiv) {
					errDiv.style.display = 'block';
					errDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
				}
				submitBtn.disabled = true;
				return false;
			}

			const agreeCheckbox = document.getElementById('agree_terms');
			if (agreeCheckbox && !agreeCheckbox.checked) {
				e.preventDefault();
				agreeCheckbox.focus();
				return false;
			}

			// Activate processing state: spinner, text change, and disabled styling
			submitBtn.classList.add('is-processing');
			submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true" style="display: inline-block; width: 1rem; height: 1rem; vertical-align: -0.125em; border: 0.15em solid currentColor; border-right-color: transparent; border-radius: 50%; animation: spinner-border .75s linear infinite; margin-right: 8px;"></span> ' + {!! json_encode(__('Processing...')) !!};
			submitBtn.style.pointerEvents = 'none';

			// Disable button after submission has been dispatched so browser delivers the POST request
			setTimeout(function() {
				submitBtn.disabled = true;
			}, 20);
		});
	}

	// ==========================================
	// Booostr SSO Modal UI Controller
	// ==========================================
	var $ssoModal = $('#booostrSsoModal');

	function showSsoStep(stepId) {
		$('.sso-step-pane').hide();
		$(stepId).fadeIn(200);
		$('#ssoAlertBox').hide().text('');
	}

	// Open Modal
	$('#triggerBooostrSsoBtn').on('click', function(e) {
		e.preventDefault();
		$ssoModal.fadeIn(200);
		$('body').css('overflow', 'hidden');
		showSsoStep('#ssoStepEmail');
		$('#ssoEmailInput').focus();
	});

	// Close Modal
	$('#booostrSsoCloseBtn, #ssoConsentCancelBtn').on('click', function() {
		$ssoModal.fadeOut(200);
		$('body').css('overflow', '');
	});

	function showSsoInlineError(fieldWrapId, errorContainerId, messageHtml) {
		$(fieldWrapId).addClass('has-error');
		$(errorContainerId).find('.sso-inline-error-text').html(messageHtml);
		$(errorContainerId).show();
	}

	function clearSsoInlineError(fieldWrapId, errorContainerId) {
		$(fieldWrapId).removeClass('has-error');
		$(errorContainerId).hide().find('.sso-inline-error-text').html('');
	}

	// Clear inline error only when user types
	$('#ssoEmailInput').on('input', function() {
		clearSsoInlineError('#ssoEmailFieldWrap', '#ssoEmailError');
	});
	$('#ssoPasswordInput').on('input', function() {
		clearSsoInlineError('#ssoPasswordFieldWrap', '#ssoPasswordError');
	});
	$('#ssoOtpInput').on('input', function() {
		clearSsoInlineError('#ssoOtpFieldWrap', '#ssoOtpError');
	});

	// Enter key triggers appropriate next button in SSO modal
	$('#ssoEmailInput').on('keydown', function(e) {
		if (e.which === 13 || e.key === 'Enter') {
			e.preventDefault();
			$('#ssoEmailNextBtn').trigger('click');
		}
	});
	$('#ssoPasswordInput').on('keydown', function(e) {
		if (e.which === 13 || e.key === 'Enter') {
			e.preventDefault();
			$('#ssoPasswordNextBtn').trigger('click');
		}
	});
	$('#ssoOtpInput').on('keydown', function(e) {
		if (e.which === 13 || e.key === 'Enter') {
			e.preventDefault();
			$('#ssoOtpNextBtn').trigger('click');
		}
	});

	var currentSsoUsername = '';
	var currentSsoDisplayEmail = '';

	// Step 1: Email -> Verify Existence before moving to Password
	$('#ssoEmailNextBtn').on('click', function() {
		var email = $('#ssoEmailInput').val().trim();
		if (!email) {
			showSsoInlineError('#ssoEmailFieldWrap', '#ssoEmailError', 'Enter an email or username');
			$('#ssoEmailInput').focus();
			return;
		}
		clearSsoInlineError('#ssoEmailFieldWrap', '#ssoEmailError');

		var $btn = $(this);
		var originalText = $btn.find('span').text();
		$btn.prop('disabled', true).find('span').text('Checking...');

		$.ajax({
			url: "{{ route('customer.sso.check') }}",
			type: "POST",
			data: {
				_token: "{{ csrf_token() }}",
				username: email
			},
			success: function(res) {
				$btn.prop('disabled', false).find('span').text(originalText);
				currentSsoUsername = email;
				currentSsoDisplayEmail = res.display_email || email;
				$('.sso-user-email-text').text(currentSsoDisplayEmail);
				$('#ssoUserChipEmail').show();
				showSsoStep('#ssoStepPassword');
				$('#ssoPasswordInput').val('').focus();
			},
			error: function(xhr) {
				$btn.prop('disabled', false).find('span').text(originalText);
				var res = xhr.responseJSON || {};
				var msg = res.message || "Couldn't find your Booostr account. Please create a Booostr User Account first.";

				if (res.status === 'already_exists') {
					var loginUrl = res.login_url || "{{ route('customer.login') }}";
					var clubName = res.club_title || 'this store';
					msg = 'An account already exists with ' + clubName + '. <a href="' + loginUrl + '" style="color:#0084d6;font-weight:600;text-decoration:underline;">Click here to Log In</a>';
				}

				showSsoInlineError('#ssoEmailFieldWrap', '#ssoEmailError', msg);
				$('#ssoEmailInput').focus();
			}
		});
	});

	// Step 2: Password Back -> Step 1
	$('#ssoPasswordBackBtn').on('click', function() {
		clearSsoInlineError('#ssoPasswordFieldWrap', '#ssoPasswordError');
		showSsoStep('#ssoStepEmail');
		$('#ssoEmailInput').focus();
	});

	// Toggle Password Visibility
	$('#ssoShowPasswordToggle').on('change', function() {
		$('#ssoPasswordInput').attr('type', $(this).is(':checked') ? 'text' : 'password');
	});

	// Step 2: Password Next -> Verify Credentials via Backend
	$('#ssoPasswordNextBtn').on('click', function() {
		var pass = $('#ssoPasswordInput').val();
		if (!pass) {
			showSsoInlineError('#ssoPasswordFieldWrap', '#ssoPasswordError', 'Enter your password');
			$('#ssoPasswordInput').focus();
			return;
		}
		clearSsoInlineError('#ssoPasswordFieldWrap', '#ssoPasswordError');

		var $btn = $(this);
		var originalText = $btn.find('span').text();
		$btn.prop('disabled', true).find('span').text('Verifying...');

		$.ajax({
			url: "{{ route('customer.sso.credentials') }}",
			type: "POST",
			data: {
				_token: "{{ csrf_token() }}",
				username: currentSsoUsername,
				password: pass
			},
			success: function(res) {
				$btn.prop('disabled', false).find('span').text(originalText);

				var fullEmail = currentSsoDisplayEmail || res.email || currentSsoUsername;
				$('.sso-user-email-text').text(fullEmail);

				showSsoStep('#ssoStepOtp');
				$('.sso-otp-digit').val('').removeClass('has-val');
				$('#ssoOtpInput').val('');
				clearSsoInlineError('#ssoOtpFieldWrap', '#ssoOtpError');
				$('.sso-otp-digit[data-index="0"]').focus();

				startResendCooldown(60);
				startOtpValidityTimer(600);
			},
			error: function(xhr) {
				$btn.prop('disabled', false).find('span').text(originalText);
				var res = xhr.responseJSON || {};
				var msg = res.message || "Wrong password. Try again or click Forgot password.";
				showSsoInlineError('#ssoPasswordFieldWrap', '#ssoPasswordError', msg);
				$('#ssoPasswordInput').focus();
			}
		});
	});

	// 6-Digit OTP Box Controller
	function syncOtpValue() {
		var code = '';
		$('.sso-otp-digit').each(function() {
			code += $(this).val();
		});
		$('#ssoOtpInput').val(code);
		return code;
	}

	$('.sso-otp-digit').on('input', function(e) {
		var $this = $(this);
		var val = $this.val().replace(/[^0-9]/g, '');
		$this.val(val);

		if (val) {
			$this.addClass('has-val');
			var nextIndex = parseInt($this.data('index')) + 1;
			if (nextIndex < 6) {
				$('.sso-otp-digit[data-index="' + nextIndex + '"]').focus();
			}
		} else {
			$this.removeClass('has-val');
		}

		var code = syncOtpValue();
		clearSsoInlineError('#ssoOtpFieldWrap', '#ssoOtpError');

		if (code.length === 6) {
			$('#ssoOtpNextBtn').trigger('click');
		}
	});

	$('.sso-otp-digit').on('keydown', function(e) {
		var $this = $(this);
		var index = parseInt($this.data('index'));

		if (e.key === 'Backspace' || e.which === 8) {
			if (!$this.val() && index > 0) {
				var prevInput = $('.sso-otp-digit[data-index="' + (index - 1) + '"]');
				prevInput.val('').removeClass('has-val').focus();
				syncOtpValue();
				e.preventDefault();
			}
		} else if (e.key === 'ArrowLeft' && index > 0) {
			$('.sso-otp-digit[data-index="' + (index - 1) + '"]').focus();
		} else if (e.key === 'ArrowRight' && index < 5) {
			$('.sso-otp-digit[data-index="' + (index + 1) + '"]').focus();
		} else if (e.key === 'Enter' || e.which === 13) {
			e.preventDefault();
			$('#ssoOtpNextBtn').trigger('click');
		}
	});

	// Support paste of 6-digit code
	$('.sso-otp-digit').on('paste', function(e) {
		e.preventDefault();
		var clipboardData = (e.originalEvent || e).clipboardData || window.clipboardData;
		var pastedData = clipboardData.getData('text').trim().replace(/[^0-9]/g, '').slice(0, 6);

		if (pastedData) {
			for (var i = 0; i < pastedData.length; i++) {
				var digitInput = $('.sso-otp-digit[data-index="' + i + '"]');
				digitInput.val(pastedData[i]).addClass('has-val');
			}
			syncOtpValue();
			clearSsoInlineError('#ssoOtpFieldWrap', '#ssoOtpError');

			var nextFocus = Math.min(pastedData.length, 5);
			$('.sso-otp-digit[data-index="' + nextFocus + '"]').focus();

			if (pastedData.length === 6) {
				$('#ssoOtpNextBtn').trigger('click');
			}
		}
	});

	var resendCooldownTimer = null;
	var otpValidityTimer = null;

	function startResendCooldown(seconds) {
		var $link = $('#ssoResendOtpLink');
		$link.addClass('disabled').css({'pointer-events': 'none', 'opacity': '0.6'});

		if (resendCooldownTimer) clearInterval(resendCooldownTimer);

		var remaining = seconds || 60;
		$link.text('Resend in ' + remaining + 's');

		resendCooldownTimer = setInterval(function() {
			remaining--;
			if (remaining > 0) {
				$link.text('Resend in ' + remaining + 's');
			} else {
				clearInterval(resendCooldownTimer);
				$link.removeClass('disabled').css({'pointer-events': '', 'opacity': ''}).text('Resend code');
			}
		}, 1000);
	}

	function startOtpValidityTimer(seconds) {
		if (otpValidityTimer) clearInterval(otpValidityTimer);

		var remaining = seconds || 600;
		otpValidityTimer = setInterval(function() {
			remaining--;
			if (remaining <= 0) {
				clearInterval(otpValidityTimer);
				showSsoInlineError('#ssoOtpFieldWrap', '#ssoOtpError', 'Your verification code has expired (10 minutes). Please click Resend code to get a new one.');
				$('.sso-otp-digit').val('').removeClass('has-val');
				$('#ssoOtpInput').val('');

				if (resendCooldownTimer) clearInterval(resendCooldownTimer);
				$('#ssoResendOtpLink').removeClass('disabled').css({'pointer-events': '', 'opacity': ''}).text('Resend code');
			}
		}, 1000);
	}

	$('#ssoResendOtpLink').on('click', function(e) {
		e.preventDefault();
		var $link = $(this);
		if ($link.hasClass('disabled')) return;

		var originalText = $link.text();
		$link.addClass('disabled').css({'pointer-events': 'none', 'opacity': '0.6'}).text('Sending code...');

		$.ajax({
			url: "{{ route('customer.sso.resend') }}",
			type: "POST",
			data: {
				_token: "{{ csrf_token() }}",
				username: currentSsoUsername
			},
			success: function(res) {
				$('.sso-otp-digit').val('').removeClass('has-val');
				$('#ssoOtpInput').val('');
				clearSsoInlineError('#ssoOtpFieldWrap', '#ssoOtpError');
				$('.sso-otp-digit[data-index="0"]').focus();

				var cooldown = res.cooldown || 60;
				startResendCooldown(cooldown);
				startOtpValidityTimer(600);
			},
			error: function(xhr) {
				var res = xhr.responseJSON || {};
				var msg = res.message || 'Unable to resend code right now.';
				var isRateLimited = (xhr.status === 429) || (xhr.status === 400 && msg.toLowerCase().indexOf('wait a moment') !== -1);

				if (isRateLimited) {
					startResendCooldown(60);
				} else {
					$link.removeClass('disabled').css({'pointer-events': '', 'opacity': ''}).text(originalText);
				}
				showSsoInlineError('#ssoOtpFieldWrap', '#ssoOtpError', msg);
			}
		});
	});

	// Step 3: OTP Back -> Step 2
	$('#ssoOtpBackBtn').on('click', function() {
		if (resendCooldownTimer) clearInterval(resendCooldownTimer);
		if (otpValidityTimer) clearInterval(otpValidityTimer);
		clearSsoInlineError('#ssoOtpFieldWrap', '#ssoOtpError');
		showSsoStep('#ssoStepPassword');
		$('#ssoPasswordInput').focus();
	});

	// Step 3: OTP Next -> Verify OTP
	$('#ssoOtpNextBtn').on('click', function() {
		var otp = syncOtpValue().trim();
		if (otp.length < 6) {
			showSsoInlineError('#ssoOtpFieldWrap', '#ssoOtpError', 'Enter the 6-digit verification code');
			var firstEmpty = $('.sso-otp-digit').filter(function() { return !$(this).val(); }).first();
			(firstEmpty.length ? firstEmpty : $('.sso-otp-digit[data-index="0"]')).focus();
			return;
		}
		clearSsoInlineError('#ssoOtpFieldWrap', '#ssoOtpError');

		var $btn = $(this);
		var originalText = $btn.find('span').text();
		$btn.prop('disabled', true).find('span').text('Verifying...');

		$.ajax({
			url: "{{ route('customer.sso.otp') }}",
			type: "POST",
			data: {
				_token: "{{ csrf_token() }}",
				username: currentSsoUsername,
				otp: otp
			},
			success: function(res) {
				$btn.prop('disabled', false).find('span').text(originalText);

				if (resendCooldownTimer) clearInterval(resendCooldownTimer);
				if (otpValidityTimer) clearInterval(otpValidityTimer);

				var user = res.user || {};
				var firstName = $.trim(user.first_name || '');
				var lastName = $.trim(user.last_name || '');
				var fullName = $.trim(firstName + ' ' + lastName);
				if (!fullName) {
					fullName = user.user_nicename || user.username || user.user_login || currentSsoUsername;
				}

				var username = user.user_login || user.username || '';
				var displayEmail = user.user_email || user.email || currentSsoUsername;
				var age = user.age || '';
				var userPhoto = user.user_photo || user.image || '';

				// Populate Step 4 Consent elements
				$('#ssoConsentDisplayName').text(fullName);
				if (username) {
					$('#ssoConsentUsername').text(username).show();
				} else {
					$('#ssoConsentUsername').hide();
				}

				$('.sso-user-email-text').text(displayEmail);
				var rawAge = $.trim(age).toLowerCase();
				var ageDisplay = '';
				if (rawAge === 'adult') {
					ageDisplay = 'Above 19';
				} else if (rawAge === 'child') {
					ageDisplay = 'Above 13 - Under 19';
				} else if (age) {
					ageDisplay = age;
				}

				if (ageDisplay) {
					$('#ssoConsentAge').text(ageDisplay).show();
				} else {
					$('#ssoConsentAge').hide();
				}

				if (userPhoto) {
					$('#ssoPermUserAvatarWrap').html('<img src="' + userPhoto + '" alt="' + fullName + '" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">');
				} else {
					$('#ssoPermUserAvatarWrap').html('<i class="las la-user"></i>');
				}

				showSsoStep('#ssoStepConsent');
			},
			error: function(xhr) {
				$btn.prop('disabled', false).find('span').text(originalText);
				var res = xhr.responseJSON || {};
				var msg = res.message || "Invalid or expired verification code.";

				if (msg.toLowerCase().indexOf('expired') !== -1) {
					$('.sso-otp-digit').val('').removeClass('has-val');
					$('#ssoOtpInput').val('');
					if (resendCooldownTimer) clearInterval(resendCooldownTimer);
					$('#ssoResendOtpLink').removeClass('disabled').css({'pointer-events': '', 'opacity': ''}).text('Resend code');
				}

				showSsoInlineError('#ssoOtpFieldWrap', '#ssoOtpError', msg);
				$('#ssoOtpInput').focus();
			}
		});
	});

	// Step 4: Continue -> Create User, Sync CRM & Auto-Login
	$('#ssoConsentContinueBtn').on('click', function() {
		var $btn = $(this);
		var originalText = $btn.find('span').text();
		$btn.prop('disabled', true).find('span').text('Signing in...');

		$.ajax({
			url: "{{ route('customer.sso.complete') }}",
			type: "POST",
			data: {
				_token: "{{ csrf_token() }}"
			},
			success: function(res) {
				var redirectUrl = res.redirect_url || "{{ url('/customer/dashboard') }}";
				window.location.href = redirectUrl;
			},
			error: function(xhr) {
				$btn.prop('disabled', false).find('span').text(originalText);
				var res = xhr.responseJSON || {};
				var msg = res.message || "Unable to complete sign-in. Please try again.";
				$('#ssoAlertBox').html(msg).fadeIn(200);
			}
		});
	});
});
</script>
@endpush

@include('theme.resto.customer.partials.sso-modal')
@endsection
