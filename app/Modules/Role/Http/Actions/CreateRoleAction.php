<?php

namespace App\Modules\Role\Http\Actions;

use App\Modules\Role\Exceptions\RoleStoreException;
use App\Modules\Role\Http\DTOs\CreateRoleDTO;
use App\Modules\Role\Interfaces\RoleInterface;
use Exception;

class CreateRoleAction
{
    protected RoleInterface $roleRepository;

    public function __construct(RoleInterface $roleRepository)
    {
        $this->roleRepository = $roleRepository;
    }

    public function execute(CreateRoleDTO $dto): mixed
    {
        try {
            return $this->roleRepository->create($dto->toArray());
        } catch (Exception $exception) {
            throw new RoleStoreException($exception);
        }
    }
}
