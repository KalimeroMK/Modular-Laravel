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
        $interface = "App\\Modules\\{$moduleName}\\Interfaces\\{$moduleName}Interface";
        $repository = "App\\Modules\\{$moduleName}\\Repositories\\{$moduleName}Repository";

        if (! $this->files->exists($providerPath)) {
            return;
        }

        $content = $this->files->get($providerPath);
        $pattern = '/protected\s+array\s+\$repositories\s*=\s*\[(.*?)\];/s';

        if (preg_match($pattern, $content, $matches)) {
            $existingEntries = mb_trim($matches[1]);
            $newEntry = "        \\{$interface}::class => \\{$repository}::class,";

            if (Str::contains($existingEntries, $newEntry)) {
                return;
            }

            $updatedEntries = $existingEntries ? "$existingEntries\n$newEntry" : $newEntry;
            $replacement = 'protected array $repositories = ['."\n".$updatedEntries."\n];";
            $content = preg_replace($pattern, $replacement, $content);

            $this->files->put($providerPath, (string) $content);
        }
    }
}
