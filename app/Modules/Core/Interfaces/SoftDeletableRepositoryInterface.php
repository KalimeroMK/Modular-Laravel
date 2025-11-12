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
     */
    public function restore(int $id): ?Model;

    /**
     * Find a record including soft-deleted ones.
     */
    public function findWithTrashed(int $id): ?Model;
}
