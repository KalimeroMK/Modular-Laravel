<?php

declare(strict_types=1);

namespace App\Modules\Role\Http\Actions;

use App\Modules\Role\Interfaces\RoleInterface;
use App\Modules\Role\Models\Role;
use Illuminate\Database\Eloquent\Model;

class GetRoleByIdAction
{
    public function __construct(protected RoleInterface $repository) {}

    public function execute(Role $role): Model
    {
        return $this->repository->findOrFail((int) $role->id);
    }
}
