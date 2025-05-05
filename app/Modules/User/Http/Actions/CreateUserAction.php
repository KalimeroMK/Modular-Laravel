<?php

namespace App\Modules\User\Http\Actions;

use App\Modules\User\Exceptions\UserStoreException;
use App\Modules\User\Http\DTOs\CreateUserDTO;
use App\Modules\User\Interfaces\UserInterface;
use Exception;

class CreateUserAction
{
    protected UserInterface $userRepository;

    public function __construct(UserInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function execute(CreateUserDTO $dto): mixed
    {
        try {
            return $this->userRepository->create($dto->toArray());
        } catch (Exception $exception) {
            throw new UserStoreException($exception);
        }
    }
}
