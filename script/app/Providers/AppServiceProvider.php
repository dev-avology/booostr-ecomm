<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        if (app()->environment('production') && config('app.debug') === true) {
            Log::critical('SECURITY WARNING: APP_DEBUG is TRUE in production environment.');
        }

        try {
            $key = get_secret('AWS_ACCESS_KEY_ID');
            $secret = get_secret('AWS_SECRET_ACCESS_KEY');
            $region = get_secret('AWS_DEFAULT_REGION', 'us-east-1');

            if (!empty($key) && !empty($secret)) {
                Config::set('services.ses.key', $key);
                Config::set('services.ses.secret', $secret);
                Config::set('services.ses.region', $region);
            }
        } catch (\Throwable $e) {
            Log::error('SES DB credentials load failed: ' . $e->getMessage());
        }

        try {
            $mailDriver = config('mail.driver', env('MAIL_MAILER', 'smtp'));

            if ($mailDriver === 'smtp') {
                $username = get_secret('MAIL_USERNAME');
                $password = get_secret('MAIL_PASSWORD');

                if (!empty($username) && !empty($password)) {
                    Config::set('mail.username', $username);
                    Config::set('mail.password', $password);
                }
            }
        } catch (\Throwable $e) {
            Log::error('SMTP DB credentials load failed: ' . $e->getMessage());
        }
    }
}