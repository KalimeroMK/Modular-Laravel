<?php

namespace App\Modules\Permission\Repositories;

use App\Modules\Core\Interfaces\SearchInterface;
use App\Modules\Core\Repositories\Repository;
use App\Modules\Permission\Exceptions\PermissionSearchException;
use App\Modules\Permission\Interfaces\PermissionInterface;
use App\Modules\Permission\Models\Permission;

class PermissionRepository extends Repository implements PermissionInterface, SearchInterface
{
    /**
     * @var string
     */
    public $model = Permission::class;

    /**
     * The SearchException class to use for search errors.
     */
    protected string $searchException = PermissionSearchException::class;
}
