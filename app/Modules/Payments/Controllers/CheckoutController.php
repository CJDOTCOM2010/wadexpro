<?php

namespace App\Modules\Payments\Controllers;

use App\Core\Traits\ApiResponse;
use App\Modules\Logistics\Models\Order;
use App\Modules\Payments\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class CheckoutController extends Controller
{
    use ApiResponse;

    public function __construct(private PaymentService $paymentService)
    {
    }

    /**
     * Initializes a payment for a specific order
     */
    public function initialize(Request $request)
    {
        $validated = $request->validate([
            'order_id' => 'required|uuid|exists:orders,id',
            'provider' => 'required|string|in:paystack,flutterwave,stripe'
        ]);

        $order = Order::findOrFail($validated['order_id']);

        if ($order->customer_id !== $request->user()->id) {
            return $this->error('Unauthorized to pay for this order.', 403);
        }

        if ($order->payment_status === 'paid') {
            return $this->error('Order has already been paid for.', 400);
        }

        try {
            $checkoutData = $this->paymentService->checkoutOrder($order, $validated['provider']);
            return $this->success($checkoutData, 'Checkout initialized.');
        } catch (\Exception $e) {
            return $this->error('Failed to initialize payment: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Validate payment manually following a client-side success callback.
     */
    public function verify(Request $request)
    {
        $validated = $request->validate([
            'provider' => 'required|string',
            'reference' => 'required|string'
        ]);

        try {
            $result = $this->paymentService->verifyAndSettle($validated['provider'], $validated['reference']);
            return $this->success($result, 'Verification complete.');
        } catch (\Exception $e) {
            return $this->error('Failed to verify payment', 500);
        }
    }
}
