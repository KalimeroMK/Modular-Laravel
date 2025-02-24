<?php

namespace App\Modules\Role\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Modules\Role\Exceptions\RoleDestroyException;
use App\Modules\Role\Exceptions\RoleNotFoundException;
use App\Modules\Role\Exceptions\RoleStoreException;
use App\Modules\Role\Exceptions\RoleUpdateException;
use App\Modules\Role\Http\Requests\CreateRoleRequest;
use App\Modules\Role\Http\Requests\UpdateRoleRequest;
use App\Modules\Role\Http\Resources\RoleResource;
use App\Modules\Role\Services\RoleService;
use App\Modules\Core\Helpers\Helper;
use App\Modules\Core\Http\Controllers\ApiController as Controller;

class RoleController extends Controller
{
    private RoleService $role_service;

    public function __construct(RoleService $role_service)
    {
        $this->role_service = $role_service;
    }

    public function index(): ResourceCollection
    {
        return RoleResource::collection($this->role_service->getAll());
    }

    /**
      * @param  CreateRoleRequest  $request
      * @return JsonResponse
      * @throws RoleStoreException
    */
    public function store(CreateRoleRequest $request): JsonResponse
    {
        return $this
            ->setMessage(
                __('apiResponse.storeSuccess', [
                    'resource' => Helper::getResourceName(
                        $this->role_service->roleRepository->model
                    ),
                ])
            )
            ->respond(new RoleResource($this->role_service->create($request->validated())));
    }

    /**
      * @param  int $id
      * @return JsonResponse
      * @throws RoleNotFoundException
    */
    public function show(int $id): JsonResponse
    {
        return $this
            ->setMessage(
                __('apiResponse.ok', [
                    'resource' => Helper::getResourceName(
                        $this->role_service->roleRepository->model
                    ),
                ])
            )
            ->respond(new RoleResource($this->role_service->getById($id)));
    }

    /**
       * @param  UpdateRoleRequest  $request
       * @param  int $id
       * @return JsonResponse
       * @throws RoleUpdateException
    */
    public function update(UpdateRoleRequest $request, int $id): JsonResponse
    {
        return $this
            ->setMessage(
                __('apiResponse.updateSuccess', [
                    'resource' => Helper::getResourceName(
                        $this->role_service->roleRepository->model
                    ),
                ])
            )
            ->respond(new RoleResource($this->role_service->update($id, $request->validated())));
    }

    /**
       * @param  int $id
       * @return JsonResponse
       * @throws RoleDestroyException
    */
    public function destroy(int $id): JsonResponse
    {
        $this->role_service->delete($id);

        return $this
            ->setMessage(
                __('apiResponse.deleteSuccess', [
                    'resource' => Helper::getResourceName(
                        $this->role_service->roleRepository->model
                    ),
                ])
            )
            ->respond(null);
    }
}
