<?php

declare(strict_types=1);

namespace App\Modules\Role\Application\Actions;

use App\Modules\Core\Application\Actions\AbstractGetByIdAction;
use App\Modules\Role\Infrastructure\Repositories\RoleRepositoryInterface;

class GetRoleByIdAction extends AbstractGetByIdAction
{
    public function __construct(RoleRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}
