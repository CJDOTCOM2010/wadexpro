<?php

namespace App\Modules\Logistics\Controllers;

use App\Core\Traits\ApiResponse;
use App\Modules\Logistics\Models\Order;
use App\Modules\Logistics\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class PersonalOrderController extends Controller
{
    use ApiResponse;

    public function __construct(private OrderService $orderService)
    {
    }

    /**
     * Display a listing of the customer's orders.
     */
    public function index(Request $request)
    {
        $orders = Order::where('customer_id', $request->user()->id)
            ->with(['driver.user', 'stops'])
            ->latest()
            ->paginate(10);

        return $this->success($orders);
    }

    /**
     * Display the specified order details with real-time tracking context.
     */
    public function show(string $id, Request $request)
    {
        $order = Order::where('customer_id', $request->user()->id)
            ->with(['driver.user', 'stops'])
            ->findOrFail($id);

        return $this->success($order);
    }

    /**
     * Cancel the specified order if it hasn't been picked up yet.
     */
    public function cancel(string $id, Request $request)
    {
        $order = Order::where('customer_id', $request->user()->id)
            ->whereIn('status', ['pending', 'assigned'])
            ->findOrFail($id);

        $order->update(['status' => 'cancelled']);

        return $this->success($order, 'Order cancelled successfully.');
    }
}
