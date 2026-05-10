<?php

namespace App\Modules\Payments\Controllers;

use App\Core\Traits\ApiResponse;
use App\Modules\Payments\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class WebhookController extends Controller
{
    use ApiResponse;

    public function __construct(private PaymentService $paymentService)
    {
    }

    /**
     * Handle incoming webhooks from external providers
     */
    public function handle(Request $request, string $provider)
    {
        try {
            $result = $this->paymentService->processWebhook($provider, $request);

            if ($result['status'] === 'success') {
                return response()->json(['status' => 'success', 'message' => 'Webhook received and processed.']);
            }

            return response()->json(['status' => 'error', 'message' => $result['message']], 400);

        } catch (\Exception $e) {
            // Log severe webhook verification failures
            \Log::error("Webhook processing failed for [{$provider}]: " . $e->getMessage());
            
            // Always return 200 so gateways don't infinitely retry failing payloads
            return response()->json(['status' => 'error', 'message' => 'Webhook signature failed verification.']);
        }
    }
}
