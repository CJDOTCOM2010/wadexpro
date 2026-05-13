<?php

namespace App\Core\Traits;

use App\Modules\Admin\Models\Role;

/**
 * Add to the User model to enable role/permission checks.
 */
trait HasRolesAndPermissions
{
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_role');
    }

    public function hasRole(string $roleName): bool
    {
        return $this->roles()->where('name', $roleName)->exists();
    }

    public function hasAnyRole(array $roleNames): bool
    {
        return $this->roles()->whereIn('name', $roleNames)->exists();
    }

    public function hasPermission(string $permissionName): bool
    {
        // Super admins bypass all checks
        if ($this->user_type === 'super_admin' || $this->hasRole('super_admin')) {
            return true;
        }

        return $this->roles()
            ->whereHas('permissions', fn ($q) => $q->where('name', $permissionName))
            ->exists();
    }

    public function hasAnyPermission(array $permissions): bool
    {
        if ($this->user_type === 'super_admin' || $this->hasRole('super_admin')) {
            return true;
        }

        return $this->roles()
            ->whereHas('permissions', fn ($q) => $q->whereIn('name', $permissions))
            ->exists();
    }

    public function assignRole(string $roleName): void
    {
        $role = Role::where('name', $roleName)->firstOrFail();
        $this->roles()->syncWithoutDetaching([$role->id]);
    }

    public function removeRole(string $roleName): void
    {
        $role = Role::where('name', $roleName)->first();
        if ($role) {
            $this->roles()->detach($role->id);
        }
    }

    public function getAllPermissions(): \Illuminate\Support\Collection
    {
        return $this->roles()->with('permissions')->get()
            ->pluck('permissions')->flatten()->pluck('name')->unique();
    }
}
