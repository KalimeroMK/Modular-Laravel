<?php

declare(strict_types=1);

namespace App\Modules\Permission\Application\Actions;

use App\Modules\Core\Exceptions\UpdateException;
use App\Modules\Permission\Application\DTO\PermissionResponseDTO;
use App\Modules\Permission\Application\DTO\UpdatePermissionDTO;
use App\Modules\Permission\Infrastructure\Models\Permission;
use App\Modules\Permission\Infrastructure\Repositories\PermissionRepositoryInterface;

class UpdatePermissionAction
{
    public function __construct(
        protected PermissionRepositoryInterface $permissionRepository,
    ) {}

    public function execute(Permission $permission, UpdatePermissionDTO $dto): PermissionResponseDTO
    {
        $updateData = $dto->toArray();

        /** @var Permission $updatedPermission */
        $updatedPermission = $this->permissionRepository->update((int) $permission->getKey(), $updateData);

        if ($updatedPermission === null) {
            throw new UpdateException('Failed to update permission');
        }

        return PermissionResponseDTO::fromPermission($updatedPermission);
    }
}
