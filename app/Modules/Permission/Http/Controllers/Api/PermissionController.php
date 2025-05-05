<?php

namespace App\Modules\Permission\Http\Controllers\Api;

use App\Modules\Core\Http\Controllers\ApiController as Controller;
use App\Modules\Permission\Exceptions\PermissionDestroyException;
use App\Modules\Permission\Exceptions\PermissionNotFoundException;
use App\Modules\Permission\Exceptions\PermissionStoreException;
use App\Modules\Permission\Exceptions\PermissionUpdateException;
use App\Modules\Permission\Http\Requests\CreatePermissionRequest;
use App\Modules\Permission\Http\Requests\UpdatePermissionRequest;
use App\Modules\Permission\Http\Resources\PermissionResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;

class PermissionController extends Controller
{
    public function __construct()
    {
        // No longer inject the service, use action classes
    }

    public function index(): ResourceCollection
    {
        $permissions = app(\App\Modules\Permission\Http\Actions\GetAllPermissionAction::class)->execute();

        return PermissionResource::collection($permissions);
    }

    /**
     * @throws PermissionStoreException
     */
    public function store(CreatePermissionRequest $request): JsonResponse
    {
        $dto = \App\Modules\Permission\Http\DTOs\CreatePermissionDTO::fromArray($request->validated());
        $permission = app(\App\Modules\Permission\Http\Actions\CreatePermissionAction::class)->execute($dto);

        return $this
            ->setMessage(__('apiResponse.storeSuccess', [
                'resource' => 'Permission',
            ]))
            ->respond(new PermissionResource($permission));
    }

    /**
     * @throws PermissionNotFoundException
     */
    public function show(int $id): JsonResponse
    {
        $permission = app(\App\Modules\Permission\Http\Actions\GetPermissionByIdAction::class)->execute($id);

        return $this
            ->setMessage(__('apiResponse.ok', [
                'resource' => 'Permission',
            ]))
            ->respond(new PermissionResource($permission));
    }

    /**
     * @throws PermissionUpdateException
     */
    public function update(UpdatePermissionRequest $request, int $id): JsonResponse
    {
        $dto = \App\Modules\Permission\Http\DTOs\UpdatePermissionDTO::fromArray($request->validated());
        $permission = app(\App\Modules\Permission\Http\Actions\UpdatePermissionAction::class)->execute($id, $dto);

        return $this
            ->setMessage(__('apiResponse.updateSuccess', [
                'resource' => 'Permission',
            ]))
            ->respond(new PermissionResource($permission));
    }

    /**
     * @throws PermissionDestroyException
     */
    public function destroy(int $id): JsonResponse
    {
        app(\App\Modules\Permission\Http\Actions\DeletePermissionAction::class)->execute($id);

        return $this
            ->setMessage(__('apiResponse.deleteSuccess', [
                'resource' => 'Permission',
            ]))
            ->respond(null);
    }
}
