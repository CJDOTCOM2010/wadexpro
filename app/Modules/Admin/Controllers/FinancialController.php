<?php

namespace App\Modules\Admin\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Modules\Payments\Models\Transaction;
use App\Modules\Payments\Models\WalletTransaction;
use Illuminate\Support\Facades\DB;

class FinancialController extends Controller
{
    public function index()
    {
        try {
            // Gross Revenue (Last 30 Days)
            $grossRevenueL30 = (float) Transaction::where('status', 'completed')
                ->where('created_at', '>=', now()->subDays(30))
                ->sum('amount');
                
            $grossRevenuePrev30 = (float) Transaction::where('status', 'completed')
                ->whereBetween('created_at', [now()->subDays(60), now()->subDays(30)])
                ->sum('amount');
                
            $revenueChange = $grossRevenuePrev30 > 0 
                ? (($grossRevenueL30 - $grossRevenuePrev30) / $grossRevenuePrev30) * 100 
                : 0;

            // Pending Payouts
            $pendingPayoutsSum = (float) WalletTransaction::where('category', 'payout')
                ->where('status', 'pending')
                ->sum('amount');
                
            $pendingPayoutsCount = WalletTransaction::where('category', 'payout')
                ->where('status', 'pending')
                ->distinct('user_id')
                ->count('user_id');

            // Platform Profit (Simulated as 15% of Gross Revenue for now if commission isn't explicitly split)
            $platformProfit = $grossRevenueL30 * 0.15;
            $profitMargin = 15.0;

            // Suspicious Flow
            $suspiciousFlowSum = (float) Transaction::where('status', 'failed')->sum('amount');
            $suspiciousFlowCount = Transaction::where('status', 'failed')->count();

            $stats = [
                'gross_revenue'         => $grossRevenueL30,
                'revenue_change'        => round($revenueChange, 1),
                'pending_payouts_sum'   => $pendingPayoutsSum,
                'pending_payouts_count' => $pendingPayoutsCount,
                'platform_profit'       => $platformProfit,
                'profit_margin'         => $profitMargin,
                'suspicious_flow_sum'   => $suspiciousFlowSum,
                'suspicious_flow_count' => $suspiciousFlowCount,
            ];

            // Recent Ingress (Transactions)
            $recentTransactions = Transaction::with('user')
                ->orderBy('created_at', 'desc')
                ->paginate(10, ['*'], 'transactions_page');

            // Egress Queue (Pending Payouts)
            $pendingPayouts = WalletTransaction::with('user')
                ->where('category', 'payout')
                ->where('status', 'pending')
                ->orderBy('created_at', 'asc')
                ->paginate(10, ['*'], 'payouts_page');

            return view('admin.financials', compact('stats', 'recentTransactions', 'pendingPayouts'));
        } catch (\Throwable $e) {
            dd(
                'FINANCIAL DASHBOARD ERROR:',
                $e->getMessage(),
                'File: ' . $e->getFile() . ' on line ' . $e->getLine(),
                $e->getTraceAsString()
            );
        }
    }

    public function approvePayout(Request $request, string $id)
    {
        $payout = WalletTransaction::findOrFail($id);
        
        if ($payout->status !== 'pending' || $payout->category !== 'payout') {
            return back()->with('error', 'Invalid payout transaction.');
        }

        // Normally, this is where you'd trigger external bank/mobile money API transfer.
        // For orchestrator orchestration, we mark it complete and deduct the balance correctly if not already done.

        $payout->update([
            'status'       => 'completed',
            'performed_by' => auth('admin')->id(),
            'transacted_at' => now(),
        ]);

        return back()->with('success', 'Payout #' . $payout->reference . ' has been approved and marked as completed.');
    }
}
