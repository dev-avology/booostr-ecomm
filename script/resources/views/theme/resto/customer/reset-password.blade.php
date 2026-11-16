@extends('theme.resto.layouts.app')
@section('content')
<!-- Start Reset Password Section -->
<section class="login section py-5" style="background-color: #f8fafc; min-height: 80vh;">
	<div class="container">
		<div class="row justify-content-center">
			<div class="col-lg-6 col-md-8 col-12" style="max-width: 550px;">
				<div class="form-head bg-white" style="border: 1px solid #edf2f7; border-radius: 14px; box-shadow: 0 10px 30px rgba(0,0,0,0.06); padding: 48px 45px;">
					<h3 class="title text-center" style="font-size: 26px; font-weight: 700; color: #1e293b; letter-spacing: -0.5px; margin-bottom: 24px;">
						{{ __('Reset Your Password') }}
					</h3>

					<!-- Alert Notifications Area -->
					<div id="resetAlertArea">
						@if(Session::has('msg'))
							<div class="alert alert-{{ Session::get('type', 'danger') === 'danger' ? 'danger' : 'success' }}" role="alert" style="margin-bottom: 24px; border-radius: 8px; font-size: 14px; line-height: 1.55;">
								{!! Session::get('msg') !!}
							</div>
						@endif
						@if(Session::has('error'))
							<div class="alert alert-danger" role="alert" style="margin-bottom: 24px; border-radius: 8px; font-size: 14px; line-height: 1.55;">
								{{ Session::get('error') }}
							</div>
						@endif
					</div>

					@if(empty($tokenValid))
						<!-- Invalid / Expired Token State -->
						<div class="alert alert-danger" role="alert" style="margin-bottom: 24px; border-radius: 8px; font-size: 14px; line-height: 1.55;">
							{{ __('This password reset link is invalid or has expired. Please request a new reset link.') }}
						</div>
						<div class="text-center mt-3">
							<a href="{{ route('customer.forget.password') }}" class="btn w-100" style="height: 52px; background: #528CD7; color: #ffffff; font-size: 15.5px; font-weight: 600; border-radius: 8px; display: inline-flex; align-items: center; justify-content: center; text-decoration: none; box-shadow: 0 4px 14px rgba(82, 140, 215, 0.3);">
								{{ __('Request New Reset Link') }}
							</a>
						</div>
					@else
						<!-- Valid Token - Password Reset Form -->
						<p class="text-center" style="font-size: 14.5px; color: #64748b; margin-bottom: 24px;">
							{{ __('Create a new password for user account:') }} <strong style="color: #1e293b;">{{ $username }}</strong>
						</p>

						<form action="{{ route('customer.reset.password.post') }}" method="post" id="resetPasswordForm">
							@csrf

							<input type="hidden" name="token" value="{{ $token }}">
							<input type="hidden" name="username" value="{{ $username }}">

							<!-- New Password with Eye Toggle -->
							<div class="form-group mb-3">
								<label class="form-label" style="font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 6px; display: block;">
									{{ __('New Password') }} <span style="color: #ef4444; font-weight: 700;">*</span>
								</label>
								<div class="password-wrapper" style="position: relative;">
									<input class="custom-input" type="password" id="resetPassword" name="password" required placeholder="{{ __('Minimum 8 characters') }}" autocomplete="new-password" style="padding-right: 48px;" />
									<span class="password-toggle-icon" onclick="togglePasswordVisibility('resetPassword', this)" title="Show / Hide Password" style="position: absolute; right: 16px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #94a3b8; font-size: 19px; z-index: 2;">
										<i class="icofont-eye"></i>
									</span>
								</div>
								@error('password')
								<span class="text-danger small d-block mt-1"><strong>{{ $message }}</strong></span>
								@enderror
							</div>

							<!-- Confirm New Password with Eye Toggle -->
							<div class="form-group mb-3">
								<label class="form-label" style="font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 6px; display: block;">
									{{ __('Confirm New Password') }} <span style="color: #ef4444; font-weight: 700;">*</span>
								</label>
								<div class="password-wrapper" style="position: relative;">
									<input class="custom-input" type="password" id="resetPasswordConfirm" name="password_confirmation" required placeholder="{{ __('Confirm your new password') }}" autocomplete="new-password" style="padding-right: 48px;" />
									<span class="password-toggle-icon" onclick="togglePasswordVisibility('resetPasswordConfirm', this)" title="Show / Hide Password" style="position: absolute; right: 16px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #94a3b8; font-size: 19px; z-index: 2;">
										<i class="icofont-eye"></i>
									</span>
								</div>
								@error('password_confirmation')
								<span class="text-danger small d-block mt-1"><strong>{{ $message }}</strong></span>
								@enderror
							</div>

							<!-- Password Mismatch Feedback Message -->
							<div id="passwordMismatchError" class="text-danger small mt-1 mb-2 font-weight-bold" style="display: none;">
								{{ __('Passwords do not match.') }}
							</div>

							<!-- Submit Button -->
							<div class="reset-button-wrapper mt-4">
								<button type="submit" id="resetSubmitBtn" class="btn w-100" disabled>
									{{ __('Update Password') }}
								</button>
							</div>
						</form>
					@endif

					<!-- Navigation Back to Login -->
					<div class="text-center mt-4 pt-2">
						<p style="font-size: 14px; color: #64748b; margin-bottom: 0;">
							{{ __('Remembered your password?') }} 
							<a href="{{ url('/customer/login') }}" style="color: #528CD7; font-weight: 600; text-decoration: none;">
								{{ __('Back to login') }}
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

