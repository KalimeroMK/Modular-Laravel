<?php

namespace App\Modules\Role\Http\Actions;

use App\Modules\Role\Exceptions\RoleDestroyException;
use App\Modules\Role\Interfaces\RoleInterface;
use Exception;

class DeleteRoleAction
{
    protected RoleInterface $roleRepository;

    public function __construct(RoleInterface $roleRepository)
    {
        $this->roleRepository = $roleRepository;
    }

    public function execute(int $id): void
    {
        try {
            $this->roleRepository->delete($id);
        } catch (Exception $exception) {
            throw new RoleDestroyException($exception);
        }
    }
}
