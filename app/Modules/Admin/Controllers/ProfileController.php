<?php

namespace App\Modules\Admin\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /**
     * Show the profile & account settings page.
     */
    public function index()
    {
        $admin = auth('admin')->user();
        return view('admin.profile', compact('admin'));
    }

    /**
     * Update basic profile information (name, email, phone, bio, avatar).
     */
    public function updateProfile(Request $request)
    {
        $admin = auth('admin')->user();

        $validated = $request->validate([
            'name'       => 'required|string|max:100',
            'email'      => 'required|email|max:150|unique:admins,email,' . $admin->id,
            'phone'      => 'nullable|string|max:20',
            'first_name' => 'nullable|string|max:60',
            'last_name'  => 'nullable|string|max:60',
            'avatar'     => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            if ($admin->avatar_url && !str_starts_with($admin->avatar_url, 'http')) {
                Storage::disk('public')->delete(ltrim($admin->avatar_url, '/storage/'));
            }
            $path = $request->file('avatar')->store('admin/avatars', 'public');
            $validated['avatar_url'] = '/storage/' . $path;
        }

        unset($validated['avatar']);
        $admin->update($validated);

        return back()->with('success', 'Profile updated successfully.');
    }

    /**
     * Change account password with current password verification.
     */
    public function changePassword(Request $request)
    {
        $admin = auth('admin')->user();

        $request->validate([
            'current_password' => ['required', function ($attr, $value, $fail) use ($admin) {
                if (!Hash::check($value, $admin->password)) {
                    $fail('Current password is incorrect.');
                }
            }],
            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()->symbols()],
        ]);

        $admin->update(['password' => Hash::make($request->password)]);

        return back()->with('success', 'Password changed successfully. Please re-authenticate on your next session.');
    }

    /**
     * Update notification preferences.
     */
    public function updateNotifications(Request $request)
    {
        $admin = auth('admin')->user();

        $prefs = $request->only([
            'notify_new_tickets', 'notify_sos_alerts', 'notify_new_users',
            'notify_failed_payments', 'notify_driver_flagged', 'notify_low_drivers',
        ]);

        // Store as JSON in a meta column or as individual settings
        $admin->update([
            'notification_preferences' => json_encode($prefs),
        ]);

        return back()->with('success', 'Notification preferences saved.');
    }

    /**
     * Revoke all active sessions/tokens for this admin (force re-login everywhere).
     */
    public function revokeAllSessions(Request $request)
    {
        $admin = auth('admin')->user();

        // Revoke all Sanctum tokens
        if (method_exists($admin, 'tokens')) {
            $admin->tokens()->delete();
        }

        return back()->with('success', 'All sessions have been revoked. You will need to sign in again on other devices.');
    }
}
