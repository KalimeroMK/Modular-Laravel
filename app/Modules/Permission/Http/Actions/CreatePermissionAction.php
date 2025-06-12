<?php

namespace App\Modules\Permission\Http\Actions;

use App\Modules\Permission\Http\DTOs\CreatePermissionDTO;
use App\Modules\Permission\Interfaces\PermissionInterface;
use App\Modules\Permission\Models\Permission;

class CreatePermissionAction
{
    public function __construct(protected PermissionInterface $repository) {}

    public function execute(CreatePermissionDTO $dto): Permission
    {
        return $this->repository->create($dto->toArray());
    }
}