<?php

namespace App\Modules\Logistics\Services;

use App\Modules\Logistics\Models\Driver;
use App\Modules\Logistics\Models\Order;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PerformanceService
{
    /**
     * Get weekly analytics for a specific driver.
     */
    public function getWeeklyDriverStats(string $driverId): array
    {
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();

        $orders = Order::where('driver_id', $driverId)
            ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
            ->get();

        $totalOrders = $orders->count();
        $deliveredOrders = $orders->where('status', 'delivered')->count();
        $totalDistance = $orders->sum('actual_distance_km');
        $totalEarnings = $orders->sum('total_amount') * 0.8; // Assume 80% driver cut

        return [
            'period' => $startOfWeek->format('Y-M-d') . ' to ' . $endOfWeek->format('Y-M-d'),
            'total_orders' => $totalOrders,
            'completed_orders' => $deliveredOrders,
            'completion_rate' => $totalOrders > 0 ? round(($deliveredOrders / $totalOrders) * 100, 1) : 0,
            'total_distance_km' => round($totalDistance, 2),
            'total_earnings' => round($totalEarnings, 2),
            'currency' => 'GHS',
        ];
    }

    /**
     * Get global platform performance for admin dashboard.
     */
    public function getGlobalStats(): array
    {
        return [
            'active_deliveries' => Order::whereIn('status', ['assigned', 'picked_up', 'in_transit'])->count(),
            'online_drivers' => Driver::where('is_online', true)->count(),
            'total_revenue_today' => Order::whereDate('created_at', Carbon::today())
                ->where('payment_status', 'paid')
                ->sum('total_amount'),
        ];
    }
}
