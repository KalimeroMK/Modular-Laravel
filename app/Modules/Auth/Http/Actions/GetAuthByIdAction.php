<?php

namespace App\Modules\Auth\Http\Actions;

use App\Modules\Auth\Exceptions\AuthNotFoundException;
use App\Modules\Auth\Interfaces\AuthInterface;
use Exception;

class GetAuthByIdAction
{
    protected AuthInterface $authRepository;

    public function __construct(AuthInterface $authRepository)
    {
        $this->authRepository = $authRepository;
    }

    public function execute(int $id): mixed
    {
        try {
            return $this->authRepository->findById($id);
        } catch (Exception $exception) {
            throw new AuthNotFoundException($exception);
        }
    }
}
