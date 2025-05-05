<?php

namespace App\Modules\Role\Http\Controllers\Api;

use App\Modules\Core\Http\Controllers\ApiController as Controller;
use App\Modules\Role\Exceptions\RoleDestroyException;
use App\Modules\Role\Exceptions\RoleNotFoundException;
use App\Modules\Role\Exceptions\RoleStoreException;
use App\Modules\Role\Exceptions\RoleUpdateException;
use App\Modules\Role\Http\Requests\CreateRoleRequest;
use App\Modules\Role\Http\Requests\UpdateRoleRequest;
use App\Modules\Role\Http\Resources\RoleResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;

class RoleController extends Controller
{
    public function __construct()
    {
        // No longer inject the service, use action classes
    }

    public function index(): ResourceCollection
    {
        $roles = app(\App\Modules\Role\Http\Actions\GetAllRoleAction::class)->execute();

        return RoleResource::collection($roles);
    }

    /**
     * @throws RoleStoreException
     */
    public function store(CreateRoleRequest $request): JsonResponse
    {
        $dto = \App\Modules\Role\Http\DTOs\CreateRoleDTO::fromArray($request->validated());
        $role = app(\App\Modules\Role\Http\Actions\CreateRoleAction::class)->execute($dto);

        return $this
            ->setMessage(__('apiResponse.storeSuccess', [
                'resource' => 'Role',
            ]))
            ->respond(new RoleResource($role));
    }

    /**
     * @throws RoleNotFoundException
     */
    public function show(int $id): JsonResponse
    {
        $role = app(\App\Modules\Role\Http\Actions\GetRoleByIdAction::class)->execute($id);

        return $this
            ->setMessage(__('apiResponse.ok', [
                'resource' => 'Role',
            ]))
            ->respond(new RoleResource($role));
    }

    /**
     * @throws RoleUpdateException
     */
    public function update(UpdateRoleRequest $request, int $id): JsonResponse
    {
        $dto = \App\Modules\Role\Http\DTOs\UpdateRoleDTO::fromArray($request->validated());
        $role = app(\App\Modules\Role\Http\Actions\UpdateRoleAction::class)->execute($id, $dto);

        return $this
            ->setMessage(__('apiResponse.updateSuccess', [
                'resource' => 'Role',
            ]))
            ->respond(new RoleResource($role));
    }

    /**
     * @throws RoleDestroyException
     */
    public function destroy(int $id): JsonResponse
    {
        app(\App\Modules\Role\Http\Actions\DeleteRoleAction::class)->execute($id);

        return $this
            ->setMessage(__('apiResponse.deleteSuccess', [
                'resource' => 'Role',
            ]))
            ->respond(null);
    }
}
