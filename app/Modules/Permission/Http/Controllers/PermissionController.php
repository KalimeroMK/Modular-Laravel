<?php

namespace App\Modules\Permission\Http\Controllers;

use App\Modules\Permission\Http\Actions\CreatePermissionAction;
use App\Modules\Permission\Http\Actions\DeletePermissionAction;
use App\Modules\Permission\Http\Actions\GetAllPermissionAction;
use App\Modules\Permission\Http\Actions\GetPermissionByIdAction;
use App\Modules\Permission\Http\Actions\UpdatePermissionAction;
use App\Modules\Permission\Http\DTOs\PermissionDTO;
use App\Modules\Permission\Http\Requests\CreatePermissionRequest;
use App\Modules\Permission\Http\Requests\UpdatePermissionRequest;
use App\Modules\Permission\Http\Resources\PermissionResource;
use App\Modules\Permission\Models\Permission;
use Illuminate\Http\JsonResponse;

class PermissionController
{
    public function index(GetAllPermissionAction $action): JsonResponse
    {
        return response()->json(['data' => PermissionResource::collection($action->execute())]);
    }

    public function show(Permission $permission, GetPermissionByIdAction $action): JsonResponse
    {
        return response()->json(['data' => new PermissionResource($action->execute($permission))]);
    }

    public function store(CreatePermissionRequest $request, CreatePermissionAction $action): JsonResponse
    {
        return response()->json(['data' => new PermissionResource(
            $action->execute(PermissionDTO::fromRequest($request))
        )]);
    }

    public function update(Permission $permission, UpdatePermissionRequest $request, UpdatePermissionAction $action): JsonResponse
    {
        return response()->json([
            'data' => new PermissionResource(
                $action->execute(PermissionDTO::fromRequest($request, $permission->id, $permission))
            ),
        ]);
    }

    public function destroy(Permission $permission, DeletePermissionAction $action): JsonResponse
    {
        $action->execute($permission);

        return response()->json(['message' => 'Permission deleted']);
    }
}
