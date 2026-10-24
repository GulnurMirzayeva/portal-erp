<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            // Dashboard
            'dashboard.view',

            // Mühasibatlıq: Elektron Qaimələr
            'accounting.view',
            'accounting.edit',
            'accounting.export',

            // Mühasibatlıq: Xərclər Cədvəli
            'expenses.view',
            'expenses.create',
            'expenses.edit',
            'expenses.delete',
            'expenses.export',

            // Mühasibatlıq: Xərc Təsnifatları
            'expense-classifications.view',
            'expense-classifications.create',
            'expense-classifications.edit',
            'expense-classifications.delete',

            // CRM
            'crm.view',
            'crm.create',
            'crm.edit',
            'crm.delete',
            'crm.export',

            // İstifadəçilər və İşçilər
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',

            'employees.view',
            'employees.create',
            'employees.edit',
            'employees.delete',

            // Davamiyyət (QR)
            'attendance.view',
            'attendance.export',
            'attendance.sync',
            'attendance.manage-branches',
            'attendance.manage-workers',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }
    }
}