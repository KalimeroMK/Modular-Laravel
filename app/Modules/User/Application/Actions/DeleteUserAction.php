<?php

declare(strict_types=1);

namespace App\Modules\User\Application\Actions;

use App\Modules\Core\Application\Actions\AbstractDeleteAction;
use App\Modules\User\Infrastructure\Repositories\UserRepositoryInterface;

class DeleteUserAction extends AbstractDeleteAction
{
    public function __construct(UserRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}
