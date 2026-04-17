<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Modules\Permission\Infrastructure\Models\Permission;
use App\Modules\Role\Infrastructure\Models\Role;
use App\Modules\User\Infrastructure\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $modules = ['users', 'roles', 'permissions'];
        $actions = ['view', 'create', 'edit', 'delete'];

        foreach ($modules as $module) {
            foreach ($actions as $action) {
                Permission::firstOrCreate(
                    ['name' => "{$module}.{$action}", 'guard_name' => 'api']
                );
            }
        }

        $superAdmin = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'api']);
        $editor = Role::firstOrCreate(['name' => 'Editor', 'guard_name' => 'api']);
        $viewer = Role::firstOrCreate(['name' => 'Viewer', 'guard_name' => 'api']);

        $superAdmin->syncPermissions(Permission::all()->all());

        $editor->syncPermissions([
            'users.view', 'users.create', 'users.edit',
            'roles.view', 'roles.create', 'roles.edit',
            'permissions.view', 'permissions.create', 'permissions.edit',
        ]);

        $viewer->syncPermissions([
            'users.view',
            'roles.view',
            'permissions.view',
        ]);

        $adminUser = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);
        $adminUser->assignRole($superAdmin);

        $editorUser = User::factory()->create([
            'name' => 'Editor User',
            'email' => 'editor@example.com',
            'password' => Hash::make('password'),
        ]);
        $editorUser->assignRole($editor);

        $viewerUser = User::factory()->create([
            'name' => 'Viewer User',
            'email' => 'viewer@example.com',
            'password' => Hash::make('password'),
        ]);
        $viewerUser->assignRole($viewer);
    }
}
