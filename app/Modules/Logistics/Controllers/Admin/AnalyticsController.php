<?php

namespace App\Modules\Logistics\Controllers\Admin;

use App\Core\Traits\ApiResponse;
use App\Modules\Logistics\Models\RideRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    use ApiResponse;

    /**
     * Get high-level overview of the logistics operation.
     */
    public function overview()
    {
        $today = Carbon::today();
        $startOfMonth = Carbon::now()->startOfMonth();
        $thirtyDaysAgo = Carbon::now()->subDays(30);

        // Standardized Revenue Calculation
        $grossRevenue = RideRequest::where('status', 'completed')
            ->where('created_at', '>=', $startOfMonth)
            ->sum('final_price');

        // Real-time Online Count from Redis (graceful fallback if Redis is unavailable)
        try {
            $onlineDrivers = \Illuminate\Support\Facades\Redis::zCard('drivers:locations');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning('Redis unavailable for driver location count: ' . $e->getMessage());
            $onlineDrivers = 0;
        }

        // Customer Stats
        $totalCustomers = DB::table('users')->where('user_type', 'customer')->count();
        $activeCustomers = DB::table('users')
            ->where('user_type', 'customer')
            ->whereExists(function ($query) use ($thirtyDaysAgo) {
                $query->select(DB::raw(1))
                    ->from('ride_requests')
                    ->whereColumn('ride_requests.user_id', 'users.id')
                    ->where('created_at', '>=', $thirtyDaysAgo);
            })
            ->count();

        // Driver Stats
        $totalDrivers = DB::table('drivers')->count();
        $activeDrivers = DB::table('drivers')->where('status', 'active')->count();
        $inactiveDrivers = DB::table('drivers')->where('status', '!=', 'active')->count();

        $stats = [
            'total_rides' => RideRequest::count(),
            'today_rides' => RideRequest::whereDate('created_at', $today)->count(),
            'monthly_gross_revenue' => (float) $grossRevenue,
            'monthly_net_commission' => (float) ($grossRevenue * 0.20), // 20% Standard Platform Fee
            
            'drivers' => [
                'total' => $totalDrivers,
                'active' => $activeDrivers,
                'inactive' => $inactiveDrivers,
                'online' => (int) $onlineDrivers,
            ],
            'customers' => [
                'total' => $totalCustomers,
                'active' => $activeCustomers,
                'inactive' => $totalCustomers - $activeCustomers,
            ],
            
            'active_drivers' => $activeDrivers, // Keep for backward compatibility if needed
            'active_search_count' => RideRequest::where('status', 'searching')->count(),
            'pending_sos' => DB::table('sos_events')->where('status', 'triggered')->count(),
        ];

        return $this->success($stats, 'Overview analytics retrieved from live telemetry.');
    }

    /**
     * Get detailed revenue data for charts.
     */
    public function revenue(Request $request)
    {
        $days = $request->get('days', 30);
        $startDate = Carbon::now()->subDays($days);

        $revenueData = RideRequest::where('status', 'completed')
            ->where('created_at', '>=', $startDate)
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(final_price) as total'),
                DB::raw('COUNT(*) as ride_count')
            )
            ->groupBy('date')
            ->orderBy('date', 'ASC')
            ->get();

        return $this->success($revenueData, 'Revenue analytics retrieved.');
    }

    /**
     * Get ride volume distribution by vehicle type and status.
     */
    public function rides()
    {
        $byStatus = RideRequest::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get();

        $byType = RideRequest::select('vehicle_type', DB::raw('count(*) as count'))
            ->groupBy('vehicle_type')
            ->get();

        return $this->success([
            'by_status' => $byStatus,
            'by_type' => $byType,
        ], 'Ride volume analytics retrieved.');
    }

    /**
     * Get driver performance metrics.
     */
    public function drivers()
    {
        $performance = DB::table('drivers')
            ->join('users', 'drivers.user_id', '=', 'users.id')
            ->leftJoin('ride_requests', 'drivers.id', '=', 'ride_requests.driver_id')
            ->select(
                'users.name as driver_name',
                'drivers.vehicle_type',
                DB::raw('COUNT(ride_requests.id) as total_trips'),
                DB::raw('AVG(ride_requests.final_price) as avg_fare')
            )
            ->where('ride_requests.status', 'completed')
            ->groupBy('drivers.id', 'users.name', 'drivers.vehicle_type')
            ->orderByDesc('total_trips')
            ->limit(10)
            ->get();

        return $this->success($performance, 'Top driver performance retrieved.');
    }

    /**
     * Get real-time demand intensity (Search Intent) for heatmap rendering.
     */
    public function demandHeatmap()
    {
        // Pull all search intents from the last 5 minutes (via Socket -> Redis)
        // Note: DEMAND_GEO_KEY is 'demand:intent:geo'
        $intents = \Illuminate\Support\Facades\Redis::georadius('demand:intent:geo', -0.1870, 5.6037, 50000, 'm', 'WITHCOORD');
        
        $heatmapData = [];
        foreach ($intents as $intent) {
            // [lon, lat]
            $heatmapData[] = [
                (float) $intent[1][1], // lat
                (float) $intent[1][0], // lon
                1.0 // intensity
            ];
        }

        return $this->success($heatmapData, 'Real-time demand intensity retrieved.');
    }
}
