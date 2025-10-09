<?php

declare(strict_types=1);

namespace App\Modules\Permission\Infrastructure\Repositories;

use App\Modules\Core\Interfaces\RepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\Permission\Models\Permission;

interface PermissionRepositoryInterface extends RepositoryInterface
{
    public function findByName(string $name): ?Permission;

    public function paginate(int $perPage = 15): LengthAwarePaginator;
}
