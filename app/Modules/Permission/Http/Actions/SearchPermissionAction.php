<?php

namespace App\Modules\Permission\Http\Actions;

use App\Modules\Permission\Exceptions\PermissionSearchException;
use App\Modules\Permission\Http\DTOs\SearchPermissionDTO;
use App\Modules\Permission\Interfaces\PermissionInterface;
use Exception;

class SearchPermissionAction
{
    protected PermissionInterface $permissionRepository;

    public function __construct(PermissionInterface $permissionRepository)
    {
        $this->permissionRepository = $permissionRepository;
    }

    public function execute(SearchPermissionDTO $dto): mixed
    {
        try {
            return $this->permissionRepository->search($dto->toArray());
        } catch (Exception $exception) {
            throw new PermissionSearchException($exception);
        }
    }
}
