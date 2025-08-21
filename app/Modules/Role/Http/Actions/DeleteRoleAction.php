<?php

declare(strict_types=1);

namespace App\Modules\Role\Http\Actions;

use App\Modules\Role\Interfaces\RoleInterface;
use App\Modules\Role\Models\Role;

class DeleteRoleAction
{
    public function __construct(protected RoleInterface $repository) {}

    public function execute(Role $role): void
    {
        $this->repository->delete((int) $role->id);
    }
}
