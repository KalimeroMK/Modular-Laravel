<?php

declare(strict_types=1);

namespace App\Modules\User\Application\Actions;

use App\Modules\User\Infrastructure\Repositories\UserRepositoryInterface;

class DeleteUserAction
{
    public function __construct(
        protected UserRepositoryInterface $userRepository,
    ) {}

    public function execute(int $id): bool
    {
        // Validate that the user exists
        $this->userRepository->findOrFail($id);

        return $this->userRepository->delete($id);
    }
}
