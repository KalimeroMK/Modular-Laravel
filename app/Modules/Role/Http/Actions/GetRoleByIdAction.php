<?php

namespace App\Modules\Role\Http\Actions;

use App\Modules\Role\Exceptions\RoleNotFoundException;
use App\Modules\Role\Interfaces\RoleInterface;
use Exception;

class GetRoleByIdAction
{
    protected RoleInterface $roleRepository;

    public function __construct(RoleInterface $roleRepository)
    {
        $this->roleRepository = $roleRepository;
    }

    public function execute(int $id): mixed
    {
        try {
            return $this->roleRepository->findById($id);
        } catch (Exception $exception) {
            throw new RoleNotFoundException($exception);
        }
    }
}
