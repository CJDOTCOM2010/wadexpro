<?php

namespace App\Modules\Logistics\Services;

use App\Modules\Logistics\Models\Driver;
use App\Modules\Logistics\Models\Order;
use Illuminate\Support\Facades\DB;

class PayoutService
{
    /**
     * Settle payouts for a driver for a specific period.
     * By default, it settles all un-payout'd, delivered orders.
     */
    public function calculateDriverPayout(Driver $driver, ?string $startDate = null, ?string $endDate = null): array
    {
        $query = Order::where('driver_id', $driver->id)
                      ->where('status', 'delivered')
                      ->whereNull('payout_id'); // payout_id ties order to a specific payout receipt

        if ($startDate) {
            $query->whereDate('delivered_at', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('delivered_at', '<=', $endDate);
        }

        $orders = $query->get();
        if ($orders->isEmpty()) {
            return [
                'success' => false,
                'message' => 'No unsettled orders found for this driver in the specified period.',
            ];
        }

        // Logic for payout calculation. E.g., Driver gets 80% of the delivery fee, no cut from package cost.
        $totalDeliveryFee = $orders->sum('delivery_fee');
        $commissionRate = 0.80; // 80% to driver
        
        $payoutAmount = $totalDeliveryFee * $commissionRate;
        $commissionAmount = $totalDeliveryFee - $payoutAmount;

        // DB Transaction to lock order updates and create payout record
        return DB::transaction(function () use ($driver, $orders, $payoutAmount, $totalDeliveryFee, $commissionAmount) {
            $payout = DB::table('driver_payouts')->insertGetId([
                'id' => \Illuminate\Support\Str::uuid(),
                'driver_id' => $driver->id,
                'period_start' => $orders->min('delivered_at'),
                'period_end' => $orders->max('delivered_at'),
                'gross_amount' => $totalDeliveryFee,
                'commission_rate' => 80.00,
                'commission_amount' => $commissionAmount,
                'net_amount' => $payoutAmount,
                'currency' => $orders->first()->currency ?? 'GHS',
                'status' => 'pending', // Requires admin approval / actual bank transfer
                'total_deliveries' => $orders->count(),
                'metadata' => json_encode([
                    'total_gross_delivery_fees' => $totalDeliveryFee,
                    'commission_rate' => '80%',
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Retrieve the full UUID since insertGetId on UUID PKs behaves poorly sometimes in Laravel
            $payoutId = DB::table('driver_payouts')->where('driver_id', $driver->id)->latest('created_at')->value('id');

            // Tag orders
            Order::whereIn('id', $orders->pluck('id'))->update(['payout_id' => $payoutId]);

            return [
                'success' => true,
                'message' => 'Payout initiated.',
                'payout_id' => $payoutId,
                'amount' => $payoutAmount,
                'orders_count' => $orders->count()
            ];
        });
    }

    /**
     * Confirms the payout execution via integration with external bank/MM API.
     */
    public function executePayout(string $payoutId, string $referenceProcessor = 'manual'): bool
    {
        $updated = DB::table('driver_payouts')
            ->where('id', $payoutId)
            ->where('status', 'pending')
            ->update([
                'status' => 'completed',
                'processed_at' => now(),
                'reference' => 'WDX_PAYOUT_' . strtoupper(uniqid())
            ]);

        return $updated > 0;
    }
}
