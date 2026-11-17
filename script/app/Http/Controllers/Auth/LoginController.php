<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Auth;
use Session;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    //protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function credentials(Request $request)
    {
        $login = $request->input('email');
        $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'name';
        return [$field => $login, 'password' => $request->input('password')];
    }

    protected function authenticated(Request $request, $user)
    {
        if (Session::has('url.intended')) {
            return redirect(Session::pull('url.intended'));
        }
    }

    public function redirectTo()
    {
        if (Auth::user()->role_id==1) {
          return $this->redirectTo=route('admin.dashboard');
        }
        elseif (Auth::user()->role_id==2) {
                  
           return $this->redirectTo=route('merchant.dashboard');
       }
       elseif (Auth::user()->role_id==3) {
                  
           return $this->redirectTo=url('/seller/dashboard');
       }elseif(Auth::user()->role_id==4)
       {
            return $this->redirectTo=url('/customer/dashboard');
       }elseif(Auth::user()->role_id==4)
       {
            return $this->redirectTo=url('/rider/dashboard');
       }
      
       $this->middleware('guest')->except('logout');
    }

    /**
     * Log the user out of the application and redirect based on role.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        $roleId = Auth::check() ? Auth::user()->role_id : null;

        $this->guard()->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        if ($response = $this->loggedOut($request)) {
            return $response;
        }

        if ($request->wantsJson()) {
            return new \Illuminate\Http\JsonResponse([], 204);
        }

        // Role-based post-logout redirection
        if ($roleId == 3) {
            // Seller / Store Admin redirects to seller login
            return redirect('/seller/login');
        } elseif ($roleId == 5) {
            // Rider redirects to rider login
            return redirect('/rider/login');
        } elseif ($roleId == 1) {
            // Landlord / Super admin
            return redirect('/login');
        }

        // Storefront Customer default redirect
        return redirect('/customer/login');
    }
}
