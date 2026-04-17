<?php

declare(strict_types=1);

namespace App\Modules\User\Application\Actions;

use App\Modules\Core\Application\Actions\AbstractGetAllAction;
use App\Modules\User\Infrastructure\Repositories\UserRepositoryInterface;

class GetAllUsersAction extends AbstractGetAllAction
{
    public function __construct(UserRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}
