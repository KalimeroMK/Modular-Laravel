<?php

declare(strict_types=1);

namespace App\Modules\Role\Application\Actions;

use App\Modules\Role\Infrastructure\Models\Role;
use App\Modules\Role\Infrastructure\Repositories\RoleRepositoryInterface;

class GetRoleByIdAction
{
    public function __construct(protected RoleRepositoryInterface $repository) {}

    public function execute(int $id): Role
    {
        /** @var Role $role */
        $role = $this->repository->findOrFail($id);

        return $role;
    }
}
