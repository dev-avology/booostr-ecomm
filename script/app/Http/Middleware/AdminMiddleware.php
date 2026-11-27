<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (Auth::check() && Auth::User()->role_id == 1) {
            
            $centralDomain = config('app.protocoless_url') ?: (env('APP_PROTOCOLESS_URL') ?: (parse_url((string) config('app.url'), PHP_URL_HOST) ?: 'booostrstore.co'));
            if (str_replace('www.', '', $request->getHost()) != $centralDomain) {
                Auth::logout();
                abort(404);
            }
            if (Auth::user()->status != 1) {
                Auth::logout();
                return redirect()->route('login');
            }
            return $next($request);
           
        }
        return redirect()->route('login'); 
    }
}
