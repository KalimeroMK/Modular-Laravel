<?php

declare(strict_types=1);

namespace App\Modules\Core\Interfaces;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

interface ReadableRepositoryInterface
{
    public function all(array $with = []): Collection;

    public function find(int|string $id, array $with = []): ?Model;

    public function findOrFail(int|string $id, array $with = []): Model;

    public function findBy(string $column, mixed $value, array $with = []): ?Model;

    public function paginate(int $perPage = 15, array $with = []): \Illuminate\Contracts\Pagination\LengthAwarePaginator;
}
