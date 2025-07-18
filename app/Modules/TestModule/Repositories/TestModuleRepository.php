<?php

namespace App\Modules\TestModule\Repositories;

use App\Modules\Core\Repositories\EloquentRepository;
use App\Modules\TestModule\Interfaces\TestModuleInterface;
use App\Modules\TestModule\Models\TestModule;

class TestModuleRepository extends EloquentRepository implements TestModuleInterface
{
    public function __construct(TestModule $model)
    {
        parent::__construct($model);
    }
}
