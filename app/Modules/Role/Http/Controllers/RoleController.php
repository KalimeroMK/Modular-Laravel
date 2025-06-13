<?php

namespace App\Modules\Role\Http\Controllers;

use App\Modules\Role\Http\Actions\CreateRoleAction;
use App\Modules\Role\Http\Actions\DeleteRoleAction;
use App\Modules\Role\Http\Actions\GetAllRoleAction;
use App\Modules\Role\Http\Actions\GetRoleByIdAction;
use App\Modules\Role\Http\Actions\UpdateRoleAction;
use App\Modules\Role\Http\DTOs\RoleDTO;
use App\Modules\Role\Http\Requests\CreateRoleRequest;
use App\Modules\Role\Http\Requests\UpdateRoleRequest;
use App\Modules\Role\Http\Resources\RoleResource;
use App\Modules\Role\Models\Role;
use Illuminate\Http\JsonResponse;

class RoleController
{
    public function index(GetAllRoleAction $action): JsonResponse
    {
        return response()->json(['data' => RoleResource::collection($action->execute())]);
    }

    public function show(Role $role, GetRoleByIdAction $action): JsonResponse
    {
        return response()->json(['data' => new RoleResource($action->execute($role))]);
    }

    public function store(CreateRoleRequest $request, CreateRoleAction $action): JsonResponse
    {
        return response()->json(['data' => new RoleResource($action->execute(RoleDTO::fromRequest($request)))]);
    }

    public function update(Role $role, UpdateRoleRequest $request, UpdateRoleAction $action): JsonResponse
    {
        return response()->json(['data' => new RoleResource($action->execute($role, RoleDTO::fromRequest($request)))]);
    }

    public function destroy(Role $role, DeleteRoleAction $action): JsonResponse
    {
        $action->execute($role);

        return response()->json(['message' => 'Role deleted']);
    }
}
