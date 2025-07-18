<?php

namespace App\Modules\TestModule\Http\Actions;

use App\Modules\TestModule\Interfaces\TestModuleInterface;
use App\Modules\TestModule\Models\TestModule;

class DeleteTestModuleAction
{
    public function __construct(protected TestModuleInterface $repository) {}

    public function execute(TestModule $model): bool
    {
        return $this->repository->delete($model->id);
    }
}
