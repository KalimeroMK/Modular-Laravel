<?php

namespace App\Modules\Auth\Http\Actions;

use App\Modules\Auth\Exceptions\AuthUpdateException;
use App\Modules\Auth\Http\DTOs\UpdateAuthDTO;
use App\Modules\Auth\Interfaces\AuthInterface;
use Exception;

class UpdateAuthAction
{
    protected AuthInterface $authRepository;

    public function __construct(AuthInterface $authRepository)
    {
        $this->authRepository = $authRepository;
    }

    /**
     * @throws AuthUpdateException
     */
    public function execute(int $id, UpdateAuthDTO $dto): mixed
    {
        try {
            return $this->authRepository->update($id, $dto->toArray());
        } catch (Exception $exception) {
            throw new AuthUpdateException($exception);
        }
    }
}
