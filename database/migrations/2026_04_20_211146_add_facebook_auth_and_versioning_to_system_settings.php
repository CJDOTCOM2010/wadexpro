<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $settings = [
            // Facebook Auth
            [
                'id' => Str::uuid(),
                'key' => 'facebook_auth_enabled',
                'value' => 'false',
                'type' => 'boolean',
                'group' => 'authentication',
                'label' => 'Enable Facebook Authentication',
                'tooltip' => 'Toggle Facebook Sign-In visibility in mobile apps',
                'is_public' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid(),
                'key' => 'facebook_app_id',
                'value' => '',
                'type' => 'string',
                'group' => 'authentication',
                'label' => 'Facebook App ID',
                'tooltip' => 'The App ID from your Facebook Developer Console',
                'is_public' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid(),
                'key' => 'facebook_client_token',
                'value' => '',
                'type' => 'string',
                'group' => 'authentication',
                'label' => 'Facebook Client Token',
                'tooltip' => 'The Client Token from Facebook App Settings',
                'is_public' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Versioning - Customer
            [
                'id' => Str::uuid(),
                'key' => 'min_customer_app_version',
                'value' => '1.0.0',
                'type' => 'string',
                'group' => 'manifest',
                'label' => 'Min Customer App Version',
                'tooltip' => 'Users on versions below this will be forced to update',
                'is_public' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid(),
                'key' => 'play_store_customer_link',
                'value' => 'https://play.google.com/store/apps/details?id=com.wadexpro.customer',
                'type' => 'string',
                'group' => 'manifest',
                'label' => 'Customer Play Store Link',
                'tooltip' => 'Link to the customer app on Android',
                'is_public' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid(),
                'key' => 'app_store_customer_link',
                'value' => 'https://apps.apple.com/app/wadexpro-customer',
                'type' => 'string',
                'group' => 'manifest',
                'label' => 'Customer App Store Link',
                'tooltip' => 'Link to the customer app on iOS',
                'is_public' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Versioning - Driver
            [
                'id' => Str::uuid(),
                'key' => 'min_driver_app_version',
                'value' => '1.0.0',
                'type' => 'string',
                'group' => 'manifest',
                'label' => 'Min Driver App Version',
                'tooltip' => 'Drivers on versions below this will be forced to update',
                'is_public' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid(),
                'key' => 'play_store_driver_link',
                'value' => 'https://play.google.com/store/apps/details?id=com.wadexpro.driver',
                'type' => 'string',
                'group' => 'manifest',
                'label' => 'Driver Play Store Link',
                'tooltip' => 'Link to the driver app on Android',
                'is_public' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid(),
                'key' => 'app_store_driver_link',
                'value' => 'https://apps.apple.com/app/wadexpro-driver',
                'type' => 'string',
                'group' => 'manifest',
                'label' => 'Driver App Store Link',
                'tooltip' => 'Link to the driver app on iOS',
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
            'facebook_auth_enabled',
            'facebook_app_id',
            'facebook_client_token',
            'min_customer_app_version',
            'play_store_customer_link',
            'app_store_customer_link',
            'min_driver_app_version',
            'play_store_driver_link',
            'app_store_driver_link',
        ])->delete();
    }
};
