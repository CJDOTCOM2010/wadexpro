<?php

namespace App\Modules\Logistics\Controllers;

use App\Core\Traits\ApiResponse;
use App\Http\Controllers\Controller;
use App\Modules\Logistics\Models\Transaction;
use App\Modules\Logistics\Models\RideRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DriverFinancialController extends Controller
{
    use ApiResponse;

    /**
     * Get daily earnings for the last 7 days.
     */
    public function weeklySummary(Request $request)
    {
        $user = $request->user();
        $startDate = Carbon::now()->subDays(6)->startOfDay();

        $earnings = Transaction::where('user_id', $user->id)
            ->where('type', 'earning')
            ->where('created_at', '>=', $startDate)
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(amount) as amount'),
                DB::raw('COUNT(*) as trip_count')
            )
            ->groupBy('date')
            ->orderBy('date', 'ASC')
            ->get();

        // Fill missing days with zero
        $summary = [];
        for ($i = 0; $i < 7; $i++) {
            $date = Carbon::now()->subDays(6 - $i)->format('Y-m-d');
            $dayData = $earnings->firstWhere('date', $date);
            
            $summary[] = [
                'day' => Carbon::parse($date)->format('D'),
                'date' => $date,
                'amount' => $dayData ? (float) $dayData->amount : 0.0,
                'trip_count' => $dayData ? (int) $dayData->trip_count : 0,
            ];
        }

        return $this->success([
            'days' => $summary,
            'total_weekly' => array_sum(array_column($summary, 'amount')),
            'average_daily' => array_sum(array_column($summary, 'amount')) / 7
        ], 'Weekly financial summary retrieved.');
    }

    /**
     * Submit a payout / withdrawal request.
     */
    public function requestPayout(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:50',
        ]);

        $user = $request->user();
        $wallet = $user->wallet;

        if (!$wallet || $wallet->balance < $request->amount) {
            return $this->error('Insufficient balance for withdrawal.', 400);
        }

        return DB::transaction(function () use ($user, $wallet, $request) {
            // Debit the wallet (marked as pending payout)
            // In a real system, we might "freeze" the amount instead
            $wallet->balance -= $request->amount;
            $wallet->save();

            $tx = Transaction::create([
                'reference' => 'WDX_PO_' . strtoupper(\Illuminate\Support\Str::random(10)),
                'user_id' => $user->id,
                'type' => 'payout',
                'amount' => $request->amount,
                'currency' => $wallet->currency,
                'status' => 'pending',
                'description' => 'Withdrawal request',
                'processed_at' => null,
            ]);

            return $this->success($tx, 'Payout request submitted and is under review.');
        });
    }
}
