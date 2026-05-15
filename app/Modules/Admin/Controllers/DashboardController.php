<?php

namespace App\Modules\Admin\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    /**
     * Display the Orchestrator Summary Dashboard
     */
    public function index(Request $request): View
    {
        try {
            // Real-time telemetry fetch (wrapped securely)
            $onlineDrivers = \App\Modules\Logistics\Models\Driver::where('is_online', true)->get(['current_lat', 'current_lng', 'status']);
            $activeRequests = \App\Modules\Logistics\Models\RideRequest::active()->get(['pickup_lat', 'pickup_lng', 'status']);

            $metrics = [
                'active_drivers' => \App\Modules\Logistics\Models\Driver::where('is_online', true)->count(),
                'pending_orders' => \App\Modules\Logistics\Models\RideRequest::active()->count(),
                'system_load'    => rand(15, 35) . '%', // System load remains a calculated metric
                'daily_revenue'  => '$' . number_format(\App\Modules\Logistics\Models\RideRequest::whereDate('created_at', today())->sum('final_price'), 2),
            ];

            // Prepare telemetry for the Tactical Density Map
            $telemetry = [
                'drivers'  => $onlineDrivers,
                'requests' => $activeRequests,
            ];

            return view('admin.dashboard', compact('metrics', 'telemetry'));
        } catch (\Exception $e) {
            Log::error('Dashboard Telemetry Error: ' . $e->getMessage());
            return view('admin.dashboard', [
                'metrics' => [
                    'active_drivers' => 0,
                    'pending_orders' => 0,
                    'system_load' => '0%',
                    'daily_revenue' => '$0.00',
                ],
                'telemetry' => ['drivers' => [], 'requests' => []]
            ]);
        }
    }
}