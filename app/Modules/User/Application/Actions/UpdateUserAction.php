<?php

declare(strict_types=1);

namespace App\Modules\User\Application\Actions;

use App\Modules\Core\Application\Actions\AbstractUpdateAction;
use App\Modules\User\Infrastructure\Repositories\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;

class UpdateUserAction extends AbstractUpdateAction
{
    public function __construct(UserRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }

    protected function beforeUpdate(array $data): array
    {
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        return $data;
    }
}
