<?php

namespace App\Modules\Permission\Http\Actions;

use App\Modules\Permission\Http\DTOs\SearchPermissionDTO;
use App\Modules\Permission\Interfaces\PermissionInterface;
use App\Modules\Permission\Exceptions\PermissionSearchException;
use Exception;

class SearchPermissionAction
{
    public function __construct(protected PermissionInterface $repository) {}

    public function execute(SearchPermissionDTO $dto): mixed
    {
        try {
            return $this->repository->search($dto->toArray());
        } catch (Exception $exception) {
            throw new PermissionSearchException($exception);
        }
    }
}