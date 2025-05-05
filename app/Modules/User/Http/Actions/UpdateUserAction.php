<?php

namespace App\Modules\User\Http\Actions;

use App\Modules\User\Exceptions\UserUpdateException;
use App\Modules\User\Http\DTOs\UpdateUserDTO;
use App\Modules\User\Interfaces\UserInterface;
use Exception;

class UpdateUserAction
{
    protected UserInterface $userRepository;

    public function __construct(UserInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function execute(int $id, UpdateUserDTO $dto): mixed
    {
        try {
            return $this->userRepository->update($id, $dto->toArray());
        } catch (Exception $exception) {
            throw new UserUpdateException($exception);
        }
    }
}
