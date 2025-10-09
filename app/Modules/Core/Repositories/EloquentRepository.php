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
     * @param  array<int, string>  $with
     * @return Collection<int, Model>
     */
    final public function all(array $with = []): Collection
    {
        $query = $this->query();
        
        if (!empty($with)) {
            $query->with($with);
        }
        
        return $query->get();
    }

    /**
     * @param  array<int, string>  $with
     */
    final public function find(int $id, array $with = []): ?Model
    {
        $query = $this->query();
        
        if (!empty($with)) {
            $query->with($with);
        }
        
        return $query->find($id);
    }

    /**
     * @param  array<int, string>  $with
     */
    final public function findOrFail(int $id, array $with = []): Model
    {
        $query = $this->query();
        
        if (!empty($with)) {
            $query->with($with);
        }
        
        return $query->findOrFail($id);
    }

    /**
     * @param  array<int, string>  $with
     */
    final public function findBy(string $column, mixed $value, array $with = []): ?Model
    {
        $query = $this->query()->where($column, $value);
        
        if (!empty($with)) {
            $query->with($with);
        }
        
        return $query->first();
    }

    /**
     * @param  array<string, mixed>  $data
     */
    final public function create(array $data): ?Model
    {
        $created = $this->model->newInstance()->create($data);

        return $created ? $created->fresh() : null;
    }

    /**
     * @param  array<int, array<string, mixed>>  $data
     */
    final public function insert(array $data): bool
    {
        return $this->model->newInstance()->insert($data);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    final public function update(int $id, array $data): ?Model
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

        $query = $this->model->newQuery();
        if (method_exists($query, 'withTrashed')) {
            $model = $query->withTrashed()->find($id);
        } else {
            $model = $query->find($id);
        }

        if ($model) {
            $model->restore();
        }

        return $model;
    }

    final public function findWithTrashed(int $id): ?Model
    {
        $query = $this->model->newQuery();
        if (method_exists($query, 'withTrashed')) {
            return $query->withTrashed()->find($id);
        }

        return $query->find($id);
    }

    /**
     * @return Builder<Model>
     */
    protected function query(): Builder
    {
        return $this->model->newQuery();
    }

    /**
     * Get paginated results with optional eager loading
     * 
     * @param  array<int, string>  $with
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    final public function paginate(int $perPage = 15, array $with = []): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $query = $this->query();
        
        if (!empty($with)) {
            $query->with($with);
        }
        
        return $query->paginate($perPage);
    }

    /**
     * Get cached results for expensive queries
     * 
     * @param  array<int, string>  $with
     * @param  int  $ttl Cache time in seconds
     * @return Collection<int, Model>
     */
    final public function allCached(array $with = [], int $ttl = 3600): Collection
    {
        $cacheKey = $this->getCacheKey('all', $with);
        
        return \Illuminate\Support\Facades\Cache::remember($cacheKey, $ttl, function () use ($with) {
            return $this->all($with);
        });
    }

    /**
     * Get cached single record
     * 
     * @param  array<int, string>  $with
     * @param  int  $ttl Cache time in seconds
     */
    final public function findCached(int $id, array $with = [], int $ttl = 3600): ?Model
    {
        $cacheKey = $this->getCacheKey('find', $with, $id);
        
        return \Illuminate\Support\Facades\Cache::remember($cacheKey, $ttl, function () use ($id, $with) {
            return $this->find($id, $with);
        });
    }

    /**
     * Clear cache for this model
     */
    final public function clearCache(): void
    {
        $pattern = $this->getCacheKey('*');
        \Illuminate\Support\Facades\Cache::forget($pattern);
    }

    /**
     * Generate cache key for this model
     * 
     * @param  array<int, string>  $with
     */
    protected function getCacheKey(string $method, array $with = [], ?int $id = null): string
    {
        $modelName = class_basename($this->model);
        $withString = !empty($with) ? '_' . implode('_', $with) : '';
        $idString = $id ? "_$id" : '';
        
        return "repository_{$modelName}_{$method}{$withString}{$idString}";
    }
}
