<?php

namespace App\Modules\Permission\Repositories;

use App\Modules\Core\Repositories\EloquentRepository;
use App\Modules\Permission\Interfaces\PermissionInterface;
use App\Modules\Permission\Models\Permission;

class PermissionRepository extends EloquentRepository implements PermissionInterface
{
    public function __construct(Permission $model)
    {
        parent::__construct($model);
    }
}
