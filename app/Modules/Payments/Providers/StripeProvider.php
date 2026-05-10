<?php

namespace App\Modules\Payments\Providers;

use App\Modules\Logistics\Models\Order;
use App\Modules\Payments\Contracts\PaymentGatewayInterface;
use Illuminate\Support\Facades\Http;

class StripeProvider implements PaymentGatewayInterface
{
    private string $secretKey;
    private string $baseUrl = 'https://api.stripe.com/v1';

    public function __construct()
    {
        $this->secretKey = config('payment-gateways.stripe.secret_key', '');
    }

    public function getProviderName(): string
    {
        return 'stripe';
    }

    public function initializeTransaction(Order $order, float $amount, string $currency): array
    {
        // Stripe expects amounts in smallest unit (e.g. cents)
        $amountInCents = (int) ($amount * 100);
        $reference = 'WDX_ST_' . strtoupper(uniqid());

        // Basic PaymentIntent implementation
        // For production, Stripe API bindings map are preferred over raw HTTP
        $response = Http::withToken($this->secretKey)
            ->asForm()
            ->post("{$this->baseUrl}/payment_intents", [
                'amount' => $amountInCents,
                'currency' => strtolower($currency),
                'metadata' => [
                    'order_id' => $order->id,
                    'reference' => $reference,
                ]
            ]);

        if (!$response->successful()) {
            throw new \Exception('Stripe initialization failed: ' . $response->body());
        }

        $data = $response->json();

        return [
            'provider' => $this->getProviderName(),
            'reference' => $data['id'], // We use Stripe's PI ID as reference
            'checkout_url' => null, // Stripe usually uses client secret on frontend
            'access_code' => $data['client_secret']
        ];
    }

    public function verifyTransaction(string $paymentIntentId): array
    {
        $response = Http::withToken($this->secretKey)
            ->get("{$this->baseUrl}/payment_intents/{$paymentIntentId}");

        $data = $response->json();

        return [
            'is_successful' => $response->successful() && $data['status'] === 'succeeded',
            'amount' => ($data['amount_received'] ?? 0) / 100,
            'currency' => strtoupper($data['currency'] ?? 'USD'),
            'metadata' => $data['metadata'] ?? []
        ];
    }

    public function validateWebhookSignature(\Illuminate\Http\Request $request): bool
    {
        // Requires official Stripe SDK to construct standard webhook event via payload + stripe-signature
        // Stubbed for API scaffold.
        $signature = $request->header('Stripe-Signature');
        $secret = config('payment-gateways.stripe.webhook_secret', '');

        // Validation logic using raw body to map against Stripe standard signature format...
        return !empty($signature); 
    }
}
