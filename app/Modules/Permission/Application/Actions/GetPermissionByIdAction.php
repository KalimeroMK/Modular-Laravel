<?php

declare(strict_types=1);

namespace App\Modules\Permission\Application\Actions;

use App\Modules\Permission\Application\DTO\PermissionResponseDTO;
use App\Modules\Permission\Infrastructure\Repositories\PermissionRepositoryInterface;
use Spatie\Permission\Models\Permission;

class GetPermissionByIdAction
{
    public function __construct(
        protected PermissionRepositoryInterface $permissionRepository,
    ) {}

    public function execute(int $id): ?PermissionResponseDTO
    {
        $permission = $this->permissionRepository->find($id);
        
        if (!$permission) {
            return null;
        }
        
        return PermissionResponseDTO::fromPermission($permission);
    }
}
