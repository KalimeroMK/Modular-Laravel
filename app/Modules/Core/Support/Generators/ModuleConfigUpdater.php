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

    /**
     * Add module to config/modules.php with enabled: true
     */
    public function addModule(string $moduleName): void
    {
        if (! file_exists($this->configPath)) {
            return;
        }

        $content = file_get_contents($this->configPath);

        // Check if module already exists in config
        if ($this->moduleExists($content, $moduleName)) {
            return;
        }

        // Find the 'specific' array and add the new module
        $newModuleEntry = $this->buildModuleEntry($moduleName);
        $content = $this->insertModuleEntry($content, $newModuleEntry);

        file_put_contents($this->configPath, $content);
    }

    /**
     * Remove module from config (for rollback)
     */
    public function removeModule(string $moduleName): void
    {
        if (! file_exists($this->configPath)) {
            return;
        }

        $content = file_get_contents($this->configPath);

        // Remove the module entry
        $pattern = "/\s+'{$moduleName}' => \[\s+.*?\s+\],\n/s";
        $content = preg_replace($pattern, '', $content);

        file_put_contents($this->configPath, $content);
    }

    /**
     * Check if module already exists in config
     */
    protected function moduleExists(string $content, string $moduleName): bool
    {
        return str_contains($content, "'{$moduleName}' =>");
    }

    /**
     * Build the module entry string
     */
    protected function buildModuleEntry(string $moduleName): string
    {
        return "        '{$moduleName}' => [\n            'enabled' => true,\n        ],";
    }

    /**
     * Insert module entry into the 'specific' array
     */
    protected function insertModuleEntry(string $content, string $newEntry): string
    {
        // Find the last entry in 'specific' array before the closing bracket
        $pattern = "/(    'specific' => \[.*?)(\n    \],\n\];)/s";

        if (preg_match($pattern, $content, $matches)) {
            $before = $matches[1];
            $after = $matches[2];

            // Add new module entry before the closing bracket
            return str_replace($matches[0], $before."\n".$newEntry.$after, $content);
        }

        return $content;
    }
}
