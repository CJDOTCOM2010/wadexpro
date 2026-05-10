<?php

namespace App\Modules\Logistics\Observers;

use App\Modules\Logistics\Models\RideRequest;
use App\Modules\Logistics\Services\WalletService;
use Illuminate\Support\Facades\Log;

class RideRequestObserver
{
    /**
     * Handle the RideRequest "updated" event.
     */
    public function updated(RideRequest $ride): void
    {
        // If status changed to completed, trigger automated settlement
        if ($ride->wasChanged('status') && $ride->status === 'completed' && $ride->payment_status !== 'settled') {
            try {
                $walletService = app(WalletService::class);
                $walletService->settleRide($ride);
                Log::info('Automated ride settlement processed for Ride ID: ' . $ride->id);
            } catch (\Exception $e) {
                Log::error('Automated ride settlement FAILED for Ride ID: ' . $ride->id . ' - Error: ' . $e->getMessage());
                $ride->update(['payment_status' => 'failed']);
            }
        }
    }
}
