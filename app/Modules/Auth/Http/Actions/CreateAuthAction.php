<?php

namespace App\Modules\Auth\Http\Actions;

use App\Modules\Auth\Exceptions\AuthStoreException;
use App\Modules\Auth\Http\DTOs\CreateAuthDTO;
use App\Modules\Auth\Interfaces\AuthInterface;
use Exception;

class CreateAuthAction
{
    protected AuthInterface $authRepository;

    public function __construct(AuthInterface $authRepository)
    {
        $this->authRepository = $authRepository;
    }

    /**
     * @throws AuthStoreException
     */
    public function execute(CreateAuthDTO $dto): mixed
    {
        try {
            return $this->authRepository->create($dto->toArray());
        } catch (Exception $exception) {
            throw new AuthStoreException($exception);
        }
    }
}
