<?php

namespace App\Modules\Payments\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Payments\Services\WalletService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    public function __construct(
        private WalletService $walletService
    ) {}

    /**
     * Get wallet balance.
     */
    public function balance(Request $request): JsonResponse
    {
        $balance = $this->walletService->getBalance($request->user()->id);
        $wallet = $this->walletService->getOrCreateWallet($request->user()->id);

        return response()->json([
            'data' => [
                'balance'   => $balance,
                'currency'  => $wallet->currency,
                'is_frozen' => $wallet->is_frozen,
            ],
        ]);
    }

    /**
     * Get wallet transaction history.
     */
    public function history(Request $request): JsonResponse
    {
        $limit = (int) $request->query('limit', 20);
        $transactions = $this->walletService->getTransactionHistory($request->user()->id, $limit);

        return response()->json(['data' => $transactions]);
    }

    /**
     * Get weekly earnings summary (optimized for charts).
     */
    public function weeklySummary(Request $request): JsonResponse
    {
        $userId = $request->user()->id;
        $start = now()->subDays(6)->startOfDay();

        $dailyEarnings = \App\Modules\Payments\Models\Transaction::where('user_id', $userId)
            ->where('type', 'credit')
            ->where('status', 'success')
            ->where('created_at', '>=', $start)
            ->selectRaw('DATE(created_at) as date, SUM(amount) as total')
            ->groupBy('date')
            ->orderBy('date', 'ASC')
            ->get();

        return response()->json([
            'data' => $dailyEarnings
        ]);
    }

    /**
     * Request a wallet payout (withdrawal).
     */
    public function payout(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:10',
        ]);

        $userId = $request->user()->id;
        $amount = (float) $validated['amount'];

        try {
            // Validate balance
            $balance = $this->walletService->getBalance($userId);
            if ($balance < $amount) {
                return $this->error('Insufficient balance for payout.', 400);
            }

            // Register pending withdrawal
            $transaction = \App\Modules\Payments\Models\Transaction::create([
                'user_id'   => $userId,
                'type'      => 'debit',
                'amount'    => $amount,
                'status'    => 'pending',
                'reference' => 'PO-' . strtoupper(str_random(8)),
                'gateway'   => 'manual_settlement',
                'metadata'  => ['requested_at' => now()->toDateTimeString()]
            ]);

            return response()->json([
                'message' => 'Payout request submitted successfully.',
                'data'    => $transaction
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Payout failed: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Top up wallet (initiates payment gateway flow).
     */
    public function topup(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'amount'  => 'required|numeric|min:1|max:10000',
            'gateway' => 'required|string|in:paystack,flutterwave,stripe',
        ]);

        // Create a wallet top-up order and redirect to payment
        $user = $request->user();
        $amount = (float) $validated['amount'];

        // For wallet top-ups, we credit after webhook confirms payment
        return response()->json([
            'message' => 'Proceed to payment gateway.',
            'data'    => [
                'amount'   => $amount,
                'currency' => 'GHS',
                'gateway'  => $validated['gateway'],
                'purpose'  => 'wallet_topup',
                'user_id'  => $user->id,
            ],
        ]);
    }
}
