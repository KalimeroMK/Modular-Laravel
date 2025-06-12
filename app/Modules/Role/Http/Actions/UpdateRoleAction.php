<?php

namespace App\Modules\Role\Http\Actions;

use App\Modules\Role\Http\DTOs\UpdateRoleDTO;
use App\Modules\Role\Interfaces\RoleInterface;
use App\Modules\Role\Models\Role;
use Illuminate\Database\Eloquent\Model;

class UpdateRoleAction
{
    public function __construct(protected RoleInterface $repository) {}

    public function execute(Role $role, UpdateRoleDTO $dto): Model
    {
        return $this->repository->update($role->id, [
            'name' => $dto->name,
        ]);
    }
}