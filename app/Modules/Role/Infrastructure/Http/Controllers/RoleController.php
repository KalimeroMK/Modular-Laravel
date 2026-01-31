<?php

declare(strict_types=1);

namespace App\Modules\Role\Infrastructure\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Core\Support\ApiResponse;
use App\Modules\Core\Traits\SwaggerTrait;
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

class RoleController extends Controller
{
    use SwaggerTrait;

    public function __construct(
        protected GetAllRolesAction $getAllRolesAction,
        protected GetRoleByIdAction $getRoleByIdAction,
        protected CreateRoleAction $createRoleAction,
        protected UpdateRoleAction $updateRoleAction,
        protected DeleteRoleAction $deleteRoleAction,
    ) {}

    



























    public function index(): JsonResponse
    {
        $roles = $this->getAllRolesAction->execute();

        return ApiResponse::paginated($roles, 'Roles retrieved successfully', RoleResource::collection($roles->items()));
    }

    









































    public function show(int $id): JsonResponse
    {
        return ApiResponse::success(new RoleResource($this->getRoleByIdAction->execute($id)), 'Role retrieved successfully');
    }

    











































    public function store(CreateRoleRequest $request): JsonResponse
    {
        return ApiResponse::created(new RoleResource($this->createRoleAction->execute(CreateRoleDTO::fromArray($request->validated()))), 'Role created successfully');
    }

    


























































    public function update(int $id, UpdateRoleRequest $request): JsonResponse
    {
        return ApiResponse::success(new RoleResource($this->updateRoleAction->execute($id, UpdateRoleDTO::fromArray($request->validated()))), 'Role updated successfully');
    }

    









































    public function destroy(int $id): JsonResponse
    {
        $this->deleteRoleAction->execute($id);

        return ApiResponse::success(null, 'Role deleted successfully');
    }
}
