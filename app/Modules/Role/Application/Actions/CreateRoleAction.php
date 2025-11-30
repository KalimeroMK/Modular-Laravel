<?php

declare(strict_types=1);

namespace App\Modules\Role\Application\Actions;

use App\Modules\Core\Exceptions\CreateException;
use App\Modules\Role\Application\DTO\CreateRoleDTO;
use App\Modules\Role\Infrastructure\Models\Role;
use App\Modules\Role\Infrastructure\Repositories\RoleRepositoryInterface;

class CreateRoleAction
{
    public function __construct(
        protected RoleRepositoryInterface $roleRepository,
    ) {}

    public function execute(CreateRoleDTO $dto): Role
    {
        $roleData = [
            'name' => $dto->name,
            'guard_name' => $dto->guardName,
        ];

        /** @var Role|null $role */
        $role = $this->roleRepository->create($roleData);

        if ($role === null) {
            throw new CreateException('Failed to create role');
        }

        return $role;
    }
}
