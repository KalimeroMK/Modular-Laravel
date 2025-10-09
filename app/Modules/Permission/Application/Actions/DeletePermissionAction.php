<?php

declare(strict_types=1);

namespace App\Modules\Permission\Application\Actions;

use App\Modules\Permission\Infrastructure\Repositories\PermissionRepositoryInterface;
use Spatie\Permission\Models\Permission;

class DeletePermissionAction
{
    public function __construct(
        protected PermissionRepositoryInterface $permissionRepository,
    ) {}

    public function execute(Permission $permission): bool
    {
        return $this->permissionRepository->delete((int) $permission->id);
    }
}
