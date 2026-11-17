@extends('theme.resto.customer.app')

@section('customer_content')
{{-- 
    =================================================================================
    Site Builder Standard: Customer Edit Profile View
    Author / Context: Foodsify & Booostr Central Auth Integration
    Features:
      - Clean 2-column layout matching Site Builder user/edit-profile specification
      - Fields: Name, Email, Mobile, Age, Address, City, State, Country, Image Upload
      - Preserves existing meta JSON (post_code, lat, long, username, wpuid)
      - Pure frontend implementation without disturbing existing controller APIs
    =================================================================================
--}}
<div class="dashboard-block mt-0 profile-settings-block bg-white p-4 rounded shadow-sm">
	<h3 class="block-title fs-5 mb-4 border-bottom pb-2">
		<i class="icofont-ui-user me-2 text-primary"></i>{{ __('Edit Profile') }}
	</h3>

	<div class="inner-block">
		<div id="profileAlertArea"></div>

		<form class="profile-setting-form" id="profileEditForm" method="post" action="{{ route('customer.profile.update') }}" enctype="multipart/form-data">
			@csrf

			<div class="row g-3">
				{{-- Row 1: First Name & Last Name --}}
				<div class="col-lg-6 col-12">
					<div class="form-group mb-3">
						<label class="form-label font-weight-bold">{{ __('First Name') }} <span style="color: #ef4444; font-weight: 700;">*</span></label>
						<input type="text" name="fname" class="form-control" maxlength="191" value="{{ $fname ?? '' }}" required placeholder="{{ __('First Name') }}" />
					</div>
				</div>

				<div class="col-lg-6 col-12">
					<div class="form-group mb-3">
						<label class="form-label font-weight-bold">{{ __('Last Name') }} <span style="color: #ef4444; font-weight: 700;">*</span></label>
						<input type="text" name="lname" class="form-control" maxlength="191" value="{{ $lname ?? '' }}" required placeholder="{{ __('Last Name') }}" />
					</div>
				</div>

				{{-- Row 2: Email & Mobile --}}
				<div class="col-lg-6 col-12">
					<div class="form-group mb-3">
						<label class="form-label font-weight-bold">{{ __('Email') }}</label>
						<input type="email" name="email" class="form-control" value="{{ Auth::user()->email }}" readonly />
					</div>
				</div>

				<div class="col-lg-6 col-12">
					<div class="form-group mb-3">
						<label class="form-label font-weight-bold">{{ __('Mobile') }} <span style="color: #ef4444; font-weight: 700;">*</span></label>
						<input type="text" name="phone" class="form-control" maxlength="20" value="{{ Auth::user()->phone }}" required placeholder="{{ __('Mobile') }}" />
					</div>
				</div>

				{{-- Row 3: Age & Address --}}
				<div class="col-lg-6 col-12">
					<div class="form-group mb-3">
						<label class="form-label font-weight-bold">{{ __('Age') }} <span style="color: #ef4444; font-weight: 700;">*</span></label>
						<select name="age" class="form-control form-select" required>
							<option value="" disabled>{{ __('Choose Age') }}</option>
							<option value="child" {{ (($meta->age ?? '') == 'child' || ($meta->age ?? '') == 'Above 13 - Under 19') ? 'selected' : '' }}>{{ __('Above 13 - Under 19') }}</option>
							<option value="adult" {{ (($meta->age ?? '') == 'adult' || ($meta->age ?? '') == 'Above 19' || empty($meta->age)) ? 'selected' : '' }}>{{ __('Above 19') }}</option>
						</select>
					</div>
				</div>

				<div class="col-lg-6 col-12">
					<div class="form-group mb-3">
						<label class="form-label font-weight-bold">{{ __('Address') }}</label>
						<input type="text" name="address" class="form-control" value="{{ $meta->address ?? '' }}" id="location_input" placeholder="{{ __('Address') }}" />
					</div>
				</div>

				{{-- Row 4: City & State --}}
				<div class="col-lg-6 col-12">
					<div class="form-group mb-3">
						<label class="form-label font-weight-bold">{{ __('City') }}</label>
						<input type="text" name="city" class="form-control" value="{{ $meta->city ?? '' }}" placeholder="{{ __('City') }}" />
					</div>
				</div>

				<div class="col-lg-6 col-12">
					<div class="form-group mb-3">
						<label class="form-label font-weight-bold">{{ __('State') }}</label>
						<select name="state" class="form-control form-select">
							<option value="">{{ __('Select State') }}</option>
							@foreach(($usStates ?? []) as $code => $stateName)
								<option value="{{ $code }}" {{ (strtoupper($meta->state ?? '') === $code || strcasecmp($meta->state ?? '', $stateName) === 0) ? 'selected' : '' }}>
									{{ $stateName }}
								</option>
							@endforeach
						</select>
					</div>
				</div>

				{{-- Row 5: Country & Image Upload --}}
				<div class="col-lg-6 col-12">
					<div class="form-group mb-3">
						<label class="form-label font-weight-bold">{{ __('Country') }}</label>
						<input type="text" class="form-control" value="United States of America" readonly />
						<input type="hidden" name="country" value="USA" />
					</div>
				</div>

				<div class="col-lg-6 col-12">
					<div class="form-group mb-3">
						<label class="form-label font-weight-bold d-block">{{ __('Image') }}</label>
						<div class="d-flex align-items-center">
							@if(!empty($meta->avatar ?? ''))
							<img src="{{ asset($meta->avatar) }}" id="avatar_preview" class="rounded-circle me-3 border" style="width: 44px; height: 44px; object-fit: cover;" alt="Avatar" />
							@else
							<img src="" id="avatar_preview" class="rounded-circle me-3 border d-none" style="width: 44px; height: 44px; object-fit: cover;" alt="Avatar" />
							@endif
							<input type="file" name="image" id="profile_image_input" class="d-none" accept="image/*">
							<button type="button" class="btn profile-upload-btn" onclick="document.getElementById('profile_image_input').click();">
								{{ __('Upload') }}
							</button>
							<span id="selected_image_name" class="ms-3 text-muted small"></span>
						</div>
					</div>
				</div>

				{{-- Row 6: Submit Button --}}
				<div class="col-12 mt-3">
					<div class="form-group button mb-0">
						<button type="submit" id="saveProfileBtn" class="btn btn-primary px-4 py-2">
							<span id="saveProfileBtnText">{{ __('Save changes') }}</span>
						</button>
					</div>
				</div>
			</div>
		</form>
	</div>
