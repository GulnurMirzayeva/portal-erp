<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = Permission::pluck('name')->toArray();

        $adminRole = Role::firstOrCreate([
            'name' => 'Admin',
        ]);

        $adminRole->syncPermissions($permissions);

        Role::firstOrCreate([
            'name' => 'Director',
        ]);

        Role::firstOrCreate([
            'name' => 'HR',
        ]);

        Role::firstOrCreate([
            'name' => 'Accountant',
        ]);

        Role::firstOrCreate([
            'name' => 'Director Manager',
        ]);

        Role::firstOrCreate([
            'name' => 'Marketing',
        ]);
    }
}