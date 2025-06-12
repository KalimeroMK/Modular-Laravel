<?php

declare(strict_types=1);

namespace App\Modules\Core\Interfaces;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

interface RepositoryInterface
{
    public function all(array $with = []): Collection;

    public function find(int $id, array $with = []): ?Model;

    public function findOrFail(int $id, array $with = []): Model;

    public function findBy(string $column, mixed $value, array $with = []): ?Model;

    public function create(array $data): Model;

    public function insert(array $data): bool;

    public function update(int $id, array $data): Model;

    public function delete(int $id): bool;

    public function restore(int $id): ?Model;

    public function findWithTrashed(int $id): ?Model;
}
