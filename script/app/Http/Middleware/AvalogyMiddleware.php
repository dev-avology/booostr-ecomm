<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
class AvalogyMiddleware
{
    private $token="gjhgjhghvnasd1231";
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request)
    {
        if (empty($request->header('ADMIN_TOKEN')) || strtolower($request->header('ADMIN_TOKEN'))!=$this->token) {
            exit("Sorry, you seems spam, and we blocked you");
        }
    }
}
