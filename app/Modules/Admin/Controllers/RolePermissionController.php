<?php

namespace App\Modules\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Admin\Models\Role;
use App\Modules\Admin\Models\Permission;
use App\Models\User;
use Illuminate\Http\Request;

class RolePermissionController extends Controller
{
    public function index()
    {
        $roles = Role::withCount('users')->with('permissions')->orderBy('name')->get();
        $permissions = Permission::orderBy('module')->orderBy('name')->get()->groupBy('module');
        return view('admin.roles.index', compact('roles', 'permissions'));
    }

    public function storeRole(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:roles,name',
            'label' => 'nullable|string|max:200',
            'description' => 'nullable|string|max:500',
            'permissions' => 'array',
        ]);

        $role = Role::create([
            'name' => strtolower(str_replace(' ', '_', $request->name)),
            'label' => $request->label ?? $request->name,
            'description' => $request->description,
            'is_system' => false,
        ]);

        if ($request->permissions) {
            $role->permissions()->sync($request->permissions);
        }

        return back()->with('success', "Role '{$role->label}' created successfully.");
    }

    public function updateRole(Request $request, string $id)
    {
        $role = Role::findOrFail($id);

        $request->validate([
            'label' => 'nullable|string|max:200',
            'description' => 'nullable|string|max:500',
            'permissions' => 'array',
        ]);

        $role->update([
            'label' => $request->label ?? $role->label,
            'description' => $request->description,
        ]);

        $role->permissions()->sync($request->permissions ?? []);

        return back()->with('success', "Role '{$role->label}' updated.");
    }

    public function destroyRole(string $id)
    {
        $role = Role::findOrFail($id);
        if ($role->is_system) {
            return back()->with('error', 'System roles cannot be deleted.');
        }
        $role->delete();
        return back()->with('success', "Role '{$role->label}' removed.");
    }

    public function storePermission(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:permissions,name',
            'module' => 'required|string|max:50',
            'label' => 'nullable|string|max:200',
        ]);

        Permission::create([
            'name' => strtolower($request->name),
            'module' => strtolower($request->module),
            'label' => $request->label ?? $request->name,
        ]);

        return back()->with('success', "Permission '{$request->name}' created.");
    }

    public function destroyPermission(string $id)
    {
        $perm = Permission::findOrFail($id);
        $perm->delete();
        return back()->with('success', "Permission removed.");
    }

    public function assignRole(Request $request, string $userId)
    {
        $user = User::findOrFail($userId);
        $request->validate(['role_id' => 'required|exists:roles,id']);
        $user->roles()->syncWithoutDetaching([$request->role_id]);
        return back()->with('success', "Role assigned to {$user->name}.");
    }

    public function revokeRole(string $userId, string $roleId)
    {
        $user = User::findOrFail($userId);
        $user->roles()->detach($roleId);
        return back()->with('success', "Role revoked from {$user->name}.");
    }
}
