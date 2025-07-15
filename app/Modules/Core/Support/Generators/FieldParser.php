<?php

declare(strict_types=1);

namespace App\Modules\Core\Support\Generators;

class FieldParser
{
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
