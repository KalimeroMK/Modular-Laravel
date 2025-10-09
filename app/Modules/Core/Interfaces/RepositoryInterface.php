<?php

declare(strict_types=1);

namespace App\Modules\Core\Interfaces;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

interface RepositoryInterface
{
    /**
     * @param  array<int, string>  $with
     * @return Collection<int, Model>
     */
    public function all(array $with = []): Collection;

    /**
     * @param  array<int, string>  $with
     */
    public function find(int $id, array $with = []): ?Model;

    /**
     * @param  array<int, string>  $with
     */
    public function findOrFail(int $id, array $with = []): Model;

    /**
     * @param  array<int, string>  $with
     */
    public function findBy(string $column, mixed $value, array $with = []): ?Model;

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): ?Model;

    /**
     * @param  array<int, array<string, mixed>>  $data
     */
    public function insert(array $data): bool;

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(int $id, array $data): ?Model;

    public function delete(int $id): bool;

    public function restore(int $id): ?Model;

    public function findWithTrashed(int $id): ?Model;

    /**
     * Get paginated results with optional eager loading
     * 
     * @param  array<int, string>  $with
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function paginate(int $perPage = 15, array $with = []): \Illuminate\Contracts\Pagination\LengthAwarePaginator;

    /**
     * Get cached results for expensive queries
     * 
     * @param  array<int, string>  $with
     * @param  int  $ttl Cache time in seconds
     * @return Collection<int, Model>
     */
    public function allCached(array $with = [], int $ttl = 3600): Collection;

    /**
     * Get cached single record
     * 
     * @param  array<int, string>  $with
     * @param  int  $ttl Cache time in seconds
     */
    public function findCached(int $id, array $with = [], int $ttl = 3600): ?Model;

    /**
     * Clear cache for this model
     */
    public function clearCache(): void;
}
