<?php

declare(strict_types=1);

namespace App\Modules\User\Application\Actions;

use App\Modules\Core\Application\Actions\AbstractGetByIdAction;
use App\Modules\User\Infrastructure\Repositories\UserRepositoryInterface;

class GetUserByIdAction extends AbstractGetByIdAction
{
    public function __construct(UserRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}
