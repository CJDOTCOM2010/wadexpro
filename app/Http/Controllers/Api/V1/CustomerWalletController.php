<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Modules\Payments\Models\Wallet;
use App\Modules\Payments\Models\Transaction;

class CustomerWalletController extends Controller
{
    /**
     * Get the unified hub data for the wallet
     */
    public function getHubData(Request $request)
    {
        try {
            $user = $request->user('sanctum');

            if (!$user) {
                return response()->json([
                    'status' => 'success',
                    'data' => [
                        'balance' => 0.00,
                        'currency' => 'GHS',
                        'payment_methods' => [],
                        'referral_count' => 0,
                        'referral_code' => 'WADEXPRO-VRT',
                    ]
                ]);
            }

            // Auto-provision wallet if it doesn't exist
            $wallet = $user->wallet()->firstOrCreate(
                ['user_id' => $user->id],
                ['balance' => 0.00, 'currency' => 'GHS']
            );

            return response()->json([
                'status' => 'success',
                'data' => [
                    'balance' => $wallet->balance,
                    'currency' => $wallet->currency,
                    'payment_methods' => [], // In a real app, fetch from a PaymentMethod model
                    'referral_count' => $user->referrals()->count(),
                    'referral_code' => $user->referral_code,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve wallet hub data.',
                'data' => [
                    'balance' => 0.00,
                    'currency' => 'GHS',
                    'payment_methods' => [],
                    'referral_count' => 0,
                ]
            ]);
        }
    }

    /**
     * Get transaction history
     */
    public function getTransactions(Request $request)
    {
        try {
            $user = $request->user('sanctum');

            if (!$user) {
                return response()->json(['status' => 'success', 'data' => []]);
            }

            $transactions = Transaction::where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->limit(50)
                ->get();

            return response()->json([
                'status' => 'success',
                'data' => $transactions->toArray(),
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'success', 'data' => []]);
        }
    }

    /**
     * Initialize a Top Up process
     */
    public function initializeTopUp(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1'
        ]);

        try {
            $user = $request->user('sanctum');
            if (!$user) {
                // Return a mock webview URL for virtual sessions
                return response()->json([
                    'status' => 'success',
                    'data' => [
                        'authorization_url' => config('app.url') . '/api/v1/mock/wallet/topup/webview?reference=VRT-MOCK-' . time(),
                        'reference' => 'VRT-MOCK-' . time(),
                    ]
                ]);
            }

            // Create a pending transaction
            $reference = 'WADEX-TX-' . time() . '-' . rand(1000, 9999);
            
            Transaction::create([
                'user_id' => $user->id,
                'reference' => $reference,
                'type' => 'wallet_topup',
                'amount' => $request->amount,
                'currency' => 'GHS',
                'status' => 'pending',
                'gateway' => 'paystack', // or whatever
            ]);

            // For now, return a mock URL that instantly approves for testing
            $mockUrl = config('app.url') . '/api/v1/mock/wallet/topup/webview?reference=' . $reference;

            return response()->json([
                'status' => 'success',
                'data' => [
                    'authorization_url' => $mockUrl,
                    'reference' => $reference,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Could not initialize top up.'
            ], 500);
        }
    }

    /**
     * Verify a Top Up
     */
    public function verifyTopUp(Request $request)
    {
        $request->validate(['reference' => 'required|string']);

        try {
            $user = $request->user('sanctum');
            $reference = $request->reference;

            if (!$user || str_starts_with($reference, 'VRT-MOCK-')) {
                return response()->json(['status' => 'success', 'message' => 'Top up verified (Mock).']);
            }

            $tx = Transaction::where('reference', $reference)->where('user_id', $user->id)->first();
            
            if ($tx && $tx->status === 'pending') {
                $tx->status = 'successful';
                $tx->processed_at = now();
                $tx->save();

                $wallet = $user->wallet()->firstOrCreate(
                    ['user_id' => $user->id],
                    ['balance' => 0.00, 'currency' => 'GHS']
                );

                $wallet->balance += $tx->amount;
                $wallet->save();

                return response()->json([
                    'status' => 'success', 
                    'message' => 'Top up successful',
                    'data' => ['balance' => $wallet->balance]
                ]);
            }

            return response()->json(['status' => 'error', 'message' => 'Invalid or already processed transaction.'], 400);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Verification failed'], 500);
        }
    }

    /**
     * Check Promos
     */
    public function checkPromo(Request $request)
    {
        return response()->json([
            'status' => 'success',
            'data' => ['valid' => false, 'message' => 'Promo code system not active yet.']
        ]);
    }
}
