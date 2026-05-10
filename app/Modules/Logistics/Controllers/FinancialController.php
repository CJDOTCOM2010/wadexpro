<?php

namespace App\Modules\Logistics\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Logistics\Services\WalletService;
use App\Modules\Logistics\Services\PaystackService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FinancialController extends Controller
{
    protected WalletService $walletService;
    protected PaystackService $paystackService;

    public function __construct(WalletService $walletService, PaystackService $paystackService)
    {
        $this->walletService = $walletService;
        $this->paystackService = $paystackService;
    }

    /**
     * Get the current user's wallet balance.
     */
    public function getBalance(Request $request)
    {
        $wallet = $this->walletService->getWallet($request->user());

        return response()->json([
            'balance' => (float) $wallet->balance,
            'currency' => $wallet->currency,
            'is_frozen' => $wallet->is_frozen,
        ]);
    }

    /**
     * Get transaction history for the user.
     */
    public function getTransactions(Request $request)
    {
        $transactions = $request->user()->transactions()
            ->latest()
            ->paginate(20);

        return response()->json($transactions);
    }

    /**
     * Initialize a wallet top-up via Paystack.
     */
    public function initializeTopUp(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
        ]);

        $result = $this->paystackService->initializeTransaction(
            $request->user(),
            $request->amount,
            ['type' => 'wallet_topup']
        );

        if ($result['success']) {
            return response()->json($result);
        }

        return response()->json(['message' => $result['message']], 400);
    }

    /**
     * Verify a top-up transaction.
     * This is usually the callback/webhook endpoint.
     */
    public function verifyTopUp(Request $request)
    {
        $reference = $request->query('reference') ?? $request->input('reference');

        if (!$reference) {
            return response()->json(['message' => 'No reference provided.'], 400);
        }

        $result = $this->paystackService->verifyTransaction($reference);

        if ($result['success']) {
            // Check if transaction was already processed to prevent double crediting
            $existing = \App\Modules\Payments\Models\Transaction::where('gateway_ref', $result['reference'])->first();
            
            if (!$existing) {
                $user = $request->user();
                $this->walletService->credit(
                    $user,
                    $result['amount'],
                    'wallet_topup',
                    ['gateway' => 'paystack', 'gateway_ref' => $result['reference']]
                );
            }

            return response()->json([
                'success' => true,
                'message' => 'Wallet topped up successfully.',
                'amount' => $result['amount']
            ]);
        }

        return response()->json(['message' => $result['message']], 400);
    }
}
