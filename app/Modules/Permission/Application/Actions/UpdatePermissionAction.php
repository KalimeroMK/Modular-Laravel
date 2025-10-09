<?php

declare(strict_types=1);

namespace App\Modules\Permission\Application\Actions;

use App\Modules\Permission\Application\DTO\PermissionResponseDTO;
use App\Modules\Permission\Application\DTO\UpdatePermissionDTO;
use App\Modules\Permission\Infrastructure\Repositories\PermissionRepositoryInterface;
use Exception;
use Spatie\Permission\Models\Permission;

class UpdatePermissionAction
{
    public function __construct(
        protected PermissionRepositoryInterface $permissionRepository,
    ) {}

    public function execute(int $id, UpdatePermissionDTO $dto): PermissionResponseDTO
    {
        $updateData = $dto->toArray();

        /** @var Permission $updatedPermission */
        $updatedPermission = $this->permissionRepository->update($id, $updateData);

        if ($updatedPermission === null) {
            throw new Exception('Failed to update permission');
        }

        return PermissionResponseDTO::fromPermission($updatedPermission);
    }
}
