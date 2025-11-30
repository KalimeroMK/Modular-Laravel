<?php

declare(strict_types=1);

namespace App\Modules\Permission\Infrastructure\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Core\Support\ApiResponse;
use App\Modules\Core\Traits\SwaggerTrait;
use App\Modules\Permission\Application\Actions\CreatePermissionAction;
use App\Modules\Permission\Application\Actions\DeletePermissionAction;
use App\Modules\Permission\Application\Actions\GetAllPermissionsAction;
use App\Modules\Permission\Application\Actions\GetPermissionByIdAction;
use App\Modules\Permission\Application\Actions\UpdatePermissionAction;
use App\Modules\Permission\Application\DTO\CreatePermissionDTO;
use App\Modules\Permission\Application\DTO\UpdatePermissionDTO;
use App\Modules\Permission\Infrastructure\Http\Requests\CreatePermissionRequest;
use App\Modules\Permission\Infrastructure\Http\Requests\UpdatePermissionRequest;
use App\Modules\Permission\Infrastructure\Http\Resources\PermissionResource;
use Illuminate\Http\JsonResponse;

class PermissionController extends Controller
{
    use SwaggerTrait;

    public function __construct(
        protected GetAllPermissionsAction $getAllPermissionsAction,
        protected GetPermissionByIdAction $getPermissionByIdAction,
        protected CreatePermissionAction $createPermissionAction,
        protected UpdatePermissionAction $updatePermissionAction,
        protected DeletePermissionAction $deletePermissionAction,
    ) {}

    /**
     * @OA\Get(
     *     path="/api/v1/permissions",
     *     summary="List permissions",
     *     description="Get paginated list of permissions",
     *     tags={"Permissions"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Response(
     *         response=200,
     *         description="Permissions retrieved successfully",
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
        $permissions = $this->getAllPermissionsAction->execute();

        return ApiResponse::paginated($permissions, 'Permissions retrieved successfully', PermissionResource::collection($permissions->items()));
    }

    /**
     * @OA\Get(
     *     path="/api/v1/permissions/{id}",
     *     summary="Get permission by ID",
     *     description="Get specific permission information",
     *     tags={"Permissions"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Permission ID",
     *
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Permission retrieved successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Permission not found",
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
        return ApiResponse::success(new PermissionResource($this->getPermissionByIdAction->execute($id)), 'Permission retrieved successfully');
    }

    /**
     * @OA\Post(
     *     path="/api/v1/permissions",
     *     summary="Create permission",
     *     description="Create a new permission",
     *     tags={"Permissions"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"name"},
     *
     *             @OA\Property(property="name", type="string", example="create-users"),
     *             @OA\Property(property="guard_name", type="string", example="api")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=201,
     *         description="Permission created successfully",
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
    public function store(CreatePermissionRequest $request): JsonResponse
    {
        return ApiResponse::created(new PermissionResource($this->createPermissionAction->execute(CreatePermissionDTO::fromArray($request->validated()))), 'Permission created successfully');
    }

    /**
     * @OA\Put(
     *     path="/api/v1/permissions/{id}",
     *     summary="Update permission",
     *     description="Update permission information",
     *     tags={"Permissions"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Permission ID",
     *
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="name", type="string", example="create-users"),
     *             @OA\Property(property="guard_name", type="string", example="api")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Permission updated successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Permission not found",
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
    public function update(int $id, UpdatePermissionRequest $request): JsonResponse
    {
        return ApiResponse::success(new PermissionResource($this->updatePermissionAction->execute($id, UpdatePermissionDTO::fromArray($request->validated()))), 'Permission updated successfully');
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/permissions/{id}",
     *     summary="Delete permission",
     *     description="Delete a permission",
     *     tags={"Permissions"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Permission ID",
     *
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Permission deleted successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Permission deleted")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Permission not found",
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
        $this->deletePermissionAction->execute($id);

        return ApiResponse::success(null, 'Permission deleted successfully');
    }
}
