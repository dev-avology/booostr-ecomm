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
class CustomerController extends Controller
{
    /**
     * Create a new controller instance.
     * Prevents Club Owners (role_id == 3) from accessing customer portal pages,
     * redirecting them directly to their Seller Dashboard safely without redirect loops.
     */
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (Auth::check() && Auth::user()->role_id == 3) {
                return redirect('/seller/dashboard');
            }
            return $next($request);
        })->except(['subscribe', 'loginCustomer']);
    }

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

        $clubSlug = (string) (tenant('club_id') ?: tenant('id') ?? '');
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

                    $existingMeta = !empty($localUser->meta) ? json_decode($localUser->meta, true) : [];
                    $mergedMeta = array_merge($existingMeta, [
                        'wpuid' => (int) ($wpUser->user_id ?? ($existingMeta['wpuid'] ?? 0)),
                        'username' => $wpUser->user_login ?? ($existingMeta['username'] ?? $username),
                        'age' => $wpUser->age ?? ($existingMeta['age'] ?? 'adult'),
                        'country' => $wpUser->user_country ?? ($existingMeta['country'] ?? 'United States of America'),
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
     * 5. Dispatches a branded HTML welcome email (CustomerAccountCreatedMail) with club name and login link.
     * 6. Automatically authenticates the session and redirects to the intended checkout or customer dashboard.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function registerCustomer(Request $request)
    {
        abort_if(tenant('customer_modules') != 'on',404);

        // Support both fname/lname and legacy single-name input for 100% backwards compatibility
        if ($request->filled('fname') || $request->filled('lname') || $request->has('g-recaptcha-response')) {
            $validationRules = [
                'fname' => ['required', 'string', 'max:191'],
                'lname' => ['required', 'string', 'max:191'],
                'email' => ['required', 'string', 'email', 'max:191', 'unique:users'],
                'username' => ['required', 'string', 'max:191'],
                'password' => ['required', 'string', 'min:8', 'confirmed'],
                'age' => ['required', 'string'],
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
            ];

            $recaptchaSecret = config('services.recaptcha.secret_key') ?: env('RECAPTCHA_SECRET_KEY');
            if (!empty($recaptchaSecret)) {
                $validationRules['g-recaptcha-response'] = ['required'];
                $validationMessages['g-recaptcha-response.required'] = 'Please verify that you are not a robot.';
            }

            if ($request->has('agree_terms') || $request->filled('fname')) {
                $validationRules['agree_terms'] = ['accepted'];
                $validationMessages['agree_terms.accepted'] = 'Please agree to the Terms and Conditions and Privacy Policy.';
                $validationMessages['agree_terms.required'] = 'Please agree to the Terms and Conditions and Privacy Policy.';
            }

            $request->validate($validationRules, $validationMessages);

            // Verify reCAPTCHA with Google
            if (!empty($recaptchaSecret) && $request->filled('g-recaptcha-response')) {
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
                            'g-recaptcha-response' => 'reCAPTCHA verification failed. Please try again.',
                        ])->withInput();
                    }
                } catch (\Throwable $e) {
                    \Log::error('reCAPTCHA verification error', ['error' => $e->getMessage()]);
                }
            }

            $firstName = trim($request->fname);
            $lastName = trim($request->lname);
            $name = $firstName . ' ' . $lastName;
            $username = trim($request->username);
            $age = (string) $request->age;
        } else {
            $request->validate([
                'name' => 'required|max:255',
                'email' => 'required|email|unique:users',
                'password' => 'required|confirmed',
            ]);

            $name = trim($request->name);
            $parts = explode(' ', $name);
            $firstName = $parts[0] ?? $name;
            $lastName = isset($parts[1]) ? implode(' ', array_slice($parts, 1)) : $firstName;
            $username = explode('@', $request->email)[0] ?? $name;
            $age = 'adult';
        }

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

        $metaData = [
            'wpuid' => $wpuid,
            'username' => $username,
            'fname' => $firstName,
            'lname' => $lastName,
            'age' => $age,
            'country' => $wpUser->user_country ?? 'United States of America',
            'city' => $wpUser->user_city ?? '',
            'state' => $wpUser->user_state ?? '',
            'address' => $wpUser->user_address ?? '',
        ];
        $user->meta = json_encode($metaData);
        $user->save();

        // Send Welcome Email to newly registered customer
        try {
            $clubInfo = function_exists('tenant_club_info') ? (tenant_club_info() ?: []) : [];
            $clubName = trim((string) ($clubInfo['club_name'] ?? ''));
            if ($clubName === '') {
                $clubName = trim((string) (tenant('store_name') ?? ''));
            }
            if ($clubName === '') {
                $clubName = ucwords(str_replace('-', ' ', (string) tenant('id')));
            }

            $clubLogo = '';
            $bannerLogo = get_option('banner_logo');
            if (!empty($bannerLogo)) {
                $clubLogo = asset($bannerLogo);
            } elseif (!empty($clubInfo['club_logo'])) {
                $clubLogo = $clubInfo['club_logo'];
            }

            $mailData = [
                'subject' => $firstName . ', your ' . $clubName . ' account has been created',
                'name' => $name,
                'first_name' => $firstName,
                'username' => $username,
                'email' => $request->email,
                'club_name' => $clubName,
                'site_url' => url('/'),
                'login_url' => url('/customer/login'),
                'support_email' => $clubInfo['club_email'] ?? ($clubInfo['email'] ?? null),
                'logo' => $clubLogo,
                'site_url_text' => str_replace(['https://', 'http://'], '', url('/')),
            ];

            \Illuminate\Support\Facades\Mail::to($request->email)->send(new \App\Mail\CustomerAccountCreatedMail($mailData));
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
        abort_if(tenant('customer_modules') != 'on',404);
        $meta=json_decode(Auth::user()->meta ?? '');
        $order_settings=get_option('order_settings',true);
        SEOMeta::setTitle('Settings');
        return view(baseview('customer/settings'),compact('meta','order_settings'));
    }

    public function profileUpdate(Request $request)
    {
        $request->validate([
            'name' => 'required|max:100',
            'email' => 'required|max:50|email|unique:users,email,' . Auth::user()->id,
            'phone' => 'required|max:20',
            'address' => 'max:300',
            'post_code' => 'max:10',
            'lat' => 'max:300',
            'long' => 'max:300',
        ]);

        $data['address']=$request->address;
        $data['post_code']=$request->post_code;
        $data['lat']=$request->lat;
        $data['long']=$request->long;

        $user= User::findOrFail(Auth::user()->id);
        $user->name=$request->name;
        $user->email=$request->email;
        $user->phone=$request->phone;
        $user->meta=json_encode($data);
        $user->save();

        return response()->json('Successfully your information updated...!!');
    }

    public function profilePasswordUpdate(Request $request)
    {
        $validatedData = $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],

        ]);
        $info=User::where('id',Auth::id())->first();

        $check=\Hash::check($request->current, auth()->user()->password);
        
        if ($check==true) {
            User::where('id',Auth::id())->update(['password'=>\Hash::make($request->password)]);

            return response()->json(['Password Changed']); 

        }
        else{
            $error['errors']['domain']='Enter Valid Password';
            return response()->json($error,422);
         
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
}
