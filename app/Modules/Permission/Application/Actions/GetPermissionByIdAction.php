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
        return $this->repository->findOrFail($id);
    }
}
