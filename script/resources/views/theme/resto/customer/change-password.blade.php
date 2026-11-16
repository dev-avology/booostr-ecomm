@extends('theme.resto.customer.app')

@section('customer_content')
{{-- 
    =================================================================================
    Site Builder Standard: Customer Change Password View
    Author / Context: Foodsify & Booostr Central Auth Integration
    Features:
      - Validates Current Password with Hash::check in backend.
      - Updates local customer hashed password.
      - Synchronizes password change with Booostr Central API.
      - Responsive layout matching theme/resto/customer aesthetic.
      - Eye toggles for easy password visibility switching.
    =================================================================================
--}}
<div class="dashboard-block password-change-block mt-0">
    <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2">
        <h3 class="block-title mb-0">
            <i class="icofont-key me-2 text-primary"></i> {{ __('Change Password') }}
        </h3>
        <a href="{{ url('/customer/dashboard') }}" class="btn btn-sm btn-outline-secondary">
            <i class="icofont-arrow-left me-1"></i> {{ __('Back to Dashboard') }}
        </a>
    </div>

    <div class="inner-block">
        <div class="alert alert-info py-2 px-3 mb-4" role="alert" style="font-size: 0.9rem;">
            <i class="icofont-info-circle me-1"></i> 
            {{ __('Ensure your new password has at least 8 characters. Changing your password here will also keep your Booostr account synced.') }}
        </div>

        <form class="default-form-style ajaxform_with_reset" method="post" action="{{ route('customer.password.update') }}">
            @csrf

            {{-- 1. Current Password --}}
            <div class="row">
                <div class="col-12 col-md-8">
                    <div class="form-group mb-3">
                        <label for="current_password" class="form-label font-weight-bold">
                            {{ __('Current Password') }} <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <input 
                                type="password" 
                                name="current" 
                                id="current_password" 
                                class="form-control" 
                                placeholder="{{ __('Enter current password') }}" 
                                required 
                                autocomplete="current-password"
                            >
                            <button class="btn btn-outline-secondary toggle-password-visibility" type="button" data-target="current_password" style="border: 1px solid #ced4da;">
                                <i class="icofont-eye"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 2. New Password --}}
            <div class="row">
                <div class="col-12 col-md-8">
                    <div class="form-group mb-3">
                        <label for="new_password" class="form-label font-weight-bold">
                            {{ __('New Password') }} <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <input 
                                type="password" 
                                name="password" 
                                id="new_password" 
                                class="form-control" 
                                placeholder="{{ __('Enter new password (min. 8 characters)') }}" 
                                minlength="8" 
                                required 
                                autocomplete="new-password"
                            >
                            <button class="btn btn-outline-secondary toggle-password-visibility" type="button" data-target="new_password" style="border: 1px solid #ced4da;">
                                <i class="icofont-eye"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 3. Confirm New Password --}}
            <div class="row">
                <div class="col-12 col-md-8">
                    <div class="form-group mb-4">
                        <label for="password_confirmation" class="form-label font-weight-bold">
                            {{ __('Confirm New Password') }} <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <input 
                                type="password" 
                                name="password_confirmation" 
                                id="password_confirmation" 
                                class="form-control" 
                                placeholder="{{ __('Re-enter new password') }}" 
                                minlength="8" 
                                required 
                                autocomplete="new-password"
                            >
                            <button class="btn btn-outline-secondary toggle-password-visibility" type="button" data-target="password_confirmation" style="border: 1px solid #ced4da;">
                                <i class="icofont-eye"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="row">
                <div class="col-12">
                    <div class="form-group button mb-0">
                        <button type="submit" class="btn basicbtn">
                            <i class="icofont-check-circled me-1"></i> {{ __('Update Password') }}
                        </button>
                        <a href="{{ url('/customer/dashboard') }}" class="btn btn-link text-muted ms-2">
                            {{ __('Cancel') }}
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('js')
<script>
    // Toggle password visibility for user convenience
    document.addEventListener('DOMContentLoaded', function () {
        const toggleButtons = document.querySelectorAll('.toggle-password-visibility');
        toggleButtons.forEach(button => {
            button.addEventListener('click', function () {
                const targetId = this.getAttribute('data-target');
                const targetInput = document.getElementById(targetId);
                const icon = this.querySelector('i');
                if (targetInput) {
                    if (targetInput.type === 'password') {
                        targetInput.type = 'text';
                        icon.classList.remove('icofont-eye');
                        icon.classList.add('icofont-eye-blocked');
                    } else {
                        targetInput.type = 'password';
                        icon.classList.remove('icofont-eye-blocked');
                        icon.classList.add('icofont-eye');
                    }
                }
            });
        });
    });
</script>
@endpush

