<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'view users',
            'create users',
            'edit users',
            'delete users',

            'view posts',
            'create posts',
            'edit posts',
            'delete posts',

            'view reports',
            'generate reports',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $admin = Role::firstOrCreate(['name' => 'Admin']);
        $admin->syncPermissions(Permission::all());

        $marketing = Role::firstOrCreate(['name' => 'Marketing']);
        $marketing->syncPermissions([
            'view posts',
            'create posts',
            'edit posts',
            'view reports',
            'generate reports',
        ]);

        $viewer = Role::firstOrCreate(['name' => 'Viewer']);
        $viewer->syncPermissions([
            'view users',
            'view posts',
            'view reports',
        ]);
    }
}