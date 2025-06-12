<?php

namespace App\Modules\Auth\Repositories;

use App\Modules\Core\Repositories\EloquentRepository;
use App\Modules\User\Models\User;

class AuthRepository extends EloquentRepository
{
    public function __construct(User $model)
    {
        parent::__construct($model);
    }
}
