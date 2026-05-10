<?php

namespace App\Modules\Payments\Providers;

use App\Modules\Logistics\Models\Order;
use App\Modules\Payments\Contracts\PaymentGatewayInterface;
use Illuminate\Support\Facades\Http;

class PaystackProvider implements PaymentGatewayInterface
{
    private string $secretKey;
    private string $baseUrl = 'https://api.paystack.co';

    public function __construct()
    {
        $this->secretKey = config('payment-gateways.paystack.secret_key', '');
    }

    public function getProviderName(): string
    {
        return 'paystack';
    }

    public function initializeTransaction(Order $order, float $amount, string $currency): array
    {
        // Paystack amounts are in kobo (base unit)
        $amountInKobo = (int) ($amount * 100);

        $response = Http::withToken($this->secretKey)
            ->post("{$this->baseUrl}/transaction/initialize", [
                'email' => $order->customer->email ?? 'customer@wadexp.com', // fallback
                'amount' => $amountInKobo,
                'currency' => $currency,
                'reference' => 'WDX_' . strtoupper(uniqid()),
                'metadata' => [
                    'order_id' => $order->id,
                    'customer_id' => $order->customer_id
                ]
            ]);

        if (!$response->successful()) {
            throw new \Exception('Paystack initialization failed: ' . $response->body());
        }

        $data = $response->json('data');

        return [
            'provider' => $this->getProviderName(),
            'reference' => $data['reference'],
            'checkout_url' => $data['authorization_url'],
            'access_code' => $data['access_code']
        ];
    }

    public function verifyTransaction(string $reference): array
    {
        $response = Http::withToken($this->secretKey)
            ->get("{$this->baseUrl}/transaction/verify/{$reference}");

        $data = $response->json('data');

        return [
            'is_successful' => $response->successful() && $data['status'] === 'success',
            'amount' => ($data['amount'] ?? 0) / 100, // convert back to major currency unit
            'currency' => $data['currency'] ?? 'GHS',
            'metadata' => $data['metadata'] ?? []
        ];
    }

    public function validateWebhookSignature(\Illuminate\Http\Request $request): bool
    {
        // Paystack signature comes in x-paystack-signature header
        $signature = $request->header('x-paystack-signature');
        $payload = $request->getContent();

        $hash = hash_hmac('sha512', $payload, $this->secretKey);

        return hash_equals($hash, $signature);
    }
}
