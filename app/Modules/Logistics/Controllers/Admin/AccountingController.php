<?php

namespace App\Modules\Logistics\Controllers\Admin;

use App\Core\Traits\ApiResponse;
use App\Http\Controllers\Controller;
use App\Modules\Logistics\Models\Transaction;
use App\Modules\Logistics\Models\PlatformLedger;
use App\Modules\Logistics\Models\RideRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AccountingController extends Controller
{
    use ApiResponse;

    /**
     * Get a unified ledger of all financial movements.
     */
    public function ledger(Request $request)
    {
        $limit = $request->get('limit', 50);
        
        $transactions = Transaction::with('user:id,name,email')
            ->orderBy('created_at', 'DESC')
            ->paginate($limit);

        return $this->success($transactions, 'General ledger retrieved.');
    }

    /**
     * Get high-level revenue metrics for the financial dashboard.
     */
    public function revenueSummary()
    {
        $summary = [
            'gross_volume' => (float) RideRequest::where('status', 'completed')->sum('final_price'),
            'total_commissions' => (float) PlatformLedger::where('type', 'commission')->sum('amount'),
            'payout_liability' => (float) DB::table('wallets')
                ->join('drivers', 'wallets.user_id', '=', 'drivers.user_id')
                ->sum('wallets.balance'),
            'recent_revenue' => PlatformLedger::where('created_at', '>=', now()->subDays(7))
                ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(amount) as total'))
                ->groupBy('date')
                ->get()
        ];

        return $this->success($summary, 'Revenue summary retrieved.');
    }

    /**
     * Get platform earnings breakdown.
     */
    public function earningsBreakdown()
    {
        $breakdown = PlatformLedger::select('type', DB::raw('SUM(amount) as total'), DB::raw('COUNT(*) as count'))
            ->groupBy('type')
            ->get();

        return $this->success($breakdown, 'Earnings breakdown retrieved.');
    }
}
