<?php

namespace App\Modules\Permission\Http\Actions;

use App\Modules\Permission\Interfaces\PermissionInterface;
use App\Modules\Permission\Models\Permission;

class GetPermissionByIdAction
{
    public function __construct(protected PermissionInterface $repository) {}

    public function execute(Permission $permission): Permission
    {
        return $this->repository->findById($permission->id);
    }
}