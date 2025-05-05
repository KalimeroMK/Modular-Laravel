<?php

namespace App\Modules\Core\Interfaces;

interface RepositoryInterface
{
    public function findAll(): mixed;

    public function findById(int $id): mixed;

    public function findBy(string $column, $value): mixed;

    public function create(array $data): mixed;

    public function update(int $id, array $data): mixed;

    public function delete(int $id): mixed;

    public function restore(int $id): mixed;

    public function findByIdWithTrashed(int $id): mixed;
}
