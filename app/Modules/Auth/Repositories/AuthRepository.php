<?php

namespace App\Modules\Auth\Repositories;

use App\Modules\Auth\Exceptions\AuthSearchException;
use App\Modules\Auth\Interfaces\AuthInterface;
use App\Modules\Core\Interfaces\SearchInterface;
use App\Modules\Core\Repositories\Repository;
use App\Modules\User\Models\User;

class AuthRepository extends Repository implements AuthInterface, SearchInterface
{
    /**
     * @var string
     */
    public $model = User::class;

    /**
     * The SearchException class to use for search errors.
     */
    protected string $searchException = AuthSearchException::class;

    public function findByEmail(string $email): mixed
    {
        return $this->findBy('email', $email);
    }
}
