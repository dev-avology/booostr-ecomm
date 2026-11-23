{{-- Booostr SSO Modal Component --}}
@php
    $club_info = function_exists('tenant_club_info') ? (tenant_club_info() ?: []) : [];
    $club_title = trim((string) ($club_info['club_name'] ?? (tenant('id') ?? 'Club')));
    $club_logo_url = function_exists('tenant_club_logo') && tenant_club_logo() ? tenant_club_logo() : asset('uploads/booostr-logo.png');
    $booostr_logo = asset('uploads/booostr-logo.png');
@endphp

<link rel="stylesheet" href="https://maxst.icons8.com/vue-static/landings/line-awesome/line-awesome/1.3.0/css/line-awesome.min.css">

<div id="booostrSsoModal" class="booostr-sso-overlay" style="display: none;">
    <div class="booostr-sso-viewport">
        <div class="booostr-sso-card">
            {{-- Card Header: Brand identity matching Image 2 --}}
            <div class="booostr-sso-header">
                <div class="booostr-brand-badge">
                    <img src="{{ $booostr_logo }}" onerror="this.onerror=null; this.src='https://booostr.site/assets/Booostr/booostr-logo.png';" alt="Booostr" class="booostr-sso-logo">
                    <span>{{ __('Sign up with your Booostr User Account') }}</span>
                </div>
            </div>

            <div class="booostr-sso-body">
                {{-- Global Alert Banner inside Modal --}}
                <div id="ssoAlertBox" class="alert alert-danger sso-alert-box" style="display: none;"></div>

                {{-- ==================== STEP 1: EMAIL / USERNAME ==================== --}}
                <div class="sso-step-pane" id="ssoStepEmail">
                    <div class="sso-grid-layout">
                        <div class="sso-left-col">
                            <div class="club-avatar-wrap">
                                <img src="{{ $club_logo_url }}" alt="{{ $club_title }}" class="club-avatar-img">
                            </div>
                            <h1 class="sso-main-title">{{ __('Sign in') }}</h1>
                            <p class="sso-subtitle-to">
                                {{ __('to continue to') }} <span class="sso-club-brand-name">{{ $club_title }}</span>
                            </p>
                            <div class="sso-user-chip" id="ssoUserChipEmail" style="display: none;">
                                <i class="las la-user-circle"></i>
                                <span class="sso-user-email-text"></span>
                            </div>
                        </div>
                        <div class="sso-right-col">
                            <div class="sso-form-group">
                                <div class="sso-floating-input" id="ssoEmailFieldWrap">
                                    <input type="text" id="ssoEmailInput" class="sso-input-control" placeholder=" " autocomplete="username">
                                    <label for="ssoEmailInput">{{ __('Email or Username') }}</label>
                                </div>
                                <div class="sso-inline-error" id="ssoEmailError" style="display: none;">
                                    <i class="las la-exclamation-circle"></i>
                                    <span class="sso-inline-error-text"></span>
                                </div>
                            </div>

                            <p class="sso-policy-notice">
                                {{ __('Before using this site or app, you can review') }} {{ $club_title }}{{ __("'s Privacy Policy and Terms of Service on their") }}
                                <a href="{{ url('/') }}" target="_blank">{{ __('Website.') }}</a>
                            </p>

                            <div class="sso-action-bar sso-email-actions">
                                <div class="sso-action-right-group">
                                    <a href="https://booostr.co/registration/" target="_blank" class="sso-link-create">{{ __('Create Booostr User Account') }}</a>
                                    <button type="button" class="sso-btn-next" id="ssoEmailNextBtn">
                                        <span>{{ __('Next') }}</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ==================== STEP 2: PASSWORD ==================== --}}
                <div class="sso-step-pane" id="ssoStepPassword" style="display: none;">
                    <div class="sso-grid-layout">
                        <div class="sso-left-col">
                            <div class="club-avatar-wrap">
                                <img src="{{ $club_logo_url }}" alt="{{ $club_title }}" class="club-avatar-img">
                            </div>
                            <h1 class="sso-main-title">{{ __('Welcome') }}</h1>
                            <div class="sso-user-chip">
                                <i class="las la-user-circle"></i>
                                <span class="sso-user-email-text"></span>
                            </div>
                        </div>
                        <div class="sso-right-col">
                            <div class="sso-form-group">
                                <div class="sso-floating-input" id="ssoPasswordFieldWrap">
                                    <input type="password" id="ssoPasswordInput" class="sso-input-control" placeholder=" " autocomplete="current-password">
                                    <label for="ssoPasswordInput">{{ __('Enter your password') }}</label>
                                </div>
                                <div class="sso-inline-error" id="ssoPasswordError" style="display: none;">
                                    <i class="las la-exclamation-circle"></i>
                                    <span class="sso-inline-error-text"></span>
                                </div>
                            </div>

                            <div class="sso-checkbox-wrap">
                                <label class="sso-check-label">
                                    <input type="checkbox" id="ssoShowPasswordToggle">
                                    <span class="checkmark"></span>
                                    <span>{{ __('Show password') }}</span>
                                </label>
                            </div>

                            <p class="sso-policy-notice">
                                {{ __('Before using this site or app, you can review') }} {{ $club_title }}{{ __("'s Privacy Policy and Terms of Service on their") }}
                                <a href="{{ url('/') }}" target="_blank">{{ __('Website.') }}</a>
                            </p>

                            <div class="sso-action-bar">
                                <a href="https://booostr.co/main-login/lost-password/" target="_blank" class="sso-link-forgot">{{ __('Forgot password?') }}</a>
                                <div class="sso-btn-group">
                                    <button type="button" class="sso-btn-back" id="ssoPasswordBackBtn">{{ __('Back') }}</button>
                                    <button type="button" class="sso-btn-next" id="ssoPasswordNextBtn">
                                        <span>{{ __('Next') }}</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ==================== STEP 3: 6-DIGIT EMAIL VERIFICATION (OTP) ==================== --}}
                <div class="sso-step-pane" id="ssoStepOtp" style="display: none;">
                    <div class="sso-grid-layout sso-otp-grid-layout">
                        <div class="sso-left-col sso-otp-left-col">
                            <div class="club-avatar-wrap">
                                <img src="{{ $club_logo_url }}" alt="{{ $club_title }}" class="club-avatar-img">
                            </div>
                            <h1 class="sso-main-title sso-otp-title">{{ __('2-Step Verification') }}</h1>
                            <p class="sso-subtitle-to sso-otp-subtitle">
                                {{ __('Enter the 6-digit code sent to:') }}
                            </p>
                            <div class="sso-otp-email-badge">
                                <i class="las la-envelope"></i>
                                <span class="sso-user-email-text sso-full-email-text"></span>
                            </div>
                        </div>
                        <div class="sso-right-col">
                            <div class="sso-form-group mb-3">
                                <label class="sso-otp-label">{{ __('Enter 6-digit code') }}</label>
                                <div class="sso-otp-digit-boxes" id="ssoOtpFieldWrap">
                                    <input type="text" inputmode="numeric" pattern="[0-9]*" maxlength="1" class="sso-otp-digit" data-index="0" autocomplete="off">
                                    <input type="text" inputmode="numeric" pattern="[0-9]*" maxlength="1" class="sso-otp-digit" data-index="1" autocomplete="off">
                                    <input type="text" inputmode="numeric" pattern="[0-9]*" maxlength="1" class="sso-otp-digit" data-index="2" autocomplete="off">
                                    <span class="sso-otp-divider">-</span>
                                    <input type="text" inputmode="numeric" pattern="[0-9]*" maxlength="1" class="sso-otp-digit" data-index="3" autocomplete="off">
                                    <input type="text" inputmode="numeric" pattern="[0-9]*" maxlength="1" class="sso-otp-digit" data-index="4" autocomplete="off">
                                    <input type="text" inputmode="numeric" pattern="[0-9]*" maxlength="1" class="sso-otp-digit" data-index="5" autocomplete="off">
                                </div>
                                <input type="hidden" id="ssoOtpInput" name="otp">
                                <div class="sso-inline-error" id="ssoOtpError" style="display: none;">
                                    <i class="las la-exclamation-circle"></i>
                                    <span class="sso-inline-error-text"></span>
                                </div>
                            </div>

                            <div class="sso-resend-wrap">
                                <span>{{ __("Didn't receive code?") }}</span>
                                <a href="javascript:void(0)" id="ssoResendOtpLink">{{ __('Resend code') }}</a>
                            </div>

                            <div class="sso-action-bar">
                                <button type="button" class="sso-btn-back" id="ssoOtpBackBtn">{{ __('Back') }}</button>
                                <button type="button" class="sso-btn-next" id="ssoOtpNextBtn">
                                    <span>{{ __('Verify') }}</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ==================== STEP 4: PERMISSIONS & CONSENT ==================== --}}
                <div class="sso-step-pane" id="ssoStepConsent" style="display: none;">
                    <div class="sso-grid-layout sso-consent-layout">
                        <div class="sso-left-col">
                            <div class="club-avatar-wrap">
                                <img src="{{ $club_logo_url }}" alt="{{ $club_title }}" class="club-avatar-img">
                            </div>
                            <h1 class="sso-main-title">{{ __('Sign in to') }} {{ $club_title }}</h1>
                            <div class="sso-user-chip">
                                <i class="las la-user-circle"></i>
                                <span class="sso-user-email-text"></span>
                            </div>
                        </div>
                        <div class="sso-right-col">
                            <h4 class="sso-consent-headline">
                                {{ __('Booostr will allow') }} <span style="color: #0b57d0;">{{ $club_title }}</span> {{ __('to access this info about you') }}
                            </h4>

                            <div class="sso-permission-list">
                                <div class="sso-perm-item">
                                    <div class="sso-perm-icon" id="ssoPermUserAvatarWrap"><i class="las la-user"></i></div>
                                    <div class="sso-perm-details">
                                        <strong id="ssoConsentDisplayName">{{ __('Your Full Name') }}</strong>
                                        <span class="sso-perm-sub" id="ssoConsentUsername"></span>
                                        <span class="sso-perm-desc">{{ __('Name, user name and profile picture') }}</span>
                                    </div>
                                </div>
                                <div class="sso-perm-item">
                                    <div class="sso-perm-icon"><i class="las la-envelope"></i></div>
                                    <div class="sso-perm-details">
                                        <strong class="sso-user-email-text" id="ssoConsentEmail">{{ __('Email Address') }}</strong>
                                        <span class="sso-perm-sub" id="ssoConsentAge"></span>
                                        <span class="sso-perm-desc">{{ __('email address, age range') }}</span>
                                    </div>
                                </div>
                            </div>

                            <p class="sso-policy-notice sso-consent-fineprint">
                                {{ __('Review') }} {{ $club_title }}{{ __("'s Privacy Policy and Terms of Service on their") }}
                                <a href="{{ url('/') }}" target="_blank">{{ __('Website') }}</a>
                                {{ __('to understand how they will process and protect your data.') }}
                            </p>

                            <div class="sso-action-bar sso-consent-actions">
                                <button type="button" class="sso-btn-cancel" id="ssoConsentCancelBtn">{{ __('Cancel') }}</button>
                                <button type="button" class="sso-btn-continue" id="ssoConsentContinueBtn">
                                    <span>{{ __('Continue') }}</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        {{-- External Footer (Under the Card, Matching Image 1 & 2) --}}
        <div class="booostr-sso-footer">
            <span class="sso-footer-powered">
                {{ __('powered by Booostr - need assistance? email') }}
                <a href="mailto:support@booostr.co">support@booostr.co</a>
            </span>
            <div class="sso-footer-links">
                <a href="https://booostr.co/privacy-policy/" target="_blank">{{ __('Privacy') }}</a>
                <a href="https://booostr.co/terms-and-conditions/" target="_blank">{{ __('Terms') }}</a>
                <a href="javascript:void(0)" class="sso-exit-link" id="booostrSsoCloseBtn">{{ __('Back to Registration') }}</a>
            </div>
        </div>
    </div>
