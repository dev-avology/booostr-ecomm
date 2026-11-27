<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Order;
use App\Models\Ordermeta;
use App\Models\Review;
use App\Models\Term;
use Auth;
use Artesaos\SEOTools\Facades\SEOMeta;
use Artesaos\SEOTools\Facades\OpenGraph;
use Artesaos\SEOTools\Facades\TwitterCard;
use Artesaos\SEOTools\Facades\JsonLd;
use Artesaos\SEOTools\Facades\JsonLdMulti;
use Artesaos\SEOTools\Facades\SEOTools;
use Newsletter;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\UserResetEmail;
use App\Mail\BooostrAccountCreatedMail;
use App\Models\Category;
use GuzzleHttp\Client;
class CustomerController extends Controller
{
    public function subscribe(Request $request)
    {
        $request->validate([
          'email' => 'required|email|max:100',
        ]);
        $mailchimp=get_option('mailchimp',true);
        $mailchimp=$mailchimp->meta ?? '';
        \Config::set('newsletter.apiKey', $mailchimp->mailchimp_api_key);
        \Config::set('newsletter.lists.subscribers.id', $mailchimp->mailchimp_list_id);

        Newsletter::subscribe($request->email);
       return response()->json('Thanks for subscribe');
    }

    public function login()
    {
        abort_if(tenant('customer_modules') != 'on',404);

        // Ensure any lingering non-checkout redirect target is cleared
        if (session()->has('url.intended')) {
            $intended = (string) session()->get('url.intended');
            if (!str_contains($intended, '/checkout') && !str_contains($intended, 'checkout')) {
                session()->forget('url.intended');
            }
        }
        
        store_set_seo('Login');
        
        return view(baseview('customer/login'));
    }

