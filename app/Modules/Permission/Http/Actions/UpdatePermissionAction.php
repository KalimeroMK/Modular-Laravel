<?php

namespace App\Modules\Permission\Http\Actions;

use App\Modules\Permission\Exceptions\PermissionUpdateException;
use App\Modules\Permission\Http\DTOs\UpdatePermissionDTO;
use App\Modules\Permission\Interfaces\PermissionInterface;
use Exception;

class UpdatePermissionAction
{
    protected PermissionInterface $permissionRepository;

    public function __construct(PermissionInterface $permissionRepository)
    {
        $this->permissionRepository = $permissionRepository;
    }

    public function execute(int $id, UpdatePermissionDTO $dto): mixed
    {
        try {
            return $this->permissionRepository->update($id, $dto->toArray());
        } catch (Exception $exception) {
            throw new PermissionUpdateException($exception);
        }
    }
}
