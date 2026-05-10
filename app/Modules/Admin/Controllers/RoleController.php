<?php

namespace App\Modules\Admin\Controllers;

use App\Core\Support\PermissionCache;
use App\Core\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

/**
 * Full CRUD management of Roles and Permissions.
 * Only accessible by super_admin.
 */
class RoleController extends Controller
{
    use ApiResponse;

    // Temporarily disabled to resolve missing class binding crash
    // public function __construct(private readonly PermissionCache $permissionCache)
    // {
    // }

    // -----------------------------------------------------------------------
    // Roles
    // -----------------------------------------------------------------------

    public function indexRoles(): JsonResponse
    {
        $roles = Role::with('permissions')->orderBy('name')->get()->map(fn ($role) => [
            'id'          => $role->id,
            'name'        => $role->name,
            'permissions' => $role->permissions->pluck('name'),
            'users_count' => $role->users()->count(),
        ]);

        return $this->success($roles, 'Roles retrieved.');
    }

    public function storeRole(Request $request): JsonResponse
    {
        $request->validate([
            'name'        => ['required', 'string', 'unique:roles,name', 'max:100'],
            'permissions' => ['sometimes', 'array'],
            'permissions.*' => ['exists:permissions,name'],
        ]);

        $role = Role::create(['name' => $request->name, 'guard_name' => 'api']);

        if ($request->permissions) {
            $role->syncPermissions($request->permissions);
        }

        return $this->created([
            'id'          => $role->id,
            'name'        => $role->name,
            'permissions' => $role->permissions->pluck('name'),
        ], 'Role created.');
    }

    public function updateRole(Request $request, int $id): JsonResponse
    {
        $role = Role::findOrFail($id);

        $request->validate([
            'name'        => ['sometimes', 'string', 'unique:roles,name,' . $id, 'max:100'],
            'permissions' => ['sometimes', 'array'],
            'permissions.*' => ['exists:permissions,name'],
        ]);

        if ($request->has('name')) {
            $role->update(['name' => $request->name]);
        }

        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
            // Invalidate cached permissions for all users in this role
            $this->permissionCache->invalidateByRole($role->name);
        }

        return $this->success([
            'id'          => $role->id,
            'name'        => $role->name,
            'permissions' => $role->fresh('permissions')->permissions->pluck('name'),
        ], 'Role updated.');
    }

    public function destroyRole(int $id): JsonResponse
    {
        $role = Role::findOrFail($id);

        // Prevent deletion of system roles
        if (in_array($role->name, ['super_admin', 'admin', 'driver', 'customer', 'employee'], true)) {
            return $this->error('System roles cannot be deleted.', 403);
        }

        $this->permissionCache->invalidateByRole($role->name);
        $role->delete();

        return $this->success(null, 'Role deleted.');
    }

    // -----------------------------------------------------------------------
    // Permissions
    // -----------------------------------------------------------------------

    public function indexPermissions(): JsonResponse
    {
        $permissions = Permission::orderBy('name')->get()
            ->groupBy(fn ($p) => explode('.', $p->name)[0]) // group by module slug
            ->map(fn ($group) => $group->pluck('name'));

        return $this->success($permissions, 'Permissions retrieved.');
    }

    /**
     * PATCH /v1/admin/roles/{id}/permissions
     * Bulk-sync the permission list for a role.
     */
    public function syncPermissions(Request $request, int $id): JsonResponse
    {
        $role = Role::findOrFail($id);

        $request->validate([
            'permissions'   => ['required', 'array'],
            'permissions.*' => ['exists:permissions,name'],
        ]);

        $role->syncPermissions($request->permissions);
        $this->permissionCache->invalidateByRole($role->name);

        return $this->success(
            $role->fresh('permissions')->permissions->pluck('name'),
            'Permissions synced for role.'
        );
    }

    /**
     * POST /v1/admin/users/{userId}/roles
     * Assign roles to a specific user.
     */
    public function assignUserRoles(Request $request, string $userId): JsonResponse
    {
        $user = \App\Models\User::findOrFail($userId);

        $request->validate([
            'roles'   => ['required', 'array'],
            'roles.*' => ['exists:roles,name'],
        ]);

        $user->syncRoles($request->roles);
        $this->permissionCache->invalidate($userId);

        return $this->success(
            $user->fresh('roles')->roles->pluck('name'),
            'Roles assigned to user.'
        );
    }
}
