<?php

namespace App\Modules\Role\Repositories;

use App\Modules\Core\Interfaces\SearchInterface;
use App\Modules\Core\Repositories\Repository;
use App\Modules\Role\Exceptions\RoleSearchException;
use App\Modules\Role\Interfaces\RoleInterface;
use App\Modules\Role\Models\Role;
use Exception;
use Illuminate\Support\Arr;

class RoleRepository extends Repository implements RoleInterface, SearchInterface
{
    /**
     * @var string
     */
    public $model = Role::class;

    /**
     * Perform a search on the resource.
     *
     * @param array $request
     * @return mixed
     * @throws RoleSearchException
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
            throw new RoleSearchException($exception);
        }
    }
}
