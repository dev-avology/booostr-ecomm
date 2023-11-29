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
    public function handle(Request $request,Closure $next)
    {
        if (empty($request->header('Apitoken')) || strtolower($request->header('Apitoken'))!=$this->token) {
            $response = [
                'status' => 'error',
                'message' => 'Sorry, you seems spam, and we blocked you',
            ];
            return response()->json($response, 413);
        }else{

           if($request->hasHeader('X-Tenant')){
                if(get_option('tax') == "" || (float)get_option('tax') == 0){
                    $response = [
                        'status' => 'error',
                        'message' => 'Oops the store is temporary disabled.Set Sales Tax in Store Setting...!!',
                    ];
                    return response()->json($response, 413);
               }
           }


            return $next($request);
        }
    }
}
