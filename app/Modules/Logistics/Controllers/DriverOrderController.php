<?php

namespace App\Modules\Logistics\Controllers;

use App\Core\Traits\ApiResponse;
use App\Modules\Logistics\Services\DriverService;
use App\Modules\Logistics\Services\OrderService;
use App\Modules\Logistics\Services\SafetyGuardService;
use App\Modules\Logistics\Models\Order;
use App\Modules\Logistics\Models\RideRequest;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class DriverOrderController extends Controller
{
    use ApiResponse;

    public function __construct(
        private DriverService $driverService, 
        private OrderService $orderService,
        private AntifraudService $antifraudService,
        private SafetyGuardService $safetyService
    ) {
    }

    /**
     * Driver updates their current GPS coordinates.
     * This hits constantly so we keep it lean. 
     */
    public function updateLocation(Request $request)
    {
        $validated = $request->validate([
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
            'speed_kmh' => 'nullable|numeric',
            'bearing' => 'nullable|numeric',
        ]);

        $driver = $request->user()->driver;

        if (!$driver) {
            return $this->error('User is not registered as a driver.', 403);
        }

        // Integrity Layer: GPS Plausibility Check
        $fraudCheck = $this->antifraudService->validateGpsUpdate(
            $driver, 
            (float) $validated['lat'], 
            (float) $validated['lng'], 
            (float) ($validated['speed_kmh'] ?? 0)
        );

        if (!$fraudCheck['valid']) {
            return $this->error('GPS telemetry rejected: ' . $fraudCheck['reason'], 422, [
                'details' => $fraudCheck['details']
            ]);
        }

        $this->driverService->recordTrackingEvent($driver, $validated);

        // Advanced Safety: Route Deviation Monitoring
        // Check if there's an active RideRequest (Taxi)
        $activeRide = RideRequest::where('driver_id', $driver->id)
            ->where('status', 'in_progress')
            ->first();
        
        if ($activeRide) {
            $this->safetyService->monitorTelemetry($activeRide, (float) $validated['lat'], (float) $validated['lng']);
        }

        return $this->success(null, 'Location tracked.');
    }

    /**
     * Driver changes the state of an assigned order.
     */
    public function updateOrderStatus(Request $request, string $orderId)
    {
        $validated = $request->validate([
            'status' => 'required|in:picked_up,in_transit,delivered,cancelled'
        ]);

        $driver = $request->user()->driver;
        $order = Order::findOrFail($orderId);

        if ($order->driver_id !== $driver->id) {
            return $this->error('Not authorized to update this order.', 403);
        }

        $order = $this->orderService->updateStatus($order, $validated['status']);

        return $this->success($order, 'Order status updated.');
    }
}
