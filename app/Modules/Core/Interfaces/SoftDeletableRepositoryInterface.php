<?php

declare(strict_types=1);

namespace App\Modules\Core\Interfaces;

use Illuminate\Database\Eloquent\Model;




interface SoftDeletableRepositoryInterface
{
    




    public function restore(int|string $id): ?Model;

    




    public function findWithTrashed(int|string $id): ?Model;
}
