<?php

namespace App\Modules\Core\Http\Actions;

use App\Modules\Core\Interfaces\RepositoryInterface;
use Illuminate\Support\Collection;

class GetAllCoreAction
{
    protected RepositoryInterface $repository;

    public function __construct(RepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute(): Collection
    {
        return $this->repository->findAll();
    }
}
