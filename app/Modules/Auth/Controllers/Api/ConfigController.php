<?php

namespace App\Modules\Auth\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Modules\Admin\Models\SystemSetting;
use Illuminate\Http\Request;

class ConfigController extends Controller
{
    /**
     * Retrieve public configuration for the mobile/web applications.
     */
    public function getPublicConfig()
    {
        // Build logo URL with full path if it's a relative storage path
        $logoUrl = SystemSetting::get('brand_logo_url', '');
        if ($logoUrl && str_starts_with($logoUrl, '/storage')) {
            $logoUrl = url($logoUrl);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'google_auth_enabled' => SystemSetting::get('google_auth_enabled', false),
                'google_auth_web_client_id' => SystemSetting::get('google_auth_web_client_id'),
                'google_auth_ios_client_id' => SystemSetting::get('google_auth_ios_client_id'),
                
                'facebook_auth_enabled' => SystemSetting::get('facebook_auth_enabled', false),
                'facebook_app_id' => SystemSetting::get('facebook_app_id'),
                'facebook_client_token' => SystemSetting::get('facebook_client_token'),
                
                // --- Mobile App Branding ---
                'branding' => [
                    'brand_name'             => SystemSetting::get('brand_name', 'WADEXPRO'),
                    'brand_short_name'       => SystemSetting::get('brand_short_name', 'WADEX'),
                    'brand_tagline'          => SystemSetting::get('brand_tagline', 'Move. Deliver. Thrive.'),
                    'brand_logo_url'         => $logoUrl ?: null,
                    'brand_primary_color'    => SystemSetting::get('brand_primary_color', '#156400'),
                    'brand_accent_color'     => SystemSetting::get('brand_accent_color', '#FFCC00'),
                    'brand_dark_color'       => SystemSetting::get('brand_dark_color', '#0A0A0A'),
                    'brand_secondary_color'  => SystemSetting::get('brand_secondary_color', '#0D4000'),
                    'customer_app_name'      => SystemSetting::get('customer_app_display_name', 'WADEXPRO'),
                    'driver_app_name'        => SystemSetting::get('driver_app_display_name', 'WADEXPRO Driver'),
                    'support_email'          => SystemSetting::get('support_email', 'ops@wadexpro.com'),
                    'support_phone'          => SystemSetting::get('brand_support_phone', ''),
                ],

                'manifest' => [
                    'api_url'              => SystemSetting::get('flutter_api_url'),
                    'rtc_url'              => SystemSetting::get('flutter_rtc_url'),
                    'min_customer_version' => SystemSetting::get('min_customer_app_version', '1.0.0'),
                    'min_driver_version'   => SystemSetting::get('min_driver_app_version', '1.0.0'),
                    'customer_play_store'  => SystemSetting::get('play_store_customer_link'),
                    'customer_app_store'   => SystemSetting::get('app_store_customer_link'),
                    'driver_play_store'    => SystemSetting::get('play_store_driver_link'),
                    'driver_app_store'     => SystemSetting::get('app_store_driver_link'),
                ]
            ]
        ]);
    }
}
