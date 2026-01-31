<?php

declare(strict_types=1);

namespace App\Modules\Core\Interfaces;






interface RepositoryInterface extends CacheableRepositoryInterface, ReadableRepositoryInterface, SoftDeletableRepositoryInterface, WritableRepositoryInterface
{
    
    
}
