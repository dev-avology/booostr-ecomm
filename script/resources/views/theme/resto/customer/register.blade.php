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
						<a href="{{ url('/customer/login') }}" class="booostr-sso-btn">
							<div class="sso-logo-box">
								<img src="https://app.booostr.co/wp-content/themes/booostr-app/assets/images/Header-logo.png" alt="Booostr">
							</div>
							<div class="sso-text-box">
								<span>{{ __('Sign up with your Booostr User Account') }}</span>
							</div>
						</a>
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
							<select name="age" class="custom-input" required style="width: 100%; height: 48px; border: 1px solid #e2e8f0; border-radius: 8px; padding: 0 16px; font-size: 14px; color: #334155; background-color: #fff;">
								<option value="" disabled {{ old('age') ? '' : 'selected' }}>{{ __('Choose Age') }}</option>
								<option value="child" {{ old('age') == 'child' ? 'selected' : '' }}>{{ __('Above 13 - Under 19') }}</option>
								<option value="adult" {{ old('age') == 'adult' ? 'selected' : '' }}>{{ __('Above 19') }}</option>
							</select>
							@error('age')
							<span class="text-danger small d-block mt-1"><strong>{{ $message }}</strong></span>
							@enderror
						</div>

						<!-- Terms and condition checkbox -->
						<div class="form-group mb-3 mt-3">
							<div class="d-flex align-items-start" style="gap: 10px;">
								<input type="checkbox" name="agree_terms" id="agree_terms" required {{ old('agree_terms') ? 'checked' : '' }} style="width: 18px; height: 18px; margin-top: 3px; cursor: pointer; accent-color: var(--primary, #00A347);">
								<label for="agree_terms" style="font-size: 13px; color: #475569; cursor: pointer; line-height: 1.5; margin-bottom: 0;">
									{{ __("I've read and agree with") }} 
									<a href="{{ url('/page/terms-and-conditions') }}" target="_blank" style="color: var(--primary, #00A347); font-weight: 600; text-decoration: underline;">{{ __('Terms and condition') }}</a> 
									{{ __('and') }} 
									<a href="{{ url('/page/privacy-policy') }}" target="_blank" style="color: var(--primary, #00A347); font-weight: 600; text-decoration: underline;">{{ __('privacy policy') }}</a>
								</label>
							</div>
							@error('agree_terms')
							<span class="text-danger small d-block mt-1"><strong>{{ $message }}</strong></span>
							@enderror
						</div>

						<!-- reCAPTCHA Widget -->
						<div class="form-group my-3 d-flex justify-content-center">
							<div class="g-recaptcha" data-sitekey="{{ config('services.recaptcha.site_key') ?: env('RECAPTCHA_SITE_KEY') }}"></div>
						</div>
						@error('g-recaptcha-response')
						<span class="text-danger small d-block text-center mb-2"><strong>{{ $message }}</strong></span>
						@enderror
						
						<!-- Register Submit Button (disabled until terms checked) -->
						<div class="button mt-4">
							<button type="submit" id="registerSubmitBtn" class="btn theme-btn w-100" style="height: 48px; border-radius: 8px; font-weight: 600; font-size: 15px; letter-spacing: 0.3px; transition: all 0.3s ease;">
								{{ __('Register') }}
							</button>
						</div>

						<p class="outer-link mt-4 text-center" style="font-size: 14px; color: #64748b;">
							{{ __('Already have an account?') }} 
							<a href="{{ url('/customer/login') }}" style="color: var(--primary, #00A347); font-weight: 600; text-decoration: none;">
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

.login .form-head .custom-input {
	width: 100% !important;
	height: 48px !important;
	min-height: 48px !important;
	padding: 10px 16px !important;
	color: #1e293b !important;
	background-color: #ffffff !important;
	border: 1px solid #e2e8f0 !important;
	border-radius: 8px !important;
	font-size: 14px !important;
	line-height: 1.5 !important;
	outline: none !important;
	box-shadow: none !important;
	transition: border-color 0.2s ease, box-shadow 0.2s ease !important;
}

.login .form-head .custom-input:focus {
	border-color: var(--primary, #00A347) !important;
	box-shadow: 0 0 0 3px rgba(0, 163, 71, 0.12) !important;
	background-color: #ffffff !important;
}

.login .form-head .custom-input::placeholder {
	color: #94a3b8 !important;
	font-size: 13.5px !important;
}

.login .form-head .nice-select {
	display: none !important; /* Hide unstyled nice-select replacement if initialized */
}

.login .form-head select.custom-input {
	display: block !important;
	-webkit-appearance: menulist !important;
	-moz-appearance: menulist !important;
	appearance: menulist !important;
}

.booostr-sso-btn {
	display: flex;
	align-items: stretch;
	border: 1.5px solid #2563eb;
	border-radius: 8px;
	overflow: hidden;
	min-height: 50px;
	text-decoration: none;
	transition: all 0.2s ease;
}

.booostr-sso-btn:hover {
	opacity: 0.95;
	box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2);
}

.booostr-sso-btn .sso-logo-box {
	background: #ffffff;
	width: 110px;
	min-width: 110px;
	display: flex;
	align-items: center;
	justify-content: center;
	padding: 4px 12px;
	border-right: 1.5px solid #2563eb;
}

.booostr-sso-btn .sso-logo-box img {
	max-height: 28px;
	max-width: 100%;
	object-fit: contain;
}

.booostr-sso-btn .sso-text-box {
	background: #2563eb;
	flex: 1;
	display: flex;
	align-items: center;
	justify-content: center;
	color: #ffffff;
	font-weight: 600;
	font-size: 14px;
	padding: 0 12px;
	text-align: center;
}

.password-toggle-icon:hover {
	color: #475569 !important;
}

#registerSubmitBtn:disabled {
	opacity: 0.55 !important;
	cursor: not-allowed !important;
	filter: grayscale(25%);
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
	.booostr-sso-btn .sso-logo-box {
		width: 85px;
		min-width: 85px;
		padding: 4px 8px;
	}
	.booostr-sso-btn .sso-logo-box img {
		max-height: 22px !important;
	}
	.booostr-sso-btn .sso-text-box {
		font-size: 12.5px;
		padding: 8px 10px;
		line-height: 1.3;
	}
	.g-recaptcha {
		transform: scale(0.92);
		-webkit-transform: scale(0.92);
		transform-origin: center center;
		-webkit-transform-origin: center center;
	}
}

@media (max-width: 360px) {
	.g-recaptcha {
		transform: scale(0.82);
		-webkit-transform: scale(0.82);
		transform-origin: center center;
		-webkit-transform-origin: center center;
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
