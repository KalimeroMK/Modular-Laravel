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

    /**
     * @param array<int, string> $with
     * @return Collection<int, Model>
     */
    final public function all(array $with = []): Collection
    {
        return $this->query()->with($with)->get();
    }

    /**
     * @param array<int, string> $with
     */
    final public function find(int $id, array $with = []): ?Model
    {
        return $this->query()->with($with)->find($id);
    }

    /**
     * @param array<int, string> $with
     */
    final public function findOrFail(int $id, array $with = []): Model
    {
        return $this->query()->with($with)->findOrFail($id);
    }

    /**
     * @param array<int, string> $with
     */
    final public function findBy(string $column, mixed $value, array $with = []): ?Model
    {
        return $this->query()->with($with)->where($column, $value)->first();
    }

    /**
     * @param array<string, mixed> $data
     * @return Model|null
     */
    final public function create(array $data): ?Model
    {
        $created = $this->model->newInstance()->create($data);
        return $created ? $created->fresh() : null;
    }

    /**
     * @param array<int, array<string, mixed>> $data
     */
    final public function insert(array $data): bool
    {
        return $this->model->newInstance()->insert($data);
    }

    /**
     * @param array<string, mixed> $data
     * @return Model|null
     */
    final public function update(int $id, array $data): ?Model
    {
        $model = $this->findOrFail($id);
        if ($model) {
            $model->fill($data)->save();
            return $model->fresh();
        }
        return null;
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

    /**
     * @return Builder<Model>
     */
    protected function query(): Builder
    {
        return $this->model->newQuery();
    }
}
