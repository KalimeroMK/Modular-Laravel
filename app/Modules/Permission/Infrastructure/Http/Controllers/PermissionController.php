<?php

declare(strict_types=1);

namespace App\Modules\Permission\Infrastructure\Http\Controllers;

use App\Modules\Core\Http\Controllers\AbstractCrudController;
use App\Modules\Permission\Application\Actions\CreatePermissionAction;
use App\Modules\Permission\Application\Actions\DeletePermissionAction;
use App\Modules\Permission\Application\Actions\GetAllPermissionsAction;
use App\Modules\Permission\Application\Actions\GetPermissionByIdAction;
use App\Modules\Permission\Application\Actions\UpdatePermissionAction;
use App\Modules\Permission\Application\DTO\CreatePermissionDTO;
use App\Modules\Permission\Application\DTO\UpdatePermissionDTO;
use App\Modules\Permission\Infrastructure\Http\Requests\CreatePermissionRequest;
use App\Modules\Permission\Infrastructure\Http\Requests\UpdatePermissionRequest;
use App\Modules\Permission\Infrastructure\Http\Resources\PermissionResource;
use Illuminate\Http\JsonResponse;

class PermissionController extends AbstractCrudController
{
    public function __construct(
        GetAllPermissionsAction $getAllPermissionsAction,
        GetPermissionByIdAction $getPermissionByIdAction,
        CreatePermissionAction $createPermissionAction,
        UpdatePermissionAction $updatePermissionAction,
        DeletePermissionAction $deletePermissionAction,
    ) {
        parent::__construct(
            $getAllPermissionsAction,
            $getPermissionByIdAction,
            $createPermissionAction,
            $updatePermissionAction,
            $deletePermissionAction,
        );
    }

    public function store(CreatePermissionRequest $request): JsonResponse
    {
        return $this->handleStore($request);
    }

    public function update(int $id, UpdatePermissionRequest $request): JsonResponse
    {
        return $this->handleUpdate($id, $request);
    }

    protected function getCreateDtoClass(): string
    {
        return CreatePermissionDTO::class;
    }

    protected function getUpdateDtoClass(): string
    {
        return UpdatePermissionDTO::class;
    }

    protected function getResourceClass(): string
    {
        return PermissionResource::class;
    }

    protected function getEntityLabel(): string
    {
        return 'Permission';
    }

    protected function getModelClass(): string
    {
        return \App\Modules\Permission\Infrastructure\Models\Permission::class;
    }
}
