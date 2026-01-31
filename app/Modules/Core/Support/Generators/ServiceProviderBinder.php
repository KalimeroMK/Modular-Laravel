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

        
        
        $pattern = '/->withProviders\(\[(.*?)\]\)->create\(\)/s';
        if (preg_match($pattern, $content, $matches)) {
            $existingProviders = mb_trim($matches[1]);

            
            if ($existingProviders === '' || $existingProviders === '0') {
                
                $newContent = str_replace(
                    '->withProviders([',
                    "->withProviders([\n        {$providerClass},",
                    $content
                );
            } else {
                
                
                
                $lastProviderPattern = '/(\s+)(App\\\\Modules\\\\[^,]+::class)(,?)(\]\)->create\(\))/s';
                if (preg_match_all($lastProviderPattern, $content, $allMatches, PREG_SET_ORDER)) {
                    
                    $lastMatch = end($allMatches);
                    if (is_array($lastMatch) && count($lastMatch) >= 5) {
                        $indent = $lastMatch[1];
                        $lastProvider = $lastMatch[2];
                        $closing = $lastMatch[4];

                        
                        $replacement = "{$indent}{$lastProvider},\n{$indent}{$providerClass},\n{$indent}]{$closing}";

                        $newContent = str_replace($lastMatch[0], $replacement, $content);
                    }
                } else {
                    
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
