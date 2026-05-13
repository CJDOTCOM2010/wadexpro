<?php

namespace Database\Seeders;

use App\Modules\Admin\Models\Role;
use App\Modules\Admin\Models\Permission;
use Illuminate\Database\Seeder;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // ── Default Permissions ──────────────────────────────────────────
        $permissions = [
            'dashboard'   => ['dashboard.view'],
            'users'       => ['users.view', 'users.create', 'users.edit', 'users.delete', 'users.assign_role'],
            'drivers'     => ['drivers.view', 'drivers.approve', 'drivers.reject', 'drivers.suspend', 'drivers.documents'],
            'vehicles'    => ['vehicles.view', 'vehicles.create', 'vehicles.edit', 'vehicles.delete'],
            'financials'  => ['financials.view', 'financials.approve_payout', 'financials.export'],
            'support'     => ['support.view', 'support.reply', 'support.assign', 'support.resolve'],
            'marketing'   => ['marketing.promos', 'marketing.banners', 'marketing.create', 'marketing.delete'],
            'cms'         => ['cms.blog', 'cms.faq', 'cms.menus', 'cms.create', 'cms.edit', 'cms.delete'],
            'hr'          => ['hr.view', 'hr.create', 'hr.edit', 'hr.deactivate', 'hr.reset_password'],
            'analytics'   => ['analytics.view', 'analytics.export'],
            'settings'    => ['settings.view', 'settings.edit', 'settings.branding', 'settings.notifications'],
            'roles'       => ['roles.view', 'roles.create', 'roles.edit', 'roles.delete', 'roles.assign'],
            'templates'   => ['templates.view', 'templates.create', 'templates.edit', 'templates.delete'],
            'infrastructure' => ['infrastructure.view', 'infrastructure.commands', 'infrastructure.modules'],
        ];

        $labels = [
            'dashboard.view' => 'View Dashboard',
            'users.view' => 'View Users', 'users.create' => 'Create Users', 'users.edit' => 'Edit Users', 'users.delete' => 'Delete Users', 'users.assign_role' => 'Assign Roles to Users',
            'drivers.view' => 'View Drivers', 'drivers.approve' => 'Approve Drivers', 'drivers.reject' => 'Reject Drivers', 'drivers.suspend' => 'Suspend Drivers', 'drivers.documents' => 'View Driver Documents',
            'vehicles.view' => 'View Vehicles', 'vehicles.create' => 'Create Vehicle Types', 'vehicles.edit' => 'Edit Vehicle Types', 'vehicles.delete' => 'Delete Vehicle Types',
            'financials.view' => 'View Financials', 'financials.approve_payout' => 'Approve Payouts', 'financials.export' => 'Export Financial Data',
            'support.view' => 'View Support Tickets', 'support.reply' => 'Reply to Tickets', 'support.assign' => 'Assign Tickets', 'support.resolve' => 'Resolve Tickets',
            'marketing.promos' => 'View Promos', 'marketing.banners' => 'View Banners', 'marketing.create' => 'Create Campaigns', 'marketing.delete' => 'Delete Campaigns',
            'cms.blog' => 'Manage Blog', 'cms.faq' => 'Manage FAQ', 'cms.menus' => 'Manage Menus', 'cms.create' => 'Create Content', 'cms.edit' => 'Edit Content', 'cms.delete' => 'Delete Content',
            'hr.view' => 'View Staff', 'hr.create' => 'Add Staff', 'hr.edit' => 'Edit Staff', 'hr.deactivate' => 'Deactivate Staff', 'hr.reset_password' => 'Reset Staff Passwords',
            'analytics.view' => 'View Analytics', 'analytics.export' => 'Export Reports',
            'settings.view' => 'View Settings', 'settings.edit' => 'Edit Settings', 'settings.branding' => 'Manage Branding', 'settings.notifications' => 'Manage Notifications',
            'roles.view' => 'View Roles', 'roles.create' => 'Create Roles', 'roles.edit' => 'Edit Roles', 'roles.delete' => 'Delete Roles', 'roles.assign' => 'Assign Roles',
            'templates.view' => 'View Templates', 'templates.create' => 'Create Templates', 'templates.edit' => 'Edit Templates', 'templates.delete' => 'Delete Templates',
            'infrastructure.view' => 'View Infrastructure', 'infrastructure.commands' => 'Run Cache Commands', 'infrastructure.modules' => 'Module Hardening',
        ];

        foreach ($permissions as $module => $perms) {
            foreach ($perms as $name) {
                Permission::firstOrCreate(
                    ['name' => $name],
                    ['module' => $module, 'label' => $labels[$name] ?? $name]
                );
            }
        }

        $allPermIds = Permission::pluck('id')->toArray();

        // ── Default Roles ────────────────────────────────────────────────
        $superAdmin = Role::firstOrCreate(
            ['name' => 'super_admin'],
            ['label' => 'Super Admin', 'description' => 'Full unrestricted access to the entire platform.', 'is_system' => true]
        );
        $superAdmin->permissions()->sync($allPermIds);

        $admin = Role::firstOrCreate(
            ['name' => 'admin'],
            ['label' => 'Admin', 'description' => 'General administrative access with some restrictions.', 'is_system' => true]
        );
        $adminPerms = Permission::whereIn('module', ['dashboard', 'users', 'drivers', 'vehicles', 'financials', 'support', 'analytics'])->pluck('id')->toArray();
        $admin->permissions()->sync($adminPerms);

        $manager = Role::firstOrCreate(
            ['name' => 'manager'],
            ['label' => 'Manager', 'description' => 'Department-level management access.', 'is_system' => false]
        );
        $managerPerms = Permission::whereIn('module', ['dashboard', 'drivers', 'vehicles', 'support', 'analytics'])->pluck('id')->toArray();
        $manager->permissions()->sync($managerPerms);

        $support = Role::firstOrCreate(
            ['name' => 'support_agent'],
            ['label' => 'Support Agent', 'description' => 'Customer support ticket access only.', 'is_system' => false]
        );
        $supportPerms = Permission::whereIn('module', ['dashboard', 'support'])->pluck('id')->toArray();
        $support->permissions()->sync($supportPerms);

        $finance = Role::firstOrCreate(
            ['name' => 'finance_officer'],
            ['label' => 'Finance Officer', 'description' => 'Financial reports and payout approvals.', 'is_system' => false]
        );
        $financePerms = Permission::whereIn('module', ['dashboard', 'financials'])->pluck('id')->toArray();
        $finance->permissions()->sync($financePerms);

        $content = Role::firstOrCreate(
            ['name' => 'content_editor'],
            ['label' => 'Content Editor', 'description' => 'Blog, FAQ, and marketing content management.', 'is_system' => false]
        );
        $contentPerms = Permission::whereIn('module', ['dashboard', 'cms', 'marketing', 'templates'])->pluck('id')->toArray();
        $content->permissions()->sync($contentPerms);

        Role::firstOrCreate(
            ['name' => 'viewer'],
            ['label' => 'Viewer (Read-Only)', 'description' => 'Read-only access across modules.', 'is_system' => false]
        );
        $viewerPerms = Permission::where('name', 'like', '%.view')->pluck('id')->toArray();
        Role::where('name', 'viewer')->first()?->permissions()->sync($viewerPerms);
    }
}
