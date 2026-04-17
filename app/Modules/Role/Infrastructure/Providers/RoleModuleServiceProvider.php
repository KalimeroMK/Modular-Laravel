<?php

declare(strict_types=1);

namespace App\Modules\Role\Infrastructure\Providers;

use App\Modules\Core\Infrastructure\Providers\AbstractCrudModuleServiceProvider;
use App\Modules\Role\Infrastructure\Models\Role;
use App\Modules\Role\Infrastructure\Policies\RolePolicy;
use App\Modules\Role\Infrastructure\Repositories\RoleRepository;
use App\Modules\Role\Infrastructure\Repositories\RoleRepositoryInterface;

class RoleModuleServiceProvider extends AbstractCrudModuleServiceProvider
{
    protected function getModuleConfig(): array
    {
        return [
            'moduleName' => 'Role',
            'model' => Role::class,
            'policy' => RolePolicy::class,
            'repository' => RoleRepository::class,
            'interface' => RoleRepositoryInterface::class,
            'routeFile' => __DIR__.'/../Routes/roles.php',
        ];
    }
}
