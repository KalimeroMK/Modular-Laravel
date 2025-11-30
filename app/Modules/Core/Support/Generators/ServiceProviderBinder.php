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
        // Pattern to match: ->withProviders([...])->create()
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
                // Find the last provider line - handle case where last provider is on same line as ])->create()
                // Pattern to match: "        Provider::class,])->create()" or "        Provider::class])->create()"
                // Use preg_match_all to find all matches, then get the last one
                $lastProviderPattern = '/(\s+)(App\\\\Modules\\\\[^,]+::class)(,?)(\]\)->create\(\))/s';
                if (preg_match_all($lastProviderPattern, $content, $allMatches, PREG_SET_ORDER)) {
                    // Get the last match
                    $lastMatch = end($allMatches);
                    if (is_array($lastMatch) && count($lastMatch) >= 5) {
                        $indent = $lastMatch[1];
                        $lastProvider = $lastMatch[2];
                        $closing = $lastMatch[4];

                        // Build replacement: last provider with comma, new provider with comma, then closing on new line
                        $replacement = "{$indent}{$lastProvider},\n{$indent}{$providerClass},\n{$indent}]{$closing}";

                        $newContent = str_replace($lastMatch[0], $replacement, $content);
                    }
                } else {
                    // Fallback: try to find any provider followed by ])->create()
                    $simplePattern = '/(App\\\\Modules\\\\[^,]+::class)(,?)(\]\)->create\(\))/s';
                    if (preg_match_all($simplePattern, $content, $allSimpleMatches, PREG_SET_ORDER)) {
                        $simpleMatch = end($allSimpleMatches);
                        if (is_array($simpleMatch) && count($simpleMatch) >= 4) {
                            $lastProvider = $simpleMatch[1];
                            $closing = $simpleMatch[3];

                            $replacement = "{$lastProvider},\n        {$providerClass},\n    ]{$closing}";
                            $newContent = str_replace($simpleMatch[0], $replacement, $content);
                        }
                    } else {
                        // Last resort: replace ])->create() with new provider + ])->create()
                        $newContent = str_replace(
                            '])->create()',
                            "        {$providerClass},\n    ])->create()",
                            $content
                        );
                    }
                }
            }

            if (isset($newContent) && $newContent !== $content) {
                $this->files->put($bootstrapPath, $newContent);
            }
        }
    }
}
