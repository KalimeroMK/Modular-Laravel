<?php

declare(strict_types=1);

namespace App\Modules\Role\Infrastructure\Repositories;

use App\Modules\Core\Interfaces\RepositoryInterface;
use App\Modules\Role\Infrastructure\Models\Role;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface RoleRepositoryInterface extends RepositoryInterface
{
    public function findByName(string $name): ?Role;

    /**
     * @param  array<int, string>  $with
     * @return LengthAwarePaginator<int, Role>
     *
     * @phpstan-return LengthAwarePaginator<int, Role>
     */
    public function paginate(int $perPage = 15, array $with = []): LengthAwarePaginator;
}
