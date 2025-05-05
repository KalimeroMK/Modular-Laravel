<?php

namespace App\Modules\Permission\Http\Actions;

use App\Modules\Permission\Exceptions\PermissionDestroyException;
use App\Modules\Permission\Interfaces\PermissionInterface;
use Exception;

class DeletePermissionAction
{
    protected PermissionInterface $permissionRepository;

    public function __construct(PermissionInterface $permissionRepository)
    {
        $this->permissionRepository = $permissionRepository;
    }

    public function execute(int $id): void
    {
        try {
            $this->permissionRepository->delete($id);
        } catch (Exception $exception) {
            throw new PermissionDestroyException($exception);
        }
    }
}
