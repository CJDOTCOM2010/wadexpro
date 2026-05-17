<?php

use App\Modules\Admin\Models\SystemSetting;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $settings = [
            // Driver App API Configuration
            [
                'key' => 'api_driver_base_url',
                'value' => 'https://wadexpro-4rexnj1k.on-forge.com/api/v1',
                'type' => 'string',
                'group' => 'api_configuration',
                'label' => 'Driver App API Base URL',
                'tooltip' => 'Base URL for the Driver mobile application API endpoints',
                'is_public' => true,
                'is_encrypted' => false,
            ],
            [
                'key' => 'api_driver_socket_url',
                'value' => 'https://wadexpro-4rexnj1k.on-forge.com:3000',
                'type' => 'string',
                'group' => 'api_configuration',
                'label' => 'Driver App Socket URL',
                'tooltip' => 'WebSocket server URL for real-time driver communication',
                'is_public' => true,
                'is_encrypted' => false,
            ],
            // Customer App API Configuration
            [
                'key' => 'api_customer_base_url',
                'value' => 'https://wadexpro-4rexnj1k.on-forge.com/api/v1',
                'type' => 'string',
                'group' => 'api_configuration',
                'label' => 'Customer App API Base URL',
                'tooltip' => 'Base URL for the Customer (Rider) mobile application API endpoints',
                'is_public' => true,
                'is_encrypted' => false,
            ],
            [
                'key' => 'api_customer_socket_url',
                'value' => 'https://wadexpro-4rexnj1k.on-forge.com:3000',
                'type' => 'string',
                'group' => 'api_configuration',
                'label' => 'Customer App Socket URL',
                'tooltip' => 'WebSocket server URL for real-time customer communication',
                'is_public' => true,
                'is_encrypted' => false,
            ],
            // Platform Configuration
            [
                'key' => 'api_platform_timeout',
                'value' => '30',
                'type' => 'integer',
                'group' => 'api_configuration',
                'label' => 'API Timeout (seconds)',
                'tooltip' => 'Default timeout for API requests in seconds',
                'is_public' => true,
                'is_encrypted' => false,
            ],
            [
                'key' => 'api_platform_retry_attempts',
                'value' => '3',
                'type' => 'integer',
                'group' => 'api_configuration',
                'label' => 'Retry Attempts',
                'tooltip' => 'Number of retry attempts on failed API requests',
                'is_public' => true,
                'is_encrypted' => false,
            ],
        ];

        foreach ($settings as $setting) {
            SystemSetting::updateOrCreate(
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
        SystemSetting::whereIn('key', [
            'api_driver_base_url',
            'api_driver_socket_url',
            'api_customer_base_url',
            'api_customer_socket_url',
            'api_platform_timeout',
            'api_platform_retry_attempts',
        ])->delete();
    }
};
