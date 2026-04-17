<?php

declare(strict_types=1);

namespace App\Modules\Permission\Application\Actions;

use App\Modules\Core\Application\Actions\AbstractCreateAction;
use App\Modules\Permission\Infrastructure\Repositories\PermissionRepositoryInterface;

class CreatePermissionAction extends AbstractCreateAction
{
    public function __construct(PermissionRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}
