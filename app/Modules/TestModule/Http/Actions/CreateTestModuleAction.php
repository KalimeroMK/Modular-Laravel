<?php

namespace App\Modules\TestModule\Http\Actions;

use App\Modules\TestModule\Http\DTOs\TestModuleDTO;
use App\Modules\TestModule\Interfaces\TestModuleInterface;
use App\Modules\TestModule\Models\TestModule;

class CreateTestModuleAction
{
    public function __construct(protected TestModuleInterface $repository) {}

    /**
     * @return TestModule|null
     */
    public function execute(TestModuleDTO $dto): ?TestModule
    {
        return $this->repository->create($dto->toArray());
    }
}
