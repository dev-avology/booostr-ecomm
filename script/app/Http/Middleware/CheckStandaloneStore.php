<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Auth;

class CheckStandaloneStore
{
    /**
     * Handle an incoming request for standalone store frontend.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // 1. If standalone store is explicitly ON, or logged in as Seller / Admin, allow storefront access (WordPress Admin model)
        if (get_option('standalone_store_status') === 'on' || (Auth::check() && in_array(Auth::user()->role_id, [1, 3]))) {
            return $next($request);
        }

        // 2. If it's an AJAX / JSON request, return 403
        if ($request->ajax() || $request->expectsJson()) {
            return response()->json([
                'status'  => 'error',
                'message' => __('Standalone store is currently unavailable.'),
            ], 403);
        }

        // 3. Standalone store is OFF: redirect visitor to their club website
        $clubInfo = function_exists('tenant_club_info') ? tenant_club_info() : [];
        $clubUrl = $clubInfo['club_url'] ?? null;

        if (!empty($clubUrl) && filter_var($clubUrl, FILTER_VALIDATE_URL)) {
            $parsedClubHost = parse_url($clubUrl, PHP_URL_HOST);
            $currentHost = $request->getHost();

            // Guard against infinite redirect loops if club_url is identical to current store domain
            if ($parsedClubHost && strcasecmp($parsedClubHost, $currentHost) !== 0) {
                return redirect()->away($clubUrl);
            }
        }

        // Fallback: Redirect to central Booostr app if no external club website configured
        return redirect()->away('https://app.booostr.co');
    }
}

