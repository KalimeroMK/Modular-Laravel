<?php

namespace App\Modules\User\Http\Controllers\Api;

use App\Modules\Core\Http\Controllers\ApiController as Controller;
use App\Modules\User\Exceptions\UserDestroyException;
use App\Modules\User\Exceptions\UserNotFoundException;
use App\Modules\User\Exceptions\UserStoreException;
use App\Modules\User\Exceptions\UserUpdateException;
use App\Modules\User\Http\Requests\CreateUserRequest;
use App\Modules\User\Http\Requests\UpdateUserRequest;
use App\Modules\User\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;

class UserController extends Controller
{
    public function __construct()
    {
        // No longer inject the service, use action classes
    }

    public function index(): ResourceCollection
    {
        $users = app(\App\Modules\User\Http\Actions\GetAllUserAction::class)->execute();

        return UserResource::collection($users);
    }

    /**
     * @throws UserStoreException
     */
    public function store(CreateUserRequest $request): JsonResponse
    {
        $dto = \App\Modules\User\Http\DTOs\CreateUserDTO::fromArray($request->validated());
        $user = app(\App\Modules\User\Http\Actions\CreateUserAction::class)->execute($dto);

        return $this
            ->setMessage(__('apiResponse.storeSuccess', [
                'resource' => 'User',
            ]))
            ->respond(new UserResource($user));
    }

    /**
     * @throws UserNotFoundException
     */
    public function show(int $id): JsonResponse
    {
        $user = app(\App\Modules\User\Http\Actions\GetUserByIdAction::class)->execute($id);

        return $this
            ->setMessage(__('apiResponse.ok', [
                'resource' => 'User',
            ]))
            ->respond(new UserResource($user));
    }

    /**
     * @throws UserUpdateException
     */
    public function update(UpdateUserRequest $request, int $id): JsonResponse
    {
        $dto = \App\Modules\User\Http\DTOs\UpdateUserDTO::fromArray($request->validated());
        $user = app(\App\Modules\User\Http\Actions\UpdateUserAction::class)->execute($id, $dto);

        return $this
            ->setMessage(__('apiResponse.updateSuccess', [
                'resource' => 'User',
            ]))
            ->respond(new UserResource($user));
    }

    /**
     * @throws UserDestroyException
     */
    public function destroy(int $id): JsonResponse
    {
        app(\App\Modules\User\Http\Actions\DeleteUserAction::class)->execute($id);

        return $this
            ->setMessage(__('apiResponse.deleteSuccess', [
                'resource' => 'User',
            ]))
            ->respond(null);
    }
}
