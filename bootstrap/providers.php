<?php

declare(strict_types=1);

return [
    App\Providers\AppServiceProvider::class,
    App\Providers\ModularServiceProvider::class,
    // RepositoryServiceProvider removed - each module registers its own repository bindings
];
