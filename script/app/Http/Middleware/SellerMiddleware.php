<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Auth;
class SellerMiddleware
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
         if (Auth::check() && Auth::User()->role_id == 3 && session('seller_session_active') === true) {
            if (Auth::user()->status != 1) {
                \Session::flash('error','Oops your account is disabled please contact with support');
                Auth::logout();
                return redirect()->away('https://app.booostr.co');
            }
            return $next($request);
           
        }
         return redirect()->away('https://app.booostr.co'); 
    }
}
