<?php

namespace App\Modules\Admin\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Modules\Logistics\Models\Order;
use App\Modules\Logistics\Models\Driver;
use App\Modules\Logistics\Models\SafetyAlert;
use App\Modules\Admin\Models\SystemSetting;

class OperationsController extends Controller
{
    /**
     * The Global Queue (List of all orders/rides)
     */
    public function globalQueue(Request $request)
    {
        try {
            $activePipeline = Order::whereIn('status', ['pending', 'assigned', 'picked_up', 'in_transit'])->count();
            $awaitingNode = Order::where('status', 'pending')->count();
            $inProgress = Order::whereIn('status', ['assigned', 'picked_up', 'in_transit'])->count();
            
            $anomalies = Order::where('status', 'cancelled')->where('updated_at', '>=', now()->subDay())->count();

            $stats = [
                'active_pipeline' => $activePipeline,
                'awaiting_node'   => $awaitingNode,
                'in_progress'     => $inProgress,
                'anomalies'       => $anomalies,
            ];

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
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Global Queue Error: ' . $e->getMessage());
            return view('admin.global_queue', [
                'stats' => ['active_pipeline' => 0, 'awaiting_node' => 0, 'in_progress' => 0, 'anomalies' => 0],
                'orders' => new \Illuminate\Pagination\LengthAwarePaginator([], 0, 15),
            ])->with('error', 'Unable to load orders queue.');
        }
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

        $google_maps_api_key = SystemSetting::get('google_maps_api_key');

        return view('admin.dispatcher', compact('stats', 'priorityOrders', 'activeDrivers', 'google_maps_api_key'));
    }

    /**
     * Operations Map (Real-time tracking and events)
     */
    public function map()
    {
        try {
            $liveNodes = Driver::where('is_online', true)->count();

            $highValueOrders = Order::where('total_amount', '>', 500)
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();

            $sosAlerts = collect([]); // SafetyAlert may not exist
            try {
                $sosAlerts = SafetyAlert::with('ride.customer')
                    ->where('status', 'open')
                    ->orderBy('created_at', 'desc')
                    ->get();
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::warning('SafetyAlert table not available: ' . $e->getMessage());
            }

            $recentDeployments = Driver::with('user')
                ->where('is_online', true)
                ->orderBy('last_location_at', 'desc')
                ->limit(5)
                ->get();

            $google_maps_api_key = SystemSetting::get('google_maps_api_key', '');

            return view('admin.operations_map', compact('liveNodes', 'highValueOrders', 'sosAlerts', 'recentDeployments', 'google_maps_api_key'));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Operations Map Error: ' . $e->getMessage());
            return view('admin.operations_map', [
                'liveNodes' => 0,
                'highValueOrders' => collect([]),
                'sosAlerts' => collect([]),
                'recentDeployments' => collect([]),
                'google_maps_api_key' => '',
            ])->with('error', 'Unable to load operations map.');
        }
    }
}
