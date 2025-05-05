<?php

namespace App\Modules\Permission\Http\Actions;

use App\Modules\Permission\Exceptions\PermissionStoreException;
use App\Modules\Permission\Http\DTOs\CreatePermissionDTO;
use App\Modules\Permission\Interfaces\PermissionInterface;
use Exception;

class CreatePermissionAction
{
    protected PermissionInterface $permissionRepository;

    public function __construct(PermissionInterface $permissionRepository)
    {
        $this->permissionRepository = $permissionRepository;
    }

    public function execute(CreatePermissionDTO $dto): mixed
    {
        try {
            return $this->permissionRepository->create($dto->toArray());
        } catch (Exception $exception) {
            throw new PermissionStoreException($exception);
        }
    }
}
