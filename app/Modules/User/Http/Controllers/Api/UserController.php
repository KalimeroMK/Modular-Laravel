<?php

namespace App\Modules\User\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Modules\User\Exceptions\UserDestroyException;
use App\Modules\User\Exceptions\UserNotFoundException;
use App\Modules\User\Exceptions\UserStoreException;
use App\Modules\User\Exceptions\UserUpdateException;
use App\Modules\User\Http\Requests\CreateUserRequest;
use App\Modules\User\Http\Requests\UpdateUserRequest;
use App\Modules\User\Http\Resources\UserResource;
use App\Modules\User\Services\UserService;
use App\Modules\Core\Helpers\Helper;
use App\Modules\Core\Http\Controllers\ApiController as Controller;

class UserController extends Controller
{
    private UserService $user_service;

    public function __construct(UserService $user_service)
    {
        $this->user_service = $user_service;
    }

    public function index(): ResourceCollection
    {
        return UserResource::collection($this->user_service->getAll());
    }

    /**
      * @param  CreateUserRequest  $request
      * @return JsonResponse
      * @throws UserStoreException
    */
    public function store(CreateUserRequest $request): JsonResponse
    {
        return $this
            ->setMessage(
                __('apiResponse.storeSuccess', [
                    'resource' => Helper::getResourceName(
                        $this->user_service->userRepository->model
                    ),
                ])
            )
            ->respond(new UserResource($this->user_service->create($request->validated())));
    }

    /**
      * @param  int $id
      * @return JsonResponse
      * @throws UserNotFoundException
    */
    public function show(int $id): JsonResponse
    {
        return $this
            ->setMessage(
                __('apiResponse.ok', [
                    'resource' => Helper::getResourceName(
                        $this->user_service->userRepository->model
                    ),
                ])
            )
            ->respond(new UserResource($this->user_service->getById($id)));
    }

    /**
       * @param  UpdateUserRequest  $request
       * @param  int $id
       * @return JsonResponse
       * @throws UserUpdateException
    */
    public function update(UpdateUserRequest $request, int $id): JsonResponse
    {
        return $this
            ->setMessage(
                __('apiResponse.updateSuccess', [
                    'resource' => Helper::getResourceName(
                        $this->user_service->userRepository->model
                    ),
                ])
            )
            ->respond(new UserResource($this->user_service->update($id, $request->validated())));
    }

    /**
       * @param  int $id
       * @return JsonResponse
       * @throws UserDestroyException
    */
    public function destroy(int $id): JsonResponse
    {
        $this->user_service->delete($id);

        return $this
            ->setMessage(
                __('apiResponse.deleteSuccess', [
                    'resource' => Helper::getResourceName(
                        $this->user_service->userRepository->model
                    ),
                ])
            )
            ->respond(null);
    }
}
