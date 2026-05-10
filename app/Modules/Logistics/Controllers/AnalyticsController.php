<?php

use App\Modules\Logistics\Services\FleetIntelligenceService;
use App\Modules\Logistics\Models\AnalyticsSnapshot;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    use ApiResponse;

    public function __construct(private FleetIntelligenceService $intelService) {}

    /**
     * Get high-level pulse stats for the dashboard header.
     */
    public function getOverview()
    {
        $today = Carbon::today();
        
        // Use snapshots for fast loading if available, otherwise fallback to live calculation
        $revenue = AnalyticsSnapshot::where('metric_name', 'DAILY_REVENUE')
            ->whereDate('start_at', $today)
            ->value('metric_value') ?? Order::whereDate('created_at', $today)->sum('total_amount');

        $activeDrivers = Driver::where('is_online', true)->count();
        $inProgressRides = RideRequest::whereIn('status', ['accepted', 'in_progress'])->count();

        return $this->success([
            'metrics' => [
                'revenue' => [
                    'current' => (float) $revenue,
                    'label' => 'Gross MTD Revenue'
                ],
                'rides' => [
                    'active' => $inProgressRides,
                    'label' => 'In-Flight Operations'
                ],
                'fleet' => [
                    'online' => $activeDrivers,
                    'utilization' => $activeDrivers > 0 ? round(($inProgressRides / $activeDrivers) * 100, 1) : 0,
                    'label' => 'Supply/Demand Pulse'
                ]
            ]
        ]);
    }

    /**
     * Get real-time supply vs demand ratio for surge intelligence.
     */
    public function getSupplyDemandRatio()
    {
        $activeRiders = RideRequest::where('created_at', '>', now()->subMinutes(15))->count();
        $availableDrivers = Driver::where('is_online', true)->where('is_available', true)->count();

        return $this->success([
            'demand' => $activeRiders,
            'supply' => $availableDrivers,
            'ratio'  => $availableDrivers > 0 ? round($activeRiders / $availableDrivers, 2) : $activeRiders
        ]);
    }

    /**
     * Get top performers (Drivers & Organizations).
     */
    public function getLeaderboards()
    {
        $driverLeaderboard = $this->intelService->getLeaderboard(5);
        
        $orgLeaderboard = Organization::withCount(['rideRequests', 'orders'])
            ->orderByDesc('ride_requests_count')
            ->limit(5)
            ->get()
            ->map(fn($org) => [
                'name'  => $org->name,
                'trips' => $org->ride_requests_count + $org->orders_count
            ]);

        return $this->success([
            'drivers' => $driverLeaderboard,
            'organizations' => $orgLeaderboard
        ]);
    }

    /**
     * Get revenue and ride volume trend data for the last 30 days.
     */
    public function getTrends()
    {
        // Indexed query for snapshots
        $data = AnalyticsSnapshot::where('metric_name', 'DAILY_REVENUE')
            ->where('start_at', '>=', Carbon::now()->subDays(30))
            ->orderBy('start_at')
            ->get(['start_at as date', 'metric_value as revenue']);

        return $this->success($data);
    }

    /**
     * Get demand density data for the heatmap.
     */
    public function demandHeatmap()
    {
        $requests = RideRequest::where('created_at', '>=', Carbon::now()->subHours(12))
            ->get(['pickup_lat', 'pickup_lng']);

        $data = $requests->map(fn($req) => [(float)$req->pickup_lat, (float)$req->pickup_lng, 0.8]);

        return $this->success($data);
    }
}
