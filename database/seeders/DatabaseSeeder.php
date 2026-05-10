<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Core Platform Admins
        User::firstOrCreate(
            ['email' => 'admin@wadexpro.com'],
            [
                'id' => \Illuminate\Support\Str::uuid()->toString(),
                'name' => 'Super Admin',
                'password' => bcrypt('WadexPro2026!'), // Ensure strong initial pass
                'email_verified_at' => now(),
                'user_type' => 'super_admin'
            ]
        );

        // Required CMS Seedings
        $this->call([
            CmsLandingPageSeeder::class,
            MenuSeeder::class,
            OnboardingSlideSeeder::class,
        ]);
    }
}