.password-toggle-icon:hover {
	color: #528CD7 !important;
}

.reset-button-wrapper {
	width: 100% !important;
}

#resetSubmitBtn {
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

#resetSubmitBtn::before,
#resetSubmitBtn::after {
	display: none !important;
	content: none !important;
}

#resetSubmitBtn:hover:not(:disabled) {
	background-color: #467fcb !important;
	color: #ffffff !important;
	box-shadow: 0 6px 18px rgba(70, 127, 203, 0.35) !important;
	transform: translateY(-1px);
}

#resetSubmitBtn:active:not(:disabled) {
	transform: translateY(0);
}

#resetSubmitBtn:disabled {
	background-color: #a0c4f1 !important;
	color: #ffffff !important;
	cursor: not-allowed !important;
	opacity: 0.8 !important;
	box-shadow: none !important;
	pointer-events: none !important;
}

#resetSubmitBtn.is-processing {
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
		margin-bottom: 18px !important;
	}
	.custom-input {
		height: 48px !important;
		font-size: 14px !important;
	}
	#resetSubmitBtn {
		height: 48px !important;
		font-size: 15px !important;
	}
}
</style>
@endpush

@push('js')
<script>
function togglePasswordVisibility(fieldId, iconElem) {
	const field = document.getElementById(fieldId);
	const icon = iconElem ? iconElem.querySelector('i') : null;
	if (!field) return;

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
	const form = document.getElementById('resetPasswordForm');
	const passInput = document.getElementById('resetPassword');
	const confirmInput = document.getElementById('resetPasswordConfirm');
	const submitBtn = document.getElementById('resetSubmitBtn');
	const mismatchErr = document.getElementById('passwordMismatchError');

	if (!form || !passInput || !confirmInput || !submitBtn) return;

	function validatePasswordForm() {
		if (submitBtn.classList.contains('is-processing')) return;

		const pass = passInput.value ? passInput.value : '';
		const confirm = confirmInput.value ? confirmInput.value : '';

		// Password must be at least 8 characters and match confirm
		const isLengthValid = (pass.length >= 8);
		const isMatch = (pass.length > 0 && confirm.length > 0 && pass === confirm);

		if (confirm.length > 0 && pass !== confirm) {
			if (mismatchErr) mismatchErr.style.display = 'block';
		} else {
			if (mismatchErr) mismatchErr.style.display = 'none';
		}

		submitBtn.disabled = !(isLengthValid && isMatch);
	}

	passInput.addEventListener('input', validatePasswordForm);
	passInput.addEventListener('keyup', validatePasswordForm);
	confirmInput.addEventListener('input', validatePasswordForm);
	confirmInput.addEventListener('keyup', validatePasswordForm);

	form.addEventListener('submit', function(e) {
		if (submitBtn.classList.contains('is-processing') || submitBtn.disabled) {
			e.preventDefault();
			return false;
		}

		const pass = passInput.value ? passInput.value : '';
		const confirm = confirmInput.value ? confirmInput.value : '';

		if (pass.length < 8 || pass !== confirm) {
			e.preventDefault();
			return false;
		}

		submitBtn.classList.add('is-processing');
		submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true" style="display: inline-block; width: 1rem; height: 1rem; vertical-align: -0.125em; border: 0.15em solid currentColor; border-right-color: transparent; border-radius: 50%; animation: spinner-border .75s linear infinite; margin-right: 8px;"></span> ' + {!! json_encode(__('Updating Password...')) !!};
		submitBtn.style.pointerEvents = 'none';

		setTimeout(function() {
			submitBtn.disabled = true;
		}, 20);
	});
});
</script>
@endpush
@endsection

