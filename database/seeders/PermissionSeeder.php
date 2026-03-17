<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        // List of permissions (adjust names to your app)
        $permissions = [
            'product.view',
            'product.create',
            'product.edit',
            'product.delete',
            'user.view',
            'user.create',
            'user.edit',
            'user.delete',
        ];

        // Create permissions (idempotent: firstOrCreate)
        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        // Create roles
        $admin = Role::firstOrCreate(['name' => 'Admin']);
        $manager = Role::firstOrCreate(['name' => 'Manager']);
        $staff = Role::firstOrCreate(['name' => 'Staff']);

        // Assign permissions to roles
        $admin->syncPermissions($permissions); // Admin gets everything

        $managerPerms = [
            'product.view',
            'product.create',
            'product.edit',
            'user.view',
        ];
        $manager->syncPermissions($managerPerms);

        $staff->syncPermissions(['product.view']);
    }
}
