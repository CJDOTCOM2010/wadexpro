<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $existingAdmins = Admin::count();

        if ($existingAdmins === 0) {
            Admin::create([
                'name' => 'Super Admin',
                'email' => 'admin@wadexpro.com',
                'password' => Hash::make('password123'),
                'role' => 'super_admin',
                'level' => 'super_admin',
                'is_active' => true,
                'is_super_admin' => true,
                'department' => 'Executive',
                'email_verified_at' => now(),
            ]);

            $this->command->info('Super Admin created: admin@wadexpro.com / password123');
        }

        Admin::updateOrCreate(
            ['email' => 'system@wadexpro.com'],
            [
                'name' => 'System Administrator',
                'password' => Hash::make('sys_' . uniqid()),
                'role' => 'admin',
                'level' => 'admin',
                'is_active' => true,
                'is_super_admin' => false,
                'department' => 'IT Operations',
            ]
        );
    }
}