</div>

<style>
/* ==========================================================================
   Booostr SSO - Authentic Google-Style Full-Viewport Architecture
   Scoped strictly inside .booostr-sso-overlay to protect site layout
   ========================================================================== */
@import url('https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap');

.booostr-sso-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    z-index: 999999;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: #f0f4f9; /* Authentic Google SSO background */
    overflow-y: auto;
    padding: 24px;
    box-sizing: border-box;
    font-family: 'Roboto', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
    -webkit-font-smoothing: antialiased;
}

.booostr-sso-viewport {
    width: 100%;
    max-width: 880px;
    margin: auto;
}

.booostr-sso-card {
    background: #ffffff;
    border-radius: 28px;
    border: 1px solid #e0e3e7;
    box-shadow: 0 1px 3px 0 rgba(60,64,67,0.1), 0 4px 8px 3px rgba(60,64,67,0.06);
    overflow: hidden;
    display: flex;
    flex-direction: column;
    min-height: 440px;
    box-sizing: border-box;
}

/* Header matching Screenshot 2 */
.booostr-sso-header {
    display: flex;
    align-items: center;
    justify-content: flex-start;
    padding: 32px 40px 10px 40px;
}

.booostr-brand-badge {
    display: flex;
    align-items: center;
    gap: 14px;
}

