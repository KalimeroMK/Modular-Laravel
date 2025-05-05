<?php

namespace App\Modules\Auth\Http\Actions;

use App\Modules\Auth\Exceptions\AuthNotFoundException;
use App\Modules\Auth\Interfaces\AuthInterface;
use Exception;

class FindAuthByEmailAction
{
    protected AuthInterface $authRepository;

    public function __construct(AuthInterface $authRepository)
    {
        $this->authRepository = $authRepository;
    }

    /**
     * @throws AuthNotFoundException
     */
    public function execute(string $email): mixed
    {
        try {
            return $this->authRepository->findByEmail($email);
        } catch (Exception $exception) {
            throw new AuthNotFoundException($exception);
        }
    }
}
