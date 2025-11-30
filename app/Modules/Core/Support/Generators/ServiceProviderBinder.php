<?php

declare(strict_types=1);

namespace App\Modules\Core\Support\Generators;

use Illuminate\Filesystem\Filesystem;

class ServiceProviderBinder
{
    public function __construct(protected Filesystem $files) {}

    /**
     * Register module service provider in bootstrap/app.php
     */
    public function bind(string $moduleName): void
    {
        $providerPath = app_path("Modules/{$moduleName}/Infrastructure/Providers/{$moduleName}ModuleServiceProvider.php");

        if (! $this->files->exists($providerPath)) {
            return;
        }

        $bootstrapPath = base_path('bootstrap/app.php');

        if (! $this->files->exists($bootstrapPath)) {
            return;
        }

        $content = $this->files->get($bootstrapPath);
        $providerClass = "App\\Modules\\{$moduleName}\\Infrastructure\\Providers\\{$moduleName}ModuleServiceProvider::class";

        // Check if provider is already registered
        if (str_contains($content, $providerClass)) {
            return;
        }

        // Find the withProviders array and add new provider at the end
        // Match the entire withProviders block including all providers
        $pattern = '/->withProviders\(\[(.*?)\]\)->create\(\)/s';
        if (preg_match($pattern, $content, $matches)) {
            $existingProviders = trim($matches[1]);

            // Add new provider before the closing bracket (at the end of the array)
            if (empty($existingProviders)) {
                // Empty array, add first provider
                $newContent = str_replace(
                    '->withProviders([',
                    "->withProviders([\n        {$providerClass},",
                    $content
                );
            } else {
                // Add to existing providers at the end, before the closing bracket
                // Simply replace the closing bracket with new provider + closing bracket
                $newContent = str_replace(
                    '    ])->create()',
                    "        {$providerClass},\n    ])->create()",
                    $content
                );
            }

            $this->files->put($bootstrapPath, $newContent);
        }
    }
}
