<?php

namespace App\Console\Commands;

use App\Modules\Admin\Models\Permission;
use Illuminate\Console\Command;

class SeedDashboardPermissions extends Command
{
    protected $signature = 'admin:seed-dashboard-permissions';

    protected $description = 'Seed dashboard widget permissions for role-based access control';

    public function handle(): int
    {
        $permissions = [
            ['name' => 'dashboard.revenue.view', 'module' => 'Dashboard', 'label' => 'View Revenue Card'],
            ['name' => 'dashboard.revenue_year.view', 'module' => 'Dashboard', 'label' => 'View Year Revenue'],
            ['name' => 'dashboard.revenue_trend.view', 'module' => 'Dashboard', 'label' => 'View Monthly Revenue Chart'],
            ['name' => 'dashboard.drivers.view', 'module' => 'Dashboard', 'label' => 'View Driver Stats Card'],
            ['name' => 'dashboard.drivers_top.view', 'module' => 'Dashboard', 'label' => 'View Top Drivers Leaderboard'],
            ['name' => 'dashboard.rides.view', 'module' => 'Dashboard', 'label' => 'View Ride Stats Card'],
            ['name' => 'dashboard.rides_recent.view', 'module' => 'Dashboard', 'label' => 'View Recent Rides Feed'],
            ['name' => 'dashboard.customers.view', 'module' => 'Dashboard', 'label' => 'View Customer Stats Card'],
            ['name' => 'dashboard.weekly_chart.view', 'module' => 'Dashboard', 'label' => 'View Weekly Performance Chart'],
            ['name' => 'dashboard.regional.view', 'module' => 'Dashboard', 'label' => 'View Regional Distribution'],
            ['name' => 'dashboard.activity.view', 'module' => 'Dashboard', 'label' => 'View System Activity Log'],
            ['name' => 'dashboard.map.view', 'module' => 'Dashboard', 'label' => 'View Tactical Density Map'],
            ['name' => 'dashboard.health.view', 'module' => 'Dashboard', 'label' => 'View System Health Bar'],
            ['name' => 'dashboard.alerts.view', 'module' => 'Dashboard', 'label' => 'View System Alerts'],
            ['name' => 'dashboard.pending.view', 'module' => 'Dashboard', 'label' => 'View Pending Actions'],
            ['name' => 'dashboard.vehicle_types.view', 'module' => 'Dashboard', 'label' => 'View Vehicle Type Distribution'],
            ['name' => 'dashboard.staff.view', 'module' => 'Dashboard', 'label' => 'View Admin Staff Stats'],
            ['name' => 'dashboard.quick_actions.view', 'module' => 'Dashboard', 'label' => 'View Quick Actions'],
        ];

        $count = 0;
        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm['name']], $perm);
            $count++;
        }

        $this->info("Seeded {$count} dashboard permissions.");

        return Command::SUCCESS;
    }
}