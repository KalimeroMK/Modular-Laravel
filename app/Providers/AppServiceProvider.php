<?php

declare(strict_types=1);

namespace App\Providers;

use App\Modules\Core\Support\Generators\ModuleConfigUpdater;
use App\Modules\Core\Support\Generators\ModuleGenerationTracker;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\ServiceProvider;
use Override;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    #[Override]
    public function register(): void
    {
        // Register ModuleGenerationTracker as singleton for tracking generated files
        $this->app->singleton(ModuleGenerationTracker::class, fn ($app) => new ModuleGenerationTracker(
            $app->make(Filesystem::class),
            $app->make(ModuleConfigUpdater::class)
        ));
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Prevent lazy loading in non-production environments to catch N+1 queries during development
        Model::preventLazyLoading(! $this->app->isProduction());

        // Prevent silently discarding attributes that don't have a corresponding column in the database
        Model::preventSilentlyDiscardingAttributes(! $this->app->isProduction());

        // Prevent accessing missing attributes (throws exception instead of returning null)
        Model::preventAccessingMissingAttributes(! $this->app->isProduction());
    }
}
