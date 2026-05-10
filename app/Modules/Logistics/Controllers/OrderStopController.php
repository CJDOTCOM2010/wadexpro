<?php

namespace App\Modules\Logistics\Controllers;

use App\Core\Traits\ApiResponse;
use App\Modules\Logistics\Models\Order;
use App\Modules\Logistics\Models\OrderStop;
use App\Modules\Logistics\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class OrderStopController extends Controller
{
    use ApiResponse;

    public function __construct(private OrderService $orderService)
    {
    }

    /**
     * Driver updates the status of a specific stop in a multi-stop order.
     */
    public function updateStatus(Request $request, string $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:arrived,picked_up,delivered,failed',
            'notes' => 'nullable|string',
            'pod_photo' => 'nullable|string', // Base64 or URL (MVP)
        ]);

        $stop = OrderStop::with('order')->findOrFail($id);
        $order = $stop->order;

        // Authorization
        if ($order->driver_id !== $request->user()->driver->id) {
            return $this->error('Unauthorized to update this stop.', 403);
        }

        // Sequence Enforcement
        if ($stop->sequence > 1) {
            $previousStop = OrderStop::where('order_id', $order->id)
                ->where('sequence', $stop->sequence - 1)
                ->first();

            if ($previousStop && $previousStop->status !== 'delivered' && $previousStop->status !== 'picked_up') {
                return $this->error('Cannot update this stop until the previous stop is completed.', 422);
            }
        }

        $stop->update([
            'status' => $validated['status'],
            'actual_arrival_time' => $validated['status'] === 'arrived' ? now() : $stop->actual_arrival_time,
            'completed_at' => ($validated['status'] === 'delivered' || $validated['status'] === 'picked_up') ? now() : $stop->completed_at,
            'notes' => $validated['notes'] ?? $stop->notes,
        ]);

        // If the last stop is delivered, complete the whole order
        $totalStops = OrderStop::where('order_id', $order->id)->count();
        if ($stop->sequence === $totalStops && $validated['status'] === 'delivered') {
            $this->orderService->updateStatus($order, 'delivered');
        }

        // Broadcast event for real-time customer tracking
        event(new \App\Modules\Logistics\Events\OrderStopsUpdated($order));

        return $this->success($stop, "Stop status updated to {$validated['status']}.");
    }
}
