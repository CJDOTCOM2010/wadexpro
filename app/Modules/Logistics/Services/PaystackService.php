<?php

namespace App\Modules\Logistics\Services;

use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaystackService
{
    protected string $secretKey;
    protected string $baseUrl;

    public function __construct()
    {
        $this->secretKey = config('services.paystack.secret_key', env('PAYSTACK_SECRET_KEY'));
        $this->baseUrl = config('services.paystack.base_url', env('PAYSTACK_PAYMENT_URL', 'https://api.paystack.co'));
    }

    /**
     * Initialize a transaction and get a checkout URL.
     */
    public function initializeTransaction(User $user, float $amount, array $metadata = []): array
    {
        try {
            $response = Http::withToken($this->secretKey)
                ->post("{$this->baseUrl}/transaction/initialize", [
                    'amount' => $amount * 100, // Paystack expects amount in kobo/pesewas
                    'email' => $user->email,
                    'reference' => 'WDX_TOPUP_' . uniqid(),
                    'callback_url' => url('/api/v1/logistics/wallet/verify'),
                    'metadata' => array_merge($metadata, [
                        'user_id' => $user->id,
                        'app' => 'wadexpro',
                    ]),
                ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'authorization_url' => $response->json('data.authorization_url'),
                    'reference' => $response->json('data.reference'),
                ];
            }

            Log::error('Paystack Initialization Failed', ['response' => $response->json()]);
            return [
                'success' => false,
                'message' => $response->json('message') ?? 'Unable to initialize transaction.',
            ];
        } catch (\Exception $e) {
            Log::error('Paystack Service Error', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'message' => 'Internal server error during payment initialization.',
            ];
        }
    }

    /**
     * Verify a transaction and get the status.
     */
    public function verifyTransaction(string $reference): array
    {
        try {
            $response = Http::withToken($this->secretKey)
                ->get("{$this->baseUrl}/transaction/verify/{$reference}");

            if ($response->successful() && $response->json('data.status') === 'success') {
                return [
                    'success' => true,
                    'amount' => $response->json('data.amount') / 100,
                    'metadata' => $response->json('data.metadata'),
                    'reference' => $response->json('data.reference'),
                ];
            }

            return [
                'success' => false,
                'message' => $response->json('message') ?? 'Transaction verification failed.',
            ];
        } catch (\Exception $e) {
            Log::error('Paystack Verification Error', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'message' => 'Internal server error during payment verification.',
            ];
        }
    }
}
