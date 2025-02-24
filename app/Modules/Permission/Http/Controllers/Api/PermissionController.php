<?php

namespace App\Modules\Permission\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Modules\Permission\Exceptions\PermissionDestroyException;
use App\Modules\Permission\Exceptions\PermissionNotFoundException;
use App\Modules\Permission\Exceptions\PermissionStoreException;
use App\Modules\Permission\Exceptions\PermissionUpdateException;
use App\Modules\Permission\Http\Requests\CreatePermissionRequest;
use App\Modules\Permission\Http\Requests\UpdatePermissionRequest;
use App\Modules\Permission\Http\Resources\PermissionResource;
use App\Modules\Permission\Services\PermissionService;
use App\Modules\Core\Helpers\Helper;
use App\Modules\Core\Http\Controllers\ApiController as Controller;

class PermissionController extends Controller
{
    private PermissionService $permission_service;

    public function __construct(PermissionService $permission_service)
    {
        $this->permission_service = $permission_service;
    }

    public function index(): ResourceCollection
    {
        return PermissionResource::collection($this->permission_service->getAll());
    }

    /**
      * @param  CreatePermissionRequest  $request
      * @return JsonResponse
      * @throws PermissionStoreException
    */
    public function store(CreatePermissionRequest $request): JsonResponse
    {
        return $this
            ->setMessage(
                __('apiResponse.storeSuccess', [
                    'resource' => Helper::getResourceName(
                        $this->permission_service->permissionRepository->model
                    ),
                ])
            )
            ->respond(new PermissionResource($this->permission_service->create($request->validated())));
    }

    /**
      * @param  int $id
      * @return JsonResponse
      * @throws PermissionNotFoundException
    */
    public function show(int $id): JsonResponse
    {
        return $this
            ->setMessage(
                __('apiResponse.ok', [
                    'resource' => Helper::getResourceName(
                        $this->permission_service->permissionRepository->model
                    ),
                ])
            )
            ->respond(new PermissionResource($this->permission_service->getById($id)));
    }

    /**
       * @param  UpdatePermissionRequest  $request
       * @param  int $id
       * @return JsonResponse
       * @throws PermissionUpdateException
    */
    public function update(UpdatePermissionRequest $request, int $id): JsonResponse
    {
        return $this
            ->setMessage(
                __('apiResponse.updateSuccess', [
                    'resource' => Helper::getResourceName(
                        $this->permission_service->permissionRepository->model
                    ),
                ])
            )
            ->respond(new PermissionResource($this->permission_service->update($id, $request->validated())));
    }

    /**
       * @param  int $id
       * @return JsonResponse
       * @throws PermissionDestroyException
    */
    public function destroy(int $id): JsonResponse
    {
        $this->permission_service->delete($id);

        return $this
            ->setMessage(
                __('apiResponse.deleteSuccess', [
                    'resource' => Helper::getResourceName(
                        $this->permission_service->permissionRepository->model
                    ),
                ])
            )
            ->respond(null);
    }
}
