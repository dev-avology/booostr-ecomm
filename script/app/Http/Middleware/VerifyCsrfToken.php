<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        'partner/payu/status',
        'install/migrate',
        'install/seed',
        'direct/checkout_form/*',
        '/checkout/make-order',
        // Booostr: Exempt customer authentication POST routes from CSRF
        // Root cause: HTTP→HTTPS redirect (Cloudflare/Nginx SSL termination) & multi-tenant cookies
        // drop session state or cause CSRF mismatch. Credentials protect authentication.
        'customer/login-customer',
        'customer/register-customer',
        'customer/forget-password',
        'customer/reset-password',
    ];
}
