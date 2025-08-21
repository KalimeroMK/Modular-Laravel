<?php

declare(strict_types=1);

namespace App\Modules\Core\Support\Generators;

class FieldParser
{
    /**
     * @return array<int, array{name: string, type: string, references?: string, on?: string, morphable_name?: string}>
     */
    public function parse(string $fieldsOption): array
    {
        $fields = [];
        foreach (explode(',', $fieldsOption) as $field) {
            $parts = explode(':', $field);
            $name = $parts[0];

            if (isset($parts[1]) && $parts[1] === 'foreign') {
                $fields[] = [
                    'name' => $name,
                    'type' => 'foreign',
                    'references' => $parts[3] ?? 'id',
                    'on' => $parts[2] ?? (str_ends_with($name, '_id') ? mb_rtrim($name, '_id').'s' : $name.'s'),
                ];

                continue;
            }

            if (isset($parts[1]) && $parts[1] === 'morphable') {
                // Handle morphable fields - generate both type and id fields
                $morphableName = $parts[2] ?? $name;

                $fields[] = [
                    'name' => $morphableName.'_type',
                    'type' => 'string',
                    'morphable_name' => $morphableName,
                ];

                $fields[] = [
                    'name' => $morphableName.'_id',
                    'type' => 'int',
                    'morphable_name' => $morphableName,
                ];

                continue;
            }

            $type = match ($parts[1] ?? 'string') {
                'int', 'integer', 'bigint', 'smallint', 'tinyint', 'foreignId' => 'int',
                'bool', 'boolean' => 'bool',
                'float', 'double', 'decimal' => 'float',
                'json', 'array' => 'array',
                default => 'string',
            };

            $fields[] = [
                'name' => $name,
                'type' => $type,
            ];
        }

        return $fields;
    }
}
