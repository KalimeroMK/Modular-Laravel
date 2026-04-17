<?php

declare(strict_types=1);

namespace App\Modules\Core\Interfaces;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

interface CacheableRepositoryInterface
{
    public function allCached(array $with = [], int $ttl = 3600): Collection;

    public function findCached(int|string $id, array $with = [], int $ttl = 3600): ?Model;

    public function clearCache(): void;
}
