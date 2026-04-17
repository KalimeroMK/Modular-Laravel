<?php

declare(strict_types=1);

namespace App\Modules\Role\Application\Actions;

use App\Modules\Core\Application\Actions\AbstractGetAllAction;
use App\Modules\Role\Infrastructure\Repositories\RoleRepositoryInterface;

class GetAllRolesAction extends AbstractGetAllAction
{
    public function __construct(RoleRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}
