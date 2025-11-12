<?php

declare(strict_types=1);

namespace App\Modules\Permission\Application\Actions;

use App\Modules\Core\Exceptions\CreateException;
use App\Modules\Permission\Application\DTO\CreatePermissionDTO;
use App\Modules\Permission\Application\DTO\PermissionResponseDTO;
use App\Modules\Permission\Infrastructure\Repositories\PermissionRepositoryInterface;

class CreatePermissionAction
{
    public function __construct(
        protected PermissionRepositoryInterface $permissionRepository,
    ) {}

    public function execute(CreatePermissionDTO $dto): PermissionResponseDTO
    {
        $permissionData = [
            'name' => $dto->name,
            'guard_name' => $dto->guardName,
        ];

        /** @var \App\Modules\Permission\Infrastructure\Models\Permission $permission */
        $permission = $this->permissionRepository->create($permissionData);

        if ($permission === null) {
            throw new CreateException('Failed to create permission');
        }

        return PermissionResponseDTO::fromPermission($permission);
    }
}
