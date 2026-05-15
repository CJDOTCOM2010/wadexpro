<?php

use App\Models\Admin;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    public function up(): void
    {
        $adminUsers = DB::table('users')
            ->whereIn('user_type', ['admin', 'super_admin'])
            ->where('is_active', true)
            ->get();

        foreach ($adminUsers as $user) {
            Admin::updateOrCreate(
                ['email' => $user->email],
                [
                    'name' => $user->name,
                    'phone' => $user->phone,
                    'email_verified_at' => $user->email_verified_at,
                    'password' => $user->password,
                    'role' => $user->user_type === 'super_admin' ? 'super_admin' : 'admin',
                    'level' => $user->user_type === 'super_admin' ? 'super_admin' : 'admin',
                    'avatar_url' => $user->avatar_url,
                    'is_active' => $user->is_active,
                    'is_super_admin' => $user->user_type === 'super_admin',
                    'last_login_at' => $user->last_login_at,
                ]
            );
        }

        $this->command->info("Migrated {$adminUsers->count()} admin users to admins table.");
    }

    public function down(): void
    {
        // No rollback needed - this is a one-way migration
    }
};