<?php

declare(strict_types=1);

namespace App\Modules\Role\Infrastructure\Http\Controllers;

use App\Modules\Core\Http\Controllers\AbstractCrudController;
use App\Modules\Role\Application\Actions\CreateRoleAction;
use App\Modules\Role\Application\Actions\DeleteRoleAction;
use App\Modules\Role\Application\Actions\GetAllRolesAction;
use App\Modules\Role\Application\Actions\GetRoleByIdAction;
use App\Modules\Role\Application\Actions\UpdateRoleAction;
use App\Modules\Role\Application\DTO\CreateRoleDTO;
use App\Modules\Role\Application\DTO\UpdateRoleDTO;
use App\Modules\Role\Infrastructure\Http\Requests\CreateRoleRequest;
use App\Modules\Role\Infrastructure\Http\Requests\UpdateRoleRequest;
use App\Modules\Role\Infrastructure\Http\Resources\RoleResource;
use Illuminate\Http\JsonResponse;

class RoleController extends AbstractCrudController
{
    public function __construct(
        GetAllRolesAction $getAllRolesAction,
        GetRoleByIdAction $getRoleByIdAction,
        CreateRoleAction $createRoleAction,
        UpdateRoleAction $updateRoleAction,
        DeleteRoleAction $deleteRoleAction,
    ) {
        parent::__construct(
            $getAllRolesAction,
            $getRoleByIdAction,
            $createRoleAction,
            $updateRoleAction,
            $deleteRoleAction,
        );
    }

    public function store(CreateRoleRequest $request): JsonResponse
    {
        return $this->handleStore($request);
    }

    public function update(int $id, UpdateRoleRequest $request): JsonResponse
    {
        return $this->handleUpdate($id, $request);
    }

    protected function getCreateDtoClass(): string
    {
        return CreateRoleDTO::class;
    }

    protected function getUpdateDtoClass(): string
    {
        return UpdateRoleDTO::class;
    }

    protected function getResourceClass(): string
    {
        return RoleResource::class;
    }

    protected function getEntityLabel(): string
    {
        return 'Role';
    }
}
