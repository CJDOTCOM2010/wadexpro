<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
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
                'value' => '',
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
                'value' => '',
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

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('system_settings')->whereIn('key', [
            'google_auth_enabled',
            'google_auth_web_client_id',
            'google_auth_ios_client_id',
        ])->delete();
    }
};