</div>

<style>
	/* 1. Eliminate nice-select wrapper that shrinks dropdowns */
	.profile-settings-block .nice-select {
		display: none !important;
	}

	/* 2. Uniform, High-Contrast Inputs & Dropdowns */
	.profile-settings-block .form-control,
	.profile-settings-block .form-select,
	.profile-settings-block select,
	.profile-settings-block input[type="text"],
	.profile-settings-block input[type="email"] {
		display: block !important;
		width: 100% !important;
		height: 48px !important;
		min-height: 48px !important;
		border: 1.5px solid #cbd5e1 !important; /* Clearly defined high-contrast border */
		border-radius: 8px !important;
		padding: 0 16px !important;
		font-size: 14.5px !important;
		color: #1e293b !important;
		background-color: #ffffff !important;
		box-sizing: border-box !important;
		outline: none !important;
		box-shadow: none !important;
		transition: border-color 0.2s ease, box-shadow 0.2s ease !important;
	}

	/* 3. Native dropdown arrow and behavior */
	.profile-settings-block select,
	.profile-settings-block select.form-control,
	.profile-settings-block select.form-select {
		-webkit-appearance: menulist !important;
		-moz-appearance: menulist !important;
		appearance: menulist !important;
		cursor: pointer !important;
		background-color: #ffffff !important;
	}

	/* 4. Hover & Focus States */
	.profile-settings-block .form-control:hover:not(:disabled):not([readonly]),
	.profile-settings-block select:hover {
		border-color: #94a3b8 !important;
	}

	.profile-settings-block .form-control:focus,
	.profile-settings-block select:focus {
		border-color: #528CD7 !important;
		box-shadow: 0 0 0 3px rgba(82, 140, 215, 0.2) !important;
		background-color: #ffffff !important;
	}

	/* 5. Readonly fields (Email, Country) */
	.profile-settings-block .form-control[readonly],
	.profile-settings-block .form-control:disabled {
		background-color: #f1f5f9 !important;
		border-color: #cbd5e1 !important;
		color: #64748b !important;
		cursor: not-allowed !important;
	}

	/* 6. Form Labels */
	.profile-settings-block .form-label {
		font-size: 13.5px !important;
		font-weight: 600 !important;
		color: #334155 !important;
		margin-bottom: 7px !important;
		display: block !important;
	}

	/* 7. Buttons */
	.profile-upload-btn {
		background-color: #0ea5e9 !important;
		border-color: #0ea5e9 !important;
		color: #ffffff !important;
		height: 42px !important;
		padding: 0 20px !important;
		border-radius: 6px !important;
		font-size: 14px !important;
		font-weight: 600 !important;
		display: inline-flex !important;
		align-items: center !important;
		justify-content: center !important;
		transition: background-color 0.2s ease !important;
	}
	.profile-upload-btn:hover {
		background-color: #0284c7 !important;
		color: #ffffff !important;
	}

	#saveProfileBtn {
		background: #528CD7 !important;
		background-color: #528CD7 !important;
		border-color: #528CD7 !important;
		color: #ffffff !important;
		height: 48px !important;
		padding: 0 32px !important;
		border-radius: 8px !important;
		font-weight: 600 !important;
		font-size: 15px !important;
		letter-spacing: 0.2px !important;
		transition: background-color 0.2s ease, box-shadow 0.2s ease !important;
	}
	#saveProfileBtn:hover:not(:disabled) {
		background: #467fcb !important;
		background-color: #467fcb !important;
		border-color: #467fcb !important;
		box-shadow: 0 4px 12px rgba(82, 140, 215, 0.25) !important;
	}
	#saveProfileBtn:disabled {
		opacity: 0.6 !important;
		cursor: not-allowed !important;
	}
