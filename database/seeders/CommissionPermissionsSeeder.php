<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
class CommissionPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       $permissions = [
            'commissions.view',
            'commissions.export',
            'commissions.create',
            'commissions.complete',
            'commissions.deliver',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        $adminRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'Admin']);
$adminRole->givePermissionTo($permissions);
    }
}