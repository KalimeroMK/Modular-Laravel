<?php

namespace App\Modules\Role\Http\Actions;

use App\Modules\Role\Http\DTOs\CreateRoleDTO;
use App\Modules\Role\Interfaces\RoleInterface;
use App\Modules\Role\Models\Role;

class CreateRoleAction
{
    public function __construct(protected RoleInterface $repository) {}

    public function execute(CreateRoleDTO $dto): Role
    {
        return $this->repository->create([
            'name' => $dto->name,
        ]);
    }
}