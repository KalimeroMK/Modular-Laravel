<?php

declare(strict_types=1);

namespace App\Modules\Permission\Application\Actions;

use App\Modules\Core\Exceptions\UpdateException;
use App\Modules\Permission\Application\DTO\UpdatePermissionDTO;
use App\Modules\Permission\Infrastructure\Models\Permission;
use App\Modules\Permission\Infrastructure\Repositories\PermissionRepositoryInterface;

class UpdatePermissionAction
{
    public function __construct(
        protected PermissionRepositoryInterface $permissionRepository,
    ) {}

    public function execute(int $id, UpdatePermissionDTO $dto): Permission
    {
        
        $this->permissionRepository->findOrFail($id);

        $updateData = $dto->toArray();

        
        $updateData = array_filter($updateData, fn ($value) => $value !== null);

         
        $updatedPermission = $this->permissionRepository->update($id, $updateData);

        if ($updatedPermission === null) {
            throw new UpdateException('Failed to update permission');
        }

        return $updatedPermission;
    }
}
