<?php

declare(strict_types=1);

namespace App\Providers;

use App\Modules\Auth\Infrastructure\Repositories\AuthRepository;
use App\Modules\Auth\Infrastructure\Repositories\AuthRepositoryInterface;
use App\Modules\Permission\Infrastructure\Repositories\PermissionRepository;
use App\Modules\Permission\Infrastructure\Repositories\PermissionRepositoryInterface;
use App\Modules\Role\Infrastructure\Repositories\RoleRepository;
use App\Modules\Role\Infrastructure\Repositories\RoleRepositoryInterface;
use App\Modules\User\Infrastructure\Repositories\UserRepository;
use App\Modules\User\Infrastructure\Repositories\UserRepositoryInterface;
use Illuminate\Support\ServiceProvider;
use ReflectionClass;
use ReflectionNamedType;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * @var string[]
     */
    protected array $repositories = [
        UserRepositoryInterface::class => UserRepository::class,
        AuthRepositoryInterface::class => AuthRepository::class,
        RoleRepositoryInterface::class => RoleRepository::class,
        PermissionRepositoryInterface::class => PermissionRepository::class,
    ];

    /**
     * Register services.
     */
    public function register(): void
    {

        foreach ($this->repositories as $interface => $repository) {
            $this->app->bind($interface, function ($app) use ($repository) {
                /** @var class-string $repository */
                $reflector = new ReflectionClass($repository);
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
