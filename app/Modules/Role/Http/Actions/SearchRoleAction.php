<?php

namespace App\Modules\Role\Http\Actions;

use App\Modules\Role\Exceptions\RoleSearchException;
use App\Modules\Role\Http\DTOs\SearchRoleDTO;
use App\Modules\Role\Interfaces\RoleInterface;
use Exception;

class SearchRoleAction
{
    protected RoleInterface $roleRepository;

    public function __construct(RoleInterface $roleRepository)
    {
        $this->roleRepository = $roleRepository;
    }

    public function execute(SearchRoleDTO $dto): mixed
    {
        try {
            return $this->roleRepository->search($dto->toArray());
        } catch (Exception $exception) {
            throw new RoleSearchException($exception);
        }
    }
}
