<?php

declare(strict_types=1);

namespace App\Modules\Role\Application\Actions;

use App\Modules\Role\Application\DTO\RoleResponseDTO;
use App\Modules\Role\Application\DTO\UpdateRoleDTO;
use App\Modules\Role\Infrastructure\Models\Role;
use App\Modules\Role\Infrastructure\Repositories\RoleRepositoryInterface;
use Exception;

class UpdateRoleAction
{
    public function __construct(
        protected RoleRepositoryInterface $roleRepository,
    ) {}

    public function execute(int $id, UpdateRoleDTO $dto): RoleResponseDTO
    {
        $updateData = $dto->toArray();

        /** @var Role $updatedRole */
        $updatedRole = $this->roleRepository->update($id, $updateData);

        if ($updatedRole === null) {
            throw new Exception('Failed to update role');
        }

        return RoleResponseDTO::fromRole($updatedRole);
    }
}
