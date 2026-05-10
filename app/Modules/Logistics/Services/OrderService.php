<?php

namespace App\Modules\Logistics\Services;

use App\Modules\Logistics\Models\Order;
use App\Modules\Logistics\Models\OrderStop;
use App\Modules\Notifications\Notifications\OrderStatusChangedNotification;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function __construct(
        private PricingService $pricingService,
        private ReferralService $referralService
    ) {
    }

    /**
     * Create a new logistics run (order).
     */
    public function createOrder(string $customerId, array $data, array $stops): Order
    {
        return DB::transaction(function () use ($customerId, $data, $stops) {
            
            // Calculate distance based on pickup and first dropoff (naive routing for MVP)
            $firstStop = $stops[0];
            $distanceKm = $this->pricingService->calculateStraightLineDistance(
                $data['pickup_lat'], $data['pickup_lng'],
                $firstStop['lat'], $firstStop['lng']
            );
            
            // Calculate pricing
            $pricing = $this->pricingService->calculate(
                $distanceKm, 
                $data['package_weight_kg'] ?? 1.0, 
                $data['priority'] ?? 'standard'
            );

            // Create Order
            $order = Order::create(array_merge([
                'customer_id' => $customerId,
                'status' => 'pending',
                'payment_status' => 'unpaid',
                'estimated_distance_km' => $distanceKm,
                'currency' => 'GHS'
            ], $data, $pricing));

            // Create Stops
            foreach ($stops as $index => $stop) {
                OrderStop::create(array_merge($stop, [
                    'order_id' => $order->id,
                    'sequence' => $index + 1,
                    'status' => 'pending'
                ]));
            }

            return $order;
        });
    }

    /**
     * Move order through lifecycle states: assigned -> picked_up -> in_transit -> delivered
     */
    public function updateStatus(Order $order, string $status): Order
    {
        $payload = ['status' => $status];

        switch ($status) {
            case 'picked_up':
                $payload['picked_up_at'] = now();
                break;
            case 'delivered':
                $payload['delivered_at'] = now();
                $payload['payment_status'] = 'settled';
                
                // Process any pending referral rewards
                $this->referralService->processOrderCompletion($order);

                // Automated Financial Settlement (Split 80/20)
                // We reuse the WalletService logic if applicable or implement specific courier splits
                try {
                    $walletService = app(\App\Modules\Logistics\Services\WalletService::class);
                    // For Courier, total_amount includes delivery_fee
                    // We might want to credit driver specific amounts.
                    $walletService->credit($order->driver->user, $order->total_amount * 0.85, 'delivery_earning', [
                        'order_id' => $order->id,
                        'reference' => $order->reference
                    ]);
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error("Order Settlement Failed for {$order->id}: " . $e->getMessage());
                }
                
                // If the order has a driver, put them back on the market
                if ($order->driver) {
                    $order->driver->update(['is_available' => true]);
                }
                break;
            case 'cancelled':
                $payload['cancelled_at'] = now();
                if ($order->driver) {
                    $order->driver->update(['is_available' => true]);
                }
                break;
        }

        $order->update($payload);

        // Broadcast OrderStopsUpdated event to listeners (Admin, Customer)
        event(new \App\Modules\Logistics\Events\OrderStopsUpdated($order));

        // Notify the customer about the status change
        if ($order->customer) {
            $order->customer->notify(new OrderStatusChangedNotification($order));
        }

        return $order;
    }

    /**
     * Calculate remaining distance for a multi-stop order for real-time ETA updates.
     */
    public function getRemainingDistance(Order $order, float $currentLat, float $currentLng): float
    {
        $pendingStops = $order->stops()->where('status', 'pending')->orderBy('sequence')->get();
        if ($pendingStops->isEmpty()) return 0;

        $totalDistance = 0;
        $lastLat = $currentLat;
        $lastLng = $currentLng;

        foreach ($pendingStops as $stop) {
            $totalDistance += $this->pricingService->calculateStraightLineDistance(
                $lastLat, $lastLng, $stop->lat, $stop->lng
            );
            $lastLat = $stop->lat;
            $lastLng = $stop->lng;
        }

        return $totalDistance;
    }
}
