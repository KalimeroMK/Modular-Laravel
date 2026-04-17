<?php

declare(strict_types=1);

namespace App\Modules\Role\Application\Actions;

use App\Modules\Core\Application\Actions\AbstractCreateAction;
use App\Modules\Role\Infrastructure\Repositories\RoleRepositoryInterface;

class CreateRoleAction extends AbstractCreateAction
{
    public function __construct(RoleRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}
