<?php

namespace App\Modules\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Admin\Models\SystemSetting;
use Illuminate\Http\Request;

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
        return view('admin.settings.localization');
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
            'google_auth_enabled', 'facebook_auth_enabled', 'apple_auth_enabled',
            'cash_on_delivery_enabled', 'wallet_payments_enabled',
            'momo_enabled', 'googlepay_enabled',
            'paystack_enabled', 'flutterwave_enabled', 'stripe_enabled',
            'google_maps_enabled', 'mapbox_enabled',
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

        foreach ($settings as $key => $value) {
            // Skip masked placeholder values for sensitive keys
            if (in_array($key, $encryptedKeys)) {
                if ($value === '********' || empty($value)) {
                    continue;
                }
                $value = \Illuminate\Support\Facades\Crypt::encryptString($value);
                SystemSetting::updateOrCreate(['key' => $key], ['value' => $value, 'is_encrypted' => true, 'group' => 'payments']);
                continue;
            }

            // Handle boolean settings
            if (in_array($key, $booleanKeys)) {
                $value = ($value === 'true' || $value === true || $value === '1') ? 'true' : 'false';
            }

            SystemSetting::set($key, $value);
        }

        // Handle brand logo file upload (Mobile Apps)
        if ($request->hasFile('brand_logo')) {
            $request->validate(['brand_logo' => 'image|mimes:png,jpg,jpeg,svg,webp|max:2048']);
            $path = $request->file('brand_logo')->store('branding', 'public');
            SystemSetting::set('brand_logo_url', '/storage/' . $path);
        }

        // Handle dashboard logo file upload
        if ($request->hasFile('dashboard_logo')) {
            $request->validate(['dashboard_logo' => 'image|mimes:png,jpg,jpeg,svg,webp|max:2048']);
            $path = $request->file('dashboard_logo')->store('branding/dashboard', 'public');
            SystemSetting::set('dashboard_logo_url', '/storage/' . $path);
        }

        // Handle dashboard favicon file upload
        if ($request->hasFile('dashboard_favicon')) {
            $request->validate(['dashboard_favicon' => 'image|mimes:png,jpg,jpeg,svg,webp,ico|max:1024']);
            $path = $request->file('dashboard_favicon')->store('branding/dashboard', 'public');
            SystemSetting::set('dashboard_favicon_url', '/storage/' . $path);
        }

        // Flush the public settings cache so mobile apps pick up changes immediately
        \Illuminate\Support\Facades\Cache::forget('system:public_settings');

        return back()->with('success', 'Configuration synchronized successfully.');
    }
}
