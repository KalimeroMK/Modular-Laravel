<?php

declare(strict_types=1);

namespace App\Modules\Permission\Http\Actions;

use App\Modules\Permission\Interfaces\PermissionInterface;
use App\Modules\Permission\Models\Permission;
use Illuminate\Database\Eloquent\Model;

class GetPermissionByIdAction
{
    public function __construct(protected PermissionInterface $repository) {}

    public function execute(Permission $permission): Model
    {
        return $this->repository->findOrFail((int) $permission->id);
    }
}
