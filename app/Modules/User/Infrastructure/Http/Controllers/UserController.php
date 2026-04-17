<?php

declare(strict_types=1);

namespace App\Modules\User\Infrastructure\Http\Controllers;

use App\Modules\Core\Http\Controllers\AbstractCrudController;
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

class UserController extends AbstractCrudController
{
    public function __construct(
        GetAllUsersAction $getAllUsersAction,
        GetUserByIdAction $getUserByIdAction,
        CreateUserAction $createUserAction,
        UpdateUserAction $updateUserAction,
        DeleteUserAction $deleteUserAction,
    ) {
        parent::__construct(
            $getAllUsersAction,
            $getUserByIdAction,
            $createUserAction,
            $updateUserAction,
            $deleteUserAction,
        );
    }

    public function store(CreateUserRequest $request): JsonResponse
    {
        return $this->handleStore($request);
    }

    public function update(int $id, UpdateUserRequest $request): JsonResponse
    {
        return $this->handleUpdate($id, $request);
    }

    protected function getCreateDtoClass(): string
    {
        return CreateUserDTO::class;
    }

    protected function getUpdateDtoClass(): string
    {
        return UpdateUserDTO::class;
    }

    protected function getResourceClass(): string
    {
        return UserResource::class;
    }

    protected function getEntityLabel(): string
    {
        return 'User';
    }
}
