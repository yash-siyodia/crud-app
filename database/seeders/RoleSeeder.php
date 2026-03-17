<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run()
    {
        // Example roles
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $manager = Role::firstOrCreate(['name' => 'manager']);
        $viewer = Role::firstOrCreate(['name' => 'viewer']);

        // give all permissions to admin
        $allPermissions = Permission::pluck('name')->toArray();
        $admin->syncPermissions($allPermissions);

        // give subset to manager (example)
        $manager->syncPermissions([
            'product.view','product.create','product.edit'
        ]);

        // viewer
        $viewer->syncPermissions(['product.view','user.view']);
    }
}
