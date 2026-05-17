<?php

namespace App\Modules\Auth\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Modules\Admin\Models\SystemSetting;

class ConfigController extends Controller
{
    /**
     * Retrieve public configuration for the mobile/web applications.
     */
    public function getPublicConfig()
    {
        // Helper to build full URL for storage paths
        $buildUrl = function ($path) {
            if (! $path) {
                return null;
            }
            if (str_starts_with($path, 'http')) {
                return $path;
            }
            if (str_starts_with($path, '/storage')) {
                return url($path);
            }

            return '/storage/'.$path;
        };

        return response()->json([
            'status' => 'success',
            'data' => [
                // Social Auth
                'google_auth_enabled' => SystemSetting::get('google_auth_enabled', false),
                'google_auth_web_client_id' => SystemSetting::get('google_auth_web_client_id'),
                'google_auth_ios_client_id' => SystemSetting::get('google_auth_ios_client_id'),

                'facebook_auth_enabled' => SystemSetting::get('facebook_auth_enabled', false),
                'facebook_app_id' => SystemSetting::get('facebook_app_id'),
                'facebook_client_token' => SystemSetting::get('facebook_client_token'),

                // Mobile App Branding
                'branding' => [
                    'brand_name' => SystemSetting::get('brand_name', 'WADEXPRO'),
                    'brand_short_name' => SystemSetting::get('brand_short_name', 'WADEX'),
                    'brand_tagline' => SystemSetting::get('brand_tagline', 'Move. Deliver. Thrive.'),
                    'brand_logo_url' => $buildUrl(SystemSetting::get('brand_logo_url')),
                    'app_icon_url' => $buildUrl(SystemSetting::get('app_icon_url')),
                    'driver_app_icon_url' => $buildUrl(SystemSetting::get('driver_app_icon_url')),
                    'customer_app_icon_url' => $buildUrl(SystemSetting::get('customer_app_icon_url')),
                    'splash_background_color' => SystemSetting::get('splash_background_color', '#156400'),
                    'driver_splash_background' => SystemSetting::get('driver_splash_background', '#156400'),
                    'customer_splash_background' => SystemSetting::get('customer_splash_background', '#156400'),

                    'brand_primary_color' => SystemSetting::get('brand_primary_color', '#156400'),
                    'brand_accent_color' => SystemSetting::get('brand_accent_color', '#FFCC00'),
                    'brand_secondary_color' => SystemSetting::get('brand_secondary_color', '#0D4000'),
                    'brand_dark_color' => SystemSetting::get('brand_dark_color', '#0A0A0A'),

                    'customer_app_name' => SystemSetting::get('customer_app_display_name', 'WADEXPRO'),
                    'driver_app_name' => SystemSetting::get('driver_app_display_name', 'WADEXPRO Driver'),

                    'support_email' => SystemSetting::get('support_email', 'ops@wadexpro.com'),
                    'support_phone' => SystemSetting::get('brand_support_phone', ''),

                    'enterprise_name' => SystemSetting::get('enterprise_name', 'WADEXPRO Logistics Hub'),
                ],

                // App Store Links
                'app_links' => [
                    'customer' => [
                        'play_store' => SystemSetting::get('play_store_customer_link'),
                        'app_store' => SystemSetting::get('app_store_customer_link'),
                    ],
                    'driver' => [
                        'play_store' => SystemSetting::get('play_store_driver_link'),
                        'app_store' => SystemSetting::get('app_store_driver_link'),
                    ],
                ],

                // Version Control
                'versions' => [
                    'min_customer_version' => SystemSetting::get('min_customer_app_version', '1.0.0'),
                    'min_driver_version' => SystemSetting::get('min_driver_app_version', '1.0.0'),
                ],

                // API & RTC URLs
                'manifest' => [
                    'api_url' => SystemSetting::get('flutter_api_url'),
                    'rtc_url' => SystemSetting::get('flutter_rtc_url'),
                ],

                // API Configuration from Super Admin Dashboard
                'api_configuration' => [
                    'api_driver_base_url' => SystemSetting::get('api_driver_base_url'),
                    'api_driver_socket_url' => SystemSetting::get('api_driver_socket_url'),
                    'api_customer_base_url' => SystemSetting::get('api_customer_base_url'),
                    'api_customer_socket_url' => SystemSetting::get('api_customer_socket_url'),
                    'api_platform_timeout' => SystemSetting::get('api_platform_timeout', 30),
                    'api_platform_retry_attempts' => SystemSetting::get('api_platform_retry_attempts', 3),
                ],

                // Onboarding URLs for apps
                'onboarding' => [
                    'customer' => url('/api/v1/onboarding/customer'),
                    'driver' => url('/api/v1/onboarding/driver'),
                ],

                // Splash screen URLs for apps
                'splash' => [
                    'customer' => url('/api/v1/platform/splash/customer'),
                    'driver' => url('/api/v1/platform/splash/driver'),
                ],
            ],
        ]);
    }
}