.booostr-sso-logo {
    height: 26px;
    width: auto;
    object-fit: contain;
}

.booostr-brand-badge span {
    font-size: 14px;
    font-weight: 500;
    color: #444746;
    letter-spacing: 0.1px;
}

/* Body Layout */
.booostr-sso-body {
    padding: 24px 40px 36px 40px;
    flex: 1;
}

.sso-alert-box {
    margin-bottom: 20px;
    border-radius: 8px;
    font-size: 14px;
    padding: 12px 16px;
}

/* Two Column Layout (Matching Screenshots 1 & 2) */
.sso-grid-layout {
    display: grid;
    grid-template-columns: 280px 1fr;
    gap: 48px;
    align-items: start;
}

.sso-left-col {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
}

.club-avatar-wrap {
    max-width: 120px;
    height: 80px;
    margin-bottom: 24px;
    display: flex;
    align-items: center;
    justify-content: flex-start;
}

.club-avatar-img {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
}

/* Typography matching Google SSO Image 2 */
.sso-main-title {
    font-size: 36px !important;
    font-weight: 400 !important;
    color: #1f1f1f !important;
    margin: 0 0 16px 0 !important;
    line-height: 1.25 !important;
    font-family: inherit;
}

.sso-subtitle-to {
    font-size: 16px;
    color: #444746;
    margin: 0;
    line-height: 1.45;
    font-weight: 400;
}

