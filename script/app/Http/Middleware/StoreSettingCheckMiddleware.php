<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class StoreSettingCheckMiddleware
{
    private $token="gjhgjhghvnasd1231";

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (empty($request->header('Apitoken')) || strtolower($request->header('Apitoken'))!=$this->token) {
            $response = [
                'status' => 'error',
                'message' => 'Sorry, you seems spam, and we blocked you',
            ];
            return response()->json($response, 413);
        }else{

            if($request->hasHeader('X-Tenant')){
                if(get_option('tax') == "" || (float)get_option('tax') == 0 || showAddressError()){
                    $response = [
                        'status' => 'error',
                        'message' => 'Oops the store is temporary disabled. Complete your store setting in store manager.',
                    ];
                    return response()->json($response, 413);
               }
           }


            return $next($request);
        }
    }
}
