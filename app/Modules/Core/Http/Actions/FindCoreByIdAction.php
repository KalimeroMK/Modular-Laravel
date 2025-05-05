<?php

namespace App\Modules\Core\Http\Actions;

use App\Modules\Core\Interfaces\RepositoryInterface;
use Illuminate\Database\Eloquent\Model;

class FindCoreByIdAction
{
    protected RepositoryInterface $repository;

    public function __construct(RepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute(int $id): ?Model
    {
        return $this->repository->findById($id);
    }
}
