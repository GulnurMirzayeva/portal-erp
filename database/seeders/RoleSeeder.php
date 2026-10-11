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
        // 1. Admin: Bütün icazələr tam şəkildə aktivdir
        $allPermissions = Permission::all();
        $adminRole = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        $adminRole->syncPermissions($allPermissions);

        // 2. Director: Yalnız "view" və "export" (baxış) icazələri - Redaktə/Silmə yoxdur
        $directorPermissions = Permission::where(function ($q) {
            $q->where('name', 'like', '%.view')
              ->orWhere('name', 'like', '%.export');
        })->get();
        $directorRole = Role::firstOrCreate(['name' => 'Director', 'guard_name' => 'web']);
        $directorRole->syncPermissions($directorPermissions);

        // 3. Accountant (Mühasibatlıq): Yalnız Dashboard və Mühasibatlıq/Xərclər
        $accountantPermissions = Permission::where(function ($q) {
            $q->where('name', 'dashboard.view')
              ->orWhere('name', 'like', 'accounting.%')
              ->orWhere('name', 'like', 'expenses.%')
              ->orWhere('name', 'like', 'expense-classifications.%');
        })->get();
        $accountantRole = Role::firstOrCreate(['name' => 'Accountant', 'guard_name' => 'web']);
        $accountantRole->syncPermissions($accountantPermissions);

        // 4. CRM: Yalnız Dashboard və CRM
        $crmPermissions = Permission::where(function ($q) {
            $q->where('name', 'dashboard.view')
              ->orWhere('name', 'like', 'crm.%');
        })->get();
        $crmRole = Role::firstOrCreate(['name' => 'CRM', 'guard_name' => 'web']);
        $crmRole->syncPermissions($crmPermissions);

        // Əlavə köməkçi rollar (varsa qorunur)
        Role::firstOrCreate(['name' => 'HR', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'Marketing', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'Director Manager', 'guard_name' => 'web']);
    }
}