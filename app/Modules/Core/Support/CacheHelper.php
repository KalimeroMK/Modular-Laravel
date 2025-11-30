<?php

declare(strict_types=1);

namespace App\Modules\Core\Support;

use Closure;
use Exception;
use Illuminate\Support\Facades\Cache;
use Log;

/**
 * Helper class for caching operations.
 * Provides a centralized way to handle caching with consistent TTL and key patterns.
 */
class CacheHelper
{
    /**
     * Default cache TTL in seconds (1 hour).
     */
    private const int DEFAULT_TTL = 3600;

    /**
     * Remember a value in cache.
     *
     * @param  string  $key  Cache key
     * @param  callable  $callback  Callback to execute if cache miss
     * @param  int  $ttl  Cache time in seconds
     */
    /**
     * @param  Closure(): mixed  $callback
     */
    public static function remember(string $key, callable $callback, int $ttl = self::DEFAULT_TTL): mixed
    {
        return Cache::remember($key, $ttl, Closure::fromCallable($callback));
    }

    /**
     * Remember a value in cache forever.
     *
     * @param  string  $key  Cache key
     * @param  callable  $callback  Callback to execute if cache miss
     */
    /**
     * @param  Closure(): mixed  $callback
     */
    public static function rememberForever(string $key, callable $callback): mixed
    {
        return Cache::rememberForever($key, Closure::fromCallable($callback));
    }

    /**
     * Get value from cache or return default.
     *
     * @param  string  $key  Cache key
     * @param  mixed  $default  Default value if cache miss
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        return Cache::get($key, $default);
    }

    /**
     * Put value in cache.
     *
     * @param  string  $key  Cache key
     * @param  mixed  $value  Value to cache
     * @param  int  $ttl  Cache time in seconds
     */
    public static function put(string $key, mixed $value, int $ttl = self::DEFAULT_TTL): void
    {
        Cache::put($key, $value, $ttl);
    }

    /**
     * Forget a cache key.
     *
     * @param  string  $key  Cache key
     */
    public static function forget(string $key): void
    {
        Cache::forget($key);
    }

    /**
     * Clear cache by pattern.
     * Note: This works best with Redis. For other drivers, consider using tags.
     *
     * @param  string  $pattern  Cache key pattern (e.g., 'users_*')
     */
    public static function forgetPattern(string $pattern): void
    {
        try {
            $store = Cache::getStore();

            // Only works with Redis
            if (method_exists($store, 'getRedis')) {
                $redis = $store->getRedis();
                // Redis uses * for pattern matching
                $keys = $redis->keys($pattern);

                if (! empty($keys)) {
                    $redis->del($keys);
                }
            }
        } catch (Exception $e) {
            // Fallback: If pattern matching fails, log and continue
            Log::warning("Cache pattern forget failed: {$e->getMessage()}");
        }
    }

    /**
     * Clear all cache.
     */
    public static function flush(): void
    {
        Cache::flush();
    }

    /**
     * Generate cache key with prefix.
     *
     * @param  string  $prefix  Key prefix
     * @param  string  ...$parts  Key parts
     */
    public static function key(string $prefix, string ...$parts): string
    {
        return $prefix.'_'.implode('_', $parts);
    }

    /**
     * Generate cache key for model.
     *
     * @param  string  $model  Model name (e.g., 'User')
     * @param  int  $id  Model ID
     */
    public static function modelKey(string $model, int $id): string
    {
        return self::key(mb_strtolower($model), (string) $id);
    }

    /**
     * Generate cache key for paginated results.
     *
     * @param  string  $model  Model name
     * @param  int  $perPage  Items per page
     * @param  int  $page  Page number
     */
    public static function paginatedKey(string $model, int $perPage, int $page = 1): string
    {
        return self::key(mb_strtolower($model), 'paginated', (string) $perPage, (string) $page);
    }

    /**
     * Check if cache has key.
     *
     * @param  string  $key  Cache key
     */
    public static function has(string $key): bool
    {
        return Cache::has($key);
    }
}
