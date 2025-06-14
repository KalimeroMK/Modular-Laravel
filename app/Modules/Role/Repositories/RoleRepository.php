<?php

declare(strict_types=1);

namespace App\Modules\Role\Repositories;

use App\Modules\Core\Repositories\EloquentRepository;
use App\Modules\Role\Interfaces\RoleInterface;
use App\Modules\Role\Models\Role;

class RoleRepository extends EloquentRepository implements RoleInterface
{
    public function __construct(Role $model)
    {
        parent::__construct($model);
    }
}
