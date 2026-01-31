<?php

declare(strict_types=1);

namespace App\Modules\Permission\Infrastructure\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Core\Support\ApiResponse;
use App\Modules\Core\Traits\SwaggerTrait;
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

class PermissionController extends Controller
{
    use SwaggerTrait;

    public function __construct(
        protected GetAllPermissionsAction $getAllPermissionsAction,
        protected GetPermissionByIdAction $getPermissionByIdAction,
        protected CreatePermissionAction $createPermissionAction,
        protected UpdatePermissionAction $updatePermissionAction,
        protected DeletePermissionAction $deletePermissionAction,
    ) {}

    



























    public function index(): JsonResponse
    {
        $permissions = $this->getAllPermissionsAction->execute();

        return ApiResponse::paginated($permissions, 'Permissions retrieved successfully', PermissionResource::collection($permissions->items()));
    }

    









































    public function show(int $id): JsonResponse
    {
        return ApiResponse::success(new PermissionResource($this->getPermissionByIdAction->execute($id)), 'Permission retrieved successfully');
    }

    











































    public function store(CreatePermissionRequest $request): JsonResponse
    {
        return ApiResponse::created(new PermissionResource($this->createPermissionAction->execute(CreatePermissionDTO::fromArray($request->validated()))), 'Permission created successfully');
    }

    


























































    public function update(int $id, UpdatePermissionRequest $request): JsonResponse
    {
        return ApiResponse::success(new PermissionResource($this->updatePermissionAction->execute($id, UpdatePermissionDTO::fromArray($request->validated()))), 'Permission updated successfully');
    }

    









































    public function destroy(int $id): JsonResponse
    {
        $this->deletePermissionAction->execute($id);

        return ApiResponse::success(null, 'Permission deleted successfully');
    }
}
