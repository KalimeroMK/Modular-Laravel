<?php

declare(strict_types=1);

namespace App\Modules\Core\Support\Relations;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class SyncRelations
{
    /**
     * @param  array<string, mixed>  $relations
     */
    public static function execute(Model $model, array $relations): void
    {
        $hasChanges = false;

        foreach ($relations as $relation => $value) {
            if (! method_exists($model, $relation)) {
                continue;
            }

            $relationInstance = $model->{$relation}();

            // BelongsToMany and MorphToMany: sync()
            if (($relationInstance instanceof BelongsToMany || $relationInstance instanceof MorphToMany) && is_array($value)) {
                $relationInstance->sync($value);

                continue;
            }

            // BelongsTo: set foreign key directly
            if ($relationInstance instanceof BelongsTo && (is_scalar($value) || is_null($value))) {
                $foreignKey = $relationInstance->getForeignKeyName();

                if ($model->{$foreignKey} !== $value) {
                    $model->{$foreignKey} = $value;
                    $hasChanges = true;
                }
            }

            // MorphTo: handle polymorphic relationships
            if ($relationInstance instanceof MorphTo) {
                $morphType = $relationInstance->getMorphType();
                $foreignKey = $relationInstance->getForeignKeyName();

                if (is_null($value)) {
                    // Clear the relationship
                    if ($model->{$morphType} !== null || $model->{$foreignKey} !== null) {
                        $model->{$morphType} = null;
                        $model->{$foreignKey} = null;
                        $hasChanges = true;
                    }
                } elseif (is_array($value) && isset($value['type'], $value['id'])) {
                    // Handle array format: ['type' => 'App\\Models\\User', 'id' => 123]
                    if ($model->{$morphType} !== $value['type'] || $model->{$foreignKey} !== $value['id']) {
                        $model->{$morphType} = $value['type'];
                        $model->{$foreignKey} = $value['id'];
                        $hasChanges = true;
                    }
                } elseif ($value instanceof Model) {
                    // Handle model instance
                    $newType = $value->getMorphClass();
                    $newId = $value->getKey();

                    if ($model->{$morphType} !== $newType || $model->{$foreignKey} !== $newId) {
                        $model->{$morphType} = $newType;
                        $model->{$foreignKey} = $newId;
                        $hasChanges = true;
                    }
                }
            }
        }

        if ($hasChanges) {
            $model->save();
        }
    }
}
