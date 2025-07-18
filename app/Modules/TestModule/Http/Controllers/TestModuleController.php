<?php

namespace App\Modules\TestModule\Http\Controllers;

use App\Modules\Core\Traits\SwaggerTrait;
use App\Modules\TestModule\Models\TestModule;
use App\Modules\TestModule\Http\Requests\CreateTestModuleRequest;
use App\Modules\TestModule\Http\Requests\UpdateTestModuleRequest;
use App\Modules\TestModule\Http\Resources\TestModuleResource;
use App\Modules\TestModule\Http\DTOs\TestModuleDTO;
use App\Modules\TestModule\Http\Actions\CreateTestModuleAction;
use App\Modules\TestModule\Http\Actions\UpdateTestModuleAction;
use App\Modules\TestModule\Http\Actions\DeleteTestModuleAction;
use App\Modules\TestModule\Http\Actions\GetAllTestModuleAction;
use App\Modules\TestModule\Http\Actions\GetByIdTestModuleAction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Routing\Controller;

class TestModuleController extends Controller
{
    use SwaggerTrait;
    /**
     * @OA\Get(
     *     path="/api/v1/testmodules",
     *     summary="List testmodules",
     *     description="Get list of testmodules",
     *     tags={"TestModules"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="TestModules retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     )
     * )
     */
    public function index(GetAllTestModuleAction $action): ResourceCollection
    {
        return TestModuleResource::collection($action->execute());
    }

    /**
     * @OA\Get(
     *     path="/api/v1/testmodules/{id}",
     *     summary="Get testmodule by ID",
     *     description="Get specific testmodule information",
     *     tags={"TestModules"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="TestModule ID",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="TestModule retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="TestModule not found",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     )
     * )
     */
    public function show(TestModule $testmodule, GetByIdTestModuleAction $action): JsonResponse
    {
        return response()->json(new TestModuleResource($action->execute($testmodule)));
    }

    /**
     * @OA\Post(
     *     path="/api/v1/testmodules",
     *     summary="Create testmodule",
     *     description="Create a new testmodule",
     *     tags={"TestModules"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example="Example testmodule")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="TestModule created successfully",
     *         @OA\JsonContent(
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
    public function store(CreateTestModuleRequest $request, CreateTestModuleAction $action): JsonResponse
    {
        $dto = TestModuleDTO::fromRequest($request);
        $model = $action->execute($dto);
        return response()->json(new TestModuleResource($model), 201);
    }

    /**
     * @OA\Put(
     *     path="/api/v1/testmodules/{id}",
     *     summary="Update testmodule",
     *     description="Update testmodule information",
     *     tags={"TestModules"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="TestModule ID",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Updated testmodule")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="TestModule updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="TestModule not found",
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
    public function update(UpdateTestModuleRequest $request, TestModule $testmodule, UpdateTestModuleAction $action): JsonResponse
    {
        $dto = TestModuleDTO::fromRequest($request);
        $model = $action->execute($dto, $testmodule);
        return response()->json(new TestModuleResource($model));
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/testmodules/{id}",
     *     summary="Delete testmodule",
     *     description="Delete a testmodule",
     *     tags={"TestModules"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="TestModule ID",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="TestModule deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="TestModule deleted successfully.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="TestModule not found",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     )
     * )
     */
    public function destroy(TestModule $testmodule, DeleteTestModuleAction $action): JsonResponse
    {
        $action->execute($testmodule);
        return response()->json(['message' => 'TestModule deleted successfully.']);
    }
}
