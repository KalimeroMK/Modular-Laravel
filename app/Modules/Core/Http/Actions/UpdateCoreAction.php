<?php

namespace App\Modules\Core\Http\Actions;

use App\Modules\Core\Http\DTOs\UpdateCoreDTO;
use App\Modules\Core\Interfaces\RepositoryInterface;
use Illuminate\Database\Eloquent\Model;

class UpdateCoreAction
{
    protected RepositoryInterface $repository;

    public function __construct(RepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute(int $id, UpdateCoreDTO $dto): Model
    {
        return $this->repository->update($id, $dto->toArray());
    }
}
