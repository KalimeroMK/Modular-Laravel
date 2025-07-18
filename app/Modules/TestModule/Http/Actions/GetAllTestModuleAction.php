<?php

namespace App\Modules\TestModule\Http\Actions;

use App\Modules\TestModule\Interfaces\TestModuleInterface;
use App\Modules\TestModule\Models\TestModule;

class GetAllTestModuleAction
{
    public function __construct(protected TestModuleInterface $repository) {}

    /**
     * @return iterable<TestModule>
     */
    public function execute(): iterable
    {
        return $this->repository->all();
    }
}
