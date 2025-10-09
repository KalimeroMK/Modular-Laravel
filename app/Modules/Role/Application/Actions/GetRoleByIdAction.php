<?php

declare(strict_types=1);

namespace App\Modules\Role\Application\Actions;

use App\Modules\Role\Application\DTO\RoleResponseDTO;
use App\Modules\Role\Infrastructure\Models\Role;

class GetRoleByIdAction
{
    public function execute(Role $role): RoleResponseDTO
    {
        return RoleResponseDTO::fromRole($role);
    }
}
