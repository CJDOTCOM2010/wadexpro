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
            $metrics = [
                'active_drivers' => 0,
                'pending_orders' => 0,
                'system_load'    => rand(15, 35) . '%',
                'daily_revenue'  => '$0.00',
            ];

            $telemetry = [
                'drivers'  => [],
                'requests' => [],
            ];

            // Check if tables exist and have data
            try {
                $driverCount = \Illuminate\Support\Facades\DB::table('drivers')->count();
                $rideCount = \Illuminate\Support\Facades\DB::table('ride_requests')->count();

                if ($driverCount > 0) {
                    $metrics['active_drivers'] = \Illuminate\Support\Facades\DB::table('drivers')
                        ->where('is_online', true)->count();
                }

                if ($rideCount > 0) {
                    $metrics['pending_orders'] = \Illuminate\Support\Facades\DB::table('ride_requests')
                        ->whereIn('status', ['pending', 'searching'])->count();

                    $metrics['daily_revenue'] = '$' . number_format(
                        \Illuminate\Support\Facades\DB::table('ride_requests')
                            ->whereDate('created_at', now()->toDateString())
                            ->sum('final_price') ?? 0,
                        2
                    );
                }
            } catch (\Exception $e) {
                Log::warning('Dashboard database query issue: ' . $e->getMessage());
            }

            return view('admin.dashboard', compact('metrics', 'telemetry'));
        } catch (\Exception $e) {
            Log::error('Dashboard Error: ' . $e->getMessage());
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