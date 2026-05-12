<?php

namespace App\Modules\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Logistics\Models\Order;
use App\Modules\Logistics\Models\Driver;
use App\Modules\Payments\Models\WalletTransaction;
use App\Modules\Logistics\Models\RideCancellation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrchestratorAnalyticsController extends Controller
{
    /**
     * Business Intelligence main dashboard.
     */
    public function index(Request $request)
    {
        $period = $request->get('period', '30days');

        [$startDate, $endDate] = $this->resolvePeriod($period);

        // ── Revenue KPIs ──────────────────────────────────────────────────────
        $revenue = WalletTransaction::whereBetween('transacted_at', [$startDate, $endDate])
            ->where('category', 'ride_payment')
            ->where('type', 'credit')
            ->sum('amount');

        $prevRevenue = WalletTransaction::whereBetween('transacted_at', [
                $startDate->copy()->subDays($startDate->diffInDays($endDate)),
                $startDate,
            ])
            ->where('category', 'ride_payment')
            ->where('type', 'credit')
            ->sum('amount');

        $revenueChange = $prevRevenue > 0
            ? round((($revenue - $prevRevenue) / $prevRevenue) * 100, 1)
            : 0;

        // ── Ride KPIs ─────────────────────────────────────────────────────────
        $totalRides = Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->count();

        $prevRides = Order::whereBetween('created_at', [
                $startDate->copy()->subDays($startDate->diffInDays($endDate)),
                $startDate,
            ])
            ->where('status', 'completed')
            ->count();

        $ridesChange = $prevRides > 0
            ? round((($totalRides - $prevRides) / $prevRides) * 100, 1)
            : 0;

        // ── New Customers ─────────────────────────────────────────────────────
        $newCustomers = User::whereBetween('created_at', [$startDate, $endDate])
            ->where('role', 'customer')
            ->count();

        // ── Cancellation Rate ─────────────────────────────────────────────────
        $allOrders   = Order::whereBetween('created_at', [$startDate, $endDate])->count();
        $cancelledOrders = Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'cancelled')
            ->count();
        $cancelRate = $allOrders > 0
            ? round(($cancelledOrders / $allOrders) * 100, 1)
            : 0;

        // ── Daily Revenue (last 7 days) ───────────────────────────────────────
        $dailyRevenue = WalletTransaction::select(
                DB::raw('DATE(transacted_at) as date'),
                DB::raw('SUM(amount) as total')
            )
            ->where('category', 'ride_payment')
            ->where('type', 'credit')
            ->where('transacted_at', '>=', now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        // ── Top Drivers ───────────────────────────────────────────────────────
        $topDrivers = Driver::with('user')
            ->withCount(['orders' => function ($q) use ($startDate, $endDate) {
                $q->whereBetween('created_at', [$startDate, $endDate])
                  ->where('status', 'completed');
            }])
            ->orderByDesc('orders_count')
            ->limit(5)
            ->get();

        $kpis = [
            'revenue'        => $revenue,
            'revenue_change' => $revenueChange,
            'total_rides'    => $totalRides,
            'rides_change'   => $ridesChange,
            'new_customers'  => $newCustomers,
            'cancel_rate'    => $cancelRate,
        ];

        return view('admin.analytics', compact('kpis', 'dailyRevenue', 'topDrivers', 'period'));
    }

    private function resolvePeriod(string $period): array
    {
        return match ($period) {
            '7days'   => [now()->subDays(7),  now()],
            'quarter' => [now()->startOfQuarter(), now()],
            'year'    => [now()->startOfYear(), now()],
            default   => [now()->subDays(30), now()],
        };
    }
}
