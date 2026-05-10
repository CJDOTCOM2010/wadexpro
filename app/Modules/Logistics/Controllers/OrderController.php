<?php

namespace App\Modules\Logistics\Controllers;

use App\Core\Traits\ApiResponse;
use App\Modules\Logistics\Services\OrderService;
use App\Modules\Logistics\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class OrderController extends Controller
{
    use ApiResponse;

    public function __construct(private OrderService $orderService)
    {
    }

    /**
     * Customer creates a new order.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'pickup_address' => 'required|string',
            'pickup_lat' => 'required|numeric',
            'pickup_lng' => 'required|numeric',
            'pickup_contact_name' => 'required|string',
            'pickup_contact_phone' => 'required|string',
            'package_description' => 'required|string',
            'package_weight_kg' => 'nullable|numeric|min:0.1',
            'priority' => 'nullable|in:standard,express',
            'stops' => 'required|array|min:1',
            'stops.*.address' => 'required|string',
            'stops.*.lat' => 'required|numeric',
            'stops.*.lng' => 'required|numeric',
            'stops.*.contact_name' => 'required|string',
            'stops.*.contact_phone' => 'required|string',
            'stops.*.stop_type' => 'required|string|in:dropoff,pickup',
        ]);

        $orderData = \Arr::except($validated, ['stops']);
        
        $order = $this->orderService->createOrder(
            $request->user()->id, 
            $orderData, 
            $validated['stops']
        );

        return $this->success($order->load('stops'), 'Order created successfully.', 201);
    }

    /**
     * Customer retrieves their orders.
     */
    public function index(Request $request)
    {
        $orders = Order::where('customer_id', $request->user()->id)
            ->with(['stops', 'driver.user'])
            ->latest()
            ->paginate(15);

        return $this->success($orders, 'Orders retrieved.');
    }

    /**
     * Specific order details.
     */
    public function show(Request $request, string $id)
    {
        $order = Order::with(['stops', 'driver.user', 'trackingEvents'])->findOrFail($id);
        
        if ($order->customer_id !== $request->user()->id && !$request->user()->hasRole(['admin', 'super_admin'])) {
            return $this->error('Unauthorized access.', 403);
        }

        return $this->success($order, 'Order details retrieved.');
    }
}
