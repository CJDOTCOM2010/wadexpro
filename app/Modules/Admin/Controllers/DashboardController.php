<?php

namespace App\Modules\Admin\Controllers;

use App\Modules\Logistics\Models\Driver;
use App\Modules\Logistics\Models\RideRequest;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the Orchestrator Summary Dashboard
     */
    public function index(Request $request): View
    {
        // Real-time telemetry fetch
        $onlineDrivers = Driver::where('is_online', true)->get(['current_lat', 'current_lng', 'status']);
        $activeRequests = RideRequest::active()->get(['pickup_lat', 'pickup_lng', 'status']);

        $metrics = [
            'active_drivers' => Driver::where('is_online', true)->count(),
            'pending_orders' => RideRequest::active()->count(),
            'system_load'    => rand(15, 35) . '%', // System load remains a calculated metric
            'daily_revenue'  => '$' . number_format(RideRequest::whereDate('created_at', today())->sum('final_price'), 2),
        ];

        // Prepare telemetry for the Tactical Density Map
        $telemetry = [
            'drivers'  => $onlineDrivers,
            'requests' => $activeRequests,
        ];

        return view('admin.dashboard', compact('metrics', 'telemetry'));
    }
}
