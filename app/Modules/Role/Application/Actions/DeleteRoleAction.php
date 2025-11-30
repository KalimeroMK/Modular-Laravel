<?php

declare(strict_types=1);

namespace App\Modules\Role\Application\Actions;

use App\Modules\Role\Infrastructure\Models\Role;
use App\Modules\Role\Infrastructure\Repositories\RoleRepositoryInterface;
use Illuminate\Support\Facades\DB;

class DeleteRoleAction
{
    public function __construct(
        protected RoleRepositoryInterface $roleRepository,
    ) {}

    public function execute(Role $role): bool
    {
        $roleId = (int) $role->getKey();
        if ($roleId === 0) {
            return false;
        }

        // Delete pivot table relationships first
        DB::table('role_has_permissions')->where('role_id', $roleId)->delete();
        DB::table('model_has_roles')->where('role_id', $roleId)->delete();

        // Delete the role itself
        return $this->roleRepository->delete($roleId);
    }
}
