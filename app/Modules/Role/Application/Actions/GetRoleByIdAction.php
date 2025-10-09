<?php

declare(strict_types=1);

namespace App\Modules\Role\Application\Actions;

use App\Modules\Role\Application\DTO\RoleResponseDTO;
use App\Modules\Role\Infrastructure\Repositories\RoleRepositoryInterface;
use App\Modules\Role\Infrastructure\Models\Role;

class GetRoleByIdAction
{
    public function __construct(
        protected RoleRepositoryInterface $roleRepository,
    ) {}

    public function execute(int $id): ?RoleResponseDTO
    {
        $role = $this->roleRepository->find($id);
        
        if (!$role) {
            return null;
        }
        
        return RoleResponseDTO::fromRole($role);
    }
}
