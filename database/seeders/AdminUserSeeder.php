<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::updateOrCreate(
            [
                'email' => 'admin@portal.land',
            ],
            [
                'name' => 'ERP Admin',
                'password' => 'Admin12345!',
            ],
        );

        $user->assignRole('Admin');
    }
}