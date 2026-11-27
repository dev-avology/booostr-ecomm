<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class IsinstalledMiddleware
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
        if (!empty(config('database.connections.mysql.database')) || !empty(env('DB_DATABASE'))) {
            return $next($request);
        }
        return redirect('/install');
        
    }
}
