<?php

declare(strict_types=1);

namespace App\Modules\Role\Infrastructure\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Core\Enums\ErrorCode;
use App\Modules\Core\Support\ApiResponse;
use App\Modules\Core\Traits\SwaggerTrait;
use App\Modules\Role\Application\Actions\CreateRoleAction;
use App\Modules\Role\Application\Actions\DeleteRoleAction;
use App\Modules\Role\Application\Actions\GetAllRolesAction;
use App\Modules\Role\Application\Actions\GetRoleByIdAction;
use App\Modules\Role\Application\Actions\UpdateRoleAction;
use App\Modules\Role\Application\DTO\CreateRoleDTO;
use App\Modules\Role\Application\DTO\UpdateRoleDTO;
use App\Modules\Role\Infrastructure\Http\Requests\CreateRoleRequest;
use App\Modules\Role\Infrastructure\Http\Requests\UpdateRoleRequest;
use App\Modules\Role\Infrastructure\Models\Role;
use Illuminate\Http\JsonResponse;

class RoleController extends Controller
{
    use SwaggerTrait;

    public function __construct(
        protected GetAllRolesAction $getAllRolesAction,
        protected GetRoleByIdAction $getRoleByIdAction,
        protected CreateRoleAction $createRoleAction,
        protected UpdateRoleAction $updateRoleAction,
        protected DeleteRoleAction $deleteRoleAction,
    ) {}

    /**
     * @OA\Get(
     *     path="/api/v1/roles",
     *     summary="List roles",
     *     description="Get paginated list of roles",
     *     tags={"Roles"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Response(
     *         response=200,
     *         description="Roles retrieved successfully",
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
        $roles = $this->getAllRolesAction->execute();

        return ApiResponse::paginated($roles, 'Roles retrieved successfully');
    }

    /**
     * @OA\Get(
     *     path="/api/v1/roles/{id}",
     *     summary="Get role by ID",
     *     description="Get specific role information",
     *     tags={"Roles"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Role ID",
     *
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Role retrieved successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Role not found",
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
    public function show(Role $role): JsonResponse
    {
        $roleDTO = $this->getRoleByIdAction->execute($role);

        return $roleDTO === null
            ? ApiResponse::error('Role not found', ErrorCode::RESOURCE_NOT_FOUND, [], 404)
            : ApiResponse::success($roleDTO->toArray(), 'Role retrieved successfully');
    }

    /**
     * @OA\Post(
     *     path="/api/v1/roles",
     *     summary="Create role",
     *     description="Create a new role",
     *     tags={"Roles"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"name"},
     *
     *             @OA\Property(property="name", type="string", example="admin"),
     *             @OA\Property(property="guard_name", type="string", example="api")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=201,
     *         description="Role created successfully",
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
    public function store(CreateRoleRequest $request): JsonResponse
    {
        $dto = CreateRoleDTO::fromArray($request->validated());
        $role = $this->createRoleAction->execute($dto);

        return ApiResponse::created($role->toArray(), 'Role created successfully');
    }

    /**
     * @OA\Put(
     *     path="/api/v1/roles/{id}",
     *     summary="Update role",
     *     description="Update role information",
     *     tags={"Roles"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Role ID",
     *
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="name", type="string", example="admin"),
     *             @OA\Property(property="guard_name", type="string", example="api")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Role updated successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Role not found",
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
    public function update(Role $role, UpdateRoleRequest $request): JsonResponse
    {
        $dto = UpdateRoleDTO::fromArray($request->validated());
        $updatedRole = $this->updateRoleAction->execute($role, $dto);

        return ApiResponse::success($updatedRole->toArray(), 'Role updated successfully');
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/roles/{id}",
     *     summary="Delete role",
     *     description="Delete a role",
     *     tags={"Roles"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Role ID",
     *
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Role deleted successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Role deleted")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Role not found",
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
    public function destroy(Role $role): JsonResponse
    {
        $this->deleteRoleAction->execute($role);

        return ApiResponse::success(null, 'Role deleted successfully');
    }
}
