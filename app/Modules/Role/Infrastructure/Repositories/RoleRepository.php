<?php

declare(strict_types=1);

namespace App\Modules\Role\Infrastructure\Repositories;

use App\Modules\Core\Repositories\EloquentRepository;
use App\Modules\Role\Infrastructure\Models\Role;

/**
 * @extends EloquentRepository<Role>
 */
class RoleRepository extends EloquentRepository implements RoleRepositoryInterface
{
    public function findByName(string $name): ?Role
    {
        /** @var Role|null $result */
        $result = $this->findBy('name', $name);

        return $result;
    }
}
