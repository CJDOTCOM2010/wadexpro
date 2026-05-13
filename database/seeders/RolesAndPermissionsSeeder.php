<?php

namespace Database\Seeders;

use App\Modules\Admin\Models\Role;
use App\Modules\Admin\Models\Permission;
use Illuminate\Database\Seeder;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // ══════════════════════════════════════════════════════════════════
        // GRANULAR PERMISSIONS — Each module has standalone action nodes
        // Format: module.action
        // ══════════════════════════════════════════════════════════════════

        $matrix = [
            'Dashboard' => [
                'dashboard.view' => 'View Dashboard',
                'dashboard.analytics' => 'View Analytics Widgets',
            ],
            'User Management' => [
                'users.view' => 'View Users',
                'users.create' => 'Create Users',
                'users.edit' => 'Edit Users',
                'users.delete' => 'Delete Users',
                'users.suspend' => 'Suspend Users',
                'users.assign_role' => 'Assign Roles to Users',
                'users.export' => 'Export User Data',
            ],
            'Driver Management' => [
                'drivers.view' => 'View Drivers',
                'drivers.create' => 'Register Drivers',
                'drivers.edit' => 'Edit Driver Profiles',
                'drivers.delete' => 'Delete Drivers',
                'drivers.approve' => 'Approve Driver Applications',
                'drivers.reject' => 'Reject Driver Applications',
                'drivers.suspend' => 'Suspend Drivers',
                'drivers.documents' => 'View Driver Documents',
                'drivers.export' => 'Export Driver Data',
            ],
            'Vehicle Management' => [
                'vehicles.view' => 'View Vehicle Types',
                'vehicles.create' => 'Create Vehicle Types',
                'vehicles.edit' => 'Edit Vehicle Types',
                'vehicles.delete' => 'Delete Vehicle Types',
            ],
            'Ride Management' => [
                'rides.view' => 'View Rides',
                'rides.assign' => 'Assign Rides to Drivers',
                'rides.cancel' => 'Cancel Rides',
                'rides.refund' => 'Issue Ride Refunds',
                'rides.export' => 'Export Ride Reports',
            ],
            'Financial Management' => [
                'financials.view' => 'View Financial Dashboard',
                'financials.transactions' => 'View Transactions',
                'financials.approve_payout' => 'Approve Payouts',
                'financials.reject_payout' => 'Reject Payouts',
                'financials.wallets' => 'View Wallets',
                'financials.refunds' => 'Process Refunds',
                'financials.export' => 'Export Financial Data',
            ],
            'Customer Support' => [
                'support.view' => 'View Support Tickets',
                'support.create' => 'Create Tickets',
                'support.reply' => 'Reply to Tickets',
                'support.assign' => 'Assign Tickets',
                'support.resolve' => 'Resolve Tickets',
                'support.delete' => 'Delete Tickets',
                'support.export' => 'Export Ticket Data',
            ],
            'Marketing & Promos' => [
                'marketing.view' => 'View Campaigns',
                'marketing.create' => 'Create Promos / Coupons',
                'marketing.edit' => 'Edit Promos / Coupons',
                'marketing.delete' => 'Delete Promos / Coupons',
                'marketing.banners' => 'Manage Banners',
            ],
            'Content & CMS' => [
                'cms.blog_view' => 'View Blog Posts',
                'cms.blog_create' => 'Create Blog Posts',
                'cms.blog_edit' => 'Edit Blog Posts',
                'cms.blog_delete' => 'Delete Blog Posts',
                'cms.faq_view' => 'View FAQs',
                'cms.faq_create' => 'Create FAQs',
                'cms.faq_edit' => 'Edit FAQs',
                'cms.faq_delete' => 'Delete FAQs',
                'cms.menus' => 'Manage Menus',
            ],
            'HR & Staff' => [
                'hr.view' => 'View Staff Registry',
                'hr.create' => 'Add Staff Members',
                'hr.edit' => 'Edit Staff Profiles',
                'hr.delete' => 'Remove Staff Members',
                'hr.deactivate' => 'Deactivate Staff Accounts',
                'hr.reset_password' => 'Reset Staff Passwords',
                'hr.shifts' => 'Manage Shifts',
                'hr.salary' => 'Manage Salary Grades',
                'hr.leave' => 'Manage Leave Types',
                'hr.departments' => 'Manage Departments',
                'hr.export' => 'Export Staff Data',
            ],
            'Analytics & Reports' => [
                'analytics.view' => 'View Analytics Dashboard',
                'analytics.revenue' => 'View Revenue Reports',
                'analytics.performance' => 'View Performance Metrics',
                'analytics.export' => 'Export Reports',
            ],
            'Notification Templates' => [
                'templates.view' => 'View Templates',
                'templates.create' => 'Create Templates',
                'templates.edit' => 'Edit Templates',
                'templates.delete' => 'Delete Templates',
                'templates.toggle' => 'Toggle Template Status',
            ],
            'Communication Channels' => [
                'notifications.view' => 'View Channel Settings',
                'notifications.configure' => 'Configure API Keys',
                'notifications.test' => 'Send Test Messages',
                'notifications.events' => 'Manage Event Triggers',
            ],
            'Roles & Permissions' => [
                'roles.view' => 'View Roles',
                'roles.create' => 'Create Roles',
                'roles.edit' => 'Edit Roles',
                'roles.delete' => 'Delete Roles',
                'roles.assign' => 'Assign Roles to Staff',
                'permissions.view' => 'View Permissions',
                'permissions.create' => 'Create Permissions',
                'permissions.delete' => 'Delete Permissions',
            ],
            'Platform Settings' => [
                'settings.view' => 'View Settings',
                'settings.edit' => 'Edit Settings',
                'settings.branding' => 'Manage Branding',
                'settings.authentication' => 'Manage Auth Config',
                'settings.onboarding' => 'Manage Onboarding Slides',
            ],
            'Infrastructure' => [
                'infrastructure.view' => 'View Infrastructure',
                'infrastructure.cache' => 'Clear Cache',
                'infrastructure.logs' => 'View Logs',
                'infrastructure.modules' => 'Module Hardening',
                'infrastructure.deploy' => 'Deploy Changes',
            ],
        ];

        // Seed all permissions
        foreach ($matrix as $module => $perms) {
            foreach ($perms as $name => $label) {
                Permission::firstOrCreate(
                    ['name' => $name],
                    ['module' => $module, 'label' => $label, 'guard_name' => 'web']
                );
            }
        }

        $allPermIds = Permission::pluck('id')->toArray();

        // ══════════════════════════════════════════════════════════════════
        // DEFAULT ROLES
        // ══════════════════════════════════════════════════════════════════

        // 1. Super Admin — Full unrestricted access
        $superAdmin = Role::firstOrCreate(
            ['name' => 'super_admin'],
            ['label' => 'Super Administrator', 'description' => 'Full unrestricted access to every module and action across the platform.', 'is_system' => true, 'guard_name' => 'web']
        );
        $superAdmin->permissions()->sync($allPermIds);

        // 2. Admin — General administration
        $admin = Role::firstOrCreate(
            ['name' => 'admin'],
            ['label' => 'Administrator', 'description' => 'General administrative access with some restrictions on infrastructure and role management.', 'is_system' => true, 'guard_name' => 'web']
        );
        $adminPerms = Permission::whereNotIn('module', ['Infrastructure', 'Roles & Permissions'])->pluck('id')->toArray();
        $admin->permissions()->sync($adminPerms);

        // 3. Operations Manager
        $ops = Role::firstOrCreate(
            ['name' => 'operations_manager'],
            ['label' => 'Operations Manager', 'description' => 'Manages drivers, rides, vehicles and support operations.', 'is_system' => false, 'guard_name' => 'web']
        );
        $opsPerms = Permission::whereIn('module', ['Dashboard', 'Driver Management', 'Vehicle Management', 'Ride Management', 'Customer Support', 'Analytics & Reports'])->pluck('id')->toArray();
        $ops->permissions()->sync($opsPerms);

        // 4. Finance Officer
        $fin = Role::firstOrCreate(
            ['name' => 'finance_officer'],
            ['label' => 'Finance Officer', 'description' => 'Financial reports, transactions, and payout management.', 'is_system' => false, 'guard_name' => 'web']
        );
        $finPerms = Permission::whereIn('module', ['Dashboard', 'Financial Management', 'Analytics & Reports'])->pluck('id')->toArray();
        $fin->permissions()->sync($finPerms);

        // 5. Support Agent
        $support = Role::firstOrCreate(
            ['name' => 'support_agent'],
            ['label' => 'Support Agent', 'description' => 'Customer support ticket management only.', 'is_system' => false, 'guard_name' => 'web']
        );
        $supportPerms = Permission::whereIn('module', ['Dashboard', 'Customer Support'])->pluck('id')->toArray();
        $support->permissions()->sync($supportPerms);

        // 6. Content Editor
        $content = Role::firstOrCreate(
            ['name' => 'content_editor'],
            ['label' => 'Content Editor', 'description' => 'Blog, FAQ, marketing content, and notification templates.', 'is_system' => false, 'guard_name' => 'web']
        );
        $contentPerms = Permission::whereIn('module', ['Dashboard', 'Content & CMS', 'Marketing & Promos', 'Notification Templates'])->pluck('id')->toArray();
        $content->permissions()->sync($contentPerms);

        // 7. HR Manager
        $hr = Role::firstOrCreate(
            ['name' => 'hr_manager'],
            ['label' => 'HR Manager', 'description' => 'Staff registry, departments, shifts, and leave management.', 'is_system' => false, 'guard_name' => 'web']
        );
        $hrPerms = Permission::whereIn('module', ['Dashboard', 'HR & Staff', 'User Management'])->pluck('id')->toArray();
        $hr->permissions()->sync($hrPerms);

        // 8. Viewer (Read-Only)
        $viewer = Role::firstOrCreate(
            ['name' => 'viewer'],
            ['label' => 'Viewer (Read-Only)', 'description' => 'Read-only access across all visible modules.', 'is_system' => false, 'guard_name' => 'web']
        );
        $viewerPerms = Permission::where('name', 'like', '%.view')->pluck('id')->toArray();
        $viewer->permissions()->sync($viewerPerms);
    }
}
