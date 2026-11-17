<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Order;
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
        
        $home_data=optionfromcache('login_page');

        $seo=$home_data->seo ?? '';
        SEOMeta::setTitle($seo->site_title ?? '');
        SEOMeta::setDescription($seo->description ?? '');
       

        OpenGraph::setDescription($seo->description ?? '');
        OpenGraph::setTitle($seo->site_title ?? '');

        OpenGraph::addProperty('keywords', $seo->tags ?? '');

        TwitterCard::setTitle($seo->site_title ?? '');
        TwitterCard::setSite($seo->twitter_title ?? '');

        JsonLd::setTitle($seo->site_title ?? '');
        JsonLd::setDescription($seo->description ?? '');
        JsonLd::addImage($seo->meta_image ?? '');

        SEOTools::setTitle($seo->site_title ?? '');
        SEOTools::setDescription($seo->description ?? '');
        SEOTools::opengraph()->setUrl(url('/'));
        SEOTools::setCanonical(url('/'));
        
        SEOTools::twitter()->setSite($seo->twitter_title ?? '');
        SEOTools::jsonLd()->addImage($seo->meta_image ?? '');
        SEOTools::opengraph()->addProperty('keywords', $seo->tags ?? '');

        
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

        $rules = [
            'email' => 'required|string',
            'password' => 'required|string|min:6',
        ];

        $messages = [
            'email.required' => __('Username or email is required.'),
            'password.required' => __('Password is required.'),
            'password.min' => __('Password must be at least 6 characters.'),
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

                    $localUser->name = $wpUser->user_nicename ?? ($wpUser->display_name ?? $wpUser->user_login ?? $localUser->name);
                    $localUser->password = \Hash::make($password);
                    if (!empty($wpUser->user_phone)) {
                        $localUser->phone = $wpUser->user_phone;
                    }

                    $incomingCountry = $wpUser->user_country ?? ($existingMeta['country'] ?? 'USA');
                    if (empty($incomingCountry) || in_array(trim($incomingCountry), ['United States of America', 'United States', 'US'], true)) {
                        $incomingCountry = 'USA';
                    }

                    $existingMeta = !empty($localUser->meta) ? json_decode($localUser->meta, true) : [];
                    $mergedMeta = array_merge($existingMeta, [
                        'wpuid' => (int) ($wpUser->user_id ?? ($existingMeta['wpuid'] ?? 0)),
                        'username' => $wpUser->user_login ?? ($existingMeta['username'] ?? $username),
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
            Auth::login($authenticatedUser, $remember);

            // Redirect user to intended checkout / dashboard destination
            if ($authenticatedUser->role_id == 3) {
                $redirectUrl = url('/seller/dashboard');
            } else {
                $redirectUrl = session()->pull('url.intended', url('/customer/dashboard'));
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

        $home_data=optionfromcache('register_page');
        $seo=$home_data->seo ?? '';
        SEOMeta::setTitle($seo->site_title ?? '');
        SEOMeta::setDescription($seo->description ?? '');
       

        OpenGraph::setDescription($seo->description ?? '');
        OpenGraph::setTitle($seo->site_title ?? '');

        OpenGraph::addProperty('keywords', $seo->tags ?? '');

        TwitterCard::setTitle($seo->site_title ?? '');
        TwitterCard::setSite($seo->twitter_title ?? '');

        JsonLd::setTitle($seo->site_title ?? '');
        JsonLd::setDescription($seo->description ?? '');
        JsonLd::addImage($seo->meta_image ?? '');

        SEOTools::setTitle($seo->site_title ?? '');
        SEOTools::setDescription($seo->description ?? '');
        SEOTools::opengraph()->setUrl(url('/'));
        SEOTools::setCanonical(url('/'));
        
        SEOTools::twitter()->setSite($seo->twitter_title ?? '');
        SEOTools::jsonLd()->addImage($seo->meta_image ?? '');
        SEOTools::opengraph()->addProperty('keywords', $seo->tags ?? '');

        
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

        return redirect()->intended('/customer/dashboard');
    }

    public function dashboard()
    {
        abort_if(tenant('customer_modules') != 'on',404);
        $total_orders=Order::where('user_id',Auth::id())->count();
        $total_pending_orders=Order::where('user_id',Auth::id())->where('status_id','!=',2)->where('status_id','!=',1)->count();
        $orders=Order::where('user_id',Auth::id())->with('orderstatus')->latest()->take(5)->get();
        
        SEOMeta::setTitle('Dashboard');
        return view(baseview('customer/dashboard'),compact('total_orders','total_pending_orders','orders'));
    }

    public function orders()
    {
        abort_if(tenant('customer_modules') != 'on',404);
        $orders=Order::where('user_id',Auth::id())->with('orderstatus')->withCount('orderitems')->latest()->paginate(10);
        SEOMeta::setTitle('Orders');
        return view(baseview('customer/orders'),compact('orders'));
    }

    public function orderview($id)
    {
        abort_if(tenant('customer_modules') != 'on',404);
        $info=Order::where('user_id',Auth::id())->with('orderitemswithpreview','shippingwithinfo','orderstatus','ordermeta','schedule','reviews')->findorFail($id);
        SEOMeta::setTitle($info->invoice_no);
        $reviews=[];
        foreach($info->reviews ?? [] as $row){
            $reviews[$row->term_id]['rating']=$row->rating;
            $reviews[$row->term_id]['comment']=$row->comment;
        }
       
        return view(baseview('customer/order'),compact('info','reviews'));
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
        if (empty($fname) && empty($lname) && !empty($user->name)) {
            $nameParts = explode(' ', trim($user->name), 2);
            $fname = $nameParts[0] ?? '';
            $lname = $nameParts[1] ?? '';
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

        SEOMeta::setTitle('Settings');
        return view(baseview('customer/settings'), compact('meta', 'order_settings', 'fname', 'lname', 'country', 'usStates'));
    }

    public function profileUpdate(Request $request)
    {
        $validationRules = [
            'fname'   => ['required', 'string', 'max:191'],
            'lname'   => ['required', 'string', 'max:191'],
            'phone'   => ['nullable', 'string', 'max:20', 'regex:/^[0-9+\-\s().]+$/'],
            'age'     => ['required', 'string'],
            'state'   => ['nullable', 'string', 'max:50'],
            'city'    => ['nullable', 'string', 'max:100'],
            'address' => ['nullable', 'string', 'max:300'],
            'country' => ['nullable', 'string', 'max:100'],
            'image'   => ['nullable', 'image', 'max:2048'],
        ];

        $validationMessages = [
            'fname.required' => 'First name is required.',
            'lname.required' => 'Last name is required.',
            'phone.regex'    => 'Please enter a valid mobile number.',
            'age.required'   => 'Age is required.',
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

        // Handle Avatar image upload with compression
        if ($request->hasFile('image')) {
            try {
                $file = $request->file('image');
                $uploadDir = public_path('uploads/' . tenant('id') . '/');
                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                // Delete old avatar file if exists to prevent disk clutter
                if (!empty($existingMeta['avatar']) && file_exists(public_path($existingMeta['avatar']))) {
                    @unlink(public_path($existingMeta['avatar']));
                }

                $imageName = 'avatar_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
                $targetPath = $uploadDir . $imageName;

                // Compress and fit image to 400x400 with 80% quality
                $img = \Image::make($file->getRealPath());
                $img->fit(400, 400, function ($constraint) {
                    $constraint->upsize();
                });
                $img->save($targetPath, 80);

                $existingMeta['avatar'] = 'uploads/' . tenant('id') . '/' . $imageName;
            } catch (\Throwable $e) {
                \Log::warning('Avatar image upload or compression failed: ' . $e->getMessage());
            }
        }

        $user->meta = json_encode($existingMeta);
        $user->save();

        // Background synchronization to Booostr Central API (Disabled for now until Central endpoint is created/live)
        if (false) {
            try {
                $apiUsername = !empty($existingMeta['username']) ? $existingMeta['username'] : ($user->username ?: $user->email);
                $client = new \GuzzleHttp\Client();
                $apiUrl = env('WP_WEBBUILDER_PROFILE_UPDATE_API_URL') ?: 'https://app.booostr.co/wp-json/webbuilder-api/v1/user-update';

                $client->post($apiUrl, [
                    'verify' => false,
                    'timeout' => 10,
                    'headers' => ['Accept' => 'application/json'],
                    'json' => [
                        'wpuid'     => (int) ($existingMeta['wpuid'] ?? 0),
                        'username'  => $apiUsername,
                        'fname'     => $firstName,
                        'lname'     => $lastName,
                        'name'      => $user->name,
                        'email'     => $user->email,
                        'phone'     => $user->phone,
                        'age'       => $request->age,
                        'address'   => $request->address ?? '',
                        'city'      => $request->city ?? '',
                        'state'     => $request->state ?? '',
                        'country'   => $country,
                        'club'      => (string) (tenant('id') ?? ''),
                    ],
                ]);
            } catch (\Throwable $e) {
                \Log::warning('Booostr Central profile sync background call: ' . $e->getMessage());
            }
        }

        return response()->json('Successfully your information updated...!!');
    }

    /**
     * Show Change Password View for Customer Dashboard (Site Builder standard).
     */
    public function changePassword()
    {
        abort_if(tenant('customer_modules') != 'on', 404);
        SEOMeta::setTitle(__('Change Password'));
        return view(baseview('customer/change-password'));
    }

    public function profilePasswordUpdate(Request $request)
    {
        $validatedData = $request->validate([
            'current' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $check = \Hash::check($request->current, auth()->user()->password);
        
        if ($check == true) {
            User::where('id', Auth::id())->update(['password' => \Hash::make($request->password)]);

            // Synchronize password change with Booostr Central API
            try {
                $user = Auth::user();
                $uMeta = !empty($user->meta) ? json_decode($user->meta, true) : [];
                $apiUsername = !empty($uMeta['username']) ? $uMeta['username'] : ($user->username ?: $user->email);
                
                $client = new Client();
                $client->post('https://app.booostr.co/wp-json/webbuilder-api/v1/boostr-user-reset-password', [
                    'verify' => false,
                    'timeout' => 10,
                    'headers' => ['Accept' => 'application/json'],
                    'json' => [
                        'username' => $apiUsername,
                        'password' => $request->password,
                        'club'     => tenant()->id,
                    ],
                ]);
            } catch (\Throwable $e) {
                \Log::warning('Booostr profile password sync failed: ' . $e->getMessage());
            }

            return response()->json(['Password Changed Successfully']); 
        }
        else {
            $error['errors']['current'] = __('Current Password does not match our records.');
            return response()->json($error, 422);
        }
    }

    public function reviews()
    {
        abort_if(tenant('customer_modules') != 'on',404);
        $posts=Review::where('user_id',Auth::id())->whereHas('order')->with('order')->latest()->paginate(20);
        return view(baseview('customer/reviews'),compact('posts'));
    }

    public function makeReview(Request $request,$orderid,$termid,$optionid)
    {

        $request->validate([
            'rating' => 'required|max:5',
            'feedback' => 'required|max:200',
        ]);

        $order_info=Order::where('user_id',Auth::id())->whereHas('orderitems',function($q) use ($termid) {
            return $q->where('term_id',$termid);
        })->find($orderid);

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
        SEOMeta::setTitle(__('Forgot Password'));
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

        SEOMeta::setTitle(__('Reset Password'));

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

        // Update local Site Builder tenant database
        $user->password = Hash::make($request->password);
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
}
