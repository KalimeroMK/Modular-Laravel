<?php

declare(strict_types=1);

namespace App\Modules\Role\Infrastructure\Models;

use App\Modules\Role\Database\Factories\RoleFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Models\Role as BaseRole;

class Role extends BaseRole
{
    /** @use HasFactory<RoleFactory> */
    use HasFactory;

    protected $attributes = [
        'guard_name' => 'web',
    ];

    protected $table = 'roles';

    public static function factory(): RoleFactory
    {
        return RoleFactory::new();
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'id';
    }

    /**
     * Retrieve the model for bound value.
     *
     * @param  mixed  $value
     * @param  string|null  $field
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function resolveRouteBinding($value, $field = null)
    {
        $field = $field ?: $this->getRouteKeyName();
        
        return $this->newQuery()->where($field, $value)->firstOrFail();
    }
}
