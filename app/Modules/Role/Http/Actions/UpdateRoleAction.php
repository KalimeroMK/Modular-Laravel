<?php

namespace App\Modules\Role\Http\Actions;

use App\Modules\Role\Exceptions\RoleUpdateException;
use App\Modules\Role\Http\DTOs\UpdateRoleDTO;
use App\Modules\Role\Interfaces\RoleInterface;
use Exception;

class UpdateRoleAction
{
    protected RoleInterface $roleRepository;

    public function __construct(RoleInterface $roleRepository)
    {
        $this->roleRepository = $roleRepository;
    }

    public function execute(int $id, UpdateRoleDTO $dto): mixed
    {
        try {
            return $this->roleRepository->update($id, $dto->toArray());
        } catch (Exception $exception) {
            throw new RoleUpdateException($exception);
        }
    }
}
