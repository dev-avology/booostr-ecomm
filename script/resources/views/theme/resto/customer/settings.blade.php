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
		<form class="profile-setting-form ajaxform" method="post" action="{{ route('customer.profile.update') }}" enctype="multipart/form-data">
			@csrf

			<div class="row g-3">
				{{-- Row 1: Name & Email --}}
				<div class="col-lg-6 col-12">
					<div class="form-group mb-3">
						<label class="form-label font-weight-bold">{{ __('Name') }}</label>
						<input type="text" name="name" class="form-control" maxlength="100" value="{{ Auth::user()->name }}" required />
					</div>
				</div>

				<div class="col-lg-6 col-12">
					<div class="form-group mb-3">
						<label class="form-label font-weight-bold">{{ __('Email') }}</label>
						<input type="email" name="email" class="form-control" value="{{ Auth::user()->email }}" style="background-color: #f1f3f5;" required />
					</div>
				</div>

				{{-- Row 2: Mobile & Age --}}
				<div class="col-lg-6 col-12">
					<div class="form-group mb-3">
						<label class="form-label font-weight-bold">{{ __('Mobile') }}</label>
						<input type="text" name="phone" class="form-control" maxlength="20" value="{{ Auth::user()->phone }}" required />
					</div>
				</div>

				<div class="col-lg-6 col-12">
					<div class="form-group mb-3">
						<label class="form-label font-weight-bold">{{ __('Age') }}</label>
						<input type="text" name="age" class="form-control" value="{{ $meta->age ?? 'Above 19' }}" readonly style="background-color: #f1f3f5; cursor: not-allowed;" />
					</div>
				</div>

				{{-- Row 3: Address & City --}}
				<div class="col-lg-6 col-12">
					<div class="form-group mb-3">
						<label class="form-label font-weight-bold">{{ __('Address') }}</label>
						<input type="text" name="address" class="form-control" value="{{ $meta->address ?? '' }}" id="location_input" />
					</div>
				</div>

				<div class="col-lg-6 col-12">
					<div class="form-group mb-3">
						<label class="form-label font-weight-bold">{{ __('City') }}</label>
						<input type="text" name="city" class="form-control" value="{{ $meta->city ?? '' }}" />
					</div>
				</div>

				{{-- Row 4: State & Country --}}
				<div class="col-lg-6 col-12">
					<div class="form-group mb-3">
						<label class="form-label font-weight-bold">{{ __('State') }}</label>
						<input type="text" name="state" class="form-control" value="{{ $meta->state ?? '' }}" />
					</div>
				</div>

				<div class="col-lg-6 col-12">
					<div class="form-group mb-3">
						<label class="form-label font-weight-bold">{{ __('Country') }}</label>
						<input type="text" name="country" class="form-control" value="{{ $meta->country ?? 'Belarus' }}" readonly style="background-color: #f1f3f5; cursor: not-allowed;" />
					</div>
				</div>

				{{-- Row 5: Profile Image Upload --}}
				<div class="col-12">
					<div class="form-group mb-3">
						<label class="form-label font-weight-bold d-block">{{ __('Image') }}</label>
						<div class="d-flex align-items-center">
							<input type="file" name="image" id="profile_image_input" class="d-none" accept="image/*">
							<button type="button" class="btn btn-info text-white px-4 py-2" style="background-color: #0dcaf0; border-color: #0dcaf0; border-radius: 4px; font-size: 14px; font-weight: 500;" onclick="document.getElementById('profile_image_input').click();">
								{{ __('Upload') }}
							</button>
							<span id="selected_image_name" class="ms-3 text-muted small"></span>
						</div>
					</div>
				</div>

				{{-- Hidden Coordinates & Postal code for existing store delivery calculation --}}
				<input type="hidden" name="post_code" value="{{ $meta->post_code ?? '' }}" />
				<input type="hidden" name="lat" id="latitude" value="{{ $meta->lat ?? '' }}" />
				<input type="hidden" name="long" id="longitude" value="{{ $meta->long ?? '' }}" />

				@if(!empty($order_settings->google_api ?? ''))
				<div class="col-lg-12 col-12 @if(empty($meta->long ?? '')) none @endif" id="map-area">
					<label>{{ __('Drag Your Location') }}</label>
					<div id="map-canvas" class="map-canvas h-100 w-100p"></div>
				</div>
				@endif

				{{-- Row 6: Submit Button --}}
				<div class="col-12 mt-3">
					<div class="form-group button mb-0">
						<button type="submit" class="btn btn-primary basicbtn px-4 py-2" style="background-color: #0d6efd; border-color: #0d6efd; border-radius: 4px; font-weight: 500;">
							{{ __('Save changes') }}
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
	// Show selected image file name next to upload button
	document.addEventListener('DOMContentLoaded', function () {
		const fileInput = document.getElementById('profile_image_input');
		const fileNameDisplay = document.getElementById('selected_image_name');
		if (fileInput && fileNameDisplay) {
			fileInput.addEventListener('change', function () {
				if (this.files && this.files.length > 0) {
					fileNameDisplay.textContent = this.files[0].name;
				} else {
					fileNameDisplay.textContent = '';
				}
			});
		}
	});
</script>

@if(!empty($order_settings->google_api ?? ''))
<script async defer src="https://maps.googleapis.com/maps/api/js?key={{ $order_settings->google_api ?? '' }}&libraries=places&callback=initialize"></script>
@endif
@endpush
