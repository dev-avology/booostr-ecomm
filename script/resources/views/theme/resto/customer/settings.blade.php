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
						<input type="email" name="email" class="form-control" value="{{ Auth::user()->email }}" readonly style="background-color: #f1f3f5; cursor: not-allowed;" />
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
						<input type="text" class="form-control" value="United States of America" readonly style="background-color: #f1f3f5; cursor: not-allowed;" />
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
							<button type="button" class="btn btn-info text-white px-4 py-2" style="background-color: #0dcaf0; border-color: #0dcaf0; border-radius: 4px; font-size: 14px; font-weight: 500;" onclick="document.getElementById('profile_image_input').click();">
								{{ __('Upload') }}
							</button>
							<span id="selected_image_name" class="ms-3 text-muted small"></span>
						</div>
					</div>
				</div>

				{{-- Row 6: Submit Button --}}
				<div class="col-12 mt-3">
					<div class="form-group button mb-0">
						<button type="submit" id="saveProfileBtn" class="btn btn-primary px-4 py-2" style="background-color: #0d6efd; border-color: #0d6efd; border-radius: 4px; font-weight: 500;">
							<span id="saveProfileBtnText">{{ __('Save changes') }}</span>
						</button>
					</div>
				</div>
			</div>
		</form>
	</div>
</div>
@endsection

@push('js')
<script>
	document.addEventListener('DOMContentLoaded', function () {
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
