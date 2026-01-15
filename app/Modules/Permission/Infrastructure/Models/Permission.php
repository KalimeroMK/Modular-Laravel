<?php

declare(strict_types=1);

namespace App\Modules\Permission\Infrastructure\Models;

use App\Modules\Permission\Database\Factories\PermissionFactory;
use App\Modules\Role\Infrastructure\Models\Role;
use App\Modules\User\Infrastructure\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;
use Override;

/**
 * @property int $id
 * @property string $name
 * @property string $guard_name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 */
class Permission extends Model
{
    /** @use HasFactory<PermissionFactory> */
    use HasFactory;

    protected $table = 'permissions';

    protected $attributes = [
        'guard_name' => 'api',
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
    #[Override]
    public function getRouteKeyName(): string
    {
        return 'id';
    }

    /**
     * Get the roles that have this permission.
     *
     * @return BelongsToMany<Role, Model>
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_has_permissions', 'permission_id', 'role_id');
    }

    /**
     * Get the users that have this permission.
     *
     * @return BelongsToMany<User, Model>
     */
    public function users(): BelongsToMany
    {
        return $this->morphedByMany(User::class, 'model', 'model_has_permissions', 'permission_id', 'model_id');
    }
}
