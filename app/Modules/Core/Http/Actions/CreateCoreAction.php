<?php

namespace App\Modules\Core\Http\Actions;

use App\Modules\Core\Http\DTOs\CreateCoreDTO;
use App\Modules\Core\Interfaces\RepositoryInterface;
use Illuminate\Database\Eloquent\Model;

class CreateCoreAction
{
    protected RepositoryInterface $repository;

    public function __construct(RepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute(CreateCoreDTO $dto): Model
    {
        return $this->repository->create($dto->toArray());
    }
}
