<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        then: function () {
            Route::middleware('web')
                ->namespace('App\Http\Controllers')
                ->group(base_path('routes/web.php'));

            Route::prefix('api')
                ->middleware('api')
                ->namespace('App\Http\Controllers')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->namespace('App\Http\Controllers')
                ->group(base_path('routes/additional.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->append(\App\Http\Middleware\AllowIframe::class);

        $middleware->alias([
            'auth' => \App\Http\Middleware\Authenticate::class,
            'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
            'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
            'can' => \Illuminate\Auth\Middleware\Authorize::class,
            'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
            'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
            'signed' => \Illuminate\Routing\Middleware\ValidateSignature::class,
            'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
            'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
            'InitializeTenancyByPath' => \Stancl\Tenancy\Middleware\InitializeTenancyByPath::class,
            'InitializeTenancyByDomain' => \Stancl\Tenancy\Middleware\InitializeTenancyByDomain::class,
            'PreventAccessFromCentralDomains' => \Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains::class,
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'author' => \App\Http\Middleware\AuthorMiddleware::class,
            'rider' => \App\Http\Middleware\RiderMiddleware::class,
            'subdomain' => \App\Http\Middleware\SubdomainMiddleware::class,
            'customdomain' => \App\Http\Middleware\CustomdomainMiddleware::class,
            'merchant' => \App\Http\Middleware\MerchantMiddleware::class,
            'seller' => \App\Http\Middleware\SellerMiddleware::class,
            'tenantenvironment' => \App\Http\Middleware\Tenantenvironment::class,
            'user' => \App\Http\Middleware\Usermiddleware::class,
            'Isinstalled' => \App\Http\Middleware\IsinstalledMiddleware::class,
            'Avalogy' => \App\Http\Middleware\AvalogyMiddleware::class,
            'Storecheck' => \App\Http\Middleware\StoreSettingCheckMiddleware::class,
            'page.cache' => \App\Http\Middleware\PageCache::class,
            'standalone.store' => \App\Http\Middleware\CheckStandaloneStore::class,
        ]);

        $middleware->web(append: [
            \App\Http\Middleware\Localization::class,
            \App\Http\Middleware\PageCache::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
