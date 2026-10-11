<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Admin: Tam hüquqlu
        $admin = User::updateOrCreate(
            ['email' => 'admin@portal.land'],
            [
                'name' => 'ERP Admin',
                'password' => Hash::make('Admin12345!'),
            ]
        );
        $admin->syncRoles(['Admin']);

        // 2. Director: Yalnız baxış hüquqlu (view-only)
        $director = User::updateOrCreate(
            ['email' => 'director@portal.land'],
            [
                'name' => 'ERP Direktor',
                'password' => Hash::make('Director12345!'),
            ]
        );
        $director->syncRoles(['Director']);

        // 3. Mühasibatlıq: Yalnız Mühasibatlıq və Dashboard
        $accountant = User::updateOrCreate(
            ['email' => 'accountant@portal.land'],
            [
                'name' => 'Baş Mühasib',
                'password' => Hash::make('Accountant12345!'),
            ]
        );
        $accountant->syncRoles(['Accountant']);

        // 4. CRM: Yalnız CRM və Dashboard
        $crm = User::updateOrCreate(
            ['email' => 'crm@portal.land'],
            [
                'name' => 'CRM Menecer',
                'password' => Hash::make('Crm12345!'),
            ]
        );
        $crm->syncRoles(['CRM']);
    }
}