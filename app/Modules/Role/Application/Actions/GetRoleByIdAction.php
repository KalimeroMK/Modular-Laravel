<?php

declare(strict_types=1);

namespace App\Modules\Role\Application\Actions;

use App\Modules\Role\Application\DTO\RoleResponseDTO;
use App\Modules\Role\Infrastructure\Repositories\RoleRepositoryInterface;
use App\Modules\Role\Infrastructure\Models\Role;

class GetRoleByIdAction
{
    public function __construct() {}

    public function execute(Role $role): ?RoleResponseDTO
    {
        // Model is already resolved via route model binding, no need to query again
        return RoleResponseDTO::fromRole($role);
    }
}
