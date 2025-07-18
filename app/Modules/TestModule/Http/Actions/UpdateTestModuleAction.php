<?php

namespace App\Modules\TestModule\Http\Actions;

use App\Modules\TestModule\Http\DTOs\TestModuleDTO;
use App\Modules\TestModule\Interfaces\TestModuleInterface;
use App\Modules\TestModule\Models\TestModule;

class UpdateTestModuleAction
{
    public function __construct(protected TestModuleInterface $repository) {}

    /**
     * @return TestModule|null
     */
    public function execute(TestModuleDTO $dto, TestModule $model): ?TestModule
    {
        return $this->repository->update($model->id, $dto->toArray());
    }
}
