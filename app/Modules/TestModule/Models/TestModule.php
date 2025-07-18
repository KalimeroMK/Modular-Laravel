<?php

/**
 * @property int $id
 * @property string $name
 * @property string $description
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */

namespace App\Modules\TestModule\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestModule extends Model
{
    use HasFactory;

    protected $table = 'test_modules';

    protected $casts = [
                'is_active' => 'bool',
    ];

    protected $fillable = [
        'name', 'description', 'is_active'
    ];

    public static function newFactory(): \App\Modules\TestModule\Database\Factories\TestModuleFactory
    {
        return \App\Modules\TestModule\Database\Factories\TestModuleFactory::new();
    }

    // RELATIONSHIPS
    
}