<?php

declare(strict_types=1);

namespace App\Modules\Core\Support\Generators;

use Illuminate\Filesystem\Filesystem;

class FeatureTestGenerator
{
    public function __construct(protected Filesystem $files) {}

    public function generate(string $moduleName, array $fields): void
    {
        $stubPath = base_path('stubs/tests/Feature/CrudTest.stub');
        $targetPath = base_path("tests/Feature/Modules/{$moduleName}CrudTest.php");

        if (! $this->files->exists($stubPath)) {
            return;
        }

        $requiredFields = array_filter($fields, fn ($f) => ($f['required'] ?? false));
        $allStringFields = array_filter($fields, fn ($f) => $f['type'] === 'string');
        $updateField = count($allStringFields) > 0 ? $allStringFields[array_key_first($allStringFields)] : ($fields[0] ?? null);

        $foreignKeySetup = '';
        $storeData = [];
        $usedFields = count($requiredFields) ? $requiredFields : $fields;

        foreach ($usedFields as $f) {
            if (str_ends_with($f['name'], '_id') && in_array($f['type'], ['int', 'bigint', 'unsignedBigInteger'])) {
                $relatedModel = ucfirst(str_replace('_id', '', $f['name']));
                $foreignKeySetup .= "$relatedModel = \\App\\Models\\$relatedModel::factory()->create();\n";
                $storeData[$f['name']] = "\$${relatedModel}->id";
            } else {
                $storeData[$f['name']] = $f['type'] === 'bool' ? true : ($f['type'] === 'int' ? 123 : ($f['type'] === 'float' ? 1.23 : 'test'));
            }
        }

        $updateData = [];
        $updateSetup = '';

        if ($updateField) {
            if (str_ends_with($updateField['name'], '_id') && in_array($updateField['type'], ['int', 'bigint', 'unsignedBigInteger'])) {
                $relatedModel = ucfirst(str_replace('_id', '', $updateField['name']));
                $updateSetup .= "$relatedModel = \\App\\Models\\$relatedModel::factory()->create();\n";
                $updateData[$updateField['name']] = "\$${relatedModel}->id";
            } else {
                $updateData[$updateField['name']] = $updateField['type'] === 'string' ? 'updated' : ($updateField['type'] === 'bool' ? false : ($updateField['type'] === 'int' ? 456 : 4.56));
            }
        }

        $replacements = [
            '{{module}}' => $moduleName,
            '{{module_lower}}' => mb_strtolower($moduleName),
            '{{store_data_setup}}' => $foreignKeySetup,
            '{{store_data}}' => var_export($storeData, true),
            '{{update_data_setup}}' => $updateSetup,
            '{{update_data}}' => var_export($updateData, true),
        ];

        $content = $this->files->get($stubPath);
        $content = str_replace(array_keys($replacements), array_values($replacements), $content);

        $this->files->ensureDirectoryExists(dirname($targetPath));
        $this->files->put($targetPath, $content);
    }
}
