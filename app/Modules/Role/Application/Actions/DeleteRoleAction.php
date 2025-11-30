<?php

declare(strict_types=1);

namespace App\Modules\Role\Application\Actions;

use App\Modules\Role\Infrastructure\Repositories\RoleRepositoryInterface;
use Illuminate\Support\Facades\DB;

class DeleteRoleAction
{
    public function __construct(
        protected RoleRepositoryInterface $roleRepository,
    ) {}

    public function execute(int $id): bool
    {
        // Validate that the role exists
        $this->roleRepository->findOrFail($id);

        // Delete pivot table relationships first
        DB::table('role_has_permissions')->where('role_id', $id)->delete();
        DB::table('model_has_roles')->where('role_id', $id)->delete();

        // Delete the role itself
        return $this->roleRepository->delete($id);
    }
}
