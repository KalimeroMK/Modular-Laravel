<?php

namespace App\Modules\User\Http\Actions;

use App\Modules\User\Exceptions\UserIndexException;
use App\Modules\User\Interfaces\UserInterface;
use Exception;

class GetAllUserAction
{
    protected UserInterface $userRepository;

    public function __construct(UserInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function execute(): mixed
    {
        try {
            return $this->userRepository->findAll();
        } catch (Exception $exception) {
            throw new UserIndexException($exception);
        }
    }
}
