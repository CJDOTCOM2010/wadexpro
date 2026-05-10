<?php

namespace App\Modules\Logistics\Services;

class OptimizationService
{
    public function __construct(private PricingService $pricingService)
    {
    }

    /**
     * Reorders an array of stops to minimize total travel distance from the pickup point.
     * Implements a naive Nearest Neighbor algorithm for TSP.
     * 
     * In a production environment, this should bridge to OSRM Trip API or Google Maps Directions API.
     */
    /**
     * Reorders pending stops for an active order based on a starting GPS coordinate.
     * Useful for mid-route re-dispatching.
     */
    public function reOptimizeLiveOrder(\App\Modules\Logistics\Models\Order $order, float $startLat, float $startLng): void
    {
        $pendingStops = $order->stops()
            ->where('status', 'pending')
            ->get()
            ->toArray();

        if (count($pendingStops) <= 1) {
            return;
        }

        $optimized = $this->optimizeRoute($startLat, $startLng, $pendingStops);

        // Update sequences in database
        // Shift beginning sequence based on completed stops
        $completedCount = $order->stops()->where('status', '!=', 'pending')->count();
        
        foreach ($optimized as $index => $stopData) {
            \App\Modules\Logistics\Models\OrderStop::where('id', $stopData['id'])
                ->update(['sequence' => $completedCount + $index + 1]);
        }
    }

    public function optimizeRoute(float $pickupLat, float $pickupLng, array $stops): array
    {
        if (count($stops) <= 1) {
            return $stops;
        }

        $unvisited = $stops;
        $optimizedRoute = [];
        $currentLat = $pickupLat;
        $currentLng = $pickupLng;

        while (count($unvisited) > 0) {
            $nearestIndex = -1;
            $shortestDistance = PHP_FLOAT_MAX;

            foreach ($unvisited as $index => $stop) {
                // Determine distance from current coordinates to this stop
                $distance = $this->pricingService->calculateStraightLineDistance(
                    $currentLat, $currentLng, 
                    $stop['lat'], $stop['lng']
                );

                if ($distance < $shortestDistance) {
                    $shortestDistance = $distance;
                    $nearestIndex = $index;
                }
            }

            // Move to the nearest stop
            $nearestStop = $unvisited[$nearestIndex];
            $optimizedRoute[] = $nearestStop;
            
            // Set current lat/lng to this stop
            $currentLat = $nearestStop['lat'];
            $currentLng = $nearestStop['lng'];

            // Remove from unvisited list
            unset($unvisited[$nearestIndex]);
            // Re-key array to avoid gaps
            $unvisited = array_values($unvisited);
        }

        return $optimizedRoute;
    }
}
