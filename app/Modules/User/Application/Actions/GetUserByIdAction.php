<?php

declare(strict_types=1);

namespace App\Modules\User\Application\Actions;

use App\Modules\User\Infrastructure\Models\User;
use App\Modules\User\Infrastructure\Repositories\UserRepositoryInterface;

class GetUserByIdAction
{
    public function __construct(protected UserRepositoryInterface $repository) {}

    public function execute(int $id): User
    {
        return $this->repository->findOrFail($id);
    }
}
