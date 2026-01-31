<?php

declare(strict_types=1);

namespace App\Modules\Role\Application\Actions;

use App\Modules\Core\Exceptions\UpdateException;
use App\Modules\Role\Application\DTO\UpdateRoleDTO;
use App\Modules\Role\Infrastructure\Models\Role;
use App\Modules\Role\Infrastructure\Repositories\RoleRepositoryInterface;

class UpdateRoleAction
{
    public function __construct(
        protected RoleRepositoryInterface $roleRepository,
    ) {}

    public function execute(int $id, UpdateRoleDTO $dto): Role
    {
        
        $this->roleRepository->findOrFail($id);

        $updateData = $dto->toArray();

        
        $updateData = array_filter($updateData, fn ($value) => $value !== null);

         
        $updatedRole = $this->roleRepository->update($id, $updateData);

        if ($updatedRole === null) {
            throw new UpdateException('Failed to update role');
        }

        return $updatedRole;
    }
}
