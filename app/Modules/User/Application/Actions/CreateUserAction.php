<?php

declare(strict_types=1);

namespace App\Modules\User\Application\Actions;

use App\Modules\Core\Application\Actions\AbstractCreateAction;
use App\Modules\User\Infrastructure\Repositories\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;

class CreateUserAction extends AbstractCreateAction
{
    public function __construct(UserRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }

    protected function mapDtoToArray(object $dto): array
    {
        return [
            'name' => $dto->name,
            'email' => $dto->email,
            'password' => Hash::make($dto->password),
            'email_verified_at' => $dto->emailVerifiedAt,
        ];
    }
}