.sso-club-brand-name {
    color: #0b57d0;
    font-weight: 500;
}

/* User Identity Chip matching Screenshot 2 */
.sso-user-chip {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 4px 10px 4px 4px;
    border: 1px solid #747775;
    border-radius: 16px;
    font-size: 14px;
    color: #1f1f1f;
    margin-top: 14px;
    max-width: 100%;
    background: #fff;
}

.sso-user-chip i {
    font-size: 20px;
    color: #444746;
}

.sso-user-email-text {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

/* Right Column & Form Controls */
.sso-right-col {
    display: flex;
    flex-direction: column;
    padding-top: 8px;
}

.sso-form-group {
    margin-bottom: 24px;
}

/* Google Outline Text Field (Authentic Focus & Out-Focus States) */
.sso-floating-input {
    position: relative;
    width: 100%;
}

.sso-input-control {
    width: 100% !important;
    height: 56px !important;
    border: 1px solid #747775 !important;
    border-radius: 4px !important;
    padding: 16px 16px 14px 16px !important;
    font-size: 16px !important;
    color: #1f1f1f !important;
    background: transparent !important;
    outline: none !important;
    box-shadow: none !important;
    box-sizing: border-box !important;
    transition: border-color 0.15s ease, border-width 0.15s ease !important;
}

.sso-floating-input label {
    position: absolute;
    left: 14px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 16px;
    color: #444746;
    font-weight: 400;
    margin: 0;
    padding: 0 6px;
    background: #ffffff;
    line-height: 1;
    pointer-events: none;
    z-index: 2;
    transition: top 0.15s cubic-bezier(0.4, 0, 0.2, 1), 
                transform 0.15s cubic-bezier(0.4, 0, 0.2, 1), 
                font-size 0.15s cubic-bezier(0.4, 0, 0.2, 1), 
                color 0.15s ease;
}

.sso-input-control:focus {
    border: 2px solid #0b57d0 !important;
    padding: 15px 15px 13px 15px !important;
    outline: none !important;
    box-shadow: none !important;
}

.sso-input-control:focus ~ label {
    top: 0;
    transform: translateY(-50%);
    font-size: 12px;
    color: #0b57d0;
    font-weight: 500;
}

.sso-input-control:not(:placeholder-shown) ~ label {
    top: 0;
    transform: translateY(-50%);
    font-size: 12px;
    color: #444746;
    font-weight: 400;
}

.sso-input-control:focus:not(:placeholder-shown) ~ label {
    color: #0b57d0;
    font-weight: 500;
}

.sso-floating-input.has-error .sso-input-control {
    border-color: #b3261e !important;
}
.sso-floating-input.has-error .sso-input-control:focus {
    border: 2px solid #b3261e !important;
}
.sso-floating-input.has-error label {
    color: #b3261e !important;
}

.sso-inline-error {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-top: 6px;
    font-size: 12px;
    color: #b3261e;
    line-height: 1.4;
    animation: ssoErrorFadeIn 0.2s ease-in-out;
}
.sso-inline-error i {
    font-size: 16px;
    flex-shrink: 0;
}
@keyframes ssoErrorFadeIn {
    from { opacity: 0; transform: translateY(-4px); }
    to { opacity: 1; transform: translateY(0); }
}

.sso-otp-label {
    display: block;
    font-size: 14px;
    font-weight: 500;
    color: #1f1f1f;
    margin-bottom: 12px;
}

.sso-otp-digit-boxes {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 8px;
}

.sso-otp-digit {
    width: 44px !important;
    height: 52px !important;
    text-align: center !important;
    font-size: 22px !important;
    font-weight: 600 !important;
    color: #1f1f1f !important;
    background: #ffffff !important;
    border: 1.5px solid #747775 !important;
    border-radius: 8px !important;
    padding: 0 !important;
    outline: none !important;
    box-shadow: none !important;
    transition: all 0.15s ease !important;
    box-sizing: border-box !important;
}

.sso-otp-digit:focus {
    border-color: #0b57d0 !important;
    border-width: 2px !important;
    box-shadow: 0 0 0 3px rgba(11, 87, 208, 0.15) !important;
    background: #f8fbff !important;
}

.sso-otp-digit.has-val {
    border-color: #0b57d0 !important;
    background: #fbfdff !important;
}

.sso-otp-divider {
    font-size: 18px;
    font-weight: 600;
    color: #747775;
    user-select: none;
    padding: 0 2px;
}

.sso-otp-digit-boxes.has-error .sso-otp-digit {
    border-color: #b3261e !important;
    background: #fff8f8 !important;
}

.sso-otp-digit-boxes.has-error .sso-otp-digit:focus {
    box-shadow: 0 0 0 3px rgba(179, 38, 30, 0.15) !important;
}

.sso-otp-grid-layout {
    grid-template-columns: 340px 1fr !important;
    gap: 40px !important;
}

.sso-otp-title {
    font-size: 28px !important;
    font-weight: 500 !important;
    white-space: nowrap !important;
    margin-bottom: 12px !important;
    letter-spacing: -0.3px;
}

.sso-otp-subtitle {
    font-size: 14px !important;
    color: #444746 !important;
    margin-bottom: 8px !important;
    line-height: 1.4 !important;
}

.sso-otp-email-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 6px 14px;
    background: #f0f4f9;
    border-radius: 20px;
    font-size: 13px;
    color: #1f1f1f;
    max-width: 100%;
}

