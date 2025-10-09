<?php

declare(strict_types=1);

namespace App\Modules\Permission\Infrastructure\Repositories;

use App\Modules\Core\Repositories\EloquentRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\Permission\Models\Permission;

class PermissionRepository extends EloquentRepository implements PermissionRepositoryInterface
{
    public function __construct(Permission $model)
    {
        parent::__construct($model);
    }

    public function findByName(string $name): ?Permission
    {
        /** @var Permission|null $result */
        $result = $this->findBy('name', $name);

        return $result;
    }

    /**
     * @return LengthAwarePaginator<int, Permission>
     */
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        /** @var LengthAwarePaginator<int, Permission> $result */
        $result = $this->query()->paginate($perPage);

        return $result;
    }
}
