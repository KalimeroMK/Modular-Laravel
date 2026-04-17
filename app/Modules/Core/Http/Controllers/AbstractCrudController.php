<?php

declare(strict_types=1);

namespace App\Modules\Core\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Core\Application\Actions\AbstractCreateAction;
use App\Modules\Core\Application\Actions\AbstractDeleteAction;
use App\Modules\Core\Application\Actions\AbstractGetAllAction;
use App\Modules\Core\Application\Actions\AbstractGetByIdAction;
use App\Modules\Core\Application\Actions\AbstractUpdateAction;
use App\Modules\Core\Support\ApiResponse;
use App\Modules\Core\Traits\SwaggerTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

abstract class AbstractCrudController extends Controller
{
    use SwaggerTrait;

    public function __construct(
        protected AbstractGetAllAction $getAllAction,
        protected AbstractGetByIdAction $getByIdAction,
        protected AbstractCreateAction $createAction,
        protected AbstractUpdateAction $updateAction,
        protected AbstractDeleteAction $deleteAction,
    ) {}

    abstract protected function getCreateDtoClass(): string;

    abstract protected function getUpdateDtoClass(): string;

    abstract protected function getResourceClass(): string;

    abstract protected function getEntityLabel(): string;

    final public function index(): JsonResponse
    {
        $items = $this->getAllAction->execute();
        $resourceClass = $this->getResourceClass();

        return ApiResponse::paginated(
            $items,
            $this->getEntityLabel().'s retrieved successfully',
            $resourceClass::collection($items->items())
        );
    }

    final public function show(int|string $id): JsonResponse
    {
        $resourceClass = $this->getResourceClass();

        return ApiResponse::success(
            new $resourceClass($this->getByIdAction->execute($id)),
            $this->getEntityLabel().' retrieved successfully'
        );
    }

    final public function destroy(int|string $id): JsonResponse
    {
        $this->deleteAction->execute($id);

        return ApiResponse::success(null, $this->getEntityLabel().' deleted successfully');
    }

    protected function handleStore(Request $request): JsonResponse
    {
        $dtoClass = $this->getCreateDtoClass();
        $dto = $dtoClass::fromArray($request->validated());
        $resourceClass = $this->getResourceClass();

        return ApiResponse::created(
            new $resourceClass($this->createAction->execute($dto)),
            $this->getEntityLabel().' created successfully'
        );
    }

    protected function handleUpdate(int|string $id, Request $request): JsonResponse
    {
        $dtoClass = $this->getUpdateDtoClass();
        $dto = $dtoClass::fromArray($request->validated());
        $resourceClass = $this->getResourceClass();

        return ApiResponse::success(
            new $resourceClass($this->updateAction->execute($id, $dto)),
            $this->getEntityLabel().' updated successfully'
        );
    }
}
