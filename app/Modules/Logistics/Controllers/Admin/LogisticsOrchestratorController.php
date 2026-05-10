<?php

namespace App\Modules\Logistics\Controllers\Admin;

use App\Core\Traits\ApiResponse;
use App\Http\Controllers\Controller;
use App\Modules\Logistics\Models\Order;
use App\Modules\Logistics\Models\Driver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LogisticsOrchestratorController extends Controller
{
    use ApiResponse;

    /**
     * List all deliveries with their stop progress.
     */
    public function index(Request $request)
    {
        $status = $request->get('status');
        
        $query = Order::with(['customer', 'driver.user', 'stops'])
            ->orderBy('created_at', 'DESC');

        if ($status) {
            $query->where('status', $status);
        }

        $deliveries = $query->paginate($request->get('limit', 20));

        return $this->success($deliveries, 'Deliveries retrieved.');
    }

    /**
     * Get detailed progress of a single multi-stop delivery.
     */
    public function show(string $id)
    {
        $order = Order::with(['customer', 'driver.user', 'stops', 'trackingEvents'])
            ->findOrFail($id);

        return $this->success($order, 'Delivery details retrieved.');
    }

    /**
     * Fleet Overview: Get statistics about vehicles and drivers.
     */
    public function fleetOverview()
    {
        $stats = [
            'total_drivers' => Driver::count(),
            'online_drivers' => Driver::where('is_online', true)->count(),
            'active_tasks' => Order::whereIn('status', ['assigned', 'picked_up', 'in_transit'])->count(),
            'vehicle_distribution' => DB::table('vehicles')
                ->select('type', DB::raw('count(*) as count'))
                ->groupBy('type')
                ->get(),
            'utilization_rate' => 0.85
        ];

        return $this->success($stats, 'Fleet overview retrieved.');
    }

    /**
     * List all fleet vehicles with their current assignments.
     */
    public function indexVehicles()
    {
        $vehicles = DB::table('vehicles')
            ->leftJoin('drivers', 'vehicles.id', '=', 'drivers.active_vehicle_id')
            ->leftJoin('users', 'drivers.user_id', '=', 'users.id')
            ->select('vehicles.*', 'users.name as driver_name', 'drivers.is_online', 'drivers.is_available')
            ->get();

        return $this->success($vehicles, 'Fleet vehicles retrieved.');
    }
}
