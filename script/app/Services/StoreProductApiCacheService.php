<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class StoreProductApiCacheService
{
    public const TTL = 600;

    private const VERSION_KEY = 'store_product_api_cache_version';

    public function tenantId(): string
    {
        if (function_exists('tenant') && tenant()) {
            return (string) (tenant('id') ?? 'unknown');
        }

        return 'central';
    }

    public function versionStorageKey(): string
    {
        return 'store_product_api:' . $this->tenantId() . ':' . self::VERSION_KEY;
    }

    public function version(): int
    {
        try {
            $value = Redis::get($this->versionStorageKey());

            if ($value === null || $value === false || $value === '') {
                return 1;
            }

            return max(1, (int) $value);
        } catch (\Throwable $e) {
            return 1;
        }
    }

    public function key(string $suffix): string
    {
        return 'store_product_api:' . $this->tenantId() . ':v' . $this->version() . ':' . $suffix;
    }

    public function shouldSkipCache(Request $request): bool
    {
        return $request->boolean('refresh')
            || $request->boolean('cache_bust')
            || strtolower(trim((string) $request->header('X-Cache-Bust', ''))) === '1';
    }

    public function get(string $suffix): ?array
    {
        try {
            $cached = Redis::get($this->key($suffix));

            if (!$cached) {
                return null;
            }

            $decoded = json_decode($cached, true);

            return is_array($decoded) ? $decoded : null;
        } catch (\Throwable $e) {
            Log::warning('Store product API cache read failed', [
                'suffix' => $suffix,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    public function set(string $suffix, $data, int $ttl = self::TTL): void
    {
        try {
            Redis::setex($this->key($suffix), $ttl, json_encode($data));
        } catch (\Throwable $e) {
            Log::warning('Store product API cache write failed', [
                'suffix' => $suffix,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Invalidate all store product API read caches for the current tenant (version bump).
     */
    public function bump(): void
    {
        try {
            Redis::incr($this->versionStorageKey());
            $this->purgeLegacyUnversionedKeys();
        } catch (\Throwable $e) {
            Log::warning('Store product API cache bust failed', [
                'tenant' => $this->tenantId(),
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Remove pre-version cache keys so stale data cannot be served after deploy.
     */
    private function purgeLegacyUnversionedKeys(): void
    {
        try {
            $patterns = [
                'category_list',
                'product_list_*',
                'product_detail_*',
                'product_detail_slug_*',
            ];

            foreach ($patterns as $pattern) {
                $keys = Redis::keys($pattern);

                if (!empty($keys)) {
                    Redis::del(...$keys);
                }
            }
        } catch (\Throwable $e) {
            Log::warning('Store product API legacy cache purge failed', [
                'tenant' => $this->tenantId(),
                'error' => $e->getMessage(),
            ]);
        }
    }
}
