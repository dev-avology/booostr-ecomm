<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Auth;
class Usermiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::user() &&  Auth::user()->status == 1) {
            $user = Auth::user();

            if ($user->role_id == 3) {
                if (!$request->is('seller*') && !$request->is('logout') && !$request->is('api*')) {
                    return redirect('/seller/dashboard');
                }
            } elseif ($user->role_id == 5) {
                if (!$request->is('rider*') && !$request->is('logout') && !$request->is('api*')) {
                    return redirect('/rider/dashboard');
                }
            } elseif ($user->role_id == 1) {
                if (!$request->is('admin*') && !$request->is('logout') && !$request->is('api*')) {
                    return redirect()->route('admin.dashboard');
                }
            } elseif ($user->role_id == 2) {
                if (!$request->is('partner*') && !$request->is('logout') && !$request->is('api*')) {
                    return redirect()->route('merchant.dashboard');
                }
            }

            return $next($request);
        }
        else{
           \Session::flash('error','Oops your account is disabled please contact with support');

           Auth::logout();
           return redirect('/login');
        }
        
    }
}
