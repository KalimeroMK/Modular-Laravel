<?php

declare(strict_types=1);

namespace App\Modules\Role\Infrastructure\Http\Controllers;

use App\Http\Controllers\Controller;
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
use App\Modules\Role\Infrastructure\Http\Resources\RoleResource;
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

        $resourceCollection = RoleResource::collection($roles->items());
        $data = $resourceCollection->response()->getData(true);

        return response()->json([
            'status' => 'success',
            'message' => 'Roles retrieved successfully',
            'data' => $data['data'] ?? [],
            'meta' => [
                'current_page' => $roles->currentPage(),
                'last_page' => $roles->lastPage(),
                'per_page' => $roles->perPage(),
                'total' => $roles->total(),
                'from' => $roles->firstItem(),
                'to' => $roles->lastItem(),
            ],
            'links' => [
                'first' => $roles->url(1),
                'last' => $roles->url($roles->lastPage()),
                'prev' => $roles->previousPageUrl(),
                'next' => $roles->nextPageUrl(),
            ],
        ]);
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
    public function show(int $id): JsonResponse
    {
        $role = $this->getRoleByIdAction->execute($id);

        return ApiResponse::success(new RoleResource($role), 'Role retrieved successfully');
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

        return ApiResponse::created(new RoleResource($role), 'Role created successfully');
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
    public function update(int $id, UpdateRoleRequest $request): JsonResponse
    {
        $dto = UpdateRoleDTO::fromArray($request->validated());
        $updatedRole = $this->updateRoleAction->execute($id, $dto);

        return ApiResponse::success(new RoleResource($updatedRole), 'Role updated successfully');
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
    public function destroy(int $id): JsonResponse
    {
        $this->deleteRoleAction->execute($id);

        return ApiResponse::success(null, 'Role deleted successfully');
    }
}
