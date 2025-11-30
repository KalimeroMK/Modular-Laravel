<?php

declare(strict_types=1);

namespace App\Modules\Permission\Application\Actions;

use App\Modules\Permission\Infrastructure\Models\Permission;
use App\Modules\Permission\Infrastructure\Repositories\PermissionRepositoryInterface;
use Illuminate\Support\Facades\DB;

class DeletePermissionAction
{
    public function __construct(
        protected PermissionRepositoryInterface $permissionRepository,
    ) {}

    public function execute(Permission $permission): bool
    {
        $permissionId = (int) $permission->getKey();
        if ($permissionId === 0) {
            return false;
        }

        // Delete pivot table relationships first
        DB::table('role_has_permissions')->where('permission_id', $permissionId)->delete();
        DB::table('model_has_permissions')->where('permission_id', $permissionId)->delete();

        // Delete the permission itself
        return $this->permissionRepository->delete($permissionId);
    }
}
