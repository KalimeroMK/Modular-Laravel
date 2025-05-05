<?php

namespace App\Modules\Permission\Http\Actions;

use App\Modules\Permission\Exceptions\PermissionIndexException;
use App\Modules\Permission\Interfaces\PermissionInterface;
use Exception;

class GetAllPermissionAction
{
    protected PermissionInterface $permissionRepository;

    public function __construct(PermissionInterface $permissionRepository)
    {
        $this->permissionRepository = $permissionRepository;
    }

    public function execute(): mixed
    {
        try {
            return $this->permissionRepository->findAll();
        } catch (Exception $exception) {
            throw new PermissionIndexException($exception);
        }
    }
}
