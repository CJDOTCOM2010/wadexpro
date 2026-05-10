<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SystemSettingConsolidationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * This seeder ensures that all 21+ required system settings exist.
     */
    public function run(): void
    {
        $settings = [
            // --- General Branding ---
            ['key' => 'enterprise_name', 'value' => 'WADEXPRO Logistics Hub', 'type' => 'string', 'group' => 'general', 'label' => 'Enterprise Name'],
            ['key' => 'support_email', 'value' => 'ops@wadexpro.com', 'type' => 'string', 'group' => 'general', 'label' => 'Support Email'],
            
            // --- Maps & Location ---
            ['key' => 'google_maps_api_key', 'value' => '', 'type' => 'string', 'group' => 'general', 'label' => 'Google Maps API Key'],

            // --- Google Auth Configuration ---
            ['key' => 'google_auth_enabled', 'value' => 'false', 'type' => 'boolean', 'group' => 'authentication', 'label' => 'Enable Google Auth'],
            ['key' => 'google_auth_ios', 'value' => 'true', 'type' => 'boolean', 'group' => 'authentication', 'label' => 'Enable Google on iOS'],
            ['key' => 'google_auth_android', 'value' => 'true', 'type' => 'boolean', 'group' => 'authentication', 'label' => 'Enable Google on Android'],
            ['key' => 'google_auth_web', 'value' => 'true', 'type' => 'boolean', 'group' => 'authentication', 'label' => 'Enable Google on Web'],
            ['key' => 'google_auth_web_client_id', 'value' => '', 'type' => 'string', 'group' => 'authentication', 'label' => 'Google Web Client ID'],
            ['key' => 'google_auth_ios_client_id', 'value' => '', 'type' => 'string', 'group' => 'authentication', 'label' => 'Google iOS Client ID'],

            // --- Facebook Auth Configuration ---
            ['key' => 'facebook_auth_enabled', 'value' => 'false', 'type' => 'boolean', 'group' => 'authentication', 'label' => 'Enable Facebook Auth'],
            ['key' => 'facebook_auth_ios', 'value' => 'true', 'type' => 'boolean', 'group' => 'authentication', 'label' => 'Enable Facebook on iOS'],
            ['key' => 'facebook_auth_android', 'value' => 'true', 'type' => 'boolean', 'group' => 'authentication', 'label' => 'Enable Facebook on Android'],
            ['key' => 'facebook_auth_web', 'value' => 'true', 'type' => 'boolean', 'group' => 'authentication', 'label' => 'Enable Facebook on Web'],
            ['key' => 'facebook_app_id', 'value' => '', 'type' => 'string', 'group' => 'authentication', 'label' => 'Facebook App ID'],
            ['key' => 'facebook_client_token', 'value' => '', 'type' => 'string', 'group' => 'authentication', 'label' => 'Facebook Client Token'],

            // --- Versioning / Manifest ---
            ['key' => 'min_customer_app_version', 'value' => '1.0.0', 'type' => 'string', 'group' => 'manifest', 'label' => 'Min Customer Version'],
            ['key' => 'play_store_customer_link', 'value' => 'https://play.google.com/store/apps/details?id=com.wadexpro.customer', 'type' => 'string', 'group' => 'manifest', 'label' => 'Customer Play Store'],
            ['key' => 'app_store_customer_link', 'value' => 'https://apps.apple.com/app/wadexpro-customer', 'type' => 'string', 'group' => 'manifest', 'label' => 'Customer App Store'],
            ['key' => 'min_driver_app_version', 'value' => '1.0.0', 'type' => 'string', 'group' => 'manifest', 'label' => 'Min Driver Version'],
            ['key' => 'play_store_driver_link', 'value' => 'https://play.google.com/store/apps/details?id=com.wadexpro.driver', 'type' => 'string', 'group' => 'manifest', 'label' => 'Driver Play Store'],
            ['key' => 'app_store_driver_link', 'value' => 'https://apps.apple.com/app/wadexpro-driver', 'type' => 'string', 'group' => 'manifest', 'label' => 'Driver App Store'],
        ];

        foreach ($settings as $setting) {
            DB::table('system_settings')->updateOrInsert(
                ['key' => $setting['key']],
                array_merge($setting, [
                    'id' => Str::uuid(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }
}
