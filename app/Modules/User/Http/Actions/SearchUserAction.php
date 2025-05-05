<?php

namespace App\Modules\User\Http\Actions;

use App\Modules\User\Exceptions\UserSearchException;
use App\Modules\User\Http\DTOs\SearchUserDTO;
use App\Modules\User\Interfaces\UserInterface;
use Exception;

class SearchUserAction
{
    protected UserInterface $userRepository;

    public function __construct(UserInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function execute(SearchUserDTO $dto): mixed
    {
        try {
            return $this->userRepository->search($dto->toArray());
        } catch (Exception $exception) {
            throw new UserSearchException($exception);
        }
    }
}
