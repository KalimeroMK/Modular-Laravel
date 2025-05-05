<?php

namespace App\Modules\Permission\Http\Actions;

use App\Modules\Permission\Exceptions\PermissionNotFoundException;
use App\Modules\Permission\Interfaces\PermissionInterface;
use Exception;

class GetPermissionByIdAction
{
    protected PermissionInterface $permissionRepository;

    public function __construct(PermissionInterface $permissionRepository)
    {
        $this->permissionRepository = $permissionRepository;
    }

    public function execute(int $id): mixed
    {
        try {
            return $this->permissionRepository->findById($id);
        } catch (Exception $exception) {
            throw new PermissionNotFoundException($exception);
        }
    }
}
