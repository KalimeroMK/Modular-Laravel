<?php

namespace App\Modules\Permission\Http\Actions;

use App\Modules\Permission\Interfaces\PermissionInterface;
use Illuminate\Support\Collection;

class GetAllPermissionAction
{
    public function __construct(protected PermissionInterface $repository) {}

    public function execute(): Collection
    {
        return $this->repository->all();
    }
}
