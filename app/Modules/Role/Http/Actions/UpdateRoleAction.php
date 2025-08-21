<?php

declare(strict_types=1);

namespace App\Modules\Role\Http\Actions;

use App\Modules\Role\Http\DTOs\RoleDTO;
use App\Modules\Role\Interfaces\RoleInterface;
use App\Modules\Role\Models\Role;
use Illuminate\Database\Eloquent\Model;

class UpdateRoleAction
{
    public function __construct(protected RoleInterface $repository) {}

    public function execute(Role $role, RoleDTO $dto): Model
    {
        return $this->repository->update((int) $role->id, [
            'name' => $dto->name,
        ]);
    }
}
