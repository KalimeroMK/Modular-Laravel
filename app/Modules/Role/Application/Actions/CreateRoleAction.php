<?php

declare(strict_types=1);

namespace App\Modules\Role\Application\Actions;

use App\Modules\Role\Application\DTO\CreateRoleDTO;
use App\Modules\Role\Application\DTO\RoleResponseDTO;
use App\Modules\Role\Infrastructure\Repositories\RoleRepositoryInterface;
use Exception;

class CreateRoleAction
{
    public function __construct(
        protected RoleRepositoryInterface $roleRepository,
    ) {}

    public function execute(CreateRoleDTO $dto): RoleResponseDTO
    {
        $roleData = [
            'name' => $dto->name,
            'guard_name' => $dto->guardName,
        ];

        $role = $this->roleRepository->create($roleData);

        if ($role === null) {
            throw new Exception('Failed to create role');
        }

        return RoleResponseDTO::fromRole($role);
    }
}
