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
            if (Auth::user()->role_id == 3) {
                return redirect('/seller/dashboard');
            } elseif (Auth::user()->role_id == 5) {
                return redirect('/rider/dashboard');
            } elseif (Auth::user()->role_id == 1) {
                return redirect()->route('admin.dashboard');
            } elseif (Auth::user()->role_id == 2) {
                return redirect()->route('merchant.dashboard');
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
