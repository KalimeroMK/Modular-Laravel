<?php

namespace App\Modules\Auth\Http\Actions;

use App\Modules\Auth\Exceptions\AuthSearchException;
use App\Modules\Auth\Http\DTOs\SearchAuthDTO;
use App\Modules\Auth\Interfaces\AuthInterface;
use Exception;

class SearchAuthAction
{
    protected AuthInterface $authRepository;

    public function __construct(AuthInterface $authRepository)
    {
        $this->authRepository = $authRepository;
    }

    public function execute(SearchAuthDTO $dto): mixed
    {
        try {
            return $this->authRepository->search($dto->toArray());
        } catch (Exception $exception) {
            throw new AuthSearchException($exception);
        }
    }
}
