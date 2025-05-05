<?php

namespace App\Modules\User\Http\Actions;

use App\Modules\User\Exceptions\UserNotFoundException;
use App\Modules\User\Interfaces\UserInterface;
use Exception;

class GetUserByIdAction
{
    protected UserInterface $userRepository;

    public function __construct(UserInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function execute(int $id): mixed
    {
        try {
            return $this->userRepository->findById($id);
        } catch (Exception $exception) {
            throw new UserNotFoundException($exception);
        }
    }
}
