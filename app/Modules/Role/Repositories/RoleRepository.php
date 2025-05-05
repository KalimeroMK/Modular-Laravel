<?php

namespace App\Modules\Role\Repositories;

use App\Modules\Core\Interfaces\SearchInterface;
use App\Modules\Core\Repositories\Repository;
use App\Modules\Role\Exceptions\RoleSearchException;
use App\Modules\Role\Interfaces\RoleInterface;
use App\Modules\Role\Models\Role;

class RoleRepository extends Repository implements RoleInterface, SearchInterface
{
    /**
     * @var string
     */
    public $model = Role::class;

    /**
     * The SearchException class to use for search errors.
     */
    protected string $searchException = RoleSearchException::class;
}
