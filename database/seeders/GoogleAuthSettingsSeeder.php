<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class GoogleAuthSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            [
                'id' => Str::uuid(),
                'key' => 'google_auth_enabled',
                'value' => 'false',
                'type' => 'boolean',
                'group' => 'authentication',
                'label' => 'Enable Google Authentication',
                'tooltip' => 'Toggle Google Sign-In visibility in mobile apps',
                'is_public' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid(),
                'key' => 'google_auth_web_client_id',
                'value' => 'YOUR_GOOGLE_WEB_CLIENT_ID',
                'type' => 'string',
                'group' => 'authentication',
                'label' => 'Google Web Client ID',
                'tooltip' => 'OAuth 2.0 Client ID for Web/Android/Firebase',
                'is_public' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid(),
                'key' => 'google_auth_ios_client_id',
                'value' => 'YOUR_GOOGLE_IOS_CLIENT_ID',
                'type' => 'string',
                'group' => 'authentication',
                'label' => 'Google iOS Client ID',
                'tooltip' => 'OAuth 2.0 Client ID for iOS/macOS',
                'is_public' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($settings as $setting) {
            DB::table('system_settings')->updateOrInsert(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
