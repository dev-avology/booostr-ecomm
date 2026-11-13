@extends('theme.resto.layouts.app')
@section('content')
<!-- Start Login Section -->
<section class="login section py-5" style="background-color: #f8fafc; min-height: 80vh;">
	<div class="container">
		<div class="row justify-content-center">
			<div class="col-lg-5 col-md-7 col-12">
				<div class="form-head bg-white p-4 p-md-5" style="border: 1px solid #edf2f7; border-radius: 14px; box-shadow: 0 10px 30px rgba(0,0,0,0.06);">
					<h3 class="title text-center mb-4" style="font-size: 26px; font-weight: 700; color: #1e293b; letter-spacing: -0.5px;">{{ __('Login to your account') }}</h3>

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

					<form action="{{ route('login') }}" method="post" id="loginForm">
						@csrf

						<!-- Username or Email Address -->
						<div class="form-group mb-3">
							<input class="custom-input" type="text" id="loginEmail" name="email" value="{{ old('email') }}" required placeholder="{{ __('Your username or email address') }}" autocomplete="username" />
							@error('email')
							<span class="text-danger small d-block mt-1"><strong>{{ $message }}</strong></span>
							@enderror
						</div>

						<!-- Password with Eye Toggle -->
						<div class="form-group mb-3">
							<div class="password-wrapper" style="position: relative;">
								<input class="custom-input" type="password" id="loginPassword" name="password" required placeholder="{{ __('Password') }}" autocomplete="current-password" style="padding-right: 44px;" />
								<span class="password-toggle-icon" onclick="togglePasswordVisibility('loginPassword', this)" title="Show / Hide Password" style="position: absolute; right: 14px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #94a3b8; font-size: 18px; z-index: 2;">
									<i class="icofont-eye"></i>
								</span>
							</div>
							@error('password')
							<span class="text-danger small d-block mt-1"><strong>{{ $message }}</strong></span>
							@enderror
						</div>

						<!-- Remember Me and Forgot Password Row -->
						<div class="remember-forgot-row" style="display: flex; justify-content: space-between; align-items: center; margin: 18px 0 22px;">
							<div class="d-flex align-items-center" style="gap: 8px;">
								<input type="checkbox" name="remember" id="rememberMe" style="width: 17px; min-width: 17px; height: 17px; accent-color: #528CD7; cursor: pointer; margin: 0;">
								<label for="rememberMe" style="font-size: 13.5px; color: #475569; cursor: pointer; margin: 0; user-select: none;">{{ __('Remember me') }}</label>
							</div>
							<div>
								<a href="{{ url('/password/reset') }}" class="forgot-link" style="font-size: 13.5px; color: #528CD7; text-decoration: none; font-weight: 500; transition: color 0.2s ease;">{{ __('Forgot password?') }}</a>
							</div>
						</div>

						<!-- Login Submit Button -->
						<div class="login-button-wrapper mt-3">
							<button type="submit" id="loginSubmitBtn" class="btn w-100" disabled>
								{{ __('Login') }}
							</button>
						</div>

						<!-- Sign Up Footer Link matching reference wording -->
						<p class="outer-link mt-4 text-center" style="font-size: 14px; color: #64748b; margin-bottom: 0;">
							{{ __("Don't have an account with Booostr?") }} 
							<a href="{{ url('/customer/register') }}" class="signup-link" style="color: #528CD7; font-weight: 600; text-decoration: none;">
								{{ __('Sign up today!') }}
							</a>
						</p>
					</form>
				</div>
			</div>
		</div>
	</div>
</section>
<!-- End Login Section -->

<style>
/* Custom scoped styling for Login Form */
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

