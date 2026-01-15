<?php

declare(strict_types=1);

/**
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */

namespace App\Modules\NonExistentStubModule\Infrastructure\Models;

use App\Modules\NonExistentStubModule\Database\Factories\NonExistentStubModuleFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;


/**
 * NonExistentStubModule Model.
 * 
 * Represents a nonexistentstubmodule in the database.
 * 
 * @property int $id
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class NonExistentStubModule extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'test_modules';

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name'
    ];

    /**
     * Create a new factory instance for the model.
     *
     * @return NonExistentStubModuleFactory
     */
    public static function newFactory(): NonExistentStubModuleFactory
    {
        return NonExistentStubModuleFactory::new();
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'id';
    }

    // RELATIONSHIPS
    
}