<?php

namespace Database\Seeders;

use App\Models\AdminUser;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        AdminUser::updateOrCreate(
            ['email' => 'admin@newsly.app'],
            [
                'name' => 'مدير النظام',
                'password' => Hash::make(env('ADMIN_PASSWORD', 'admin123')),
                'role' => AdminUser::ROLE_SUPER_ADMIN,
                'is_active' => true,
            ]
        );

        AdminUser::updateOrCreate(
            ['email' => 'editor@newsly.app'],
            [
                'name' => 'محرر',
                'password' => Hash::make('editor123'),
                'role' => AdminUser::ROLE_EDITOR,
                'is_active' => true,
            ]
        );
    }
}


