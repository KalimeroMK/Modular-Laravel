<?php

declare(strict_types=1);

namespace App\Modules\Core\Infrastructure\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

abstract class AbstractCrudModuleServiceProvider extends ServiceProvider
{
    abstract protected function getModuleConfig(): array;

    public function register(): void
    {
        $config = $this->getModuleConfig();
        $this->app->bind($config['interface'], $config['repository']);
    }

    public function boot(): void
    {
        if (! $this->isModuleEnabled()) {
            return;
        }

        $this->registerPolicies();
        $this->loadRoutes();
    }

    protected function isModuleEnabled(): bool
    {
        $config = $this->getModuleConfig();

        return (bool) config("modules.specific.{$config['moduleName']}.enabled", true);
    }

    protected function registerPolicies(): void
    {
        $config = $this->getModuleConfig();

        if (class_exists($config['model']) && class_exists($config['policy'])) {
            Gate::policy($config['model'], $config['policy']);
        }
    }

    protected function loadRoutes(): void
    {
        $config = $this->getModuleConfig();
        $routeFile = $config['routeFile'] ?? null;

        if ($routeFile && file_exists($routeFile)) {
            require $routeFile;
        }
    }
}
