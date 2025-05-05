<?php

namespace App\Modules\Auth\Http\Actions;

use App\Modules\Auth\Exceptions\AuthIndexException;
use App\Modules\Auth\Interfaces\AuthInterface;
use Exception;

class GetAllAuthAction
{
    protected AuthInterface $authRepository;

    public function __construct(AuthInterface $authRepository)
    {
        $this->authRepository = $authRepository;
    }

    /**
     * @throws AuthIndexException
     */
    public function execute(): mixed
    {
        try {
            return $this->authRepository->findAll();
        } catch (Exception $exception) {
            throw new AuthIndexException($exception);
        }
    }
}
