<?php

namespace App\Modules\Admin\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Modules\Logistics\Models\Order;
use App\Modules\Logistics\Models\Driver;
use App\Modules\Logistics\Models\SafetyAlert;

class OperationsController extends Controller
{
    /**
     * The Global Queue (List of all orders/rides)
     */
    public function globalQueue(Request $request)
    {
        // Pipeline stats
        $activePipeline = Order::whereIn('status', ['pending', 'assigned', 'picked_up', 'in_transit'])->count();
        $awaitingNode = Order::where('status', 'pending')->count();
        $inProgress = Order::whereIn('status', ['assigned', 'picked_up', 'in_transit'])->count();
        
        // Anomalies (e.g., cancelled in the last 24h or orders with safety alerts)
        $anomalies = Order::where('status', 'cancelled')->where('updated_at', '>=', now()->subDay())->count() 
                   + SafetyAlert::where('status', 'open')->count();

        $stats = [
            'active_pipeline' => $activePipeline,
            'awaiting_node'   => $awaitingNode,
            'in_progress'     => $inProgress,
            'anomalies'       => $anomalies,
        ];

        // Search functionality
        $query = Order::with(['customer', 'driver.user', 'transaction'])->orderBy('created_at', 'desc');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('reference', 'like', "%{$search}%")
                  ->orWhereHas('customer', function ($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
        }

        $orders = $query->paginate(15);

        return view('admin.global_queue', compact('stats', 'orders'));
    }

    /**
     * The Tactical Dispatcher (Assigning rides to online drivers)
     */
    public function dispatcher()
    {
        $onlineDriversCount = Driver::where('is_online', true)->count();
        $totalDrivers = Driver::count();
        $fleetLoad = $totalDrivers > 0 ? round(($onlineDriversCount / $totalDrivers) * 100) : 0;

        $activeRidesCount = Order::whereIn('status', ['assigned', 'picked_up', 'in_transit'])->count();

        // Priority Interceptions (Pending orders with urgent/high priority)
        $priorityOrders = Order::where('status', 'pending')
            ->whereIn('priority', ['urgent', 'high'])
            ->orderBy('created_at', 'asc')
            ->get();

        $stats = [
            'fleet_load'    => $fleetLoad,
            'active_rides'  => $activeRidesCount,
            'online_nodes'  => $onlineDriversCount,
        ];

        // For the visualization, fetch online drivers with coordinates
        $activeDrivers = Driver::with('user')
            ->where('is_online', true)
            ->whereNotNull('current_lat')
            ->get();

        return view('admin.dispatcher', compact('stats', 'priorityOrders', 'activeDrivers'));
    }

    /**
     * Operations Map (Real-time tracking and events)
     */
    public function map()
    {
        $liveNodes = Driver::where('is_online', true)->count();

        // Recent high-value orders
        $highValueOrders = Order::where('total_amount', '>', 500)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Active SOS / Safety Alerts
        $sosAlerts = SafetyAlert::with('ride.customer')
            ->where('status', 'open')
            ->orderBy('created_at', 'desc')
            ->get();

        // Recent Driver Deployments (Drivers who just went online)
        $recentDeployments = Driver::with('user')
            ->where('is_online', true)
            ->orderBy('last_location_at', 'desc')
            ->limit(5)
            ->get();

        return view('admin.operations_map', compact('liveNodes', 'highValueOrders', 'sosAlerts', 'recentDeployments'));
    }
}
