<?php

declare(strict_types=1);

namespace App\Modules\Core\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

abstract class EloquentRepository
{
    protected Model $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    protected function query(): Builder
    {
        return $this->model->newQuery();
    }

    public function all(array $with = []): Collection
    {
        return $this->query()->with($with)->get();
    }

    public function find(int $id, array $with = []): ?Model
    {
        return $this->query()->with($with)->find($id);
    }

    public function findOrFail(int $id, array $with = []): Model
    {
        return $this->query()->with($with)->findOrFail($id);
    }

    public function findBy(string $column, mixed $value, array $with = []): ?Model
    {
        return $this->query()->with($with)->where($column, $value)->first();
    }

    public function create(array $data): Model
    {
        return $this->model->newInstance()->create($data)->fresh();
    }

    public function insert(array $data): bool
    {
        return $this->model->newInstance()->insert($data);
    }

    public function update(int $id, array $data): Model
    {
        $model = $this->findOrFail($id);
        $model->fill($data)->save();

        return $model->fresh();
    }

    public function delete(int $id): bool
    {
        return (bool) $this->model->destroy($id);
    }

    public function restore(int $id): ?Model
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

    public function findWithTrashed(int $id): ?Model
    {
        return $this->model->withTrashed()->find($id);
    }
}
