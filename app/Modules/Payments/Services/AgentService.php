<?php

namespace App\Modules\Payments\Services;

use App\Modules\Logistics\Models\RideRequest;
use App\Modules\Payments\Models\Wallet;
use App\Modules\Payments\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AgentService
{
    /**
     * Perform a cash top-up for a user at a physical office.
     */
    public function topUpUserWithCash(User $agent, User $customer, float $amount)
    {
        return DB::transaction(function () use ($agent, $customer, $amount) {
            $wallet = Wallet::firstOrCreate(
                ['user_id' => $customer->id],
                ['balance' => 0, 'currency' => 'GHS']
            );

            $wallet->increment('balance', $amount);

            Transaction::create([
                'user_id' => $customer->id,
                'wallet_id' => $wallet->id,
                'amount' => $amount,
                'type' => 'wallet_topup',
                'payment_method' => 'CASH',
                'status' => 'completed',
                'metadata' => [
                    'agent_id' => $agent->id,
                    'is_offline' => true,
                    'office_topup' => true
                ]
            ]);

            return $wallet;
        });
    }

    /**
     * Book a ride on behalf of a guest or existing customer.
     */
    public function bookOnBehalf(User $agent, array $details)
    {
        // Integration with RideMatchingService would go here
        // This is a specialized booking for walk-in clients
        return [
            'status' => 'success',
            'ride_id' => 'OFFLINE-' . uniqid(),
            'agent_id' => $agent->id
        ];
    }
}
