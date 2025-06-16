<?php

declare(strict_types=1);

namespace App\Modules\User\Http\Actions;

use App\Modules\User\Interfaces\UserInterface;
use Illuminate\Support\Collection;

class GetAllUserAction
{
    public function __construct(protected UserInterface $repository) {}

    public function execute(): Collection
    {
        return $this->repository->all();
    }
}
