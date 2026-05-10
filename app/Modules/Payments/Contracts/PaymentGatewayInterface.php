<?php

namespace App\Modules\Payments\Contracts;

use App\Modules\Logistics\Models\Order;

interface PaymentGatewayInterface
{
    /**
     * Get the unique machine name of the provider (e.g. 'paystack', 'flutterwave')
     */
    public function getProviderName(): string;

    /**
     * Initialize a payment transaction and return a checkout URL or client secret.
     */
    public function initializeTransaction(Order $order, float $amount, string $currency): array;

    /**
     * Verify a transaction status using the reference provided by the gateway.
     */
    public function verifyTransaction(string $reference): array;

    /**
     * Validate a webhook payload signature.
     */
    public function validateWebhookSignature(\Illuminate\Http\Request $request): bool;
}
