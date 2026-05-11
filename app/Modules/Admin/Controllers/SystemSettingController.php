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
     * Update specified system settings.
     */
    public function update(Request $request)
    {
        $settings = $request->input('settings', []);

        foreach ($settings as $key => $value) {
            // Handle booleans from checkboxes (they send '1' or '0' but we want 'true'/'false' strings for our cast)
            if ($key === 'google_auth_enabled' || $key === 'facebook_auth_enabled') {
                $value = $value ? 'true' : 'false';
            }

            SystemSetting::set($key, $value);
        }

        // Handle brand logo file upload
        if ($request->hasFile('brand_logo')) {
            $request->validate(['brand_logo' => 'image|mimes:png,jpg,jpeg,svg,webp|max:2048']);
            $path = $request->file('brand_logo')->store('branding', 'public');
            SystemSetting::set('brand_logo_url', '/storage/' . $path);
        }

        // Flush the public settings cache so mobile apps pick up changes immediately
        \Illuminate\Support\Facades\Cache::forget('system:public_settings');

        return back()->with('success', 'Configuration synchronized successfully.');
    }
}
