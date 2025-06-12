<?php

namespace App\Modules\User\Http\Controllers\Api;

use App\Modules\User\Http\Actions\GetAllUserAction;
use App\Modules\User\Http\Actions\GetUserByIdAction;
use App\Modules\User\Http\Requests\CreateUserRequest;
use App\Modules\User\Http\Requests\UpdateUserRequest;
use App\Modules\User\Http\Actions\CreateUserAction;
use App\Modules\User\Http\Actions\UpdateUserAction;
use App\Modules\User\Http\Actions\DeleteUserAction;
use App\Modules\User\Http\DTOs\CreateUserDTO;
use App\Modules\User\Http\DTOs\UpdateUserDTO;
use App\Modules\User\Http\Resources\UserResource;
use App\Modules\User\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class UserController
{
    public function index(GetAllUserAction $action): AnonymousResourceCollection
    {
        return UserResource::collection($action->execute());
    }

    public function show(User $user, GetUserByIdAction $action): UserResource
    {
        return new UserResource($action->execute($user));

    }

    public function store(CreateUserRequest $request, CreateUserAction $action): JsonResponse
    {
        $user = $action->execute(CreateUserDTO::fromRequest($request));
        return response()->json(['data' => new UserResource($user)]);
    }

    public function update(User $user, UpdateUserRequest $request, UpdateUserAction $action): JsonResponse
    {
        $user = $action->execute($user, UpdateUserDTO::fromRequest($request));
        return response()->json(['data' => $user]);
    }

    public function destroy(User $user, DeleteUserAction $action): JsonResponse
    {
        $action->execute($user);
        return response()->json(['message' => 'User deleted']);
    }
}
