<?php

declare(strict_types=1);

namespace App\Modules\User\Infrastructure\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Core\Support\ApiResponse;
use App\Modules\Core\Traits\SwaggerTrait;
use App\Modules\User\Application\Actions\CreateUserAction;
use App\Modules\User\Application\Actions\DeleteUserAction;
use App\Modules\User\Application\Actions\GetAllUsersAction;
use App\Modules\User\Application\Actions\GetUserByIdAction;
use App\Modules\User\Application\Actions\UpdateUserAction;
use App\Modules\User\Application\DTO\CreateUserDTO;
use App\Modules\User\Application\DTO\UpdateUserDTO;
use App\Modules\User\Infrastructure\Http\Requests\CreateUserRequest;
use App\Modules\User\Infrastructure\Http\Requests\UpdateUserRequest;
use App\Modules\User\Infrastructure\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    use SwaggerTrait;

    public function __construct(
        protected GetAllUsersAction $getAllUsersAction,
        protected GetUserByIdAction $getUserByIdAction,
        protected CreateUserAction $createUserAction,
        protected UpdateUserAction $updateUserAction,
        protected DeleteUserAction $deleteUserAction,
    ) {}

    



























    public function index(): JsonResponse
    {
        $users = $this->getAllUsersAction->execute();

        return ApiResponse::paginated($users, 'Users retrieved successfully', UserResource::collection($users->items()));
    }

    









































    public function show(int $id): JsonResponse
    {
        return ApiResponse::success(new UserResource($this->getUserByIdAction->execute($id)), 'User retrieved successfully');
    }

    












































    public function store(CreateUserRequest $request): JsonResponse
    {
        return ApiResponse::created(new UserResource($this->createUserAction->execute(CreateUserDTO::fromArray($request->validated()))), 'User created successfully');
    }

    


























































    public function update(int $id, UpdateUserRequest $request): JsonResponse
    {
        return ApiResponse::success(new UserResource($this->updateUserAction->execute($id, UpdateUserDTO::fromArray($request->validated()))), 'User updated successfully');
    }

    









































    public function destroy(int $id): JsonResponse
    {
        $this->deleteUserAction->execute($id);

        return ApiResponse::success(null, 'User deleted successfully');
    }
}
