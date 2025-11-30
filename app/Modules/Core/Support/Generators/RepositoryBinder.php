<?php

declare(strict_types=1);

namespace App\Modules\Core\Support\Generators;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class RepositoryBinder
{
    public function __construct(protected Filesystem $files) {}

    public function bind(string $moduleName): void
    {
        $providerPath = app_path('Providers/RepositoryServiceProvider.php');
        $interfaceClass = "App\\Modules\\{$moduleName}\\Infrastructure\\Repositories\\{$moduleName}RepositoryInterface";
        $repositoryClass = "App\\Modules\\{$moduleName}\\Infrastructure\\Repositories\\{$moduleName}Repository";
        $interfaceShort = "{$moduleName}RepositoryInterface";
        $repositoryShort = "{$moduleName}Repository";

        if (! $this->files->exists($providerPath)) {
            return;
        }

        // Verify that interface and repository files exist before binding
        $interfacePath = app_path("Modules/{$moduleName}/Infrastructure/Repositories/{$moduleName}RepositoryInterface.php");
        $repositoryPath = app_path("Modules/{$moduleName}/Infrastructure/Repositories/{$moduleName}Repository.php");

        if (! $this->files->exists($interfacePath) || ! $this->files->exists($repositoryPath)) {
            return;
        }

        $content = $this->files->get($providerPath);

        // Check if already registered
        if (Str::contains($content, $interfaceShort) || Str::contains($content, $interfaceClass)) {
            return;
        }

        // Add use statements after existing use statements
        $usePattern = '/^(use\s+[^;]+;)$/m';
        $lastUsePosition = 0;

        if (preg_match_all($usePattern, $content, $useMatches, PREG_OFFSET_CAPTURE)) {
            $lastMatch = end($useMatches[0]);
            if ($lastMatch !== false && is_array($lastMatch) && count($lastMatch) >= 2) {
                $lastUsePosition = $lastMatch[1] + mb_strlen($lastMatch[0]);
            }
        }

        $newUseStatements = "use {$interfaceClass};\nuse {$repositoryClass};\n";

        // Check if already exists
        if (Str::contains($content, "use {$interfaceClass};") || Str::contains($content, "use {$repositoryClass};")) {
            // Use statements already exist, skip adding them
        } else {
            // Find position after last use statement
            if ($lastUsePosition > 0) {
                // Find end of line after last use
                $nextLinePos = mb_strpos($content, "\n", $lastUsePosition);
                if ($nextLinePos !== false) {
                    $content = substr_replace($content, "\n".$newUseStatements, $nextLinePos, 0);
                } else {
                    $content = $content."\n".$newUseStatements;
                }
            } else {
                // No use statements found, add before class
                $classPos = mb_strpos($content, 'class RepositoryServiceProvider');
                if ($classPos !== false) {
                    $content = substr_replace($content, $newUseStatements."\n", $classPos, 0);
                }
            }
        }

        // Add to repositories array
        $arrayPattern = '/protected\s+array\s+\$repositories\s*=\s*\[(.*?)\];/s';

        if (preg_match($arrayPattern, $content, $matches)) {
            $existingEntries = mb_trim($matches[1]);
            $newEntry = "        {$interfaceShort}::class => {$repositoryShort}::class,";

            if (Str::contains($existingEntries, $interfaceShort)) {
                return;
            }

            $updatedEntries = $existingEntries ? "$existingEntries\n$newEntry" : $newEntry;
            $replacement = 'protected array $repositories = ['."\n".$updatedEntries."\n];";
            $content = preg_replace($arrayPattern, $replacement, $content);

            $this->files->put($providerPath, (string) $content);
        }
    }
}
