<?php

declare(strict_types=1);

namespace App\Modules\Permission\Infrastructure\Models;

use App\Modules\Permission\Database\Factories\PermissionFactory;
use App\Modules\Role\Infrastructure\Models\Role;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Permission\Models\Permission as BasePermission;

class Permission extends BasePermission
{
    protected $table = 'permissions';

    protected $attributes = [
        'guard_name' => 'web',
    ];

    protected $fillable = [
        'name',
        'guard_name',
    ];

    public static function factory(): PermissionFactory
    {
        return PermissionFactory::new();
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

    /**
     * @phpstan-ignore-next-line
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_has_permissions');
    }
}
