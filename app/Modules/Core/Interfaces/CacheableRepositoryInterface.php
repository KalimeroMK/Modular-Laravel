<?php

declare(strict_types=1);

namespace App\Modules\Core\Interfaces;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * Interface for repositories that support caching operations.
 */
interface CacheableRepositoryInterface
{
    /**
     * Get cached results for expensive queries.
     *
     * @param  array<int, string>  $with
     * @param  int  $ttl  Cache time in seconds
     * @return Collection<int, Model>
     */
    public function allCached(array $with = [], int $ttl = 3600): Collection;

    /**
     * Get cached single record.
     *
     * @param  int|string  $id  The record ID (supports integer IDs, ULIDs, and UUIDs)
     * @param  array<int, string>  $with
     * @param  int  $ttl  Cache time in seconds
     */
    public function findCached(int|string $id, array $with = [], int $ttl = 3600): ?Model;

    /**
     * Clear cache for this model.
     */
    public function clearCache(): void;
}
