<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Throwable;

class PageCache
{
    public function handle(Request $request, Closure $next)
    {
        if (!$request->isMethod('GET')) {
            return $next($request);
        }

        if (auth()->check()) {
            return $next($request);
        }

        $path = ltrim($request->path(), '/');

        $skipPaths = [
            'admin',
            'seller',
            'partner',
            'rider',
            'cart',
            'checkout',
            'wishlist',
            'login',
            'register',
            'password',
            'cron',
            'direct',
            'direct/*',
        ];

        foreach ($skipPaths as $skipPath) {
            if (str_starts_with($path, $skipPath)) {
                return $next($request);
            }
        }

        if ($request->hasCookie('laravel_session')) {
            return $next($request);
        }

        $response = null;

        try {
            $tenantId = 'central';

            if (function_exists('tenant') && tenant()) {
                $tenantId = tenant()->id ?? 'central';
            }

            $cacheKey = 'page_cache:' . $tenantId . ':' . md5($request->fullUrl());

            $cachedResponse = Redis::get($cacheKey);

            if ($cachedResponse) {
                $decoded = json_decode($cachedResponse, true);

                if (is_array($decoded) && isset($decoded['content'], $decoded['content_type'])) {
                    return response($decoded['content'], 200)
                        ->header('Content-Type', $decoded['content_type'])
                        ->header('X-Page-Cache', 'HIT');
                }
            }

            $response = $next($request);

            if ($response->getStatusCode() === 200) {
                $contentType = $response->headers->get('Content-Type');

                if ($contentType && str_contains($contentType, 'text/html')) {
                    Redis::setex($cacheKey, 600, json_encode([
                        'content' => $response->getContent(),
                        'content_type' => $contentType,
                    ]));
                }
            }

            return $response->header('X-Page-Cache', 'MISS');
        } catch (Throwable $e) {
            $response = $response ?: $next($request);
            $response->headers->set('X-Page-Cache-Error', $e->getMessage());
            return $response;
        }
    }
}