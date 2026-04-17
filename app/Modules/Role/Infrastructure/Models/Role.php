<?php

declare(strict_types=1);

namespace App\Modules\Role\Infrastructure\Models;

use App\Modules\Permission\Infrastructure\Models\Permission;
use App\Modules\Role\Database\Factories\RoleFactory;
use App\Modules\User\Infrastructure\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property int $id
 * @property string $name
 * @property string $guard_name
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 */
class Role extends Model
{
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

    public function getRouteKeyName(): string
    {
        return 'id';
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'role_has_permissions', 'role_id', 'permission_id');
    }

    public function users(): BelongsToMany
    {
        return $this->morphedByMany(User::class, 'model', 'model_has_roles', 'role_id', 'model_id');
    }

    public function givePermissionTo(Permission|string $permission): void
    {
        if (is_string($permission)) {
            $permission = Permission::where('name', $permission)->where('guard_name', $this->guard_name)->firstOrFail();
        }

        $this->permissions()->syncWithoutDetaching([$permission->id]);
    }

    public function revokePermissionTo(Permission|string $permission): void
    {
        if (is_string($permission)) {
            $permission = Permission::where('name', $permission)->where('guard_name', $this->guard_name)->firstOrFail();
        }

        $this->permissions()->detach($permission->id);
    }

    public function syncPermissions(array $permissions): void
    {
        $ids = collect($permissions)->map(function (Permission|string $permission) {
            if (is_string($permission)) {
                $permission = Permission::where('name', $permission)->where('guard_name', $this->guard_name)->firstOrFail();
            }

            return $permission->id;
        })->all();

        $this->permissions()->sync($ids);
    }

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
