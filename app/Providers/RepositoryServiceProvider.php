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
     * Map of interfaces to their concrete repository classes.
     *
     * @var array<class-string, class-string>
     */
    protected array $repositories = [
        UserInterface::class => UserRepository::class,
        AuthInterface::class => AuthRepository::class,
        RoleInterface::class => RoleRepository::class,
        PermissionInterface::class => PermissionRepository::class,
    ];

    /**
     * Register services.
     */
    public function register(): void
    {
        foreach ($this->repositories as $interface => $repositoryClass) {
            $this->app->bind($interface, function ($app) use ($repositoryClass) {
                $reflector = new ReflectionClass($repositoryClass);
                $constructor = $reflector->getConstructor();

                if ($constructor && $constructor->getNumberOfParameters() > 0) {
                    $param = $constructor->getParameters()[0];
                    $type = $param->getType();

                    if ($type instanceof ReflectionNamedType && ! $type->isBuiltin()) {
                        $modelClass = $type->getName();

                        return new $repositoryClass($app->make($modelClass));
                    }
                }

                return new $repositoryClass();
            });
        }
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // No boot logic needed
    }
}
