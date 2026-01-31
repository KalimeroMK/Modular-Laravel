<?php

declare(strict_types=1);

namespace App\Modules\Core\Support;

use Closure;
use Exception;
use Illuminate\Support\Facades\Cache;
use Log;





class CacheHelper
{
    


    private const int DEFAULT_TTL = 3600;

    






    


    public static function remember(string $key, callable $callback, int $ttl = self::DEFAULT_TTL): mixed
    {
        return Cache::remember($key, $ttl, Closure::fromCallable($callback));
    }

    





    


    public static function rememberForever(string $key, callable $callback): mixed
    {
        return Cache::rememberForever($key, Closure::fromCallable($callback));
    }

    





    public static function get(string $key, mixed $default = null): mixed
    {
        return Cache::get($key, $default);
    }

    






    public static function put(string $key, mixed $value, int $ttl = self::DEFAULT_TTL): void
    {
        Cache::put($key, $value, $ttl);
    }

    




    public static function forget(string $key): void
    {
        Cache::forget($key);
    }

    





    public static function forgetPattern(string $pattern): void
    {
        try {
            $store = Cache::getStore();

            
            if (method_exists($store, 'getRedis')) {
                $redis = $store->getRedis();
                
                $keys = $redis->keys($pattern);

                if (! empty($keys)) {
                    $redis->del($keys);
                }
            }
        } catch (Exception $e) {
            
            Log::warning("Cache pattern forget failed: {$e->getMessage()}");
        }
    }

    


    public static function flush(): void
    {
        Cache::flush();
    }

    





    public static function key(string $prefix, string ...$parts): string
    {
        return $prefix.'_'.implode('_', $parts);
    }

    





    public static function modelKey(string $model, int|string $id): string
    {
        return self::key(mb_strtolower($model), (string) $id);
    }

    






    public static function paginatedKey(string $model, int $perPage, int $page = 1): string
    {
        return self::key(mb_strtolower($model), 'paginated', (string) $perPage, (string) $page);
    }

    




    public static function has(string $key): bool
    {
        return Cache::has($key);
    }
}
