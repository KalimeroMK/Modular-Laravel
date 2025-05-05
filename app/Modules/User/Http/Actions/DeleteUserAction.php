<?php

namespace App\Modules\User\Http\Actions;

use App\Modules\User\Exceptions\UserDestroyException;
use App\Modules\User\Interfaces\UserInterface;
use Exception;

class DeleteUserAction
{
    protected UserInterface $userRepository;

    public function __construct(UserInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function execute(int $id): void
    {
        try {
            $this->userRepository->delete($id);
        } catch (Exception $exception) {
            throw new UserDestroyException($exception);
        }
    }
}
