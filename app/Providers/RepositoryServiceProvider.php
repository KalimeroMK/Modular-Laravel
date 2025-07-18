<?php

declare(strict_types=1);

namespace App\Providers;

use App\Modules\Auth\Interfaces\AuthInterface;
use App\Modules\Auth\Repositories\AuthRepository;
use App\Modules\Permission\Interfaces\PermissionInterface;
use App\Modules\Permission\Repositories\PermissionRepository;
use App\Modules\Role\Interfaces\RoleInterface;
use App\Modules\Role\Repositories\RoleRepository;
use App\Modules\User\Interfaces\UserInterface;
use App\Modules\User\Repositories\UserRepository;
use Illuminate\Support\ServiceProvider;
use ReflectionClass;
use ReflectionNamedType;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * @var string[]
     */
    protected array $repositories = [
UserInterface::class => UserRepository::class,
        AuthInterface::class => AuthRepository::class,
        RoleInterface::class => RoleRepository::class,
        PermissionInterface::class => PermissionRepository::class,
        \App\Modules\TestModule\Interfaces\TestModuleInterface::class => \App\Modules\TestModule\Repositories\TestModuleRepository::class,
        \App\Modules\Product\Interfaces\ProductInterface::class => \App\Modules\Product\Repositories\ProductRepository::class,
        \App\Modules\TestCLI\Interfaces\TestCLIInterface::class => \App\Modules\TestCLI\Repositories\TestCLIRepository::class,
        \App\Modules\TestYAML\Interfaces\TestYAMLInterface::class => \App\Modules\TestYAML\Repositories\TestYAMLRepository::class,
        \App\Modules\TestYAMLWithExceptions\Interfaces\TestYAMLWithExceptionsInterface::class => \App\Modules\TestYAMLWithExceptions\Repositories\TestYAMLWithExceptionsRepository::class,
        \App\Modules\TestCLIWithExceptions\Interfaces\TestCLIWithExceptionsInterface::class => \App\Modules\TestCLIWithExceptions\Repositories\TestCLIWithExceptionsRepository::class,
];

    /**
     * Register services.
     */
    public function register(): void
    {

        foreach ($this->repositories as $interface => $repository) {
            $this->app->bind($interface, function ($app) use ($repository) {
                /** @var class-string $repository */
                $reflector = new \ReflectionClass($repository);
                $constructor = $reflector->getConstructor();

                if ($constructor && $constructor->getNumberOfParameters() > 0) {
                    $param = $constructor->getParameters()[0];
                    $type = $param->getType();

                    if ($type instanceof ReflectionNamedType && ! $type->isBuiltin()) {
                        $modelClass = $type->getName();

                        return new $repository($app->make($modelClass));
                    }
                }

                return new $repository;
            });
        }
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
