<?php

declare(strict_types=1);

namespace App\Modules\NonExistentStubModule\Infrastructure\Repositories;

use App\Modules\Core\Repositories\EloquentRepository;
use App\Modules\NonExistentStubModule\Infrastructure\Repositories\NonExistentStubModuleRepositoryInterface;
use App\Modules\NonExistentStubModule\Infrastructure\Models\NonExistentStubModule;

/**
 * Repository implementation for NonExistentStubModule operations.
 * 
 * Provides database access methods for nonexistentstubmodule models.
 * 
 * @extends EloquentRepository<NonExistentStubModule>
 */
class NonExistentStubModuleRepository extends EloquentRepository implements NonExistentStubModuleRepositoryInterface
{
    /**
     * Create a new repository instance.
     *
     * @param NonExistentStubModule $model The nonexistentstubmodule model instance
     */
    public function __construct(NonExistentStubModule $model)
    {
        parent::__construct($model);
    }
}
