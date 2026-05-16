<?php

namespace App\Modules\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Admin\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;

class SystemSettingController extends Controller
{
    /**
     * Display the settings hub.
     */
    public function index()
    {
        return view('admin.settings');
    }

    /**
     * Display branding settings page.
     */
    public function branding()
    {
        return view('admin.settings.branding');
    }

    /**
     * Display dashboard branding settings page.
     */
    public function dashboardBranding()
    {
        return view('admin.settings.dashboard_branding');
    }

    /**
     * Display identity and authentication settings page.
     */
    public function auth()
    {
        return view('admin.settings.auth');
    }

    /**
     * Display mobile manifest management page.
     */
    public function manifest()
    {
        return view('admin.settings.manifest');
    }

    /**
     * Display localization settings page.
     */
    public function localization()
    {
        $settings = SystemSetting::where('group', 'localization')->get()->keyBy('key');

        return view('admin.settings.localization', compact('settings'));
    }

    /**
     * Display security settings page.
     */
    public function security()
    {
        $settings = SystemSetting::where('group', 'security')->get()->keyBy('key');

        return view('admin.settings.security', compact('settings'));
    }

    /**
     * Display API rate limiting settings page.
     */
    public function apiRateLimiting()
    {
        $settings = SystemSetting::where('group', 'api_rate_limiting')->get()->keyBy('key');

        return view('admin.settings.api_rate_limiting', compact('settings'));
    }

    /**
     * Display payment gateway management page.
     */
    public function payments()
    {
        $settings = SystemSetting::where('group', 'payments')->get()->keyBy('key');

        return view('admin.settings.payments', compact('settings'));
    }

    /**
     * Display prefix configuration page.
     */
    public function prefixes()
    {
        $settings = SystemSetting::where('group', 'prefixes')->get()->keyBy('key');

        return view('admin.settings.prefixes', compact('settings'));
    }

    /**
     * Display geolocation & maps configuration page.
     */
    public function geolocation()
    {
        $settings = SystemSetting::where('group', 'geolocation')->get()->keyBy('key');

        return view('admin.settings.geolocation', compact('settings'));
    }

    /**
     * Display social authentication configuration page.
     */
    public function socialAuth()
    {
        $settings = SystemSetting::where('group', 'social_auth')->get()->keyBy('key');

        return view('admin.settings.social_auth', compact('settings'));
    }

