<?php

namespace App\Modules\Role\Http\Actions;

use App\Modules\Role\Exceptions\RoleIndexException;
use App\Modules\Role\Interfaces\RoleInterface;
use Exception;

class GetAllRoleAction
{
    protected RoleInterface $roleRepository;

    public function __construct(RoleInterface $roleRepository)
    {
        $this->roleRepository = $roleRepository;
    }

    public function execute(): mixed
    {
        try {
            return $this->roleRepository->findAll();
        } catch (Exception $exception) {
            throw new RoleIndexException($exception);
        }
    }
}
