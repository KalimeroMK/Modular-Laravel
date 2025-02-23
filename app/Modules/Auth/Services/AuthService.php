<?php

namespace App\Modules\Auth\Services;

use App\Modules\Auth\Exceptions\AuthDestroyException;
use App\Modules\Auth\Exceptions\AuthIndexException;
use App\Modules\Auth\Exceptions\AuthNotFoundException;
use App\Modules\Auth\Exceptions\AuthSearchException;
use App\Modules\Auth\Exceptions\AuthStoreException;
use App\Modules\Auth\Exceptions\AuthUpdateException;
use App\Modules\Auth\Interfaces\AuthInterface;
use Exception;

class AuthService
{
    /**
     * @var AuthInterface
     */
    public AuthInterface $authRepository;

    /**
     * @param AuthInterface $authRepository
     */
    public function __construct(AuthInterface $authRepository)
    {
        $this->authRepository = $authRepository;
    }

    /**
     * @param int $id
     * @return mixed
     * @throws AuthNotFoundException
     */
    public function getById(int $id): mixed
    {
        try {
            return $this->authRepository->findById($id);
        } catch (Exception $exception) {
            throw new AuthNotFoundException($exception);
        }
    }

    /**
     * @return mixed
     * @throws AuthIndexException
     */
    public function getAll(): mixed
    {
        try {
            return $this->authRepository->findAll();
        } catch (Exception $exception) {
            throw new AuthIndexException($exception);
        }
    }

    /**
     * @param array $data
     * @return mixed
     * @throws AuthStoreException
     */
    public function create(array $data): mixed
    {
        try {
            return $this->authRepository->create($data);
        } catch (Exception $exception) {
            throw new AuthStoreException($exception);
        }
    }

    /**
     * @param array $data
     * @return mixed
     * @throws AuthUpdateException
     */
    public function update(int $id, array $data): mixed
    {
        try {
            return $this->authRepository->update($id, $data);
        } catch (Exception $exception) {
            throw new AuthUpdateException($exception);
        }
    }

    /**
     * @param int $id
     * @return mixed|void
     * @throws AuthDestroyException
     */
    public function delete(int $id)
    {
        try {
            return $this->authRepository->delete($id);
        } catch (Exception $exception) {
            throw new AuthDestroyException($exception);
        }
    }

    /**
     * @param array $data
     * @return mixed|void
     * @throws AuthSearchException
     */
    public function search(array $data)
    {
        try {
            return $this->authRepository->search($data);
        } catch (Exception $exception) {
            throw new AuthSearchException($exception);
        }
    }
}
