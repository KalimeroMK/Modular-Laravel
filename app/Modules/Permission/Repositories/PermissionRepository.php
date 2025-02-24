<?php

namespace App\Modules\Permission\Repositories;

use App\Modules\Core\Interfaces\SearchInterface;
use App\Modules\Core\Repositories\Repository;
use App\Modules\Permission\Exceptions\PermissionSearchException;
use App\Modules\Permission\Interfaces\PermissionInterface;
use App\Modules\Permission\Models\Permission;
use Exception;
use Illuminate\Support\Arr;

class PermissionRepository extends Repository implements PermissionInterface, SearchInterface
{
    /**
     * @var string
     */
    public $model = Permission::class;

    /**
     * Perform a search on the resource.
     *
     * @param array $request
     * @return mixed
     * @throws PermissionSearchException
     */
    public function search(array $request): mixed
    {
        try {
            $query = $this->model::filterBy($request);

            $query->orderBy(
                Arr::get($request, 'order_by', 'id'),
                Arr::get($request, 'sort', 'desc')
            );

            if (Arr::has($request, 'list') && (bool)Arr::get($request, 'list') === true) {
                return $query->get();
            }

            return $query->paginate(Arr::get($request, 'per_page', (new $this->model)->getPerPage()));
        } catch (Exception $exception) {
            throw new PermissionSearchException($exception);
        }
    }
}
