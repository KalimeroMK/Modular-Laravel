<?php

declare(strict_types=1);

namespace App\Modules\Core\Interfaces;

use Illuminate\Database\Eloquent\Model;

/**
 * Interface for repositories that support write operations.
 */
interface WritableRepositoryInterface
{
    /**
     * Create a new record.
     *
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): ?Model;

    /**
     * Insert multiple records.
     *
     * @param  array<int, array<string, mixed>>  $data
     */
    public function insert(array $data): bool;

    /**
     * Update a record by ID.
     *
     * @param  array<string, mixed>  $data
     */
    public function update(int $id, array $data): ?Model;

    /**
     * Delete a record by ID.
     */
    public function delete(int $id): bool;
}
