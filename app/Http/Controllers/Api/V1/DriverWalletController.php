<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Modules\Payments\Models\Wallet;
use App\Modules\Payments\Models\Transaction;
use Carbon\Carbon;

class DriverWalletController extends Controller
{
    /**
     * Get Driver Balance
     */
    public function getBalance(Request $request)
    {
        try {
            $user = $request->user('sanctum');

            if (!$user) {
                return response()->json([
                    'balance' => 0.00,
                    'currency' => 'GHS'
                ]);
            }

            $wallet = $user->wallet()->firstOrCreate(
                ['user_id' => $user->id],
                ['balance' => 0.00, 'currency' => 'GHS']
            );

            return response()->json([
                'balance' => $wallet->balance,
                'currency' => $wallet->currency
            ]);
        } catch (\Exception $e) {
            return response()->json(['balance' => 0.00, 'currency' => 'GHS']);
        }
    }

    /**
     * Get Driver Transactions
     */
    public function getTransactions(Request $request)
    {
        try {
            $user = $request->user('sanctum');

            if (!$user) {
                return response()->json(['data' => []]);
            }

            $transactions = Transaction::where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->limit(50)
                ->get();

            return response()->json(['data' => $transactions->toArray()]);
        } catch (\Exception $e) {
            return response()->json(['data' => []]);
        }
    }

    /**
     * Get Weekly Summary
     */
    public function getWeeklySummary(Request $request)
    {
        try {
            $user = $request->user('sanctum');
            if (!$user) {
                return response()->json(['data' => ['days' => [], 'total_weekly' => 0.0]]);
            }

            $startOfWeek = Carbon::now()->startOfWeek();
            $endOfWeek = Carbon::now()->endOfWeek();

            $transactions = Transaction::where('user_id', $user->id)
                ->where('type', 'earning')
                ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
                ->get();

            $totalWeekly = $transactions->sum('amount');
            
            // Build days array (Mon-Sun)
            $days = [];
            for ($i = 0; $i < 7; $i++) {
                $date = $startOfWeek->copy()->addDays($i);
                $dailyTotal = $transactions->where('created_at', '>=', $date->startOfDay())
                                           ->where('created_at', '<=', $date->copy()->endOfDay())
                                           ->sum('amount');
                $days[] = [
                    'day' => $date->format('D'),
                    'amount' => $dailyTotal
                ];
            }

            return response()->json([
                'data' => [
                    'days' => $days,
                    'total_weekly' => $totalWeekly
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['data' => ['days' => [], 'total_weekly' => 0.0]]);
        }
    }

    /**
     * Request Payout
     */
    public function requestPayout(Request $request)
    {
        $request->validate(['amount' => 'required|numeric|min:1']);

        try {
            $user = $request->user('sanctum');
            if (!$user) {
                return response()->json(['data' => ['status' => 'success', 'message' => 'Virtual payout processed.']]);
            }

            $wallet = $user->wallet()->first();
            if (!$wallet || $wallet->balance < $request->amount) {
                return response()->json(['message' => 'Insufficient balance'], 400);
            }

            $wallet->balance -= $request->amount;
            $wallet->save();

            Transaction::create([
                'user_id' => $user->id,
                'reference' => 'PAYOUT-' . time() . '-' . rand(1000, 9999),
                'type' => 'payout',
                'amount' => $request->amount,
                'currency' => 'GHS',
                'status' => 'pending',
                'gateway' => 'internal',
            ]);

            return response()->json([
                'data' => [
                    'status' => 'success',
                    'message' => 'Payout requested successfully. Funds will arrive shortly.'
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Could not process payout'], 500);
        }
    }
}