.sso-otp-email-badge i {
    font-size: 16px;
    color: #0b57d0;
    flex-shrink: 0;
}

.sso-full-email-text {
    font-weight: 500;
    color: #1f1f1f;
    word-break: break-all;
    white-space: normal !important;
}

.sso-policy-notice {
    font-size: 12px;
    color: #444746;
    line-height: 1.55;
    margin: 0 0 36px 0;
}

.sso-policy-notice a {
    color: #0084d6;
    text-decoration: underline;
}

.sso-action-bar {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 24px;
    margin-top: auto;
}

.sso-action-right-group {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 24px;
    width: 100%;
}

.sso-step-pane#ssoStepEmail .sso-action-bar {
    justify-content: flex-end;
}

.sso-step-pane#ssoStepPassword .sso-action-bar {
    justify-content: space-between;
}

.sso-btn-group {
    display: flex;
    align-items: center;
    gap: 12px;
}

.sso-link-create,
.sso-link-forgot {
    font-size: 14px;
    font-weight: 500;
    color: #0084d6;
    text-decoration: none;
}

.sso-link-create:hover,
.sso-link-forgot:hover {
    text-decoration: underline;
}

.sso-btn-next,
.sso-btn-continue {
    background-color: #0084d6 !important;
    color: #ffffff !important;
    border: none !important;
    border-radius: 8px !important;
    height: 40px !important;
    padding: 0 28px !important;
    font-size: 14px !important;
    font-weight: 600 !important;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    box-shadow: none !important;
    transition: background-color 0.15s ease;
}

