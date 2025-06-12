<?php

namespace App\Modules\Permission\Http\Actions;

use App\Modules\Permission\Interfaces\PermissionInterface;

class GetAllPermissionAction
{
    public function __construct(protected PermissionInterface $repository) {}

    public function execute(): mixed
    {
        return $this->repository->findAll();
    }
}