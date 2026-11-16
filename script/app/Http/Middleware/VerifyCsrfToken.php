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
        // Booostr: Exempt forgot & reset password POST routes from CSRF
        // Root cause: HTTP→HTTPS redirect (Cloudflare/Nginx SSL termination)
        // drops POST data & session cookies, causing CSRF token mismatch.
        // Login works because it uses AJAX fetch(); these forms use standard POST.
        'customer/forget-password',
        'customer/reset-password',
    ];
}
