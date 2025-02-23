<?php

namespace App\Modules\User\Services;

use App\Modules\User\Exceptions\UserDestroyException;
use App\Modules\User\Exceptions\UserIndexException;
use App\Modules\User\Exceptions\UserNotFoundException;
use App\Modules\User\Exceptions\UserSearchException;
use App\Modules\User\Exceptions\UserStoreException;
use App\Modules\User\Exceptions\UserUpdateException;
use App\Modules\User\Interfaces\UserInterface;
use Exception;

class UserService
{
    /**
     * @var UserInterface
     */
    public UserInterface $userRepository;

    /**
     * @param UserInterface $userRepository
     */
    public function __construct(UserInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @param int $id
     * @return mixed
     * @throws UserNotFoundException
     */
    public function getById(int $id): mixed
    {
        try {
            return $this->userRepository->findById($id);
        } catch (Exception $exception) {
            throw new UserNotFoundException($exception);
        }
    }

    /**
     * @return mixed
     * @throws UserIndexException
     */
    public function getAll(): mixed
    {
        try {
            return $this->userRepository->findAll();
        } catch (Exception $exception) {
            throw new UserIndexException($exception);
        }
    }

    /**
     * @param array $data
     * @return mixed
     * @throws UserStoreException
     */
    public function create(array $data): mixed
    {
        try {
            return $this->userRepository->create($data);
        } catch (Exception $exception) {
            throw new UserStoreException($exception);
        }
    }

    /**
     * @param array $data
     * @return mixed
     * @throws UserUpdateException
     */
    public function update(int $id, array $data): mixed
    {
        try {
            return $this->userRepository->update($id, $data);
        } catch (Exception $exception) {
            throw new UserUpdateException($exception);
        }
    }

    /**
     * @param int $id
     * @return mixed|void
     * @throws UserDestroyException
     */
    public function delete(int $id)
    {
        try {
            return $this->userRepository->delete($id);
        } catch (Exception $exception) {
            throw new UserDestroyException($exception);
        }
    }

    /**
     * @param array $data
     * @return mixed|void
     * @throws UserSearchException
     */
    public function search(array $data)
    {
        try {
            return $this->userRepository->search($data);
        } catch (Exception $exception) {
            throw new UserSearchException($exception);
        }
    }
}
