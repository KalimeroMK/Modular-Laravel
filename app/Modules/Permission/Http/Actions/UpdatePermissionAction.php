<?php

namespace App\Modules\Permission\Http\Actions;

use App\Modules\Permission\Http\DTOs\UpdatePermissionDTO;
use App\Modules\Permission\Interfaces\PermissionInterface;
use App\Modules\Permission\Models\Permission;

class UpdatePermissionAction
{
    public function __construct(protected PermissionInterface $repository) {}

    public function execute(Permission $permission, UpdatePermissionDTO $dto): Permission
    {
        return $this->repository->update($permission->id, $dto->toArray());
    }
}