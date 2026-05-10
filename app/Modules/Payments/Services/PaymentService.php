<?php

namespace App\Modules\Payments\Services;

use App\Modules\Logistics\Models\Order;
use App\Modules\Payments\Contracts\PaymentGatewayInterface;
use App\Modules\Payments\Models\Transaction;
use App\Modules\Accounting\Services\AccountingService;

class PaymentService
{
    /**
     * Array of available gateways mapped by machine name.
     */
    private array $gateways = [];

    public function __construct(
        iterable $providers,
        private AccountingService $accountingService
    ) {
        foreach ($providers as $provider) {
            $this->gateways[$provider->getProviderName()] = $provider;
        }
    }

    /**
     * Get a specific gateway instance.
     */
    public function getGateway(string $name): PaymentGatewayInterface
    {
        if (!isset($this->gateways[$name])) {
            throw new \InvalidArgumentException("Payment gateway [{$name}] not supported.");
        }
        return $this->gateways[$name];
    }

    /**
     * Centralized initialization flow logic.
     */
    public function checkoutOrder(Order $order, string $gatewayName): array
    {
        $gateway = $this->getGateway($gatewayName);

        // Call the provider API
        $paymentData = $gateway->initializeTransaction($order, $order->total_amount, $order->currency);

        // Record the attempt in transactions table
        Transaction::create([
            'order_id' => $order->id,
            'user_id' => $order->customer_id,
            'gateway' => $gateway->getProviderName(),
            'reference' => $paymentData['reference'],
            'amount' => $order->total_amount,
            'currency' => $order->currency,
            'status' => 'pending',
        ]);

        return $paymentData;
    }

    /**
     * Process an incoming webhook status.
     */
    public function processWebhook(string $gatewayName, \Illuminate\Http\Request $request): array
    {
        $gateway = $this->getGateway($gatewayName);

        if (!$gateway->validateWebhookSignature($request)) {
            throw new \Exception('Invalid webhook signature');
        }

        // Standardized data mapping depending on gateway logic would happen here...
        // For simplicity, we just use the reference to trigger manual verification
        $reference = $request->input('data.reference') ?? $request->input('tx_ref');

        if (!$reference) {
            return ['status' => 'ignored', 'message' => 'No reference found in webhook.'];
        }

        return $this->verifyAndSettle($gatewayName, $reference);
    }

    /**
     * Validate against the provider that the cash is definitely there, and settle.
     */
    public function verifyAndSettle(string $gatewayName, string $reference): array
    {
        $gateway = $this->getGateway($gatewayName);
        $result = $gateway->verifyTransaction($reference);

        $transaction = Transaction::where('reference', $reference)->first();

        if (!$transaction) {
            return ['status' => 'error', 'message' => 'Transaction reference not found locally.'];
        }

        if ($result['is_successful']) {
            $transaction->update(['status' => 'success']);
            
            // Mark order as paid
            $order = $transaction->order;
            if ($order && $order->payment_status !== 'paid') {
                $order->update(['payment_status' => 'paid']);

                // Record in Accounting Ledger
                $cashAccount = $this->accountingService->getAccountByCode('1000');
                $revenueAccount = $this->accountingService->getAccountByCode('4000');

                $this->accountingService->createEntry([
                    'reference' => 'PAY-' . $transaction->reference,
                    'description' => "Payment for Order #{$order->id} via {$gateway->getProviderName()}",
                    'source_type' => 'order_payment',
                    'source_id' => $order->id,
                    'lines' => [
                        [
                            'account_id' => $cashAccount->id,
                            'debit' => $transaction->amount,
                            'currency' => $transaction->currency
                        ],
                        [
                            'account_id' => $revenueAccount->id,
                            'credit' => $transaction->amount,
                            'currency' => $transaction->currency
                        ]
                    ]
                ]);
            }

            return ['status' => 'success', 'message' => 'Payment settled globally and recorded in ledger.'];
        }

        $transaction->update(['status' => 'failed']);
        return ['status' => 'failed', 'message' => 'Payment marked failed from gateway.'];
    }
}
