<?php

declare(strict_types=1);

namespace App\Modules\User\Application\Actions;

use App\Modules\User\Infrastructure\Models\User;
use App\Modules\User\Infrastructure\Repositories\UserRepositoryInterface;

class DeleteUserAction
{
    public function __construct(
        protected UserRepositoryInterface $userRepository,
    ) {}

    public function execute(User $user): bool
    {
        // Get the user ID - route model binding ensures the model exists
        $userId = (int) $user->getKey();

        if ($userId === 0) {
            return false;
        }

        return $this->userRepository->delete($userId);
    }
}
