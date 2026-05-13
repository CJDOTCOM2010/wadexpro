<?php

namespace App\Modules\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Modules\Admin\Models\Role;
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
        $query = User::whereIn('user_type', ['admin', 'support', 'staff', 'manager', 'employee'])
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
            'total'    => User::whereIn('user_type', ['admin', 'support', 'staff', 'manager', 'employee'])->count(),
            'admins'   => User::where('user_type', 'admin')->count(),
            'support'  => User::where('user_type', 'support')->count(),
        ];

        // Pull ALL roles from the database for the Add Staff modal
        $roles = Role::orderBy('name')->get();

        return view('admin.hr_management', compact('staff', 'stats', 'roles'));
    }

    /**
     * Invite / create a new staff member.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'       => 'required|string|max:255',
            'email'      => 'required|email|unique:users,email',
            'role'       => 'required|string|exists:roles,name',
            'department' => 'nullable|string|max:80',
        ]);

        $tempPassword = Str::random(12);

        $user = User::create([
            'name'       => $data['name'],
            'email'      => $data['email'],
            'user_type'  => 'employee',
            'department' => $data['department'] ?? null,
            'password'   => Hash::make($tempPassword),
            'is_active'  => true,
        ]);

        // Assign the selected role from the roles table
        $role = Role::where('name', $data['role'])->first();
        if ($role) {
            $user->roles()->syncWithoutDetaching([$role->id]);
        }

        // In production: dispatch an invitation email here
        // Mail::to($user->email)->send(new StaffInvitationMail($user, $tempPassword));

        return back()->with('success', "Staff account for {$user->name} created with role '{$role->label}'. Temporary password: {$tempPassword}");
    }

    /**
     * Update a staff member's role.
     */
    public function updateRole(Request $request, $id)
    {
        $request->validate(['role' => 'required|string|exists:roles,name']);

        $user = User::findOrFail($id);

        // Sync the new role via the pivot table
        $role = Role::where('name', $request->role)->first();
        if ($role) {
            $user->roles()->sync([$role->id]);
        }

        return back()->with('success', "Role updated to '{$role->label}' for {$user->name}.");
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

        $user->update(['is_active' => false]);

        return back()->with('success', "{$user->name}'s account has been deactivated.");
    }

    /**
     * Reactivate a staff account.
     */
    public function activate($id)
    {
        $user = User::findOrFail($id);
        $user->update(['is_active' => true]);

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
