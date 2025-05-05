<?php

namespace App\Modules\Core\Repositories;

use App\Modules\Core\Interfaces\RepositoryInterface;
use Exception;
use Illuminate\Support\Arr;

class Repository implements RepositoryInterface
{
    /**
     * Model::class
     */
    public $model;

    /**
     * The SearchException class to use for search errors.
     */
    protected string $searchException = Exception::class;

    public function findAll(): mixed
    {
        return $this->model::all();
    }

    public function findBy(string $column, $value): mixed
    {
        return $this->model::where($column, $value)->first();
    }

    public function create(array $data): mixed
    {
        return $this->model::create($data)->fresh();
    }

    public function insert(array $data): mixed
    {
        return $this->model::insert($data);
    }

    public function update(int $id, array $data): mixed
    {
        $item = $this->findById($id);
        $item->fill($data);
        $item->save();

        return $item->fresh();
    }

    public function findById(int $id): mixed
    {
        return $this->model::find($id);
    }

    public function delete(int $id): mixed
    {
        $this->model::destroy($id);
    }

    public function restore(int $id): mixed
    {
        $object = $this->findByIdWithTrashed($id);
        if ($object && method_exists($this->model, 'isSoftDelete')) {
            $object->restore($id);

            return $object;
        }
    }

    public function findByIdWithTrashed(int $id): mixed
    {
        if (method_exists($this->model, 'isSoftDelete')) {
            return $this->model::withTrashed()->find($id);
        }
    }

    /**
     * Perform a search on the resource.
     *
     * @throws Exception
     */
    public function search(array $request): mixed
    {
        try {
            $query = $this->model::filterBy($request);

            $query->orderBy(
                Arr::get($request, 'order_by', 'id'),
                Arr::get($request, 'sort', 'desc')
            );

            if (Arr::has($request, 'list') && (bool) Arr::get($request, 'list') === true) {
                return $query->get();
            }

            return $query->paginate(Arr::get($request, 'per_page', (new $this->model)->getPerPage()));
        } catch (Exception $exception) {
            $exceptionClass = $this->searchException;
            throw new $exceptionClass($exception);
        }
    }
}