    /**
     * Update specified system settings.
     */
    public function update(Request $request)
    {
        $settings = $request->input('settings', []);

        // Boolean keys that come from checkboxes
        $booleanKeys = [
            'google_auth_enabled', 'facebook_auth_enabled', 'apple_auth_enabled',
            'cash_on_delivery_enabled', 'wallet_payments_enabled',
            'momo_enabled', 'googlepay_enabled',
            'paystack_enabled', 'flutterwave_enabled', 'stripe_enabled',
            'google_maps_enabled', 'mapbox_enabled', 'geofencing_enabled',
            // Security settings
            'password_require_special', 'password_require_numbers', 'two_factor_auth',
            'ip_whitelist_enabled',
            // API settings
            'api_debug_mode',
        ];

        // Encrypted keys with masking pattern
        $encryptedKeys = [
            'paystack_secret_key', 'flutterwave_secret_key', 'stripe_secret_key',
            'flutterwave_encryption_key', 'payment_webhook_secret',
            'google_maps_key', 'mapbox_key',
            'google_client_id', 'google_client_secret',
            'facebook_client_id', 'facebook_client_secret',
            'apple_client_id', 'apple_client_secret',
        ];

        $groupMap = [
            // localization
            'default_language' => 'localization', 'supported_languages' => 'localization',
            'default_currency' => 'localization', 'supported_currencies' => 'localization',
            'default_timezone' => 'localization', 'date_format' => 'localization',
            'time_format' => 'localization', 'measurement_unit' => 'localization',
            'first_day_of_week' => 'localization', 'country' => 'localization',
            'decimal_separator' => 'localization', 'thousands_separator' => 'localization',
            'currency_position' => 'localization', 'localization_configured' => 'localization',
            // geolocation
            'google_maps_enabled' => 'geolocation', 'google_maps_key' => 'geolocation',
            'google_maps_directions' => 'geolocation', 'google_maps_places' => 'geolocation',
            'google_maps_geocoding' => 'geolocation', 'google_maps_static' => 'geolocation',
            'google_maps_streetview' => 'geolocation',
            'mapbox_enabled' => 'geolocation', 'mapbox_key' => 'geolocation',
            'default_latitude' => 'geolocation', 'default_longitude' => 'geolocation',
            'default_zoom_level' => 'geolocation', 'default_map_style' => 'geolocation',
            'geocoding_country' => 'geolocation', 'geocoding_language' => 'geolocation',
            'gps_update_interval' => 'geolocation', 'location_history_days' => 'geolocation',
            'geofencing_enabled' => 'geolocation', 'default_search_radius' => 'geolocation',
            'distance_unit' => 'geolocation', 'default_travel_mode' => 'geolocation',
            // security
            'password_min_length' => 'security', 'password_require_special' => 'security',
            'password_require_numbers' => 'security', 'password_expiry_days' => 'security',
            'session_timeout_minutes' => 'security', 'max_login_attempts' => 'security',
            'account_lockout_minutes' => 'security', 'two_factor_auth' => 'security',
            'ip_whitelist' => 'security', 'ip_whitelist_enabled' => 'security',
            // api rate limiting
            'api_rate_limit' => 'api_rate_limiting', 'api_rate_limit_burst' => 'api_rate_limiting',
            'api_debug_mode' => 'api_rate_limiting', 'max_concurrent_requests' => 'api_rate_limiting',
            'webhook_retry_attempts' => 'api_rate_limiting', 'webhook_retry_delay' => 'api_rate_limiting',
        ];

        foreach ($settings as $key => $value) {
            // Handle array values (multi-select checkboxes)
            if (is_array($value)) {
                $value = json_encode($value);
                $group = $groupMap[$key] ?? null;
                $data = ['value' => $value, 'type' => 'json'];
                if ($group) {
                    $data['group'] = $group;
                }
                SystemSetting::updateOrCreate(['key' => $key], $data);

                continue;
            }

            // Skip masked placeholder values for sensitive keys
            if (in_array($key, $encryptedKeys)) {
                if ($value === '********' || empty($value)) {
                    continue;
                }
                $value = Crypt::encryptString($value);
                $group = $groupMap[$key] ?? 'payments';
                SystemSetting::updateOrCreate(['key' => $key], ['value' => $value, 'is_encrypted' => true, 'group' => $group]);

                continue;
            }

            // Handle boolean settings
            if (in_array($key, $booleanKeys)) {
                $value = ($value === 'true' || $value === true || $value === '1') ? 'true' : 'false';
            }

            $group = $groupMap[$key] ?? null;
            $data = ['value' => $value];
            if ($group) {
                $data['group'] = $group;
            }
            SystemSetting::updateOrCreate(['key' => $key], $data);
        }

        // Handle brand logo file upload (Mobile Apps)
        if ($request->hasFile('brand_logo')) {
            $request->validate(['brand_logo' => 'image|mimes:png,jpg,jpeg,svg,webp|max:2048']);
            $path = $request->file('brand_logo')->store('branding', 'public');
            SystemSetting::set('brand_logo_url', '/storage/'.$path);
        }

        // Handle dashboard logo file upload
        if ($request->hasFile('dashboard_logo')) {
            $request->validate(['dashboard_logo' => 'image|mimes:png,jpg,jpeg,svg,webp|max:2048']);
            $path = $request->file('dashboard_logo')->store('branding/dashboard', 'public');
            SystemSetting::set('dashboard_logo_url', '/storage/'.$path);
        }

        // Handle dashboard favicon file upload
        if ($request->hasFile('dashboard_favicon')) {
            $request->validate(['dashboard_favicon' => 'image|mimes:png,jpg,jpeg,svg,webp,ico|max:1024']);
            $path = $request->file('dashboard_favicon')->store('branding/dashboard', 'public');
            SystemSetting::set('dashboard_favicon_url', '/storage/'.$path);
        }

        // Flush the public settings cache so mobile apps pick up changes immediately
        Cache::forget('system:public_settings');

        return back()->with('success', 'Configuration synchronized successfully.');
    }
}
