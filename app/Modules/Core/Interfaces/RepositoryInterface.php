<?php

declare(strict_types=1);

namespace App\Modules\Core\Interfaces;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

interface RepositoryInterface
{
    /**
     * @param  array<int, string>  $with
     * @return Collection<int, Model>
     */
    public function all(array $with = []): Collection;

    /**
     * @param  array<int, string>  $with
     */
    public function find(int $id, array $with = []): ?Model;

    /**
     * @param  array<int, string>  $with
     */
    public function findOrFail(int $id, array $with = []): Model;

    /**
     * @param  array<int, string>  $with
     */
    public function findBy(string $column, mixed $value, array $with = []): ?Model;

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): ?Model;

    /**
     * @param  array<int, array<string, mixed>>  $data
     */
    public function insert(array $data): bool;

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(int $id, array $data): ?Model;

    public function delete(int $id): bool;

    public function restore(int $id): ?Model;

    public function findWithTrashed(int $id): ?Model;
}
