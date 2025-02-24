<?php

namespace App\Modules\Role\Services;

use App\Modules\Role\Exceptions\RoleDestroyException;
use App\Modules\Role\Exceptions\RoleIndexException;
use App\Modules\Role\Exceptions\RoleNotFoundException;
use App\Modules\Role\Exceptions\RoleSearchException;
use App\Modules\Role\Exceptions\RoleStoreException;
use App\Modules\Role\Exceptions\RoleUpdateException;
use App\Modules\Role\Interfaces\RoleInterface;
use Exception;

class RoleService
{
    /**
     * @var RoleInterface
     */
    public RoleInterface $roleRepository;

    /**
     * @param RoleInterface $roleRepository
     */
    public function __construct(RoleInterface $roleRepository)
    {
        $this->roleRepository = $roleRepository;
    }

    /**
     * @param int $id
     * @return mixed
     * @throws RoleNotFoundException
     */
    public function getById(int $id): mixed
    {
        try {
            return $this->roleRepository->findById($id);
        } catch (Exception $exception) {
            throw new RoleNotFoundException($exception);
        }
    }

    /**
     * @return mixed
     * @throws RoleIndexException
     */
    public function getAll(): mixed
    {
        try {
            return $this->roleRepository->findAll();
        } catch (Exception $exception) {
            throw new RoleIndexException($exception);
        }
    }

    /**
     * @param array $data
     * @return mixed
     * @throws RoleStoreException
     */
    public function create(array $data): mixed
    {
        try {
            return $this->roleRepository->create($data);
        } catch (Exception $exception) {
            throw new RoleStoreException($exception);
        }
    }

    /**
     * @param array $data
     * @return mixed
     * @throws RoleUpdateException
     */
    public function update(int $id, array $data): mixed
    {
        try {
            return $this->roleRepository->update($id, $data);
        } catch (Exception $exception) {
            throw new RoleUpdateException($exception);
        }
    }

    /**
     * @param int $id
     * @return mixed|void
     * @throws RoleDestroyException
     */
    public function delete(int $id)
    {
        try {
            return $this->roleRepository->delete($id);
        } catch (Exception $exception) {
            throw new RoleDestroyException($exception);
        }
    }

    /**
     * @param array $data
     * @return mixed|void
     * @throws RoleSearchException
     */
    public function search(array $data)
    {
        try {
            return $this->roleRepository->search($data);
        } catch (Exception $exception) {
            throw new RoleSearchException($exception);
        }
    }
}
