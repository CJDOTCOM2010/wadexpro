<?php

namespace App\Modules\Logistics\Controllers\Admin;

use App\Core\Traits\ApiResponse;
use App\Modules\Logistics\Models\Referral;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class AdminReferralController extends Controller
{
    use ApiResponse;

    /**
     * Get high-level referral metrics.
     */
    public function getMetrics()
    {
        $totalReferrals = Referral::count();
        $successfulReferrals = Referral::where('status', 'rewarded')->count();
        $pendingReferrals = Referral::where('status', 'pending')->count();
        
        // At GH₵10 per side, total value is successful * 20
        // If config driven, this would pull from a settings table
        $totalValueGiven = $successfulReferrals * 20.00;

        return $this->success([
            'total_referrals' => $totalReferrals,
            'successful_referrals' => $successfulReferrals,
            'pending_referrals' => $pendingReferrals,
            'conversion_rate' => $totalReferrals > 0 ? round(($successfulReferrals / $totalReferrals) * 100, 1) : 0,
            'total_value_given' => $totalValueGiven
        ]);
    }

    /**
     * Get recently rewarded referrals.
     */
    public function getRecentConversions(Request $request)
    {
        $limit = $request->input('limit', 10);
        
        $conversions = Referral::with(['referrer:id,name,email', 'referred:id,name,email'])
            ->where('status', 'rewarded')
            ->orderBy('updated_at', 'desc')
            ->limit($limit)
            ->get();

        return $this->success($conversions);
    }
}
