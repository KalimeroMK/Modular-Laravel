<?php

declare(strict_types=1);

namespace App\Modules\Permission\Http\Actions;

use App\Modules\Permission\Interfaces\PermissionInterface;
use App\Modules\Permission\Models\Permission;

class DeletePermissionAction
{
    public function __construct(protected PermissionInterface $repository) {}

    public function execute(Permission $permission): void
    {
        $this->repository->delete($permission->id);
    }
}
