<?php

declare(strict_types=1);

namespace App\Modules\Permission\Application\Actions;

use App\Modules\Permission\Infrastructure\Repositories\PermissionRepositoryInterface;
use Illuminate\Support\Facades\DB;

class DeletePermissionAction
{
    public function __construct(
        protected PermissionRepositoryInterface $permissionRepository,
    ) {}

    public function execute(int $id): bool
    {
        
        $this->permissionRepository->findOrFail($id);

        
        DB::table('role_has_permissions')->where('permission_id', $id)->delete();
        DB::table('model_has_permissions')->where('permission_id', $id)->delete();

        
        return $this->permissionRepository->delete($id);
    }
}
