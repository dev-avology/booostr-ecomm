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
						<label class="form-label font-weight-bold">{{ __('Mobile') }}</label>
						<input type="text" name="phone" class="form-control" maxlength="20" value="{{ Auth::user()->phone }}" placeholder="{{ __('Mobile') }}" />
					</div>
				</div>

				{{-- Row 3: Age & Address --}}
				<div class="col-lg-6 col-12">
					<div class="form-group mb-3">
						<label class="form-label font-weight-bold">{{ __('Age') }} <span style="color: #ef4444; font-weight: 700;">*</span></label>
						<select name="age" class="form-control" required>
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
						<select name="state" class="form-control">
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
						<div class="d-flex align-items-center flex-wrap" style="gap: 10px;">
							<img src="{{ !empty($meta->avatar ?? '') ? asset($meta->avatar) : '' }}" 
							     id="avatar_preview" 
							     class="rounded-circle border {{ empty($meta->avatar ?? '') ? 'd-none' : '' }}" 
							     style="width: 44px; height: 44px; object-fit: cover;" 
							     alt="Avatar" />

							<input type="file" name="image" id="profile_image_input" class="d-none" accept="image/*">
							<input type="hidden" name="remove_image" id="remove_image_input" value="0">

							<button type="button" class="btn profile-upload-btn" onclick="document.getElementById('profile_image_input').click();">
								{{ __('Upload') }}
							</button>

							<button type="button" 
							        id="remove_avatar_btn" 
							        class="btn btn-outline-danger btn-sm {{ empty($meta->avatar ?? '') ? 'd-none' : '' }}" 
							        style="padding: 7px 14px; font-size: 13px; border-radius: 6px; font-weight: 500;">
								<i class="icofont-trash me-1"></i>{{ __('Remove') }}
							</button>

							<span id="selected_image_name" class="text-muted small"></span>
						</div>
					</div>
				</div>

				{{-- Row 6: Submit Button --}}
				<div class="col-12 mt-3">
					<div class="form-group profile-button-group mb-0">
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

	/* 3. Single clean dropdown chevron arrow (Eliminates duplicate arrows) */
	.profile-settings-block select,
	.profile-settings-block select.form-control,
	.profile-settings-block select.form-select {
		-webkit-appearance: none !important;
		-moz-appearance: none !important;
		appearance: none !important;
		background-color: #ffffff !important;
		background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%2364748b' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e") !important;
		background-repeat: no-repeat !important;
		background-position: right 16px center !important;
		background-size: 14px 10px !important;
		padding-right: 42px !important;
		cursor: pointer !important;
	}

	/* 4. Hover & Focus States */
	.profile-settings-block .form-control:hover:not(:disabled):not([readonly]),
	.profile-settings-block select:hover {
		border-color: #94a3b8 !important;
	}

	.profile-settings-block .form-control:focus,
	.profile-settings-block select:focus {
		border-color: var(--primary, #0A8CDE) !important;
		box-shadow: 0 0 0 3px rgba(10, 140, 222, 0.2) !important;
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

	/* 7. Buttons - Consistent Brand Blue, Green Hover Completely Neutralized */
	#saveProfileBtn::before,
	#saveProfileBtn::after,
	.profile-upload-btn::before,
	.profile-upload-btn::after,
	.profile-settings-block .btn::before,
	.profile-settings-block .btn::after,
	.profile-button-group .btn::before,
	.profile-button-group .btn::after,
	.button .btn#saveProfileBtn::before,
	.button .btn#saveProfileBtn::after,
	.button .btn#saveProfileBtn:hover::before,
	.button .btn.profile-upload-btn::before,
	.button .btn.profile-upload-btn:hover::before {
		display: none !important;
		content: none !important;
		width: 0 !important;
		height: 0 !important;
		background: transparent !important;
		background-color: transparent !important;
		opacity: 0 !important;
		visibility: hidden !important;
	}

	.profile-upload-btn {
		background: var(--primary, #0A8CDE) !important;
		background-color: var(--primary, #0A8CDE) !important;
		border: 1.5px solid var(--primary, #0A8CDE) !important;
		color: #ffffff !important;
		height: 42px !important;
		padding: 0 22px !important;
		border-radius: 6px !important;
		font-size: 14px !important;
		font-weight: 600 !important;
		display: inline-flex !important;
		align-items: center !important;
		justify-content: center !important;
		box-shadow: none !important;
		transition: background-color 0.2s ease, border-color 0.2s ease, box-shadow 0.2s ease !important;
		cursor: pointer !important;
	}
	.profile-upload-btn:hover,
	.profile-upload-btn:focus {
		background: var(--booostr-blue-hover, #0870b2) !important;
		background-color: var(--booostr-blue-hover, #0870b2) !important;
		border-color: var(--booostr-blue-hover, #0870b2) !important;
		color: #ffffff !important;
		box-shadow: 0 2px 8px rgba(10, 140, 222, 0.25) !important;
	}

	#saveProfileBtn {
		background: var(--primary, #0A8CDE) !important;
		background-color: var(--primary, #0A8CDE) !important;
		border: 1.5px solid var(--primary, #0A8CDE) !important;
		color: #ffffff !important;
		height: 48px !important;
		padding: 0 32px !important;
		border-radius: 8px !important;
		font-weight: 600 !important;
		font-size: 15px !important;
		letter-spacing: 0.2px !important;
		display: inline-flex !important;
		align-items: center !important;
		justify-content: center !important;
		box-shadow: none !important;
		transition: background-color 0.2s ease, border-color 0.2s ease, box-shadow 0.2s ease !important;
		cursor: pointer !important;
	}
	#saveProfileBtn:hover:not(:disabled):not(.is-processing) {
		background: var(--booostr-blue-hover, #0870b2) !important;
		background-color: var(--booostr-blue-hover, #0870b2) !important;
		border-color: var(--booostr-blue-hover, #0870b2) !important;
		color: #ffffff !important;
		box-shadow: 0 4px 12px rgba(10, 140, 222, 0.25) !important;
	}
	#saveProfileBtn:focus,
	#saveProfileBtn:active {
		background: var(--booostr-blue-hover, #0870b2) !important;
		background-color: var(--booostr-blue-hover, #0870b2) !important;
		border-color: var(--booostr-blue-hover, #0870b2) !important;
		color: #ffffff !important;
		outline: none !important;
		box-shadow: 0 0 0 3px rgba(10, 140, 222, 0.25) !important;
	}
	#saveProfileBtn:disabled {
		opacity: 0.55 !important;
		cursor: not-allowed !important;
		background: #94a3b8 !important;
		background-color: #94a3b8 !important;
		border-color: #94a3b8 !important;
		box-shadow: none !important;
	}
	#saveProfileBtn.is-processing {
		opacity: 0.8 !important;
		cursor: wait !important;
		pointer-events: none !important;
		background: var(--primary, #0A8CDE) !important;
		background-color: var(--primary, #0A8CDE) !important;
		border-color: var(--primary, #0A8CDE) !important;
		box-shadow: none !important;
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

		// Show selected image file name, live preview, and handle remove option
		const fileInput = document.getElementById('profile_image_input');
		const fileNameDisplay = document.getElementById('selected_image_name');
		const avatarPreview = document.getElementById('avatar_preview');
		const removeBtn = document.getElementById('remove_avatar_btn');
		const removeInput = document.getElementById('remove_image_input');

		if (fileInput) {
			fileInput.addEventListener('change', function () {
				if (this.files && this.files.length > 0) {
					const file = this.files[0];
					// Client-side 2MB file size safeguard to prevent server timeouts/drops
					if (file.size > 2 * 1024 * 1024) {
						if (typeof Sweet === 'function') {
							Sweet('error', '{{ __("Image size must be less than 2MB. Please choose a smaller image.") }}');
						} else {
							alert('{{ __("Image size must be less than 2MB. Please choose a smaller image.") }}');
						}
						this.value = '';
						if (fileNameDisplay) fileNameDisplay.textContent = '';
						return;
					}

					if (fileNameDisplay) fileNameDisplay.textContent = file.name;
					if (removeInput) removeInput.value = '0';
					if (removeBtn) removeBtn.classList.remove('d-none');
					if (avatarPreview) {
						const reader = new FileReader();
						reader.onload = function(e) {
							avatarPreview.src = e.target.result;
							avatarPreview.classList.remove('d-none');
						};
						reader.readAsDataURL(file);
					}
				}
			});
		}

		if (removeBtn) {
			removeBtn.addEventListener('click', function () {
				if (fileInput) fileInput.value = '';
				if (fileNameDisplay) fileNameDisplay.textContent = '';
				if (avatarPreview) {
					avatarPreview.src = '';
					avatarPreview.classList.add('d-none');
				}
				if (removeInput) removeInput.value = '1';
				removeBtn.classList.add('d-none');
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
						if (removeInput) removeInput.value = '0';
						if (fileNameDisplay) fileNameDisplay.textContent = '';
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
							const toastErr = (data && data.message) ? data.message : '{{ __("Please check the form for errors.") }}';
							Sweet('error', toastErr);
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
