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
        return response()->json([
            'status' => 'success',
            'data' => [
                'google_auth_enabled' => SystemSetting::get('google_auth_enabled', false),
                'google_auth_web_client_id' => SystemSetting::get('google_auth_web_client_id'),
                'google_auth_ios_client_id' => SystemSetting::get('google_auth_ios_client_id'),
                
                'facebook_auth_enabled' => SystemSetting::get('facebook_auth_enabled', false),
                'facebook_app_id' => SystemSetting::get('facebook_app_id'),
                'facebook_client_token' => SystemSetting::get('facebook_client_token'),
                
                'manifest' => [
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
