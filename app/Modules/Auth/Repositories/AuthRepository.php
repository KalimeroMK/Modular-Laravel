<?php

namespace App\Modules\Auth\Repositories;

use App\Modules\Core\Interfaces\SearchInterface;
use App\Modules\Core\Repositories\Repository;
use App\Modules\Auth\Exceptions\AuthSearchException;
use App\Modules\Auth\Interfaces\AuthInterface;
use App\Modules\Auth\Models\Auth;
use Exception;
use Illuminate\Support\Arr;

class AuthRepository extends Repository implements AuthInterface, SearchInterface
{
    /**
     * @var string
     */
    public $model = Auth::class;

    /**
     * Perform a search on the resource.
     *
     * @param array $request
     * @return mixed
     * @throws AuthSearchException
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
            throw new AuthSearchException($exception);
        }
    }
}
