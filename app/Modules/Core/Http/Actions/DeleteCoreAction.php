<?php

namespace App\Modules\Core\Http\Actions;

use App\Modules\Core\Interfaces\RepositoryInterface;

class DeleteCoreAction
{
    protected RepositoryInterface $repository;

    public function __construct(RepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute(int $id): void
    {
        $this->repository->delete($id);
    }
}
