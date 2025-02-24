<?php

namespace App\Modules\Permission\Services;

use App\Modules\Permission\Exceptions\PermissionDestroyException;
use App\Modules\Permission\Exceptions\PermissionIndexException;
use App\Modules\Permission\Exceptions\PermissionNotFoundException;
use App\Modules\Permission\Exceptions\PermissionSearchException;
use App\Modules\Permission\Exceptions\PermissionStoreException;
use App\Modules\Permission\Exceptions\PermissionUpdateException;
use App\Modules\Permission\Interfaces\PermissionInterface;
use Exception;

class PermissionService
{
    /**
     * @var PermissionInterface
     */
    public PermissionInterface $permissionRepository;

    /**
     * @param PermissionInterface $permissionRepository
     */
    public function __construct(PermissionInterface $permissionRepository)
    {
        $this->permissionRepository = $permissionRepository;
    }

    /**
     * @param int $id
     * @return mixed
     * @throws PermissionNotFoundException
     */
    public function getById(int $id): mixed
    {
        try {
            return $this->permissionRepository->findById($id);
        } catch (Exception $exception) {
            throw new PermissionNotFoundException($exception);
        }
    }

    /**
     * @return mixed
     * @throws PermissionIndexException
     */
    public function getAll(): mixed
    {
        try {
            return $this->permissionRepository->findAll();
        } catch (Exception $exception) {
            throw new PermissionIndexException($exception);
        }
    }

    /**
     * @param array $data
     * @return mixed
     * @throws PermissionStoreException
     */
    public function create(array $data): mixed
    {
        try {
            return $this->permissionRepository->create($data);
        } catch (Exception $exception) {
            throw new PermissionStoreException($exception);
        }
    }

    /**
     * @param array $data
     * @return mixed
     * @throws PermissionUpdateException
     */
    public function update(int $id, array $data): mixed
    {
        try {
            return $this->permissionRepository->update($id, $data);
        } catch (Exception $exception) {
            throw new PermissionUpdateException($exception);
        }
    }

    /**
     * @param int $id
     * @return mixed|void
     * @throws PermissionDestroyException
     */
    public function delete(int $id)
    {
        try {
            return $this->permissionRepository->delete($id);
        } catch (Exception $exception) {
            throw new PermissionDestroyException($exception);
        }
    }

    /**
     * @param array $data
     * @return mixed|void
     * @throws PermissionSearchException
     */
    public function search(array $data)
    {
        try {
            return $this->permissionRepository->search($data);
        } catch (Exception $exception) {
            throw new PermissionSearchException($exception);
        }
    }
}
