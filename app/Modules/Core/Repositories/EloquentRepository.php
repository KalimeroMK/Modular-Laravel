<?php

declare(strict_types=1);

namespace App\Modules\Core\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

abstract class EloquentRepository
{
    public function __construct(

        protected Model $model
    ) {}

    final public function all(array $with = []): Collection
    {
        $query = $this->query();
        if ($with !== []) {
            $query->with($with);
        }

        return $query->get();
    }

    final public function find(int|string $id, array $with = []): ?Model
    {
        $query = $this->query();
        if ($with !== []) {
            $query->with($with);
        }

        return $query->find($id);
    }

    final public function findOrFail(int|string $id, array $with = []): Model
    {
        $query = $this->query();
        if ($with !== []) {
            $query->with($with);
        }

        return $query->findOrFail($id);
    }

    final public function findBy(string $column, mixed $value, array $with = []): ?Model
    {
        $query = $this->query()->where($column, $value);
        if ($with !== []) {
            $query->with($with);
        }

        return $query->first();
    }

    final public function create(array $data): ?Model
    {
        $created = $this->model->newInstance()->create($data);
        $this->invalidateCache();

        return $created ? $created->fresh() : null;
    }

    final public function insert(array $data): bool
    {
        $result = $this->model->newInstance()->insert($data);
        $this->invalidateCache();

        return $result;
    }

    final public function update(int|string $id, array $data): ?Model
    {
        $model = $this->findOrFail($id);
        $model->fill($data)->save();
        $this->invalidateCache($id);

        return $model->fresh();
    }

    final public function delete(int|string $id): bool
    {
        $model = $this->findOrFail($id);
        $deleted = $model->delete();
        $this->invalidateCache($id);

        return $deleted;
    }

    final public function restore(int|string $id): ?Model
    {
        if (! method_exists($this->model, 'restore')) {
            return null;
        }

        $query = $this->model->newQuery();
        $model = method_exists($query, 'withTrashed') ? $query->withTrashed()->find($id) : $query->find($id);
        if ($model) {
            $model->restore();
            $this->invalidateCache($id);
        }

        return $model;
    }

    final public function findWithTrashed(int|string $id): ?Model
    {
        $query = $this->model->newQuery();

        return method_exists($query, 'withTrashed') ? $query->withTrashed()->find($id) : $query->find($id);
    }

    final public function paginate(int $perPage = 15, array $with = []): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $query = $this->query();
        if ($with !== []) {
            $query->with($with);
        }

        return $query->paginate($perPage);
    }

    final public function allCached(array $with = [], int $ttl = 3600): Collection
    {
        $cacheKey = $this->getCacheKey('all', $with);
        $this->trackCacheKey($cacheKey);

        return \Illuminate\Support\Facades\Cache::remember($cacheKey, $ttl, fn () => $this->all($with));
    }

    final public function findCached(int|string $id, array $with = [], int $ttl = 3600): ?Model
    {
        $cacheKey = $this->getCacheKey('find', $with, $id);
        $this->trackCacheKey($cacheKey);

        return \Illuminate\Support\Facades\Cache::remember($cacheKey, $ttl, fn () => $this->find($id, $with));
    }

    final public function clearCache(): void
    {
        $registryKey = "repository_{$this->getModelBaseName()}_keys";
        $keys = \Illuminate\Support\Facades\Cache::get($registryKey, []);

        foreach ($keys as $key) {
            \Illuminate\Support\Facades\Cache::forget($key);
        }

        \Illuminate\Support\Facades\Cache::forever($registryKey, []);
    }

    protected function query(): Builder
    {
        return $this->model->newQuery();
    }

    protected function getCacheKey(string $method, array $with = [], int|string|null $id = null): string
    {
        $modelName = class_basename($this->model);
        $withString = $with === [] ? '' : '_'.implode('_', $with);
        $idString = $id ? "_$id" : '';

        return "repository_{$modelName}_{$method}{$withString}{$idString}";
    }

    protected function trackCacheKey(string $key): void
    {
        $registryKey = "repository_{$this->getModelBaseName()}_keys";
        $keys = \Illuminate\Support\Facades\Cache::get($registryKey, []);
        if (! in_array($key, $keys, true)) {
            $keys[] = $key;
            \Illuminate\Support\Facades\Cache::forever($registryKey, $keys);
        }
    }

    protected function invalidateCache(int|string|null $id = null): void
    {
        $registryKey = "repository_{$this->getModelBaseName()}_keys";
        $keys = \Illuminate\Support\Facades\Cache::get($registryKey, []);
        $remaining = [];

        foreach ($keys as $key) {
            $shouldRemove = false;
            if (str_contains($key, "_all")) {
                $shouldRemove = true;
            } elseif ($id !== null && preg_match("/_\\Q{$id}\\E$/", $key)) {
                $shouldRemove = true;
            }

            if ($shouldRemove) {
                \Illuminate\Support\Facades\Cache::forget($key);
            } else {
                $remaining[] = $key;
            }
        }

        \Illuminate\Support\Facades\Cache::forever($registryKey, $remaining);
    }

    protected function getModelBaseName(): string
    {
        return class_basename($this->model);
    }
}
