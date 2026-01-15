<?php

declare(strict_types=1);

namespace App\Modules\NonExistentStubModule\Infrastructure\Http\Controllers;

use App\Modules\Core\Support\ApiResponse;
use App\Modules\Core\Traits\SwaggerTrait;
use App\Modules\NonExistentStubModule\Infrastructure\Http\Requests\CreateNonExistentStubModuleRequest;
use App\Modules\NonExistentStubModule\Infrastructure\Http\Requests\UpdateNonExistentStubModuleRequest;
use App\Modules\NonExistentStubModule\Infrastructure\Http\Resources\NonExistentStubModuleResource;
use App\Modules\NonExistentStubModule\Application\DTO\NonExistentStubModuleDTO;
use App\Modules\NonExistentStubModule\Application\Actions\CreateNonExistentStubModuleAction;
use App\Modules\NonExistentStubModule\Application\Actions\UpdateNonExistentStubModuleAction;
use App\Modules\NonExistentStubModule\Application\Actions\DeleteNonExistentStubModuleAction;
use App\Modules\NonExistentStubModule\Application\Actions\GetAllNonExistentStubModuleAction;
use App\Modules\NonExistentStubModule\Application\Actions\GetByIdNonExistentStubModuleAction;
use App\Modules\Core\Exceptions\CreateException;
use App\Modules\Core\Exceptions\DeleteException;
use App\Modules\Core\Exceptions\UpdateException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;

class NonExistentStubModuleController extends Controller
{
    use SwaggerTrait;
    
    /**
     * NonExistentStubModule Controller
     * 
     * All routes are protected with auth:sanctum middleware and Laravel default rate limiting:
     * - GET operations: 120 requests per minute
     * - POST/PUT operations: 20 requests per hour  
     * - DELETE operations: 5 requests per hour
     */
    /**
     * Get a paginated list of nonexistentstubmodules.
     *
     * @param GetAllNonExistentStubModuleAction $action Action to retrieve all nonexistentstubmodules
     * @return JsonResponse Paginated list of nonexistentstubmodules
     * 
     * @OA\Get(
     *     path="/api/v1/nonexistentstubmodules",
     *     summary="List nonexistentstubmodules",
     *     description="Get paginated list of nonexistentstubmodules",
     *     tags={"NonExistentStubModules"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Items per page",
     *         @OA\Schema(type="integer", example=15)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="NonExistentStubModules retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="NonExistentStubModules retrieved successfully"),
     *             @OA\Property(property="data", type="array", @OA\Items(type="object")),
     *             @OA\Property(property="meta", type="object"),
     *             @OA\Property(property="links", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     )
     * )
     */
    public function index(GetAllNonExistentStubModuleAction $action): JsonResponse
    {
        $nonexistentstubmodules = $action->execute((int) request()->get('per_page', 15));
        return ApiResponse::paginated($nonexistentstubmodules, 'NonExistentStubModules retrieved successfully', NonExistentStubModuleResource::collection($nonexistentstubmodules->items()));
    }

    /**
     * Get a specific nonexistentstubmodule by ID.
     *
     * @param int $id The nonexistentstubmodule ID
     * @param GetByIdNonExistentStubModuleAction $action Action to retrieve nonexistentstubmodule by ID
     * @return JsonResponse nonexistentstubmodule details
     * 
     * @OA\Get(
     *     path="/api/v1/nonexistentstubmodules/{id}",
     *     summary="Get nonexistentstubmodule by ID",
     *     description="Get specific nonexistentstubmodule information by ID",
     *     tags={"NonExistentStubModules"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="NonExistentStubModule ID",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="NonExistentStubModule retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="NonExistentStubModule retrieved successfully"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="NonExistentStubModule not found",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     )
     * )
     */
    public function show(int $id, GetByIdNonExistentStubModuleAction $action): JsonResponse
    {
        return ApiResponse::success(new NonExistentStubModuleResource($action->execute($id)), 'NonExistentStubModule retrieved successfully');
    }

    /**
     * Create a new nonexistentstubmodule.
     *
     * @param CreateNonExistentStubModuleRequest $request Validated request containing nonexistentstubmodule data
     * @param CreateNonExistentStubModuleAction $action Action to create nonexistentstubmodule
     * @return JsonResponse Created nonexistentstubmodule details
     * @throws CreateException If nonexistentstubmodule creation fails
     * 
     * @OA\Post(
     *     path="/api/v1/nonexistentstubmodules",
     *     summary="Create nonexistentstubmodule",
     *     description="Create a new nonexistentstubmodule",
     *     tags={"NonExistentStubModules"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example="Example nonexistentstubmodule")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="NonExistentStubModule created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="NonExistentStubModule created successfully"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     )
     * )
     */
    public function store(CreateNonExistentStubModuleRequest $request, CreateNonExistentStubModuleAction $action): JsonResponse
    {
        return ApiResponse::created(new NonExistentStubModuleResource($action->execute(NonExistentStubModuleDTO::fromRequest($request))), 'NonExistentStubModule created successfully');
    }

    /**
     * Update an existing nonexistentstubmodule.
     *
     * @param int $id The nonexistentstubmodule ID
     * @param UpdateNonExistentStubModuleRequest $request Validated request containing updated nonexistentstubmodule data
     * @param UpdateNonExistentStubModuleAction $action Action to update nonexistentstubmodule
     * @return JsonResponse Updated nonexistentstubmodule details
     * @throws UpdateException If nonexistentstubmodule update fails
     * 
     * @OA\Put(
     *     path="/api/v1/nonexistentstubmodules/{id}",
     *     summary="Update nonexistentstubmodule",
     *     description="Update nonexistentstubmodule information",
     *     tags={"NonExistentStubModules"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="NonExistentStubModule ID",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Updated nonexistentstubmodule")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="NonExistentStubModule updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="NonExistentStubModule updated successfully"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="NonExistentStubModule not found",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     )
     * )
     */
    public function update(int $id, UpdateNonExistentStubModuleRequest $request, UpdateNonExistentStubModuleAction $action): JsonResponse
    {
        return ApiResponse::success(new NonExistentStubModuleResource($action->execute($id, NonExistentStubModuleDTO::fromRequest($request))), 'NonExistentStubModule updated successfully');
    }

    /**
     * Delete a nonexistentstubmodule.
     *
     * @param int $id The nonexistentstubmodule ID
     * @param DeleteNonExistentStubModuleAction $action Action to delete nonexistentstubmodule
     * @return JsonResponse Success message
     * @throws DeleteException If nonexistentstubmodule deletion fails
     * 
     * @OA\Delete(
     *     path="/api/v1/nonexistentstubmodules/{id}",
     *     summary="Delete nonexistentstubmodule",
     *     description="Delete a nonexistentstubmodule",
     *     tags={"NonExistentStubModules"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="NonExistentStubModule ID",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="NonExistentStubModule deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="NonExistentStubModule deleted successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="NonExistentStubModule not found",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     )
     * )
     */
    public function destroy(int $id, DeleteNonExistentStubModuleAction $action): JsonResponse
    {
        $action->execute($id);
        return ApiResponse::success(null, 'NonExistentStubModule deleted successfully');
    }
}
