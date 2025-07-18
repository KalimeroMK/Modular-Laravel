<?php

namespace App\Modules\TestModule\Http\Actions;

use App\Modules\TestModule\Interfaces\TestModuleInterface;
use App\Modules\TestModule\Models\TestModule;

class GetByIdTestModuleAction
{
    public function __construct(protected TestModuleInterface $repository) {}

    /**
     * @return TestModule|null
     */
    public function execute(int|string $id): ?TestModule
    {
        return $this->repository->find($id);
    }
}
