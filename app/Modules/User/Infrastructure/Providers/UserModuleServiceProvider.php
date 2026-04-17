<?php

declare(strict_types=1);

namespace App\Modules\User\Infrastructure\Providers;

use App\Modules\Core\Infrastructure\Providers\AbstractCrudModuleServiceProvider;
use App\Modules\User\Infrastructure\Models\User;
use App\Modules\User\Infrastructure\Policies\UserPolicy;
use App\Modules\User\Infrastructure\Repositories\UserRepository;
use App\Modules\User\Infrastructure\Repositories\UserRepositoryInterface;

class UserModuleServiceProvider extends AbstractCrudModuleServiceProvider
{
    protected function getModuleConfig(): array
    {
        return [
            'moduleName' => 'User',
            'model' => User::class,
            'policy' => UserPolicy::class,
            'repository' => UserRepository::class,
            'interface' => UserRepositoryInterface::class,
            'routeFile' => __DIR__.'/../Routes/users.php',
        ];
    }
}
