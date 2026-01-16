<?php

declare(strict_types=1);

namespace App\Modules\Core\Interfaces;

use Illuminate\Database\Eloquent\Model;

/**
 * Interface for repositories that support soft delete operations.
 */
interface SoftDeletableRepositoryInterface
{
    /**
     * Restore a soft-deleted record.
     *
     * @param  int|string  $id  The record ID (supports integer IDs, ULIDs, and UUIDs)
     */
    public function restore(int|string $id): ?Model;

    /**
     * Find a record including soft-deleted ones.
     *
     * @param  int|string  $id  The record ID (supports integer IDs, ULIDs, and UUIDs)
     */
    public function findWithTrashed(int|string $id): ?Model;
}
