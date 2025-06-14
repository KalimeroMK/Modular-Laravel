<?php

declare(strict_types=1);

namespace App\Modules\Core\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

abstract class EloquentRepository
{
    protected Model $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    final public function all(array $with = []): Collection
    {
        return $this->query()->with($with)->get();
    }

    final public function find(int $id, array $with = []): ?Model
    {
        return $this->query()->with($with)->find($id);
    }

    final public function findOrFail(int $id, array $with = []): Model
    {
        return $this->query()->with($with)->findOrFail($id);
    }

    final public function findBy(string $column, mixed $value, array $with = []): ?Model
    {
        return $this->query()->with($with)->where($column, $value)->first();
    }

    final public function create(array $data): Model
    {
        return $this->model->newInstance()->create($data)->fresh();
    }

    final public function insert(array $data): bool
    {
        return $this->model->newInstance()->insert($data);
    }

    final public function update(int $id, array $data): Model
    {
        $model = $this->findOrFail($id);
        $model->fill($data)->save();

        return $model->fresh();
    }

    final public function delete(int $id): bool
    {
        return (bool) $this->model->destroy($id);
    }

    final public function restore(int $id): ?Model
    {
        if (! method_exists($this->model, 'restore')) {
            return null;
        }

        $model = $this->model->withTrashed()->find($id);

        if ($model) {
            $model->restore();
        }

        return $model;
    }

    final public function findWithTrashed(int $id): ?Model
    {
        return $this->model->withTrashed()->find($id);
    }

    protected function query(): Builder
    {
        return $this->model->newQuery();
    }
}
