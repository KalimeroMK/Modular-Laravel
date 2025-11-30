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
        return $this->repository->findOrFail($id);
    }
}
