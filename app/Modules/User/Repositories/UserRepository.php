<?php

namespace App\Modules\User\Repositories;

use App\Modules\Core\Interfaces\SearchInterface;
use App\Modules\Core\Repositories\Repository;
use App\Modules\User\Exceptions\UserSearchException;
use App\Modules\User\Interfaces\UserInterface;
use App\Modules\User\Models\User;
use Exception;
use Illuminate\Support\Arr;

class UserRepository extends Repository implements UserInterface, SearchInterface
{
    /**
     * @var string
     */
    public $model = User::class;

    /**
     * Perform a search on the resource.
     *
     * @param array $request
     * @return mixed
     * @throws UserSearchException
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
            throw new UserSearchException($exception);
        }
    }
}
