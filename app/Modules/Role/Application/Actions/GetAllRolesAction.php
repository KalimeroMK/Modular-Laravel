<?php

declare(strict_types=1);

namespace App\Modules\Role\Application\Actions;

use App\Modules\Role\Infrastructure\Models\Role;
use App\Modules\Role\Infrastructure\Repositories\RoleRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class GetAllRolesAction
{
    public function __construct(
        protected RoleRepositoryInterface $roleRepository,
    ) {}

    /**
     * @return LengthAwarePaginator<int, Role>
     */
    public function execute(int $perPage = 15): LengthAwarePaginator
    {
        return $this->roleRepository->paginate($perPage);
    }
}
