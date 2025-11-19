<?php

declare(strict_types=1);

namespace App\Modules\Permission\Application\Actions;

use App\Modules\Permission\Infrastructure\Models\Permission;
use App\Modules\Permission\Infrastructure\Repositories\PermissionRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class GetAllPermissionsAction
{
    public function __construct(
        protected PermissionRepositoryInterface $permissionRepository,
    ) {}

    /**
     * @return LengthAwarePaginator<int, Permission>
     */
    public function execute(int $perPage = 15): LengthAwarePaginator
    {
        return $this->permissionRepository->paginate($perPage);
    }
}
