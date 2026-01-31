<?php

declare(strict_types=1);

namespace App\Modules\Core\Support\Generators;

class ModuleConfigUpdater
{
    protected string $configPath;

    public function __construct()
    {
        $this->configPath = config_path('modules.php');
    }

    


    public function addModule(string $moduleName): void
    {
        if (! file_exists($this->configPath)) {
            return;
        }

        $content = file_get_contents($this->configPath);

        
        if ($this->moduleExists($content, $moduleName)) {
            return;
        }

        
        $newModuleEntry = $this->buildModuleEntry($moduleName);
        $content = $this->insertModuleEntry($content, $newModuleEntry);

        file_put_contents($this->configPath, $content);
    }

    


    public function removeModule(string $moduleName): void
    {
        if (! file_exists($this->configPath)) {
            return;
        }

        $content = file_get_contents($this->configPath);

        
        $pattern = "/\s+'{$moduleName}' => \[\s+.*?\s+\],\n/s";
        $content = preg_replace($pattern, '', $content);

        file_put_contents($this->configPath, $content);
    }

    


    protected function moduleExists(string $content, string $moduleName): bool
    {
        return str_contains($content, "'{$moduleName}' =>");
    }

    


    protected function buildModuleEntry(string $moduleName): string
    {
        return "        '{$moduleName}' => [\n            'enabled' => true,\n        ],";
    }

    


    protected function insertModuleEntry(string $content, string $newEntry): string
    {
        
        $pattern = "/(    'specific' => \[.*?)(\n    \],\n\];)/s";

        if (preg_match($pattern, $content, $matches)) {
            $before = $matches[1];
            $after = $matches[2];

            
            return str_replace($matches[0], $before."\n".$newEntry.$after, $content);
        }

        return $content;
    }
}
