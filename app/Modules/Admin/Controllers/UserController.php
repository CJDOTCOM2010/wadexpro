<?php

namespace App\Modules\Admin\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    public function index(Request $request)
    {
        // Stats
        $totalNodes = User::count();
        $activeSessions = User::where('updated_at', '>=', now()->subHours(24))->count();
        $revokedAccess = User::where('is_active', false)->count();

        $stats = [
            'total_nodes'     => $totalNodes,
            'active_sessions' => $activeSessions,
            'revoked_access'  => $revokedAccess,
        ];

        // Query
        $query = User::orderBy('created_at', 'desc');

        if ($request->filled('type')) {
            $query->where('user_type', $request->type);
        }

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%")
                  ->orWhere('phone', 'like', "%{$request->search}%");
            });
        }

        $users = $query->paginate(50);

        return view('admin.users', compact('stats', 'users'));
    }

    /**
     * Provision a new entity node.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'       => 'required|string|max:255',
            'email'      => 'required|email|unique:users,email',
            'phone'      => 'required|string|unique:users,phone',
            'password'   => 'required|string|min:8',
            'user_type'  => 'required|in:admin,customer,driver',
        ]);

        User::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'phone'     => $request->phone,
            'password'  => Hash::make($request->password),
            'user_type' => $request->user_type,
            'is_active' => true,
        ]);

        return back()->with('success', 'New entity provisioned successfully.');
    }

    /**
     * Update user details.
     */
    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'phone' => 'required|string|unique:users,phone,'.$user->id,
        ]);

        $user->update($request->only(['name', 'email', 'phone']));

        return back()->with('success', 'User profile updated.');
    }

    /**
     * Toggle active state.
     */
    public function toggleStatus(string $id)
    {
        $user = User::findOrFail($id);
        $user->update(['is_active' => !$user->is_active]);

        return back()->with('success', 'User access state toggled.');
    }

    /**
     * Decommission user node.
     */
    public function destroy(string $id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return back()->with('success', 'User node decommissioned.');
    }
}
