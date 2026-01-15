<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Override;

/**
 * @deprecated This service provider is no longer used.
 * Repository bindings are now registered in individual module service providers.
 * This file is kept for legacy support only and can be safely removed.
 */
class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    #[Override]
    public function register(): void
    {
        // Repository bindings are now handled by individual module service providers
        // See: App\Modules\{Module}\Infrastructure\Providers\{Module}ModuleServiceProvider
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Empty - all functionality moved to individual module service providers
    }
}
