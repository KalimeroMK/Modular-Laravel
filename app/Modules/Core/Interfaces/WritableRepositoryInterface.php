<?php

declare(strict_types=1);

namespace App\Modules\Core\Interfaces;

use Illuminate\Database\Eloquent\Model;

interface WritableRepositoryInterface
{
    public function create(array $data): ?Model;

    public function insert(array $data): bool;

    public function update(int|string $id, array $data): ?Model;

    public function delete(int|string $id): bool;
}
