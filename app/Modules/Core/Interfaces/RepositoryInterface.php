<?php

declare(strict_types=1);

namespace App\Modules\Core\Interfaces;

/**
 * Main repository interface that extends all specific interfaces.
 * This follows Interface Segregation Principle - clients can implement
 * only the interfaces they need.
 */
interface RepositoryInterface extends CacheableRepositoryInterface, ReadableRepositoryInterface, SoftDeletableRepositoryInterface, WritableRepositoryInterface
{
    // All methods are inherited from the extended interfaces
    // This follows Interface Segregation Principle
}
