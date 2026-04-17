<?php

declare(strict_types=1);

namespace App\Modules\Permission\Application\Actions;

use App\Modules\Core\Application\Actions\AbstractGetByIdAction;
use App\Modules\Permission\Infrastructure\Repositories\PermissionRepositoryInterface;

class GetPermissionByIdAction extends AbstractGetByIdAction
{
    public function __construct(PermissionRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}
