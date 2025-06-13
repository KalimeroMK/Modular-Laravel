<?php

namespace App\Modules\Permission\Http\Actions;

use App\Modules\Permission\Http\DTOs\PermissionDTO;
use App\Modules\Permission\Interfaces\PermissionInterface;
use App\Modules\Permission\Models\Permission;

class CreatePermissionAction
{
    public function __construct(protected PermissionInterface $repository) {}

    public function execute(PermissionDTO $dto): Permission
    {
        return $this->repository->create($dto->toArray());
    }
}
