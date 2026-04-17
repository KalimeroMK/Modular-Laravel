<?php

declare(strict_types=1);

namespace App\Modules\Permission\Infrastructure\Repositories;

use App\Modules\Core\Repositories\EloquentRepository;
use App\Modules\Permission\Infrastructure\Models\Permission;

class PermissionRepository extends EloquentRepository implements PermissionRepositoryInterface
{
    public function __construct(Permission $model)
    {
        parent::__construct($model);
    }

    public function findByName(string $name): ?Permission
    {

        $result = $this->findBy('name', $name);

        return $result;
    }
}
