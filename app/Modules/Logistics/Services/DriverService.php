<?php

namespace App\Modules\Logistics\Services;

use App\Modules\Logistics\Models\Driver;
use App\Modules\Logistics\Models\Order;
use App\Modules\Logistics\Models\TrackingEvent;
use App\Modules\Logistics\Jobs\ProcessGpsPing;

class DriverService
{
    /**
     * Find the closest available drivers to a latitude/longitude.
     */
    public function findNearbyAvailableDrivers(float $lat, float $lng, int $limit = 5): \Illuminate\Database\Eloquent\Collection
    {
        // Use Redis GEORADIUS for massive scale spatial search (in milliseconds)
        $results = \Illuminate\Support\Facades\Redis::georadius(
            'drivers:locations', 
            $lng, 
            $lat, 
            30, // 30km default search radius
            'km', 
            'ASC'
        );

        if (empty($results)) {
            return new \Illuminate\Database\Eloquent\Collection();
        }

        $driversList = Driver::whereIn('id', $results)
            ->where('is_available', true)
            ->where('is_online', true)
            ->where('status', 'active')
            // Don't assign to drivers already doing an active run unless stacking is enabled
            ->whereDoesntHave('activeOrder')
            ->get();
            
        // Re-sort the SQL results to match the precise distance-ordering from Redis
        $sortedDrivers = $driversList->sortBy(function($model) use ($results) {
            return array_search($model->id, $results);
        });

        // Convert back to Eloquent Collection
        return new \Illuminate\Database\Eloquent\Collection($sortedDrivers->take($limit)->values()->all());
    }

    /**
     * Dispatch an order to a driver.
     */
    public function assignOrderToDriver(Order $order, Driver $driver): bool
    {
        if ($order->status !== 'pending' && $order->status !== 'searching') {
            throw new \Exception('Order is not in a states that allows assignment.');
        }

        $order->update([
            'driver_id' => $driver->id,
            'status' => 'assigned',
            'assigned_at' => now(),
        ]);

        $driver->update([
            'is_available' => false // Off the market until delivery is complete
        ]);

        // Broadcast DriverAssignedEvent...
        
        return true;
    }

    /**
     * Record a raw GPS ping from the driver's device.
     */
    public function recordTrackingEvent(Driver $driver, array $data): void
    {
        // Offload the expensive historical record to the background
        ProcessGpsPing::dispatch([
            'order_id'    => $driver->activeOrder?->id,
            'driver_id'   => $driver->id,
            'lat'         => $data['lat'],
            'lng'         => $data['lng'],
            'speed_kmh'   => $data['speed_kmh'] ?? 0.0,
            'bearing'     => $data['bearing'] ?? 0.0,
            'metadata'    => $data['metadata'] ?? null,
            'recorded_at' => now(),
        ]);

        // Phase 24 Optimization: Store hot location strictly in Redis RAM
        // This removes the synchronous SQL UPDATE, eliminating write-locks on the drivers table
        \Illuminate\Support\Facades\Redis::geoAdd('drivers:locations', $data['lng'], $data['lat'], $driver->id);

        // Broadcast to redis so Node.js socket server can relay to customers watching the map...
        event(new \App\Modules\Logistics\Events\DriverLocationUpdated(
            $driver->id,
            (float) $data['lat'],
            (float) $data['lng'],
            $driver->activeOrder?->id
        ));
    }
}
