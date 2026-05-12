<?php

namespace App\Modules\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class HRManagementController extends Controller
{
    /**
     * Staff registry listing.
     */
    public function index(Request $request)
    {
        $query = User::whereIn('user_type', ['admin', 'support', 'staff', 'manager'])
            ->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('user_type', $request->role);
        }

        $staff = $query->paginate(20)->withQueryString();

        $stats = [
            'total'    => User::whereIn('user_type', ['admin', 'support', 'staff', 'manager'])->count(),
            'admins'   => User::where('user_type', 'admin')->count(),
            'support'  => User::where('user_type', 'support')->count(),
        ];

        return view('admin.hr_management', compact('staff', 'stats'));
    }

    /**
     * Invite / create a new staff member.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'       => 'required|string|max:255',
            'email'      => 'required|email|unique:users,email',
            'role'       => 'required|in:admin,support,staff,manager',
            'department' => 'nullable|string|max:80',
        ]);

        $tempPassword = Str::random(12);

        $user = User::create([
            'name'       => $data['name'],
            'email'      => $data['email'],
            'user_type'  => $data['role'], // Map 'role' from form to user_type
            'department' => $data['department'] ?? null,
            'password'   => Hash::make($tempPassword),
            'status'     => 'active',
        ]);

        // In production: dispatch an invitation email here
        // Mail::to($user->email)->send(new StaffInvitationMail($user, $tempPassword));

        return back()->with('success', "Staff account for {$user->name} created. Temporary password: {$tempPassword}");
    }

    /**
     * Update a staff member's role.
     */
    public function updateRole(Request $request, $id)
    {
        $request->validate(['role' => 'required|in:admin,support,staff,manager']);

        $user = User::findOrFail($id);
        $user->update(['user_type' => $request->role]);

        return back()->with('success', "Role updated to '{$request->role}' for {$user->name}.");
    }

    /**
     * Deactivate a staff account.
     */
    public function deactivate($id)
    {
        $user = User::findOrFail($id);

        // Prevent deactivating the primary super admin
        if ($user->email === config('orchestrator.super_admin_email')) {
            return back()->with('error', 'Cannot deactivate the primary Super Admin account.');
        }

        $user->update(['status' => 'inactive']);

        return back()->with('success', "{$user->name}'s account has been deactivated.");
    }

    /**
     * Reactivate a staff account.
     */
    public function activate($id)
    {
        $user = User::findOrFail($id);
        $user->update(['status' => 'active']);

        return back()->with('success', "{$user->name}'s account has been reactivated.");
    }

    /**
     * Force-reset a staff member's password.
     */
    public function resetPassword($id)
    {
        $user = User::findOrFail($id);
        $tempPassword = Str::random(12);
        $user->update(['password' => Hash::make($tempPassword)]);

        return back()->with('success', "Password reset. New temporary password: {$tempPassword}");
    }
}