</style>
@endsection

@push('js')
<script>
	document.addEventListener('DOMContentLoaded', function () {
		// Destroy niceSelect if initialized so selects stay 100% full-width and native
		function removeNiceSelect() {
			if (window.jQuery && typeof jQuery.fn.niceSelect === 'function') {
				try {
					jQuery('.profile-settings-block select').niceSelect('destroy');
				} catch (e) {}
			}
		}
		removeNiceSelect();
		setTimeout(removeNiceSelect, 100);
		setTimeout(removeNiceSelect, 500);

		// Show selected image file name and live preview
		const fileInput = document.getElementById('profile_image_input');
		const fileNameDisplay = document.getElementById('selected_image_name');
		const avatarPreview = document.getElementById('avatar_preview');
		if (fileInput) {
			fileInput.addEventListener('change', function () {
				if (this.files && this.files.length > 0) {
					if (fileNameDisplay) fileNameDisplay.textContent = this.files[0].name;
					if (avatarPreview) {
						const reader = new FileReader();
						reader.onload = function(e) {
							avatarPreview.src = e.target.result;
							avatarPreview.classList.remove('d-none');
						};
						reader.readAsDataURL(this.files[0]);
					}
				} else {
					if (fileNameDisplay) fileNameDisplay.textContent = '';
				}
			});
		}

		// AJAX Form submission
		const form = document.getElementById('profileEditForm');
		const submitBtn = document.getElementById('saveProfileBtn');
		const btnText = document.getElementById('saveProfileBtnText');
		const alertArea = document.getElementById('profileAlertArea');

		if (form) {
			form.addEventListener('submit', function (e) {
				e.preventDefault();

				// Reset previous alerts
				alertArea.innerHTML = '';
				submitBtn.disabled = true;
				const originalText = btnText.textContent;
				btnText.textContent = '{{ __("Saving...") }}';

				const formData = new FormData(form);

				fetch(form.action, {
					method: 'POST',
					headers: {
						'X-Requested-With': 'XMLHttpRequest',
						'Accept': 'application/json'
					},
					body: formData
				})
				.then(async (response) => {
					const data = await response.json().catch(() => null);
					if (response.ok) {
						const msg = (typeof data === 'string' ? data : (data && data.message ? data.message : '{{ __("Profile updated successfully!") }}'));
						alertArea.innerHTML = '<div class="alert alert-success alert-dismissible fade show" role="alert">' +
							'<i class="icofont-check-circled me-2"></i>' + msg +
							'<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
						'</div>';

						if (typeof Sweet === 'function') {
							Sweet('success', msg);
						}

						// Scroll alert into view smoothly
						alertArea.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
					} else {
						let errorHtml = '<div class="alert alert-danger alert-dismissible fade show" role="alert"><ul class="mb-0 ps-3">';
						if (data && data.errors) {
							Object.values(data.errors).forEach(errArr => {
								(Array.isArray(errArr) ? errArr : [errArr]).forEach(err => {
									errorHtml += '<li>' + err + '</li>';
								});
							});
						} else if (data && data.message) {
							errorHtml += '<li>' + data.message + '</li>';
						} else {
							errorHtml += '<li>{{ __("An error occurred while updating your profile. Please try again.") }}</li>';
						}
						errorHtml += '</ul><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
						alertArea.innerHTML = errorHtml;

						if (typeof Sweet === 'function') {
							Sweet('error', '{{ __("Please check the form for errors.") }}');
						}

						alertArea.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
					}
				})
				.catch((err) => {
					alertArea.innerHTML = '<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
						'{{ __("Network or server connection error. Please try again.") }}' +
						'<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
					'</div>';
				})
				.finally(() => {
					submitBtn.disabled = false;
					btnText.textContent = originalText;
				});
			});
		}
	});
</script>
@endpush