.sso-btn-next:hover,
.sso-btn-continue:hover {
    background-color: #0073bc !important;
}

.sso-btn-back,
.sso-btn-cancel {
    background: transparent !important;
    color: #0084d6 !important;
    border: none !important;
    border-radius: 8px !important;
    height: 40px !important;
    padding: 0 16px !important;
    font-size: 14px !important;
    font-weight: 500 !important;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    transition: background-color 0.15s ease;
}

.sso-btn-back:hover,
.sso-btn-cancel:hover {
    background-color: rgba(0, 132, 214, 0.08) !important;
}

.sso-checkbox-wrap {
    margin-bottom: 24px;
}

.sso-check-label {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    font-size: 14px;
    color: #1f1f1f;
    cursor: pointer;
    user-select: none;
    margin: 0;
}

.sso-check-label input {
    width: 18px;
    height: 18px;
    accent-color: #0084d6;
    cursor: pointer;
}

.sso-consent-headline {
    font-size: 16px;
    font-weight: 500;
    color: #1f1f1f;
    margin-bottom: 20px;
}

.sso-permission-list {
    display: flex;
    flex-direction: column;
    gap: 16px;
    margin-bottom: 24px;
}

.sso-perm-item {
    display: flex;
    align-items: flex-start;
    gap: 14px;
}

.sso-perm-icon {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: #e8f0fe;
    color: #0084d6;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    flex-shrink: 0;
}

.sso-perm-details {
    display: flex;
    flex-direction: column;
    line-height: 1.4;
}

.sso-perm-details strong {
    font-size: 14px;
    color: #1f1f1f;
    font-weight: 500;
}

.sso-perm-details .sso-perm-sub {
    font-size: 13px;
    color: #444746;
}

.sso-perm-details .sso-perm-desc {
    font-size: 12px;
    color: #747775;
    margin-top: 2px;
}

.sso-resend-wrap {
    font-size: 14px;
    margin-bottom: 20px;
    color: #444746;
}

.sso-resend-wrap a {
    color: #0084d6;
    font-weight: 500;
    margin-left: 6px;
    text-decoration: none;
}

.sso-resend-wrap a:hover {
    text-decoration: underline;
}

.booostr-sso-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 16px 20px 0 20px;
    background: transparent;
    border-top: none;
    font-size: 12px;
    color: #5f6368;
    margin-top: 8px;
}

.booostr-sso-footer a {
    color: #0084d6;
    text-decoration: none;
}

.booostr-sso-footer a:hover {
    text-decoration: underline;
}

.sso-footer-links {
    display: flex;
    gap: 24px;
    align-items: center;
}

.sso-footer-links a {
    color: #5f6368;
}

.sso-footer-links a:hover {
    color: #1f1f1f;
}

.sso-exit-link {
    color: #0084d6 !important;
    font-weight: 500;
}

@media (max-width: 768px) {
    .booostr-sso-card {
        border-radius: 16px;
        border: none;
        box-shadow: none;
    }
    .booostr-sso-overlay {
        padding: 0;
        background: #ffffff;
    }
    .sso-grid-layout {
        grid-template-columns: 1fr;
        gap: 24px;
    }
    .booostr-sso-header {
        padding: 24px 20px 8px 20px;
    }
    .booostr-sso-body {
        padding: 16px 20px 24px 20px;
    }
    .booostr-sso-footer {
        padding: 16px 20px;
        flex-direction: column;
        gap: 12px;
        text-align: center;
    }
    .sso-main-title {
        font-size: 28px !important;
    }
    .sso-otp-title {
        font-size: 24px !important;
        white-space: normal !important;
    }
    .sso-otp-grid-layout {
        grid-template-columns: 1fr !important;
    }
    .sso-action-bar,
    .sso-action-right-group {
        flex-direction: column-reverse;
        align-items: stretch;
        gap: 16px;
    }
    .sso-btn-next,
    .sso-btn-continue,
    .sso-btn-back,
    .sso-btn-cancel {
        width: 100%;
        text-align: center;
    }
    .sso-link-create,
    .sso-link-forgot {
        text-align: center;
    }
}

@media (max-width: 480px) {
    .sso-otp-digit-boxes {
        gap: 5px;
    }
    .sso-otp-digit {
        width: 38px !important;
        height: 48px !important;
        font-size: 19px !important;
    }
    .sso-otp-divider {
        font-size: 14px;
        padding: 0 1px;
    }
}
</style>
