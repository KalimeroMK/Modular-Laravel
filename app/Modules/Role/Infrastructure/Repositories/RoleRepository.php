<?php

declare(strict_types=1);

namespace App\Modules\Role\Infrastructure\Repositories;

use App\Modules\Core\Repositories\EloquentRepository;
use App\Modules\Role\Infrastructure\Models\Role;
use Illuminate\Pagination\LengthAwarePaginator;

class RoleRepository extends EloquentRepository implements RoleRepositoryInterface
{
    public function __construct(Role $model)
    {
        parent::__construct($model);
    }

    public function findByName(string $name): ?Role
    {
        /** @var Role|null $result */
        $result = $this->findBy('name', $name);

        return $result;
    }

    /**
     * @return LengthAwarePaginator<int, Role>
     */
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        /** @var LengthAwarePaginator<int, Role> $result */
        $result = $this->query()->paginate($perPage);

        return $result;
    }
}