    /**
     * Customer Login Handler (Booostr Central API + Local Sync & Fallback)
     *
     * Workflow:
     * 1. Validates username/email and password (minimum 6 characters).
     * 2. Resolves username if the customer entered their registered email address.
     * 3. Communicates with Booostr Core Central API (https://app.booostr.co/wp-json/webbuilder-api/v1/user-login).
     * 4. If credentials match on Booostr Core, synchronizes/updates the customer in the local tenant database
     *    with role_id = 4 (Store Customer), verifies email, stores metadata, and hashes password locally.
     * 5. If central API is unreachable or returns false, falls back to local database authentication (Hash::check).
     * 6. Authenticates session via Auth::login() and returns intended redirect destination (or AJAX JSON response).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function loginCustomer(Request $request)
    {
        abort_if(tenant('customer_modules') != 'on', 404);

        // Security Protocol: Limit login attempts to 1 attempt per second per IP address
        $throttleKey = 'customer-login:' . $request->ip();
        if (\Illuminate\Support\Facades\RateLimiter::tooManyAttempts($throttleKey, 1)) {
            $rateLimitMsg = __('Too many login attempts. Please wait a second before trying again.');
            if ($request->ajax()) {
                return response()->json([
                    'status' => 'error',
                    'msg' => $rateLimitMsg,
                ], 429);
            }
            return redirect()->back()->withInput($request->only('email'))->with([
                'error' => $rateLimitMsg,
                'msg' => $rateLimitMsg,
            ]);
        }
        \Illuminate\Support\Facades\RateLimiter::hit($throttleKey, 1);

        $rules = [
            'email' => 'required|string',
            'password' => 'required|string|min:6',
            'g-recaptcha-response' => 'required',
        ];

        $messages = [
            'email.required' => __('Username or email is required.'),
            'password.required' => __('Password is required.'),
            'password.min' => __('Password must be at least 6 characters.'),
            'g-recaptcha-response.required' => __('Please verify that you are not a robot.'),
        ];

        // Handle AJAX validation errors gracefully
        if ($request->ajax()) {
            $validator = \Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'msg' => $validator->errors()->first(),
                ], 422);
            }
        } else {
            $request->validate($rules, $messages);
        }

        // Verify reCAPTCHA with Google siteverify API
        $recaptchaSecret = env('RECAPTCHA_SECRET_KEY') ?: config('services.recaptcha.secret_key');
        if (empty($recaptchaSecret)) {
            \Log::error('reCAPTCHA secret key is missing from environment (.env).');
            $errMsg = __('reCAPTCHA configuration error. Please contact site administrator.');
            if ($request->ajax()) {
                return response()->json(['status' => 'error', 'msg' => $errMsg], 422);
            }
            return redirect()->back()->withErrors(['g-recaptcha-response' => $errMsg])->withInput();
        }

        try {
            $verifyClient = new \GuzzleHttp\Client();
            $verifyRes = $verifyClient->post('https://www.google.com/recaptcha/api/siteverify', [
                'verify' => false,
                'timeout' => 10,
                'form_params' => [
                    'secret' => $recaptchaSecret,
                    'response' => $request->input('g-recaptcha-response'),
                    'remoteip' => $request->ip(),
                ],
            ]);
            $verifyBody = json_decode((string) $verifyRes->getBody(), true);
            if (empty($verifyBody['success'])) {
                $errMsg = __('reCAPTCHA verification failed. Please try again.');
                if ($request->ajax()) {
                    return response()->json(['status' => 'error', 'msg' => $errMsg], 422);
                }
                return redirect()->back()->withErrors(['g-recaptcha-response' => $errMsg])->withInput();
            }
        } catch (\Throwable $e) {
            \Log::error('reCAPTCHA verification error on login', ['error' => $e->getMessage()]);
            $errMsg = __('Unable to verify reCAPTCHA at this time. Please try again.');
            if ($request->ajax()) {
                return response()->json(['status' => 'error', 'msg' => $errMsg], 422);
            }
            return redirect()->back()->withErrors(['g-recaptcha-response' => $errMsg])->withInput();
        }

        $inputIdentifier = trim($request->input('email'));
        $password = (string) $request->input('password');
        $remember = (bool) $request->filled('remember');

        // Resolve username if an email address was provided
        $username = $inputIdentifier;
        if (filter_var($inputIdentifier, FILTER_VALIDATE_EMAIL)) {
            $localUser = User::where('email', $inputIdentifier)->first();
            if ($localUser && !empty($localUser->meta)) {
                $meta = json_decode($localUser->meta, true);
                if (!empty($meta['username'])) {
                    $username = $meta['username'];
                }
            }
        }

        $clubSlug = (string) (tenant('id') ?? '');
        $apiUrl = env('WP_WEBBUILDER_LOGIN_API_URL') ?: 'https://app.booostr.co/wp-json/webbuilder-api/v1/user-login';
        $authenticatedUser = null;

        // Step 1: Attempt authentication with Booostr Central API
        try {
            $client = new \GuzzleHttp\Client();
            $response = $client->post($apiUrl, [
                'verify' => false,
                'timeout' => 15,
                'headers' => [
                    'Accept' => 'application/json',
                ],
                'json' => [
                    'username' => $username,
                    'password' => $password,
                    'club'     => $clubSlug,
                ],
            ]);

            $resBody = json_decode((string) $response->getBody());
            if ($resBody && !empty($resBody->success) && !empty($resBody->data) && ($resBody->data->status ?? '') === 'success') {
                $wpUser = $resBody->data->user ?? null;
                if ($wpUser && !empty($wpUser->user_email)) {
                    // Find or create local customer user record
                    $localUser = User::where('email', $wpUser->user_email)->first();
                    if (!$localUser) {
                        $localUser = new User();
                        $localUser->email = $wpUser->user_email;
                        $localUser->role_id = 4; // Role 4 = Store Customer in Booostr eCommerce
                        $localUser->status = 1;
                    }

                    $existingMeta = !empty($localUser->meta) ? json_decode($localUser->meta, true) : [];

                    // Extract or preserve first and last names
                    $incomingFname = $wpUser->first_name ?? ($wpUser->fname ?? ($existingMeta['fname'] ?? ''));
                    $incomingLname = $wpUser->last_name ?? ($wpUser->lname ?? ($existingMeta['lname'] ?? ''));

                    if (empty($incomingFname) && empty($incomingLname)) {
                        $nameToSplit = $wpUser->display_name ?? ($wpUser->name ?? ($wpUser->user_nicename ?? ($localUser->name ?? '')));
                        if (!empty($nameToSplit)) {
                            $nameParts = explode(' ', trim($nameToSplit), 2);
                            $incomingFname = $nameParts[0] ?? '';
                            $incomingLname = $nameParts[1] ?? '';
                        }
                    }

                    $computedName = trim($incomingFname . ' ' . $incomingLname);
                    if (empty($computedName)) {
                        $computedName = $wpUser->display_name ?? ($wpUser->user_nicename ?? ($wpUser->user_login ?? ($localUser->name ?? '')));
                    }
                    if (!empty($computedName)) {
                        $localUser->name = $computedName;
                    }

                    // Only update password for normal customers, never overwrite store owner/seller fixed password
                    if ($localUser->role_id != 3) {
                        $localUser->password = \Hash::make($password);
                    }
                    if (!empty($wpUser->user_phone)) {
                        $localUser->phone = $wpUser->user_phone;
                    }

                    $incomingCountry = $wpUser->user_country ?? ($existingMeta['country'] ?? 'USA');
                    if (empty($incomingCountry) || in_array(trim($incomingCountry), ['United States of America', 'United States', 'US'], true)) {
                        $incomingCountry = 'USA';
                    }

                    $mergedMeta = array_merge($existingMeta, [
                        'wpuid' => (int) ($wpUser->user_id ?? ($existingMeta['wpuid'] ?? 0)),
                        'username' => $wpUser->user_login ?? ($existingMeta['username'] ?? $username),
                        'fname' => $incomingFname,
                        'lname' => $incomingLname,
                        'age' => $wpUser->age ?? ($existingMeta['age'] ?? 'adult'),
                        'country' => $incomingCountry,
                        'city' => $wpUser->user_city ?? ($existingMeta['city'] ?? ''),
                        'state' => $wpUser->user_state ?? ($existingMeta['state'] ?? ''),
                        'address' => $wpUser->user_address ?? ($existingMeta['address'] ?? ''),
                    ]);
                    $localUser->meta = json_encode($mergedMeta);
                    $localUser->save();

                    $authenticatedUser = $localUser;
                }
            }
        } catch (\Throwable $e) {
            \Log::warning('Booostr Core login API attempt failed or returned error', [
                'identifier' => $inputIdentifier,
                'error' => $e->getMessage(),
            ]);
        }

        // Step 2: Fallback to local authentication (if API check failed or user is a local customer)
        if (!$authenticatedUser) {
            $field = filter_var($inputIdentifier, FILTER_VALIDATE_EMAIL) ? 'email' : 'name';
            $candidateUser = User::where($field, $inputIdentifier)->where('role_id', 4)->first();

            // Check meta username if search by name did not locate record
            if (!$candidateUser && $field === 'name') {
                $allCustomers = User::where('role_id', 4)->whereNotNull('meta')->get();
                foreach ($allCustomers as $c) {
                    $m = json_decode($c->meta, true);
                    if (!empty($m['username']) && strtolower($m['username']) === strtolower($inputIdentifier)) {
                        $candidateUser = $c;
                        break;
                    }
                }
            }

            if ($candidateUser && \Hash::check($password, $candidateUser->password)) {
                $authenticatedUser = $candidateUser;
            }
        }

        // Step 3: Handle Successful Login
        if ($authenticatedUser) {
            \Illuminate\Support\Facades\RateLimiter::clear($throttleKey);
            Auth::login($authenticatedUser, $remember);
            session(['seller_session_active' => false]);
            session()->forget(['customer_data', 'is_guest']);

            // Auto-claim unassigned guest orders placed with customer's email
            $this->claimGuestOrdersByEmail($authenticatedUser);

            // Redirect user to intended checkout / customer dashboard destination
            // (Storefront login ALWAYS directs users to the customer portal, never seller/admin)
            $intended = session()->pull('url.intended');
            if ($intended && (str_contains($intended, '/checkout') || str_contains($intended, 'checkout'))) {
                $redirectUrl = $intended;
            } else {
                $redirectUrl = url('/customer/dashboard');
            }

            if ($request->ajax()) {
                return response()->json([
                    'status' => 'success',
                    'msg' => __('Login successful! Redirecting...'),
                    'redirect' => $redirectUrl,
                ]);
            }

            return redirect()->to($redirectUrl);
        }

        // Step 4: Authentication Failed
        $errorMsg = __('User name and password do not match.');
        if ($request->ajax()) {
            return response()->json([
                'status' => 'error',
                'msg' => $errorMsg,
            ], 422);
        }

        return redirect()->back()->withInput($request->only('email'))->with([
            'error' => $errorMsg,
            'msg' => $errorMsg,
        ]);
    }

    public function register()
    {
        abort_if(tenant('customer_modules') != 'on',404);

        store_set_seo('Register');
        
        return view(baseview('customer/register'));
    }

    /**
     * Customer Registration Handler
     *
     * Workflow & Features:
     * 1. Validates First Name, Last Name, Username, Email (unique), Password (min 8, confirmed),
     *    Age category, Terms and Conditions acceptance, and Google reCAPTCHA.
     * 2. Verifies reCAPTCHA token against Google's siteverify API.
     * 3. Syncs registration with Booostr Central API (https://app.booostr.co/wp-json/webbuilder-api/v1/user-register)
     *    so the customer account is immediately recognized across the Booostr ecosystem and Contact Manager.
     * 4. Stores the customer in the local tenant database with role_id = 4 (Store Customer) and rich metadata (wpuid, age, location).
     * 5. Dispatches a branded HTML welcome email (BooostrAccountCreatedMail) with club name and login link.
     * 6. Automatically authenticates the session and redirects to the intended checkout or customer dashboard.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function registerCustomer(Request $request)
    {
        abort_if(tenant('customer_modules') != 'on',404);

        $validationRules = [
            'fname' => ['required', 'string', 'max:191'],
            'lname' => ['required', 'string', 'max:191'],
            'email' => ['required', 'string', 'email', 'max:191', 'unique:users'],
            'username' => ['required', 'string', 'max:191'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'age' => ['required', 'string'],
            'agree_terms' => ['accepted'],
            'g-recaptcha-response' => ['required'],
        ];
        $validationMessages = [
            'fname.required' => 'First name is required.',
            'lname.required' => 'Last name is required.',
            'email.required' => 'Email address is required.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'This email is already registered on this store. Please login.',
            'username.required' => 'Username is required.',
            'password.required' => 'Password is required.',
            'password.min' => 'Password must be at least 8 characters.',
            'password.confirmed' => 'Password confirmation does not match.',
            'age.required' => 'Age is required.',
            'agree_terms.accepted' => 'Please agree to the Terms and Conditions and Privacy Policy.',
            'agree_terms.required' => 'Please agree to the Terms and Conditions and Privacy Policy.',
            'g-recaptcha-response.required' => 'Please verify that you are not a robot.',
        ];

        $request->validate($validationRules, $validationMessages);

        // Verify reCAPTCHA with Google siteverify API
        $recaptchaSecret = env('RECAPTCHA_SECRET_KEY') ?: config('services.recaptcha.secret_key');
        if (empty($recaptchaSecret)) {
            \Log::error('reCAPTCHA secret key is missing from environment (.env).');
            return redirect()->back()->withErrors([
                'g-recaptcha-response' => __('reCAPTCHA configuration error. Please contact site administrator.'),
            ])->withInput();
        }

        try {
            $verifyClient = new \GuzzleHttp\Client();
            $verifyRes = $verifyClient->post('https://www.google.com/recaptcha/api/siteverify', [
                'verify' => false,
                'timeout' => 10,
                'form_params' => [
                    'secret' => $recaptchaSecret,
                    'response' => $request->input('g-recaptcha-response'),
                    'remoteip' => $request->ip(),
                ],
            ]);
            $verifyBody = json_decode((string) $verifyRes->getBody(), true);
            if (empty($verifyBody['success'])) {
                return redirect()->back()->withErrors([
                    'g-recaptcha-response' => __('reCAPTCHA verification failed. Please try again.'),
                ])->withInput();
            }
        } catch (\Throwable $e) {
            \Log::error('reCAPTCHA verification error', ['error' => $e->getMessage()]);
            return redirect()->back()->withErrors([
                'g-recaptcha-response' => __('Unable to verify reCAPTCHA at this time. Please try again.'),
            ])->withInput();
        }

        $firstName = trim($request->fname);
        $lastName = trim($request->lname);
        $name = $firstName . ' ' . $lastName;
        $username = trim($request->username);
        $age = (string) $request->age;
        $clubSlug = (string) (tenant('id') ?? '');
        $wpUser = null;
        $wpuid = 0;

        // Register user in Booostr Core (app.booostr.co) and sync to Contact Manager
        try {
            $client = new \GuzzleHttp\Client();
            $apiUrl = env('WP_WEBBUILDER_API_URL') ?: 'https://app.booostr.co/wp-json/webbuilder-api/v1/user-register';

            $response = $client->post($apiUrl, [
                'verify' => false,
                'timeout' => 15,
                'headers' => [
                    'Accept' => 'application/json',
                ],
                'json' => [
                    'username' => $username,
                    'password' => $request->password,
                    'fname' => $firstName,
                    'lname' => $lastName,
                    'email' => $request->email,
                    'age' => $age,
                    'club' => $clubSlug,
                ],
            ]);

            $resBody = json_decode((string) $response->getBody());
            if ($resBody && !empty($resBody->success) && !empty($resBody->data) && ($resBody->data->status ?? '') === 'success') {
                $wpUser = $resBody->data->user ?? null;
                if (!empty($wpUser->user_id)) {
                    $wpuid = (int) $wpUser->user_id;
                }
            }
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $errorMessage = $e->getResponse()->getBody()->getContents();
            $errorMessage = trim($errorMessage, '"');

            if (str_contains($errorMessage, 'Email already exists') || str_contains($errorMessage, 'email already registered')) {
                $errorMessage = 'This email is already registered on Booostr. Please login.';
            }

            return redirect()->back()->with([
                'type' => 'danger',
                'error' => $errorMessage,
                'msg' => $errorMessage
            ])->withInput();
        } catch (\Throwable $e) {
            \Log::error('Booostr Core user registration API failed', [
                'email' => $request->email,
                'error' => $e->getMessage(),
            ]);
        }

        $user = new User();
        $user->role_id = 4;
        $user->name = $name;
        $user->email = $request->email;
        $user->password = \Hash::make($request->password);
        $user->phone = $wpUser->user_phone ?? null;
        $user->status = 1;

        $regCountry = $wpUser->user_country ?? 'USA';
        if (empty($regCountry) || in_array(trim($regCountry), ['United States of America', 'United States', 'US'], true)) {
            $regCountry = 'USA';
        }

        $metaData = [
            'wpuid' => $wpuid,
            'username' => $username,
            'fname' => $firstName,
            'lname' => $lastName,
            'age' => $age,
            'country' => $regCountry,
            'city' => $wpUser->user_city ?? '',
            'state' => $wpUser->user_state ?? '',
            'address' => $wpUser->user_address ?? '',
        ];
        $user->meta = json_encode($metaData);
        $user->save();

        // Send Welcome Email to newly registered customer
        try {
            // Club details directly from native tenant functions (matches Admin Panel)
            $clubInfo = function_exists('tenant_club_info') ? (tenant_club_info() ?: []) : [];
            $clubName = trim((string) ($clubInfo['club_name'] ?? (tenant('id') ?? '')));
            $clubLogo = function_exists('tenant_club_logo') ? tenant_club_logo() : '';

            $supportEmail = trim((string) ($clubInfo['club_email'] ?? ($clubInfo['email'] ?? '')));

            $tenantId = function_exists('tenant') && tenant() ? (tenant('id') ?: (tenant()->id ?? '')) : '';
            $subjectTenant = !empty($tenantId) ? ($tenantId . '.booostr.site') : ($clubName . ' website');

            $mailData = [
                'subject'       => $firstName . ', your ' . $subjectTenant . ' account is created',
                'name'          => $name,
                'first_name'    => $firstName,
                'username'      => $username,
                'email'         => $request->email,
                'club_name'     => $clubName,
                'site_url'      => url('/'),
                'login_url'     => url('/customer/login'),
                'support_email' => !empty($supportEmail) ? $supportEmail : null,
                'logo'          => $clubLogo,
                'site_url_text' => str_replace(['https://', 'http://'], '', url('/')),
            ];

            Mail::to($request->email)->send(new BooostrAccountCreatedMail($mailData));
        } catch (\Throwable $e) {
            \Log::error('Customer welcome mail failed to send', [
                'email' => $request->email,
                'error' => $e->getMessage(),
            ]);
        }

        Auth::loginUsingId($user->id);

        // Auto-claim unassigned guest orders placed with customer's email
        $this->claimGuestOrdersByEmail($user);

        $intended = session()->pull('url.intended');
        if ($intended && (str_contains($intended, '/checkout') || str_contains($intended, 'checkout'))) {
            return redirect()->to($intended);
        }

        return redirect()->to(url('/customer/dashboard'));
    }

    /**
     * Booostr SSO Step 1: Verify Email/Username exists in local store or Booostr
     */
    public function ssoCheckIdentity(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
        ]);

        $identity = trim($request->username);
        $clubInfo = function_exists('tenant_club_info') ? (tenant_club_info() ?: []) : [];
        $clubTitle = trim((string) ($clubInfo['club_name'] ?? (tenant('id') ?? 'Club')));

        // 1. Check if user already exists in local Store DB
        $localUser = User::where('email', $identity)->first();
        if (!$localUser) {
            $allCustomers = User::where('role_id', 4)->whereNotNull('meta')->get();
            foreach ($allCustomers as $c) {
                $m = json_decode($c->meta, true);
                if (!empty($m['username']) && strtolower($m['username']) === strtolower($identity)) {
                    $localUser = $c;
                    break;
                }
            }
        }

        if ($localUser) {
            return response()->json([
                'status'        => 'already_exists',
                'club_title'    => $clubTitle,
                'login_url'     => route('customer.login'),
                'display_email' => $localUser->email,
                'message'       => sprintf(__('An account already exists with %s. Please log in.'), $clubTitle),
            ], 409);
        }

        // 2. If not found in local DB, check on Booostr (WordPress API)
        try {
            $client = new \GuzzleHttp\Client();
            $response = $client->post('https://app.booostr.co/wp-json/webbuilder-api/v1/boostr-sso-check-user', [
                'verify' => false,
                'timeout' => 10,
                'headers' => [
                    'Accept' => 'application/json',
                ],
                'json' => [
                    'username_or_email' => $identity,
                    'club'              => tenant('id'),
                ],
            ]);

            $result = json_decode((string) $response->getBody(), true);
            $isFound = (!empty($result['status']) && $result['status'] === 'success')
                || (!empty($result['exists']) && $result['exists'] == true);

            if ($isFound) {
                return response()->json([
                    'status'        => 'success',
                    'source'        => 'booostr',
                    'display_email' => $result['email'] ?? $identity,
                    'message'       => __('Booostr account verified.'),
                ]);
            }

            return response()->json([
                'status'  => 'not_found',
                'message' => __("Couldn't find your Booostr account. Please create a Booostr User Account first."),
            ], 404);

        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $errorBody = json_decode($e->getResponse()->getBody()->getContents(), true);
            $msg = $errorBody['message'] ?? __("Couldn't find your Booostr account. Please create a Booostr User Account first.");
            return response()->json(['status' => 'not_found', 'message' => $msg], 404);
        } catch (\Throwable $e) {
            \Log::error('Booostr SSO identity check error: ' . $e->getMessage());
            return response()->json([
                'status'        => 'success',
                'source'        => 'pending',
                'display_email' => $identity,
            ]);
        }
    }

    /**
     * Booostr SSO Step 2: Verify Credentials on WordPress and trigger 6-digit OTP
     */
    public function ssoVerifyCredentials(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $username = trim($request->username);
        $password = $request->password;
        $clubInfo = function_exists('tenant_club_info') ? (tenant_club_info() ?: []) : [];
        $clubTitle = trim((string) ($clubInfo['club_name'] ?? (tenant('id') ?? 'Club')));
        $clubEmail = trim((string) ($clubInfo['club_email'] ?? ($clubInfo['email'] ?? '')));
        $clubLogo = function_exists('tenant_club_logo') && tenant_club_logo() ? tenant_club_logo() : asset('uploads/booostr-logo.png');

        try {
            $client = new \GuzzleHttp\Client();
            $response = $client->post('https://app.booostr.co/wp-json/webbuilder-api/v1/boostr-sso-verify-credentials', [
                'verify' => false,
                'timeout' => 15,
                'headers' => [
                    'Accept' => 'application/json',
                ],
                'json' => [
                    'username_or_email' => $username,
                    'password'          => $password,
                    'club'              => tenant('id'),
                    'club_title'        => $clubTitle,
                    'club_email'        => $clubEmail,
                    'club_logo'         => $clubLogo,
                ],
            ]);

            $result = json_decode((string) $response->getBody(), true);
            $isSuccess = (!empty($result['status']) && $result['status'] === 'success')
                || (!empty($result['success']) && $result['success'] == true);

            if ($isSuccess) {
                return response()->json([
                    'status'  => 'success',
                    'message' => $result['message'] ?? __('Verification code sent to your email.'),
                ]);
            }

            $errorMsg = $result['message'] ?? ($result['data']['msg'] ?? __('Invalid credentials.'));
            return response()->json(['status' => 'error', 'message' => $errorMsg], 422);

        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $errorBody = json_decode($e->getResponse()->getBody()->getContents(), true);
            $msg = $errorBody['message'] ?? ($errorBody['data']['msg'] ?? __('Wrong password. Try again or click Forgot password.'));
            return response()->json(['status' => 'error', 'message' => $msg], 422);
        } catch (\Throwable $e) {
            \Log::error('Booostr SSO credentials verification error: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => __('Unable to verify with Booostr right now. Please try again.')], 500);
        }
    }

    /**
     * Booostr SSO Step 3: Verify 6-digit OTP code on WordPress
     */
    public function ssoVerifyOtp(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'otp'      => 'required|string|size:6',
        ]);

        $username = trim($request->username);
        $otp = trim($request->otp);

        try {
            $client = new \GuzzleHttp\Client();
            $response = $client->post('https://app.booostr.co/wp-json/webbuilder-api/v1/boostr-sso-verify-otp', [
                'verify' => false,
                'timeout' => 15,
                'headers' => [
                    'Accept' => 'application/json',
                ],
                'json' => [
                    'username_or_email' => $username,
                    'otp_code'          => $otp,
                    'club'              => tenant('id'),
                ],
            ]);

            $result = json_decode((string) $response->getBody(), true);
            $isSuccess = (!empty($result['status']) && $result['status'] === 'success')
                || (!empty($result['success']) && $result['success'] == true);

            if ($isSuccess && !empty($result['user'])) {
                session()->put('booostr_sso_verified_user', $result['user']);

                return response()->json([
                    'status' => 'success',
                    'user'   => $result['user'],
                ]);
            }

            $errorMsg = $result['message'] ?? ($result['data']['msg'] ?? __('Invalid or expired verification code.'));
            return response()->json(['status' => 'error', 'message' => $errorMsg], 422);

        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $errorBody = json_decode($e->getResponse()->getBody()->getContents(), true);
            $msg = $errorBody['message'] ?? ($errorBody['data']['msg'] ?? __('Invalid or expired verification code.'));
            return response()->json(['status' => 'error', 'message' => $msg], 422);
        } catch (\Throwable $e) {
            \Log::error('Booostr SSO OTP verification error: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => __('Unable to verify code right now. Please try again.')], 500);
        }
    }

    /**
     * Booostr SSO Step 3b: Resend 6-digit OTP code via WordPress
     */
    public function ssoResendOtp(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
        ]);

        $username = trim($request->username);
        $clubInfo = function_exists('tenant_club_info') ? (tenant_club_info() ?: []) : [];
        $clubTitle = trim((string) ($clubInfo['club_name'] ?? (tenant('id') ?? 'Club')));
        $clubEmail = trim((string) ($clubInfo['club_email'] ?? ($clubInfo['email'] ?? '')));
        $clubLogo = function_exists('tenant_club_logo') && tenant_club_logo() ? tenant_club_logo() : asset('uploads/booostr-logo.png');

        try {
            $client = new \GuzzleHttp\Client();
            $response = $client->post('https://app.booostr.co/wp-json/webbuilder-api/v1/boostr-sso-resend-otp', [
                'verify' => false,
                'timeout' => 15,
                'headers' => [
                    'Accept' => 'application/json',
                ],
                'json' => [
                    'username_or_email' => $username,
                    'club'              => tenant('id'),
                    'club_title'        => $clubTitle,
                    'club_email'        => $clubEmail,
                    'club_logo'         => $clubLogo,
                ],
            ]);

            $result = json_decode((string) $response->getBody(), true);
            $isSuccess = (!empty($result['status']) && $result['status'] === 'success')
                || (!empty($result['success']) && $result['success'] == true);

            if ($isSuccess) {
                return response()->json([
                    'status'   => 'success',
                    'message'  => $result['message'] ?? __('A new verification code has been sent.'),
                    'cooldown' => $result['cooldown'] ?? 60,
                ]);
            }

            $errorMsg = $result['message'] ?? __('Unable to resend code right now.');
            return response()->json(['status' => 'error', 'message' => $errorMsg], 422);

        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $errorBody = json_decode($e->getResponse()->getBody()->getContents(), true);
            $statusCode = $e->getResponse()->getStatusCode();
            $msg = $errorBody['message'] ?? __('Unable to resend verification code.');
            return response()->json(['status' => 'error', 'message' => $msg], $statusCode ?: 422);
        } catch (\Throwable $e) {
            \Log::error('Booostr SSO OTP resend error: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => __('Unable to resend code right now. Please try again.')], 500);
        }
    }

    /**
     * Booostr SSO Step 4: Final Consent -> Sync User, Check Club CRM & Auto Login
     */
    public function ssoComplete(Request $request)
    {
        $ssoUser = session()->get('booostr_sso_verified_user');
        $email = trim($ssoUser['user_email'] ?? '');

        if (empty($ssoUser) || empty($email)) {
            return response()->json([
                'status'  => 'error',
                'message' => __('Your session has expired. Please restart the sign in process.'),
            ], 422);
        }

        $wpuid = (int) ($ssoUser['user_id'] ?? 0);
        $firstName = trim($ssoUser['first_name'] ?? '');
        $lastName = trim($ssoUser['last_name'] ?? '');
        $name = trim($firstName . ' ' . $lastName);
        if (empty($name)) {
            $name = $ssoUser['user_nicename'] ?? ($ssoUser['user_login'] ?? 'Booostr User');
        }

        $username = $ssoUser['user_login'] ?? explode('@', $email)[0];
        $password = !empty($ssoUser['user_pass']) ? \Hash::make($ssoUser['user_pass']) : \Hash::make(\Str::random(16));
        $age = $ssoUser['age'] ?? 'adult';
        $country = $ssoUser['user_country'] ?? 'USA';
        if (empty($country) || in_array(trim($country), ['United States of America', 'United States', 'US'], true)) {
            $country = 'USA';
        }
        $city = $ssoUser['user_city'] ?? null;
        $state = $ssoUser['user_state'] ?? null;
        $address = $ssoUser['user_address'] ?? null;
        $phone = $ssoUser['user_phone'] ?? null;
        $avatar = $ssoUser['user_photo'] ?? null;

        // 1. Check if user already exists in local DB
        $user = User::where('email', $email)->first();

        $userMeta = [
            'wpuid'    => $wpuid,
            'username' => $username,
            'fname'    => $firstName,
            'lname'    => $lastName,
            'age'      => $age,
            'country'  => $country,
            'city'     => $city,
            'state'    => $state,
            'address'  => $address,
            'avatar'   => $avatar,
        ];

        if ($user) {
            $existingMeta = !empty($user->meta) ? json_decode($user->meta, true) : [];
            $mergedMeta = array_merge($existingMeta, array_filter($userMeta));
            $user->meta = json_encode($mergedMeta);
            $user->name = $name ?: $user->name;
            if ($phone) {
                $user->phone = $phone;
            }
            if (!empty($ssoUser['user_pass']) && $user->role_id != 3) {
                $user->password = $password;
            }
            $user->save();
        } else {
            $user = new User();
            $user->name = $name;
            $user->email = $email;
            $user->role_id = 4; // Customer
            $user->status = 1;
            $user->password = $password;
            if ($phone) {
                $user->phone = $phone;
            }
            $user->meta = json_encode($userMeta);
            $user->save();
        }

        // 2. Sync Contact to Booostr CRM
        try {
            $clubSlug = (string) (tenant('id') ?? '');
            $crmClient = new \GuzzleHttp\Client();
            $crmClient->post('https://app.booostr.co/wp-json/webbuilder-api/v1/boostr-sso-sync-contact', [
                'verify' => false,
                'timeout' => 10,
                'headers' => [
                    'Accept' => 'application/json',
                ],
                'json' => [
                    'club_id'    => $clubSlug,
                    'user_id'    => $wpuid,
                    'user_email' => $email,
                    'first_name' => $firstName,
                    'last_name'  => $lastName,
                ],
            ]);
        } catch (\Throwable $e) {
            \Log::warning('Booostr SSO CRM sync warning: ' . $e->getMessage());
        }

        // 3. Auto-claim unassigned guest orders
        $this->claimGuestOrdersByEmail($user);

        // 4. Clean SSO session
        session()->forget('booostr_sso_verified_user');

        // 5. Authenticate user
        Auth::login($user, true);

        // 6. Return redirect URL
        $intended = session()->pull('url.intended');
        if ($intended && (str_contains($intended, '/checkout') || str_contains($intended, 'checkout'))) {
            $redirectUrl = $intended;
        } else {
            $redirectUrl = url('/customer/dashboard');
        }

        return response()->json([
            'status'       => 'success',
            'redirect_url' => $redirectUrl,
        ]);
    }

    public function dashboard()
    {
        abort_if(tenant('customer_modules') != 'on',404);

        try {
            // Auto-claim any unassigned orders matching the customer's email
            $this->claimGuestOrdersByEmail();

            $total_orders = Order::where('user_id', Auth::id())->count();
            $total_pending_orders = Order::where('user_id', Auth::id())
                ->where('status_id', '!=', 2)
                ->where('status_id', '!=', 1)
                ->count();
            $orders = Order::where('user_id', Auth::id())
                ->with(['orderstatus', 'orderitems:id,order_id,qty'])
                ->latest()
                ->take(5)
                ->get();

            // Resolve pending status IDs for the "Check Status" link filter
            $pendingStatusIds = Order::where('user_id', Auth::id())
                ->where('status_id', '!=', 2)
                ->where('status_id', '!=', 1)
                ->distinct()
                ->pluck('status_id')
                ->toArray();

            store_set_seo('Dashboard');
            return view(baseview('customer/dashboard'), ['total_orders' => $total_orders, 'total_pending_orders' => $total_pending_orders, 'orders' => $orders, 'pendingStatusIds' => $pendingStatusIds]);
        } catch (\Throwable $e) {
            \Log::error('Customer dashboard error: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            $total_orders = 0;
            $total_pending_orders = 0;
            $orders = collect();
            $pendingStatusIds = [];

            store_set_seo('Dashboard');
            return view(baseview('customer/dashboard'), ['total_orders' => $total_orders, 'total_pending_orders' => $total_pending_orders, 'orders' => $orders, 'pendingStatusIds' => $pendingStatusIds]);
        }
    }

    public function orders(Request $request)
    {
        abort_if(tenant('customer_modules') != 'on', 404);

        try {
            // Auto-claim any unassigned orders matching the customer's email
            $this->claimGuestOrdersByEmail();

            $query = Order::where('user_id', Auth::id())
                ->with(['orderstatus', 'orderitems:id,order_id,qty'])
                ->withCount('orderitems');

            // Search by Invoice / Order Number
            if ($request->filled('search')) {
                $search = trim($request->search);
                $cleanSearch = ltrim($search, '#');
                $query->where('invoice_no', 'like', "%{$cleanSearch}%");
            }

            // Order Status Filter
            if ($request->filled('status') && $request->status !== 'all') {
                $query->where('status_id', $request->status);
            }

            // Payment Status Filter (Strict 1-to-1 matching with database value)
            if ($request->has('payment_status') && $request->payment_status !== '' && $request->payment_status !== 'all') {
                $query->where('payment_status', (int) $request->payment_status);
            }

            $orders = $query->latest()->paginate(10);
            $orderStatuses = Category::where('type', 'status')->orderBy('featured', 'ASC')->get();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'html' => view(baseview('customer/orders_table'), ['orders' => $orders])->render(),
                    'total' => $orders->total(),
                ]);
            }

            store_set_seo('Purchase History');
            return view(baseview('customer/orders'), ['orders' => $orders, 'orderStatuses' => $orderStatuses]);
        } catch (\Throwable $e) {
            \Log::error('Customer orders error: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => __('Unable to load orders at this time. Please try again.'),
                    'error' => config('app.debug') ? $e->getMessage() : null,
                ], 500);
            }

            // Graceful fallback to avoid breaking the user interface
            $orders = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10);
            $orderStatuses = collect();
            store_set_seo('Purchase History');
            return view(baseview('customer/orders'), ['orders' => $orders, 'orderStatuses' => $orderStatuses])->with('error', __('Unable to load orders at this time. Please try again.'));
        }
    }

    public function orderview($id)
    {
        abort_if(tenant('customer_modules') != 'on',404);
        $this->claimGuestOrdersByEmail();
        $info=Order::where('user_id',Auth::id())->with('orderitemswithpreview','shippingwithinfo','orderstatus','ordermeta','schedule','reviews')->findorFail($id);
        store_set_seo('Order #' . $info->invoice_no);
        $reviews=[];
        foreach($info->reviews ?? [] as $row){
            $reviews[$row->term_id]['rating']=$row->rating;
            $reviews[$row->term_id]['comment']=$row->comment;
        }
       
        return view(baseview('customer/order'),['info' => $info, 'reviews' => $reviews]);
    }

    public function settings()
    {
        abort_if(tenant('customer_modules') != 'on', 404);
        $user = Auth::user();
        $meta = json_decode($user->meta ?? '');
        $order_settings = get_option('order_settings', true);

        // Extract fname and lname with fallback to splitting user name
        $fname = $meta->fname ?? '';
        $lname = $meta->lname ?? '';
        if ((empty($fname) || empty($lname)) && !empty($user->name)) {
            $nameParts = explode(' ', trim($user->name), 2);
            if (empty($fname)) {
                $fname = $nameParts[0] ?? '';
            }
            if (empty($lname)) {
                $lname = $nameParts[1] ?? '';
            }
        }

        // Country normalization: default USA, convert 'United States of America' / 'Belarus' to USA
        $country = $meta->country ?? 'USA';
        if (empty($country) || in_array(trim($country), ['United States of America', 'United States', 'US', 'Belarus'], true)) {
            $country = 'USA';
        }

        $usStates = [
            'AL' => 'Alabama', 'AK' => 'Alaska', 'AZ' => 'Arizona', 'AR' => 'Arkansas', 'CA' => 'California',
            'CO' => 'Colorado', 'CT' => 'Connecticut', 'DE' => 'Delaware', 'FL' => 'Florida', 'GA' => 'Georgia',
            'HI' => 'Hawaii', 'ID' => 'Idaho', 'IL' => 'Illinois', 'IN' => 'Indiana', 'IA' => 'Iowa',
            'KS' => 'Kansas', 'KY' => 'Kentucky', 'LA' => 'Louisiana', 'ME' => 'Maine', 'MD' => 'Maryland',
            'MA' => 'Massachusetts', 'MI' => 'Michigan', 'MN' => 'Minnesota', 'MS' => 'Mississippi', 'MO' => 'Missouri',
            'MT' => 'Montana', 'NE' => 'Nebraska', 'NV' => 'Nevada', 'NH' => 'New Hampshire', 'NJ' => 'New Jersey',
            'NM' => 'New Mexico', 'NY' => 'New York', 'NC' => 'North Carolina', 'ND' => 'North Dakota', 'OH' => 'Ohio',
            'OK' => 'Oklahoma', 'OR' => 'Oregon', 'PA' => 'Pennsylvania', 'RI' => 'Rhode Island', 'SC' => 'South Carolina',
            'SD' => 'South Dakota', 'TN' => 'Tennessee', 'TX' => 'Texas', 'UT' => 'Utah', 'VT' => 'Vermont',
            'VA' => 'Virginia', 'WA' => 'Washington', 'WV' => 'West Virginia', 'WI' => 'Wisconsin', 'WY' => 'Wyoming',
        ];

        store_set_seo('Settings');
        return view(baseview('customer/settings'), ['meta' => $meta, 'order_settings' => $order_settings, 'fname' => $fname, 'lname' => $lname, 'country' => $country, 'usStates' => $usStates]);
    }

    public function profileUpdate(Request $request)
    {
        try {
            $validationRules = [
                'fname'   => ['required', 'string', 'max:191'],
                'lname'   => ['required', 'string', 'max:191'],
                'phone'   => ['nullable', 'string', 'max:20', 'regex:/^[0-9+\-\s().]+$/'],
                'age'     => ['required', 'string'],
                'state'   => ['nullable', 'string', 'max:50'],
                'city'    => ['nullable', 'string', 'max:100'],
                'address' => ['nullable', 'string', 'max:300'],
                'country' => ['nullable', 'string', 'max:100'],
                'image'   => ['nullable', 'image', 'max:10240'],
            ];

            $validationMessages = [
                'fname.required' => 'First name is required.',
                'lname.required' => 'Last name is required.',
                'phone.regex'    => 'Please enter a valid mobile number.',
                'age.required'   => 'Age is required.',
                'image.image'    => 'The uploaded file must be an image.',
                'image.max'      => 'The image size may not be greater than 10MB.',
            ];

            $request->validate($validationRules, $validationMessages);

            // Normalize Country
            $rawCountry = trim($request->country ?? '');
            if (empty($rawCountry) || in_array($rawCountry, ['United States of America', 'United States', 'US', 'Belarus'], true)) {
                $country = 'USA';
            } else {
                $country = $rawCountry;
            }

            $user = User::findOrFail(Auth::id());
            $firstName = trim($request->fname);
            $lastName = trim($request->lname);
            $user->name = trim($firstName . ' ' . $lastName);
            // Note: Email is readonly and not updated here per security/account standards
            $user->phone = $request->phone;

            // Preserve existing meta structure with exact key names matching registration & login
            $existingMeta = !empty($user->meta) ? json_decode($user->meta, true) : [];
            $existingMeta['fname']   = $firstName;
            $existingMeta['lname']   = $lastName;
            $existingMeta['age']     = $request->age;
            $existingMeta['address'] = $request->address ?? ($existingMeta['address'] ?? '');
            $existingMeta['city']    = $request->city ?? ($existingMeta['city'] ?? '');
            $existingMeta['state']   = $request->state ?? ($existingMeta['state'] ?? '');
            $existingMeta['country'] = $country;

            // Handle Avatar image removal or upload into dedicated uploads/avatar directory
            $removeImage = $request->input('remove_image') == '1' || $request->boolean('remove_image');

            if ($removeImage) {
                try {
                    if (!empty($existingMeta['avatar'])) {
                        $oldFile = $existingMeta['avatar'];
                        if (file_exists($oldFile)) {
                            @unlink($oldFile);
                        } elseif (file_exists(public_path($oldFile))) {
                            @unlink(public_path($oldFile));
                        }
                    }
                } catch (\Throwable) {}
                $existingMeta['avatar'] = null;
            } elseif ($request->hasFile('image')) {
                try {
                    $file = $request->file('image');
                    $avatarDir = 'uploads/avatar/';

                    if (!file_exists($avatarDir)) {
                        @mkdir($avatarDir, 0755, true);
                    }

                    // Delete old avatar file if exists
                    if (!empty($existingMeta['avatar'])) {
                        $oldFile = $existingMeta['avatar'];
                        if (file_exists($oldFile)) {
                            @unlink($oldFile);
                        } elseif (file_exists(public_path($oldFile))) {
                            @unlink(public_path($oldFile));
                        }
                    }

                    $extension = strtolower($file->extension() ?: $file->getClientOriginalExtension());
                    $imageName = 'avatar_' . $user->id . '_' . time() . '.' . $extension;

                    // Native file move directly into uploads/avatar/
                    $file->move($avatarDir, $imageName);

                    $existingMeta['avatar'] = $avatarDir . $imageName;
                } catch (\Throwable) {
                    // Safeguarded against file system exceptions
                }
            }

            $user->meta = json_encode($existingMeta);
            $user->save();

            // Synchronize updated profile to Booostr Central API
            try {
                $client = new Client();
                $apiUrl = env('WP_WEBBUILDER_PROFILE_UPDATE_API_URL') ?: 'https://app.booostr.co/wp-json/webbuilder-api/v1/user-update';

                $clubId = function_exists('tenant') && tenant() ? (tenant('club_id') ?: (tenant('id') ?: '')) : '';

                $client->post($apiUrl, [
                    'verify'  => false,
                    'timeout' => 5,
                    'headers' => ['Accept' => 'application/json'],
                    'json'    => [
                        'email'        => $user->email, // Required (Lookup identifier)
                        'first_name'   => $firstName,
                        'last_name'    => $lastName,
                        'age'          => $request->age,
                        'user_phone'   => $request->phone ?? ($user->phone ?? ''),
                        'user_address' => $request->address ?? '',
                        'user_city'    => $request->city ?? '',
                        'user_state'   => $request->state ?? '',
                        'user_country' => $country,
                        'club'         => (string) $clubId,
                    ],
                ]);
            } catch (\Throwable $e) {
                // Safeguarded against central API sync timeouts or network failures
            }

            return response()->json([
                'status'  => 'success',
                'message' => __('Successfully your information updated...!!'),
            ]);

        } catch (\Illuminate\Validation\ValidationException $ve) {
            throw $ve;
        } catch (\Throwable $e) {
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage() ?: __('Failed to update profile. Please try again.'),
                'errors'  => ['error' => [$e->getMessage() ?: __('Failed to update profile.')]]
            ], 500);
        }
    }

    /**
     * Show Change Password View for Customer Dashboard (Site Builder standard).
     */
    public function changePassword()
    {
        abort_if(tenant('customer_modules') != 'on', 404);
        // Organization/Store owner passwords are managed through Booostr Main App
        if (Auth::user()->role_id == 3) {
            return redirect('/customer/dashboard');
        }
        store_set_seo('Change Password');
        return view(baseview('customer/change-password'));
    }

    public function profilePasswordUpdate(Request $request)
    {
        if (Auth::user()->role_id == 3) {
            return response()->json([
                'status' => 'error',
                'msg' => __('Password for organization manager account is managed through Booostr Main App.')
            ], 403);
        }
        $validatedData = $request->validate([
            'current' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        try {
            $user = Auth::user();

            // 1. Check if current password matches
            if (!\Hash::check($request->current, $user->password)) {
                return response()->json([
                    'errors' => [
                        'current' => [__('The current password you entered is incorrect.')]
                    ]
                ], 422);
            }

            // 2. Ensure new password is not the same as current password
            if (\Hash::check($request->password, $user->password)) {
                return response()->json([
                    'errors' => [
                        'password' => [__('The new password must be different from your current password.')]
                    ]
                ], 422);
            }

            // 3. Update password in tenant database and keep active session logged in
            User::where('id', $user->id)->update([
                'password' => \Hash::make($request->password)
            ]);
            Auth::loginUsingId($user->id);

            // 4. Synchronize password change with Booostr Central API
            try {
                $actualMetaUsername = '';
                if (!empty($user->meta)) {
                    $uMeta = is_array($user->meta) ? $user->meta : json_decode($user->meta, true);
                    if (!empty($uMeta['username'])) {
                        $actualMetaUsername = trim($uMeta['username']);
                    }
                }
                if ($actualMetaUsername === '' && !empty($user->username)) {
                    $actualMetaUsername = trim($user->username);
                }
                if ($actualMetaUsername === '') {
                    $actualMetaUsername = $user->email;
                }

                $client = new Client();
                $client->post('https://app.booostr.co/wp-json/webbuilder-api/v1/boostr-user-reset-password', [
                    'verify' => false,
                    'timeout' => 15,
                    'headers' => ['Accept' => 'application/json'],
                    'json' => [
                        'username' => $actualMetaUsername,
                        'password' => $request->password,
                        'club'     => tenant()->id,
                        'sync_booostr_co' => true
                    ],
                ]);
            } catch (\Throwable $e) {
                \Log::warning('Booostr profile password sync failed: ' . $e->getMessage());
            }

            return response()->json([
                0 => __('Password updated successfully and synchronized with your Booostr account!'),
                'message' => __('Password updated successfully and synchronized with your Booostr account!'),
                'status' => 'success'
            ]);
        } catch (\Throwable $e) {
            \Log::error('Customer password update error: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'message' => __('Unable to update password at this time. Please try again.')
            ], 500);
        }
    }

    public function reviews()
    {
        abort_if(tenant('customer_modules') != 'on',404);
        $posts=Review::where('user_id',Auth::id())->whereHas('order')->with('order')->latest()->paginate(20);
        return view(baseview('customer/reviews'),['posts' => $posts]);
    }

    public function makeReview(Request $request,$orderid,$termid,$optionid)
    {

        $request->validate([
            'rating' => 'required|max:5',
            'feedback' => 'required|max:200',
        ]);

        $order_info=Order::where('user_id',Auth::id())->whereHas('orderitems',fn($q) => $q->where('term_id',$termid))->find($orderid);

        abort_if(empty($order_info) || $request->rating == 0,404);

       $review=Review::where('order_id',$orderid)->where('user_id',Auth::id())->where('term_id',$termid)->first();
       if (empty($review)) {
           $review=new Review;
       }


       $review->user_id=Auth::id();
       $review->order_id=$orderid;
       $review->term_id=$termid;
       $review->rating=$request->rating;
       $review->comment=$request->feedback;
       $review->save();

       $avg_ratings=Review::where('term_id',$termid)->avg('rating');

       $term=Term::findOrFail($termid);
       $term->rating=$avg_ratings;
       $term->save();

       return response()->json('Review Added Successfully');
    }

    /**
     * Show Forgot Password Form for Store Customers.
     */
    public function showForgetPasswordForm()
    {
        abort_if(tenant('customer_modules') != 'on', 404);
        store_set_seo('Forgot Password');
        return view('theme.resto.customer.forget-password');
    }

    /**
     * Send Password Reset Link to Store Customer.
     */
    public function sendUserForgetPasswordMail(Request $request)
    {
        // Support both AJAX and traditional HTTP post seamlessly
        if ($request->ajax()) {
            $validator = \Validator::make($request->all(), [
                'username' => 'required|string|max:191'
            ], [
                'username.required' => __('Username or email is required.'),
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'type' => 'danger',
                    'msg' => $validator->errors()->first(),
                ], 422);
            }
        } else {
            $this->validate($request, [
                'username' => 'required|string|max:191'
            ], [
                'username.required' => __('Username or email is required.'),
            ]);
        }

        $searchQuery = trim($request->username);

        // 1. Check Store Customer accounts first (by email or username)
        $user_info = User::where('email', $searchQuery)->first();

        // Also search in meta username if not found by primary columns
        if (!$user_info && !filter_var($searchQuery, FILTER_VALIDATE_EMAIL)) {
            $allUsers = User::whereNotNull('meta')->get();
            if (!empty($allUsers)) {
                foreach ($allUsers as $u) {
                    $m = json_decode($u->meta, true);
                    if (!empty($m['username']) && strcasecmp($m['username'], $searchQuery) === 0) {
                        $user_info = $u;
                        break;
                    }
                }
            }
        }

        if (!empty($user_info)) {
            $token_id = Str::random(30);
            DB::table('password_resets')->where('email', $user_info->email)->delete();
            DB::table('password_resets')->insert([
                'email' => $user_info->email,
                'token' => $token_id,
                'created_at' => now(),
            ]);

            // Club details directly from native tenant functions (matches Admin Panel)
            $clubInfo = function_exists('tenant_club_info') ? (tenant_club_info() ?: []) : [];
            $clubName = trim((string) ($clubInfo['club_name'] ?? (tenant('id') ?? '')));
            $clubLogo = function_exists('tenant_club_logo') ? tenant_club_logo() : '';
            $supportEmail = trim((string) ($clubInfo['club_email'] ?? ($clubInfo['email'] ?? '')));

            $siteHost = parse_url(url('/'), PHP_URL_HOST) ?: url('/');

            // Extract actual username from meta JSON
            $actualUsername = '';
            if (!empty($user_info->meta)) {
                $userMeta = json_decode($user_info->meta, true);
                if (!empty($userMeta['username'])) {
                    $actualUsername = trim($userMeta['username']);
                }
            }
            if ($actualUsername === '' && !empty($user_info->username)) {
                $actualUsername = trim($user_info->username);
            }
            if ($actualUsername === '') {
                $actualUsername = trim($user_info->name ?? '');
            }

            $resetLink = url('/customer/reset-password/' . urlencode($actualUsername) . '/' . $token_id);
            $resetUrlLink = '<a href="' . $resetLink . '" style="color:#1a73e8; text-decoration:underline;">' . __("Click Here") . '</a>';

            $message_body = "<p>Hello!</p>"
                . "<p>We received a request for a password reset for your user account: @username on @site_url. If you made this request @reset_url to reset your password.</p>"
                . "<p>If this was a mistake or you did not request to reset your password, just ignore this email and nothing will happen.</p>"
                . "<p>Best,<br>Your Friends at @club_name</p>";

            $message = str_replace(
                [
                    '@username',
                    '@name',
                    '@reset_url',
                    '@site_url',
                    '@club_name',
                ],
                [
                    $actualUsername,
                    $user_info->name ?? $actualUsername,
                    $resetUrlLink,
                    $siteHost,
                    $clubName,
                ], 
                $message_body
            );

            $data = [
                'username' => $actualUsername,
                'subject' => 'Password Reset Request for ' . $siteHost,
                'message' => $message,
                'club_name' => $clubName,
                'logo' => $clubLogo,
                'support_email' => $supportEmail,
                'site_url' => url('/'),
            ];

            try {
                Mail::to($user_info->email)->send(new UserResetEmail($data));
            } catch (\Exception $e) {
                \Log::error('Password reset email failed to send', ['error' => $e->getMessage()]);
                if ($request->ajax()) {
                    return response()->json([
                        'status' => 'error',
                        'type' => 'danger',
                        'msg' => $e->getMessage()
                    ], 500);
                }
                return redirect()->back()->with([
                    'type' => 'danger',
                    'msg' => $e->getMessage()
                ]);
            }

            if ($request->ajax()) {
                return response()->json([
                    'status' => 'success',
                    'type' => 'success',
                    'msg' => __('Check Your Mail For Reset Password Link')
                ]);
            }

            return redirect()->back()->with([
                'msg' => __('Check Your Mail For Reset Password Link'),
                'type' => 'success'
            ]);
        }

        // 2. If NOT found in local store, check Booostr User Accounts via API
        $hasBooostrAccount = false;
        try {
            $client = new Client();
            $response = $client->post('https://app.booostr.co/wp-json/webbuilder-api/v1/boostr-user-exists', [
                'verify' => false,
                'timeout' => 8,
                'headers' => [
                    'Accept' => 'application/json',
                ],
                'json' => [
                    'email' => $searchQuery,
                ],
            ]);

            $result = json_decode((string) $response->getBody(), true);
            if (!empty($result['status']) && $result['status'] === 'success' && !empty($result['exists'])) {
                $hasBooostrAccount = true;
            }
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $body = $e->getResponse()->getBody()->getContents();
            $decoded = json_decode($body, true);
            if (!empty($decoded['status']) && $decoded['status'] === 'success' && !empty($decoded['exists'])) {
                $hasBooostrAccount = true;
            }
        } catch (\Exception $e) {
            \Log::warning("Booostr user account check failed: " . $e->getMessage());
        }

        $registerUrl = url('/customer/register');
        if ($hasBooostrAccount) {
            $booostrSuccessMsg = __('We did not find a user account for our site with this email. However, because you have a Booostr User Account using this email, <a href="' . $registerUrl . '" class="text-dark" style="text-decoration: underline; font-weight: 600;">you can register with this site using your Booostr login credentials.</a>');
            if ($request->ajax()) {
                return response()->json([
                    'status' => 'success',
                    'type' => 'success',
                    'msg' => $booostrSuccessMsg,
                    'booostr_register_action' => $registerUrl
                ]);
            }
            return redirect()->back()->with([
                'msg' => $booostrSuccessMsg,
                'type' => 'success',
                'booostr_register_action' => $registerUrl
            ]);
        }

        // 3. If neither Site Builder nor Booostr Account found
        $notFoundMsg = __('There is no existing account with that username/email address found for this site. <a href="' . $registerUrl . '" class="text-dark" style="text-decoration: underline; font-weight: 600;">Please first set up an account for this site here.</a>');
        $helperNotFoundText = __('It is possible you used a separate email to create an account for our site. If so, please try submitting a different email that may have been used to create your account. If that email exists in the system, you will receive a password reset link at that email address. Click that link to reset your password.');
        if ($request->ajax()) {
            return response()->json([
                'status' => 'error',
                'type' => 'danger',
                'msg' => $notFoundMsg,
                'email_not_found' => true,
                'helper_text' => $helperNotFoundText
            ]);
        }
        return redirect()->back()->with([
            'msg' => $notFoundMsg,
            'type' => 'danger',
            'email_not_found' => true,
        ]);
    }

    /**
     * Show Reset Password Form with token and username.
     */
    public function showResetPasswordForm($username, $token)
    {
        abort_if(tenant('customer_modules') != 'on', 404);

        $tokenRecord = DB::table('password_resets')
            ->where('token', $token)
            ->first();

        $tokenValid = !empty($tokenRecord);

        store_set_seo('Reset Password');

        return view('theme.resto.customer.reset-password', [
            'username' => $username,
            'token' => $token,
            'tokenValid' => $tokenValid,
        ]);
    }

    /**
     * Process User Password Reset.
     */
    public function UserResetPassword(Request $request)
    {
        $this->validate($request, [
            'token' => 'required',
            'username' => 'required',
            'password' => 'required|string|min:8|confirmed'
        ], [
            'password.required' => __('Password is required.'),
            'password.min' => __('Password must be at least 8 characters.'),
            'password.confirmed' => __('Password confirmation does not match.'),
        ]);

        $usernameInput = trim($request->username);
        $user_info = User::where('email', $usernameInput)->first();

        // Also search in meta username if not found directly
        if (!$user_info) {
            $allUsers = User::whereNotNull('meta')->get();
            if (!empty($allUsers)) {
                foreach ($allUsers as $u) {
                    $m = json_decode($u->meta, true);
                    if (!empty($m['username']) && strcasecmp($m['username'], $usernameInput) === 0) {
                        $user_info = $u;
                        break;
                    }
                }
            }
        }

        if (empty($user_info)) {
            return redirect()->back()->with([
                'msg' => __('User account not found'),
                'type' => 'danger'
            ]);
        }

        $user = User::findOrFail($user_info->id);
        $token_iinfo = DB::table('password_resets')->where(['email' => $user_info->email, 'token' => $request->token])->first();

        if (empty($token_iinfo)) {
            return redirect()->back()->with([
                'msg' => __('Invalid or expired reset token! Please request a new reset link.'),
                'type' => 'danger'
            ]);
        }

        // Call Booostr API to update password in Booostr Main & Frontend databases
        try {
            $actualMetaUsername = '';
            if (!empty($user_info->meta)) {
                $uMeta = json_decode($user_info->meta, true);
                if (!empty($uMeta['username'])) {
                    $actualMetaUsername = trim($uMeta['username']);
                }
            }
            if ($actualMetaUsername === '' && !empty($user_info->username)) {
                $actualMetaUsername = trim($user_info->username);
            }
            if ($actualMetaUsername === '') {
                $actualMetaUsername = $user_info->email;
            }

            $client = new Client();
            $response = $client->post('https://app.booostr.co/wp-json/webbuilder-api/v1/boostr-user-reset-password', [
                'verify' => false,
                'timeout' => 15,
                'headers' => [
                    'Accept' => 'application/json',
                ],
                'json' => [
                    'username' => $actualMetaUsername,
                    'password' => $request->password,
                    'club'     => tenant()->id,
                    'sync_booostr_co' => true
                ],
            ]);

            $result = json_decode((string) $response->getBody(), true);
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $errorBody = json_decode($e->getResponse()->getBody()->getContents(), true);
            $errorMsg = $errorBody['message'] ?? __('Unable to update password. Please try again or request a new reset link.');
            return redirect()->back()->with([
                'msg' => $errorMsg,
                'type' => 'danger'
            ]);
        } catch (\Exception $e) {
            \Log::warning("Booostr password reset API call failed: " . $e->getMessage());
        }

        // Check if API response was successful (or if Booostr call completed)
        $isApiSuccess = (!empty($result['success']) && !empty($result['data']['status']) && $result['data']['status'] === 'success')
            || (!empty($result['status']) && $result['status'] === 'success');

        if (isset($result) && !$isApiSuccess) {
            $msg = $result['data']['msg'] ?? ($result['message'] ?? __('Unable to update password. Please try again.'));
            return redirect()->back()->with([
                'msg' => $msg,
                'type' => 'danger'
            ]);
        }

        // Update local Site Builder tenant database (only for normal customers, protect seller fixed password)
        if ($user->role_id != 3) {
            $user->password = Hash::make($request->password);
        }
        if (!empty($result['data']['user']['user_id'])) {
            $userMeta = !empty($user->meta) ? json_decode($user->meta, true) : [];
            if (empty($userMeta['wpuid'])) {
                $userMeta['wpuid'] = (int) $result['data']['user']['user_id'];
                $user->meta = json_encode($userMeta);
            }
        }
        $user->save();

        // Delete used token from password_resets
        DB::table('password_resets')->where('email', $user_info->email)->delete();

        return redirect('/customer/login')->with([
            'msg' => __('Password Changed Successfully'),
            'type' => 'success'
        ]);
    }

    /**
     * Automatically claims unassigned guest orders matching the customer's email address.
     * Only affects orders with user_id NULL or 0 in the tenant store.
     *
     * @param \App\Models\User|null $user
     * @return int
     */
    protected function claimGuestOrdersByEmail($user = null)
    {
        try {
            $user = $user ?: Auth::user();
            if (!$user || empty($user->email)) {
                return 0;
            }

            $userEmail = strtolower(trim($user->email));

            // Fast check: Are there any orders with user_id NULL or 0?
            $unassignedOrderIds = Order::where(function ($q) {
                $q->whereNull('user_id')->orWhere('user_id', 0);
            })->pluck('id')->toArray();

            if (empty($unassignedOrderIds)) {
                return 0;
            }

            // Look up orderinfo metadata for these unassigned orders
            $orderMetas = Ordermeta::whereIn('order_id', $unassignedOrderIds)
                ->where('key', 'orderinfo')
                ->get(['order_id', 'value']);

            $claimedIds = [];
            foreach ($orderMetas as $meta) {
                if (empty($meta->value)) {
                    continue;
                }
                $info = json_decode($meta->value, true);
                if (is_array($info) && !empty($info['email'])) {
                    if (strtolower(trim($info['email'])) === $userEmail) {
                        $claimedIds[] = (int) $meta->order_id;
                    }
                }
            }

            if ($claimedIds !== []) {
                $claimedIds = array_unique($claimedIds);
                Order::whereIn('id', $claimedIds)
                    ->where(function ($q) {
                        $q->whereNull('user_id')->orWhere('user_id', 0);
                    })
                    ->update(['user_id' => $user->id]);

                \Log::info("Claimed guest orders for customer", [
                    'user_id' => $user->id,
                    'email' => $userEmail,
                    'claimed_order_ids' => $claimedIds,
                ]);

                return count($claimedIds);
            }
        } catch (\Throwable $e) {
            \Log::error("Error linking guest orders to customer: " . $e->getMessage(), [
                'user_id' => $user->id ?? null,
                'email' => $user->email ?? null,
            ]);
        }

        return 0;
    }
}
