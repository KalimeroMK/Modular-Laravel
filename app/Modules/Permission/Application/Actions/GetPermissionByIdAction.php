<?php

declare(strict_types=1);

namespace App\Modules\Permission\Application\Actions;

use App\Modules\Permission\Application\DTO\PermissionResponseDTO;
use App\Modules\Permission\Infrastructure\Models\Permission;

class GetPermissionByIdAction
{
    public function __construct() {}

    public function execute(Permission $permission): ?PermissionResponseDTO
    {
        // Model is already resolved via route model binding, no need to query again
        return PermissionResponseDTO::fromPermission($permission);
    }
}
