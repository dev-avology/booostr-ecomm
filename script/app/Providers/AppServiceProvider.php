<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        
        // ✅ ALERT IF DEBUG MODE IS ENABLED IN PRODUCTION
        if (app()->environment('production') && config('app.debug') === true) {
            Log::critical('⚠ SECURITY WARNING: APP_DEBUG is TRUE in production environment.');
        }                                                                                                                                                                                      
        
        try {
            $key    = get_secret('AWS_ACCESS_KEY_ID');
        
            $secret = get_secret('AWS_SECRET_ACCESS_KEY');
      
            $region = get_secret('AWS_DEFAULT_REGION', 'us-east-1');
            
            if (!empty($key) && !empty($secret)) {
                Config::set('services.ses.key', $key);
                Config::set('services.ses.secret', $secret);
                Config::set('services.ses.region', $region);
    
                // metadata call band (non-EC2 me error stop)
                putenv('AWS_EC2_METADATA_DISABLED=true');
            }
        } catch (\Throwable $e) {
           Log::error('SES DB credentials load failed: ' . $e->getMessage());
        }
    

    
    
    }
}
