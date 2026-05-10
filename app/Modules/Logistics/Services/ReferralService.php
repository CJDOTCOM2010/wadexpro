<?php

namespace App\Modules\Logistics\Services;

use App\Modules\Logistics\Models\Order;
use App\Modules\Payments\Models\Referral;
use App\Modules\Logistics\Models\PromoCode;
use Illuminate\Support\Str;

class ReferralService
{
    /**
     * Checks if a user's referral should be rewarded upon order completion.
     */
    public function processOrderCompletion(Order $order): void
    {
        // 1. Is this the customer's first completed ride?
        $completedRidesCount = Order::where('customer_id', $order->customer_id)
            ->where('status', 'delivered')
            ->count();

        // If they have exactly 1 completed ride (the one just finished)
        if ($completedRidesCount === 1) {
            $this->rewardCustomerReferral($order->customer_id);
        }

        // 2. Future expansion: Driver referral checking (e.g. at 5th order)
        if ($order->driver_id) {
            $driverDeliveries = Order::where('driver_id', $order->driver_id)
                ->where('status', 'delivered')
                ->count();

            if ($driverDeliveries === 5) {
                // Reward driver referral (To be implemented or handled through driver setup)
            }
        }
    }

    protected function rewardCustomerReferral(string $userId): void
    {
        $referral = Referral::where('referee_id', $userId)
            ->where('status', 'PENDING')
            ->first();

        if (!$referral) {
            return;
        }

        // Generate Promo Code for the Referrer (GH₵10)
        $referrerPromo = PromoCode::create([
            'code' => 'REF-' . strtoupper(Str::random(6)),
            'description' => 'Referral Bonus (You referred someone!)',
            'type' => 'fixed',
            'value' => 10.00,
            'currency' => 'GHS',
            'max_uses' => 1,
            'max_uses_per_user' => 1,
            'times_used' => 0,
            'is_active' => true,
            'expires_at' => now()->addDays(30),
        ]);

        // Generate Promo Code for the newly Referred User (GH₵10)
        $referredPromo = PromoCode::create([
            'code' => 'WEL-' . strtoupper(Str::random(6)),
            'description' => 'Referral Bonus (Welcome to WadeX!)',
            'type' => 'fixed',
            'value' => 10.00,
            'currency' => 'GHS',
            'max_uses' => 1,
            'max_uses_per_user' => 1,
            'times_used' => 0,
            'is_active' => true,
            'expires_at' => now()->addDays(30),
        ]);

        // Mark as rewarded
        $referral->update([
            'status' => 'COMPLETED',
            'reward_amount' => 10.00,
            'completed_at' => now(),
        ]);

        // Dispatch Event for real-time notifications
        event(new \App\Events\Logistics\ReferralRewardProcessed($referral->load(['inviter', 'referee'])));
    }
}
