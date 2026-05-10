<?php

namespace App\Modules\Logistics\Controllers;

use App\Core\Traits\ApiResponse;
use App\Modules\Logistics\Services\PayoutService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use App\Modules\Logistics\Models\Driver;

class PayoutController extends Controller
{
    use ApiResponse;

    public function __construct(private PayoutService $payoutService)
    {
    }

    /**
     * Admin endpoints to fetch all generated payouts.
     */
    public function index()
    {
        $payouts = DB::table('driver_payouts')->latest('created_at')->paginate(20);
        return $this->paginated($payouts, 'Payouts retrieved.');
    }

    /**
     * Run the calculation aggregator for a specific driver.
     */
    public function calculate(Request $request)
    {
        $validated = $request->validate([
            'driver_id' => 'required|uuid|exists:drivers,id',
        ]);

        $driver = Driver::findOrFail($validated['driver_id']);
        $result = $this->payoutService->calculateDriverPayout($driver);

        if (!$result['success']) {
            return $this->error($result['message'], 400);
        }

        return $this->success($result, 'Payout generated.');
    }

    /**
     * Execute/Mark a payout as complete (bank transfer done).
     */
    public function execute(string $payoutId)
    {
        $success = $this->payoutService->executePayout($payoutId);
        
        if (!$success) {
            return $this->error('Payout could not be executed or was already processed.', 400);
        }

        return $this->success(null, 'Payout marked as complete successfully.');
    }
}
