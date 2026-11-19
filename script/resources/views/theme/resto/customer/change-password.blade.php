@extends('theme.resto.customer.app')

@section('customer_content')
{{-- 
    =================================================================================
    Site Builder Standard: Customer Change Password View
    Author / Context: Foodsify & Booostr Central Auth Integration
    Features:
      - Clean responsive card layout matching Edit Profile design
      - Validates Current Password against existing database hash
      - Enforces New Password != Current Password and >= 8 characters
      - Integrated eye toggles positioned inside password fields
      - Synchronizes password change with Booostr Central API
      - Pure AJAX submission with instant field validation, error highlights & toast
    =================================================================================
--}}
<div class="dashboard-block mt-0 password-change-block bg-white p-4 rounded shadow-sm">
    <div class="d-flex align-items-center justify-content-between mb-4 border-bottom pb-2">
        <h3 class="block-title fs-5 mb-0">
            <i class="icofont-key me-2 text-primary"></i>{{ __('Change Password') }}
        </h3>
        <a href="{{ url('/customer/dashboard') }}" class="btn btn-sm btn-outline-secondary back-dashboard-link">
            <i class="icofont-arrow-left me-1"></i>{{ __('Back to Dashboard') }}
        </a>
    </div>

    <div class="inner-block">
        {{-- Information Banner --}}
        <div class="alert alert-info py-2 px-3 mb-4 d-flex align-items-center" role="alert" style="font-size: 0.9rem; border-radius: 8px; border-left: 4px solid var(--primary, #0A8CDE); background-color: #eff6ff; color: #1e40af;">
            <i class="icofont-info-circle me-2 fs-5"></i> 
            <div>{{ __('Ensure your new password has at least 8 characters and is different from your current password. Changing your password here will also keep your Booostr account synced.') }}</div>
        </div>

        {{-- Dynamic Alert Area --}}
        <div id="passwordAlertArea"></div>

        <form class="password-change-form" id="changePasswordForm" method="post" action="{{ route('customer.password.update') }}">
            @csrf

            <div class="row">
                <div class="col-lg-7 col-md-9 col-12">
                    {{-- 1. Current Password --}}
                    <div class="form-group mb-3">
                        <label for="current_password" class="form-label font-weight-bold">
                            {{ __('Current Password') }} <span style="color: #ef4444; font-weight: 700;">*</span>
                        </label>
                        <div class="password-wrapper position-relative">
                            <input 
                                type="password" 
                                name="current" 
                                id="current_password" 
                                class="form-control" 
                                placeholder="{{ __('Enter current password') }}" 
                                required 
                                autocomplete="current-password"
                            >
                            <span class="password-toggle-icon" onclick="togglePasswordVisibility('current_password', this)" title="{{ __('Show / Hide Password') }}">
                                <i class="icofont-eye"></i>
                            </span>
                        </div>
                        <div class="field-error-msg text-danger small mt-1 d-none" id="error_current"></div>
                    </div>

                    {{-- 2. New Password --}}
                    <div class="form-group mb-3">
                        <label for="new_password" class="form-label font-weight-bold">
                            {{ __('New Password') }} <span style="color: #ef4444; font-weight: 700;">*</span>
                        </label>
                        <div class="password-wrapper position-relative">
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
                            <span class="password-toggle-icon" onclick="togglePasswordVisibility('new_password', this)" title="{{ __('Show / Hide Password') }}">
                                <i class="icofont-eye"></i>
                            </span>
                        </div>
                        <div class="field-error-msg text-danger small mt-1 d-none" id="error_password"></div>
                    </div>

                    {{-- 3. Confirm New Password --}}
                    <div class="form-group mb-4">
                        <label for="password_confirmation" class="form-label font-weight-bold">
                            {{ __('Confirm New Password') }} <span style="color: #ef4444; font-weight: 700;">*</span>
                        </label>
                        <div class="password-wrapper position-relative">
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
                            <span class="password-toggle-icon" onclick="togglePasswordVisibility('password_confirmation', this)" title="{{ __('Show / Hide Password') }}">
                                <i class="icofont-eye"></i>
                            </span>
                        </div>
                        <div class="field-error-msg text-danger small mt-1 d-none" id="error_password_confirmation"></div>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="form-group profile-button-group mb-0 d-flex align-items-center">
                        <button type="submit" id="updatePasswordBtn" class="btn update-password-btn px-4 py-2 me-3">
                            <i class="icofont-check-circled me-1" id="updatePasswordBtnIcon"></i>
                            <span id="updatePasswordBtnText">{{ __('Update Password') }}</span>
                        </button>
                        <a href="{{ url('/customer/dashboard') }}" class="btn btn-outline-secondary cancel-btn">
                            {{ __('Cancel') }}
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<style>
    /* 1. Uniform, High-Contrast Password Inputs */
    .password-change-block .form-control {
        display: block !important;
        width: 100% !important;
        height: 48px !important;
        min-height: 48px !important;
        border: 1.5px solid #cbd5e1 !important;
        border-radius: 8px !important;
        padding: 0 46px 0 16px !important; /* Right padding preserves room for eye toggle */
        font-size: 14.5px !important;
        color: #1e293b !important;
        background-color: #ffffff !important;
        box-sizing: border-box !important;
        outline: none !important;
        box-shadow: none !important;
        transition: border-color 0.2s ease, box-shadow 0.2s ease !important;
    }

    .password-change-block .form-control:hover:not(:disabled) {
        border-color: #94a3b8 !important;
    }

    .password-change-block .form-control:focus {
        border-color: var(--primary, #0A8CDE) !important;
        box-shadow: 0 0 0 3px rgba(10, 140, 222, 0.2) !important;
        background-color: #ffffff !important;
    }

    .password-change-block .form-control.is-invalid {
        border-color: #ef4444 !important;
        box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.15) !important;
    }

    /* 2. Embedded Eye Toggle Icon */
    .password-wrapper {
        position: relative !important;
        width: 100% !important;
    }

    .password-toggle-icon {
        position: absolute !important;
        right: 14px !important;
        top: 50% !important;
        transform: translateY(-50%) !important;
        cursor: pointer !important;
        color: #94a3b8 !important;
        font-size: 18px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        z-index: 5 !important;
        padding: 4px !important;
        transition: color 0.2s ease !important;
        user-select: none !important;
    }

    .password-toggle-icon:hover {
        color: #334155 !important;
    }

    /* 3. Form Labels */
    .password-change-block .form-label {
        font-size: 13.5px !important;
        font-weight: 600 !important;
        color: #334155 !important;
        margin-bottom: 7px !important;
        display: block !important;
    }

    /* 4. Action Buttons (Consistent Brand Blue, Never Green) */
    .update-password-btn::before,
    .update-password-btn::after,
    .password-change-block .btn::before,
    .password-change-block .btn::after,
    .profile-button-group .btn::before,
    .profile-button-group .btn::after,
    .button .btn.update-password-btn::before,
    .button .btn.update-password-btn:hover::before {
        display: none !important;
        content: none !important;
        width: 0 !important;
        height: 0 !important;
        background: transparent !important;
        background-color: transparent !important;
        opacity: 0 !important;
        visibility: hidden !important;
    }

    .update-password-btn {
        background: var(--primary, #0A8CDE) !important;
        background-color: var(--primary, #0A8CDE) !important;
        border: 1.5px solid var(--primary, #0A8CDE) !important;
        color: #ffffff !important;
        height: 48px !important;
        padding: 0 28px !important;
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

    .update-password-btn:hover:not(:disabled) {
        background: var(--booostr-blue-hover, #0870b2) !important;
        background-color: var(--booostr-blue-hover, #0870b2) !important;
        border-color: var(--booostr-blue-hover, #0870b2) !important;
        color: #ffffff !important;
        box-shadow: 0 4px 12px rgba(10, 140, 222, 0.25) !important;
    }

    .update-password-btn:focus,
    .update-password-btn:active {
        background: var(--booostr-blue-hover, #0870b2) !important;
        background-color: var(--booostr-blue-hover, #0870b2) !important;
        border-color: #467fcb !important;
        color: #ffffff !important;
        outline: none !important;
        box-shadow: 0 0 0 3px rgba(82, 140, 215, 0.25) !important;
    }

    .update-password-btn:disabled {
        opacity: 0.55 !important;
        cursor: not-allowed !important;
        background: #94b8e8 !important;
        background-color: #94b8e8 !important;
        border-color: #94b8e8 !important;
        box-shadow: none !important;
    }

    .cancel-btn {
        height: 48px !important;
        padding: 0 24px !important;
        border-radius: 8px !important;
        font-weight: 500 !important;
        font-size: 14.5px !important;
        border: 1.5px solid #cbd5e1 !important;
        color: #475569 !important;
        background-color: #f8fafc !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        transition: background-color 0.2s ease, border-color 0.2s ease, color 0.2s ease !important;
    }

    .cancel-btn:hover {
        background-color: #f1f5f9 !important;
        border-color: #94a3b8 !important;
        color: #1e293b !important;
    }

    .back-dashboard-link {
        border-radius: 6px !important;
        font-size: 13.5px !important;
        padding: 6px 14px !important;
    }
</style>
@endsection

@push('js')
<script>
    // 1. Password Visibility Toggle Function
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

    // 2. AJAX Form Submission & Field Validation
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('changePasswordForm');
        const submitBtn = document.getElementById('updatePasswordBtn');
        const btnText = document.getElementById('updatePasswordBtnText');
        const btnIcon = document.getElementById('updatePasswordBtnIcon');
        const alertArea = document.getElementById('passwordAlertArea');

        const currentInput = document.getElementById('current_password');
        const passwordInput = document.getElementById('new_password');
        const confirmInput = document.getElementById('password_confirmation');

        function clearErrors() {
            alertArea.innerHTML = '';
            document.querySelectorAll('.field-error-msg').forEach(el => {
                el.textContent = '';
                el.classList.add('d-none');
            });
            document.querySelectorAll('.password-change-block .form-control').forEach(el => {
                el.classList.remove('is-invalid');
            });
        }

        function showFieldError(inputElem, errorElemId, message) {
            if (inputElem) inputElem.classList.add('is-invalid');
            const errElem = document.getElementById(errorElemId);
            if (errElem) {
                errElem.textContent = message;
                errElem.classList.remove('d-none');
            }
        }

        // Live validation clearing on input
        [currentInput, passwordInput, confirmInput].forEach(input => {
            if (input) {
                input.addEventListener('input', function () {
                    this.classList.remove('is-invalid');
                });
            }
        });

        if (form) {
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                e.stopImmediatePropagation();
                clearErrors();

                const currentVal = currentInput ? currentInput.value.trim() : '';
                const passwordVal = passwordInput ? passwordInput.value : '';
                const confirmVal = confirmInput ? confirmInput.value : '';

                let hasClientError = false;

                // Client-side validations
                if (!currentVal) {
                    showFieldError(currentInput, 'error_current', '{{ __("Please enter your current password.") }}');
                    hasClientError = true;
                }

                if (!passwordVal || passwordVal.length < 8) {
                    showFieldError(passwordInput, 'error_password', '{{ __("New password must be at least 8 characters.") }}');
                    hasClientError = true;
                }

                if (passwordVal !== confirmVal) {
                    showFieldError(confirmInput, 'error_password_confirmation', '{{ __("New password and confirm password do not match.") }}');
                    hasClientError = true;
                }

                if (currentVal && passwordVal && currentVal === passwordVal) {
                    showFieldError(passwordInput, 'error_password', '{{ __("New password must be different from your current password.") }}');
                    hasClientError = true;
                }

                if (hasClientError) {
                    alertArea.innerHTML = `
                        <div class="alert alert-danger py-2 px-3 mb-3 d-flex align-items-center" role="alert" style="border-radius: 8px;">
                            <i class="icofont-warning-alt me-2 fs-5"></i>
                            <div>{{ __("Please correct the errors indicated below.") }}</div>
                        </div>
                    `;
                    return false;
                }

                // Activate processing state
                submitBtn.disabled = true;
                const originalText = btnText.textContent;
                btnText.textContent = '{{ __("Updating...") }}';
                if (btnIcon) {
                    btnIcon.className = 'spinner-border spinner-border-sm me-2';
                }

                const formData = new FormData(form);

                fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : ''
                    }
                })
                .then(async response => {
                    const data = await response.json().catch(() => ({}));
                    if (response.ok) {
                        const successMessage = data.message || (Array.isArray(data) ? data[0] : '{{ __("Password updated successfully and synchronized!") }}');
                        
                        // Show success banner
                        alertArea.innerHTML = `
                            <div class="alert alert-success py-2 px-3 mb-3 d-flex align-items-center" role="alert" style="border-radius: 8px;">
                                <i class="icofont-check-circled me-2 fs-5"></i>
                                <div>${successMessage}</div>
                            </div>
                        `;

                        // Reset form fields
                        form.reset();

                        // Scroll smoothly to the inline banner so user sees the confirmation
                        alertArea.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                    } else if (response.status === 422 && data.errors) {
                        let errorHtml = '<ul class="mb-0 ps-3">';
                        Object.keys(data.errors).forEach(key => {
                            const messages = data.errors[key];
                            messages.forEach(msg => {
                                errorHtml += `<li>${msg}</li>`;
                            });

                            if (key === 'current') {
                                showFieldError(currentInput, 'error_current', messages[0]);
                            } else if (key === 'password') {
                                showFieldError(passwordInput, 'error_password', messages[0]);
                            } else if (key === 'password_confirmation') {
                                showFieldError(confirmInput, 'error_password_confirmation', messages[0]);
                            }
                        });
                        errorHtml += '</ul>';

                        alertArea.innerHTML = `
                            <div class="alert alert-danger py-2 px-3 mb-3" role="alert" style="border-radius: 8px;">
                                <div class="d-flex align-items-center mb-1 font-weight-bold">
                                    <i class="icofont-warning-alt me-2 fs-5"></i>
                                    <span>{{ __("Unable to update password:") }}</span>
                                </div>
                                ${errorHtml}
                            </div>
                        `;
                    } else {
                        const genericMsg = data.message || '{{ __("An unexpected error occurred. Please try again.") }}';
                        alertArea.innerHTML = `
                            <div class="alert alert-danger py-2 px-3 mb-3 d-flex align-items-center" role="alert" style="border-radius: 8px;">
                                <i class="icofont-warning-alt me-2 fs-5"></i>
                                <div>${genericMsg}</div>
                            </div>
                        `;
                    }
                })
                .catch(err => {
                    alertArea.innerHTML = `
                        <div class="alert alert-danger py-2 px-3 mb-3 d-flex align-items-center" role="alert" style="border-radius: 8px;">
                            <i class="icofont-warning-alt me-2 fs-5"></i>
                            <div>{{ __("Network or server error. Please check your connection and try again.") }}</div>
                        </div>
                    `;
                })
                .finally(() => {
                    submitBtn.disabled = false;
                    btnText.textContent = originalText;
                    if (btnIcon) {
                        btnIcon.className = 'icofont-check-circled me-1';
                    }
                });
            });
        }
    });
</script>
@endpush
