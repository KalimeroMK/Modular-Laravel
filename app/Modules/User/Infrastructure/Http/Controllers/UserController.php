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

    /**
     * @OA\Get(
     *     path="/api/v1/users",
     *     summary="List users",
     *     description="Get paginated list of users",
     *     tags={"Users"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Response(
     *         response=200,
     *         description="Users retrieved successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="data", type="array", @OA\Items(type="object")),
     *             @OA\Property(property="links", type="object"),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     )
     * )
     */
    public function index(): JsonResponse
    {
        $users = $this->getAllUsersAction->execute();

        return ApiResponse::paginated($users, 'Users retrieved successfully', UserResource::collection($users->items()));
    }

    /**
     * @OA\Get(
     *     path="/api/v1/users/{id}",
     *     summary="Get user by ID",
     *     description="Get specific user information",
     *     tags={"Users"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="User ID",
     *
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="User retrieved successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="User not found",
     *
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     )
     * )
     */
    public function show(int $id): JsonResponse
    {
        return ApiResponse::success(new UserResource($this->getUserByIdAction->execute($id)), 'User retrieved successfully');
    }

    /**
     * @OA\Post(
     *     path="/api/v1/users",
     *     summary="Create user",
     *     description="Create a new user",
     *     tags={"Users"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"name","email","password"},
     *
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", format="email", example="user@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password123")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=201,
     *         description="User created successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *
     *         @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse")
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     )
     * )
     */
    public function store(CreateUserRequest $request): JsonResponse
    {
        return ApiResponse::created(new UserResource($this->createUserAction->execute(CreateUserDTO::fromArray($request->validated()))), 'User created successfully');
    }

    /**
     * @OA\Put(
     *     path="/api/v1/users/{id}",
     *     summary="Update user",
     *     description="Update user information",
     *     tags={"Users"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="User ID",
     *
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", format="email", example="user@example.com")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="User updated successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="User not found",
     *
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     ),
     *
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *
     *         @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse")
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     )
     * )
     */
    public function update(int $id, UpdateUserRequest $request): JsonResponse
    {
        return ApiResponse::success(new UserResource($this->updateUserAction->execute($id, UpdateUserDTO::fromArray($request->validated()))), 'User updated successfully');
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/users/{id}",
     *     summary="Delete user",
     *     description="Delete a user",
     *     tags={"Users"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="User ID",
     *
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="User deleted successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="User deleted")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="User not found",
     *
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     )
     * )
     */
    public function destroy(int $id): JsonResponse
    {
        $this->deleteUserAction->execute($id);

        return ApiResponse::success(null, 'User deleted successfully');
    }
}
