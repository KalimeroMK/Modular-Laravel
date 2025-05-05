<?php

namespace App\Modules\User\Repositories;

use App\Modules\Core\Interfaces\SearchInterface;
use App\Modules\Core\Repositories\Repository;
use App\Modules\User\Exceptions\UserSearchException;
use App\Modules\User\Interfaces\UserInterface;
use App\Modules\User\Models\User;

class UserRepository extends Repository implements SearchInterface, UserInterface
{
    /**
     * @var string
     */
    public $model = User::class;

    /**
     * The SearchException class to use for search errors.
     */
    protected string $searchException = UserSearchException::class;
}
