<?php

declare(strict_types=1);

namespace App\Modules\Permission\Application\Actions;

use App\Modules\Permission\Infrastructure\Models\Permission;
use App\Modules\Permission\Infrastructure\Repositories\PermissionRepositoryInterface;

class GetPermissionByIdAction
{
    public function __construct(protected PermissionRepositoryInterface $repository) {}

    public function execute(int $id): Permission
    {
        /** @var Permission $permission */
        $permission = $this->repository->findOrFail($id);

        return $permission;
    }
}
