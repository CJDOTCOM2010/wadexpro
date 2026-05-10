<?php

namespace App\Modules\Payments\Providers;

use App\Modules\Logistics\Models\Order;
use App\Modules\Payments\Contracts\PaymentGatewayInterface;
use Illuminate\Support\Facades\Http;

class FlutterwaveProvider implements PaymentGatewayInterface
{
    private string $secretKey;
    private string $baseUrl = 'https://api.flutterwave.com/v3';

    public function __construct()
    {
        $this->secretKey = config('payment-gateways.flutterwave.secret_key', '');
    }

    public function getProviderName(): string
    {
        return 'flutterwave';
    }

    public function initializeTransaction(Order $order, float $amount, string $currency): array
    {
        $reference = 'WDX_FW_' . strtoupper(uniqid());

        $response = Http::withToken($this->secretKey)
            ->post("{$this->baseUrl}/payments", [
                'tx_ref' => $reference,
                'amount' => $amount,
                'currency' => $currency,
                'redirect_url' => url('/api/v1/payments/flutterwave/callback'),
                'customer' => [
                    'email' => $order->customer->email ?? 'customer@wadexp.com',
                    'name' => $order->customer->name ?? 'WadExp Customer',
                ],
                'meta' => [
                    'order_id' => $order->id,
                ]
            ]);

        if (!$response->successful()) {
            throw new \Exception('Flutterwave initialization failed: ' . $response->body());
        }

        $data = $response->json('data');

        return [
            'provider' => $this->getProviderName(),
            'reference' => $reference,
            'checkout_url' => $data['link'],
            'access_code' => null
        ];
    }

    public function verifyTransaction(string $transactionId): array
    {
        $response = Http::withToken($this->secretKey)
            ->get("{$this->baseUrl}/transactions/{$transactionId}/verify");

        $data = $response->json('data');

        return [
            'is_successful' => $response->successful() && $data['status'] === 'successful',
            'amount' => $data['amount'] ?? 0,
            'currency' => $data['currency'] ?? 'GHS',
            'metadata' => $data['meta'] ?? []
        ];
    }

    public function validateWebhookSignature(\Illuminate\Http\Request $request): bool
    {
        $signature = $request->header('verif-hash');
        $secretHash = config('payment-gateways.flutterwave.webhook_hash', '');

        return $signature === $secretHash;
    }
}
