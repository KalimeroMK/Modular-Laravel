<?php

namespace App\Modules\User\Repositories;

use App\Modules\Core\Repositories\EloquentRepository;
use App\Modules\User\Interfaces\UserInterface;
use App\Modules\User\Models\User;

class UserRepository extends EloquentRepository implements UserInterface
{
    public function __construct(User $model)
    {
        parent::__construct($model);
    }
}