/* Crisp high-contrast inputs */
.login .form-head .custom-input {
	width: 100% !important;
	height: 48px !important;
	min-height: 48px !important;
	padding: 10px 16px !important;
	color: #1e293b !important;
	background-color: #ffffff !important;
	border: 1.5px solid #cbd5e1 !important;
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

.password-toggle-icon:hover {
	color: #475569 !important;
}

.forgot-link:hover {
	color: #3d71b5 !important;
	text-decoration: underline !important;
}

.signup-link:hover {
	color: #3d71b5 !important;
	text-decoration: underline !important;
}

/* Login Button Styled with #528CD7 - Green hover completely eliminated */
.login-button-wrapper,
.login .form-head form .login-button-wrapper {
	margin-top: 20px;
	width: 100%;
}

#loginSubmitBtn,
.login-button-wrapper #loginSubmitBtn,
.login .form-head form .login-button-wrapper #loginSubmitBtn,
.login .form-head form .button #loginSubmitBtn {
	background: #528CD7 !important;
	background-color: #528CD7 !important;
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
#loginSubmitBtn::before,
#loginSubmitBtn::after,
.login-button-wrapper #loginSubmitBtn::before,
.login-button-wrapper #loginSubmitBtn::after,
.login .form-head form .button #loginSubmitBtn::before,
.login .form-head form .button #loginSubmitBtn::after,
.button .btn#loginSubmitBtn::before,
.button .btn#loginSubmitBtn:hover::before {
	display: none !important;
	content: none !important;
	width: 0 !important;
	height: 0 !important;
	background: transparent !important;
	background-color: transparent !important;
	opacity: 0 !important;
	visibility: hidden !important;
}

#loginSubmitBtn:hover:not(:disabled):not(.is-processing) {
	background: #467fcb !important; /* Subtle theme blue - never green */
	background-color: #467fcb !important;
	color: #ffffff !important;
	box-shadow: 0 4px 12px rgba(82, 140, 215, 0.25) !important;
}

#loginSubmitBtn:focus,
#loginSubmitBtn:active {
	background: #467fcb !important;
	background-color: #467fcb !important;
	color: #ffffff !important;
	outline: none !important;
	box-shadow: none !important;
}

#loginSubmitBtn:disabled {
	opacity: 0.55 !important;
	cursor: not-allowed !important;
	background: #94b8e8 !important;
	background-color: #94b8e8 !important;
	box-shadow: none !important;
}

#loginSubmitBtn.is-processing {
	opacity: 0.8 !important;
	cursor: wait !important;
	pointer-events: none !important;
	background: #528CD7 !important;
	background-color: #528CD7 !important;
	box-shadow: none !important;
}

@keyframes spinner-border {
	to { transform: rotate(360deg); }
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
	.remember-forgot-row {
		flex-direction: column;
		align-items: flex-start !important;
		gap: 10px;
	}
}
</style>

@push('js')
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
	const form = document.getElementById('loginForm');
	const emailInput = document.getElementById('loginEmail');
	const passwordInput = document.getElementById('loginPassword');
	const submitBtn = document.getElementById('loginSubmitBtn');

	function checkRequiredFields() {
		if (!submitBtn || submitBtn.classList.contains('is-processing')) return;

		const hasEmail = emailInput && emailInput.value.trim() !== '';
		const hasPassword = passwordInput && passwordInput.value.trim() !== '';

		submitBtn.disabled = !(hasEmail && hasPassword);
	}

	if (form) {
		form.addEventListener('input', checkRequiredFields);
		form.addEventListener('change', checkRequiredFields);
		form.addEventListener('keyup', checkRequiredFields);
		form.addEventListener('paste', () => setTimeout(checkRequiredFields, 50));

		// Check on load (handles browser autofill and old input)
		checkRequiredFields();
		setTimeout(checkRequiredFields, 200);
		setTimeout(checkRequiredFields, 600);
	}

	if (form && submitBtn) {
		form.addEventListener('submit', function(e) {
			// Prevent double submission if already processing
			if (submitBtn.classList.contains('is-processing')) {
				e.preventDefault();
				return false;
			}

			// Validate inputs
			if (!emailInput.value.trim() || !passwordInput.value.trim()) {
				e.preventDefault();
				return false;
			}

			// Activate processing state: spinner, text change, and disabled styling
			submitBtn.classList.add('is-processing');
			submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true" style="display: inline-block; width: 1rem; height: 1rem; vertical-align: -0.125em; border: 0.15em solid currentColor; border-right-color: transparent; border-radius: 50%; animation: spinner-border .75s linear infinite; margin-right: 8px;"></span> ' + {{ json_encode(__('Processing...')) }};
			submitBtn.style.pointerEvents = 'none';

			// Disable button after submission has been dispatched so browser delivers the POST request
			setTimeout(function() {
				submitBtn.disabled = true;
			}, 20);
		});
	}
});
</script>
@endpush
@endsection
