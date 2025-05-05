<?php

namespace App\Modules\Auth\Http\Actions;

use App\Modules\Auth\Exceptions\AuthDestroyException;
use App\Modules\Auth\Interfaces\AuthInterface;
use Exception;

class DeleteAuthAction
{
    protected AuthInterface $authRepository;

    public function __construct(AuthInterface $authRepository)
    {
        $this->authRepository = $authRepository;
    }

    /**
     * @throws AuthDestroyException
     */
    public function execute(int $id): void
    {
        try {
            $this->authRepository->delete($id);
        } catch (Exception $exception) {
            throw new AuthDestroyException($exception);
        }
    }
}
