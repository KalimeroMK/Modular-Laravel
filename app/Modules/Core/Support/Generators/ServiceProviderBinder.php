<?php

declare(strict_types=1);

namespace App\Modules\Core\Support\Generators;

use Illuminate\Filesystem\Filesystem;

class ServiceProviderBinder
{
    public function __construct(protected Filesystem $files) {}

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

        if (str_contains($content, $providerClass)) {
            return;
        }

        $pattern = '/->withProviders\(\[(?<providers>.*?)\]\)\s*->create\(\)/s';
        if (! preg_match($pattern, $content, $matches)) {
            return;
        }

        $providersBlock = $matches['providers'] ?? '';

        if (str_contains($providersBlock, $providerClass)) {
            return;
        }

        $indent = '        ';
        if (preg_match_all('/\n(\s*)App\\\\Modules\\\\[^,\n]+::class/', $providersBlock, $indentMatches)) {
            $lastIndent = end($indentMatches[1]);
            if (is_string($lastIndent) && $lastIndent !== '') {
                $indent = $lastIndent;
            }
        }

        $trimmedProviders = mb_trim($providersBlock);
        if ($trimmedProviders === '') {
            $newProviders = "\n{$indent}{$providerClass},\n    ";
        } else {
            $newProviders = mb_rtrim($providersBlock)."\n{$indent}{$providerClass},\n    ";
        }

        $newContent = str_replace($providersBlock, $newProviders, $content);
        $this->files->put($bootstrapPath, $newContent);
    }
}
