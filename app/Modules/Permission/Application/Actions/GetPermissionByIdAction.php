<?php

declare(strict_types=1);

namespace App\Modules\Permission\Application\Actions;

use App\Modules\Permission\Application\DTO\PermissionResponseDTO;
use Spatie\Permission\Models\Permission;

class GetPermissionByIdAction
{
    public function execute(Permission $permission): PermissionResponseDTO
    {
        return PermissionResponseDTO::fromPermission($permission);
    }
}
