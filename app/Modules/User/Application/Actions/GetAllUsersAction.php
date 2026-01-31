<?php

declare(strict_types=1);

namespace App\Modules\User\Application\Actions;

use App\Modules\User\Infrastructure\Repositories\UserRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class GetAllUsersAction
{
    public function __construct(
        protected UserRepositoryInterface $userRepository,
    ) {}

    


    public function execute(int $perPage = 15): LengthAwarePaginator
    {
         
        $result = $this->userRepository->paginate($perPage);

        return $result;
    }
}
