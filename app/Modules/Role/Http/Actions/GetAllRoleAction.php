<?php

namespace App\Modules\Role\Http\Actions;

use App\Modules\Role\Interfaces\RoleInterface;
use Illuminate\Support\Collection;

class GetAllRoleAction
{
    public function __construct(protected RoleInterface $repository) {}

    public function execute(): Collection
    {
        return $this->repository->all();
    }
}
