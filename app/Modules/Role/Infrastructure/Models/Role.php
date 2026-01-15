<?php

declare(strict_types=1);

namespace App\Modules\Role\Infrastructure\Models;

use App\Modules\Permission\Infrastructure\Models\Permission;
use App\Modules\Role\Database\Factories\RoleFactory;
use App\Modules\User\Infrastructure\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Override;

/**
 * @property int $id
 * @property string $name
 * @property string $guard_name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static RoleFactory factory()
 */
class Role extends Model
{
    /** @use HasFactory<RoleFactory> */
    use HasFactory;

    protected $attributes = [
        'guard_name' => 'api',
    ];

    protected $fillable = [
        'name',
        'guard_name',
    ];

    protected $table = 'roles';

    public static function factory(): RoleFactory
    {
        return RoleFactory::new();
    }

    /**
     * Get the route key for the model.
     */
    #[Override]
    public function getRouteKeyName(): string
    {
        return 'id';
    }

    /**
     * Get the permissions for this role.
     *
     * @return BelongsToMany<Permission, Model>
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'role_has_permissions', 'role_id', 'permission_id');
    }

    /**
     * Get the users that have this role.
     *
     * @return BelongsToMany<User, Model>
     */
    public function users(): BelongsToMany
    {
        return $this->morphedByMany(User::class, 'model', 'model_has_roles', 'role_id', 'model_id');
    }

    /**
     * Give permission to this role.
     */
    public function givePermissionTo(Permission|string $permission): void
    {
        if (is_string($permission)) {
            $permission = Permission::where('name', $permission)->where('guard_name', $this->guard_name)->firstOrFail();
        }

        $this->permissions()->syncWithoutDetaching([$permission->id]);
    }

    /**
     * Revoke permission from this role.
     */
    public function revokePermissionTo(Permission|string $permission): void
    {
        if (is_string($permission)) {
            $permission = Permission::where('name', $permission)->where('guard_name', $this->guard_name)->firstOrFail();
        }

        $this->permissions()->detach($permission->id);
    }

    /**
     * Check if role has permission.
     */
    public function hasPermissionTo(Permission|string $permission): bool
    {
        if (is_string($permission)) {
            $permission = Permission::where('name', $permission)->where('guard_name', $this->guard_name)->first();
        }

        if (! $permission) {
            return false;
        }

        return $this->permissions()->where('permissions.id', $permission->id)->exists();
    }
}
