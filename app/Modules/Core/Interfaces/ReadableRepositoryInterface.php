<?php

declare(strict_types=1);

namespace App\Modules\Core\Interfaces;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * Interface for repositories that support read operations.
 */
interface ReadableRepositoryInterface
{
    /**
     * Get all records.
     *
     * @param  array<int, string>  $with
     * @return Collection<int, Model>
     */
    public function all(array $with = []): Collection;

    /**
     * Find a record by ID.
     *
     * @param  int|string  $id  The record ID (supports integer IDs, ULIDs, and UUIDs)
     * @param  array<int, string>  $with
     */
    public function find(int|string $id, array $with = []): ?Model;

    /**
     * Find a record by ID or throw exception.
     *
     * @param  int|string  $id  The record ID (supports integer IDs, ULIDs, and UUIDs)
     * @param  array<int, string>  $with
     */
    public function findOrFail(int|string $id, array $with = []): Model;

    /**
     * Find a record by column and value.
     *
     * @param  array<int, string>  $with
     */
    public function findBy(string $column, mixed $value, array $with = []): ?Model;

    /**
     * Get paginated results with optional eager loading.
     *
     * @param  array<int, string>  $with
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator<int, Model>
     */
    public function paginate(int $perPage = 15, array $with = []): \Illuminate\Contracts\Pagination\LengthAwarePaginator;
}
