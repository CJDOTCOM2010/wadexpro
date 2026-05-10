<?php

namespace App\Modules\Logistics\Services;

use App\Modules\Logistics\Models\Driver;
use App\Modules\Logistics\Models\RideRequest;
use App\Modules\Logistics\Events\RideRequested;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Redis;

class RideMatchingService
{
    /**
     * Maximum radius (km) to search for nearby drivers.
     */
    private const MAX_SEARCH_RADIUS_KM = 10.0;

    /**
     * Number of drivers to notify per matching round.
     */
    private const DRIVERS_PER_ROUND = 5;

    /**
     * Redis key prefix for driver geospatial index.
     */
    private const GEO_KEY = 'drivers:geo:locations';

    public function __construct(
        private PricingService $pricingService
    ) {}

    /**
     * Find the nearest available drivers to a given coordinate.
     * Uses Redis geospatial index for O(logN) lookups.
     * Falls back to database query if Redis is unavailable.
     *
     * @return Collection<int, Driver>
     */
    public function findNearbyDrivers(
        float $lat,
        float $lng,
        string $vehicleType = 'economy',
        float $radiusKm = self::MAX_SEARCH_RADIUS_KM,
        int $limit = self::DRIVERS_PER_ROUND
    ): Collection {
        try {
            return $this->findViaRedisGeo($lat, $lng, $vehicleType, $radiusKm, $limit);
        } catch (\Exception $e) {
            return $this->findViaDatabase($lat, $lng, $vehicleType, $radiusKm, $limit);
        }
    }

    /**
     * Update a driver's position in the Redis geospatial index.
     */
    public function updateDriverLocation(string $driverId, float $lat, float $lng): void
    {
        try {
            Redis::geoadd(self::GEO_KEY, $lng, $lat, $driverId);
        } catch (\Exception $e) {
            // Gracefully degrade — the database fallback will handle queries
            report($e);
        }
    }

    /**
     * Remove a driver from the geospatial index (went offline).
     */
    public function removeDriverLocation(string $driverId): void
    {
        try {
            Redis::zrem(self::GEO_KEY, $driverId);
        } catch (\Exception $e) {
            report($e);
        }
    }

    /**
     * Dispatch a ride request to nearby drivers.
     * Returns the list of notified driver IDs.
     *
     * @return array<string>
     */
    public function dispatchToNearbyDrivers(RideRequest $rideRequest): array
    {
        $drivers = $this->findNearbyDrivers(
            $rideRequest->pickup_lat,
            $rideRequest->pickup_lng,
            $rideRequest->vehicle_type,
        );

        if ($drivers->isEmpty()) {
            $rideRequest->update(['status' => 'searching']);
            return [];
        }

        $rideRequest->update(['status' => 'searching']);

        $notifiedDriverIds = [];
        foreach ($drivers as $driver) {
            $notifiedDriverIds[] = (string) $driver->id;
        }

        // Broadcast the request to identified drivers (Real-time WebSockets)
        if (!empty($notifiedDriverIds)) {
            event(new RideRequested($rideRequest, $notifiedDriverIds));

            // High-Priority Mobile Push (FCM)
            $fcmService = app(\App\Modules\Notifications\Services\FcmService::class);
            $fcmService->broadcastToDrivers($drivers, 'New Ride Request!', 'A passenger is looking for a ride nearby.', [
                'ride_id' => $rideRequest->id,
                'type'    => 'new_ride_request'
            ]);
        }

        return $notifiedDriverIds;
    }

    /**
     * Assign a specific driver to a ride request.
     */
    public function assignDriver(RideRequest $rideRequest, Driver $driver): RideRequest
    {
        $rideRequest->update([
            'driver_id' => $driver->id,
            'status'    => 'driver_assigned',
        ]);

        $driver->update([
            'is_available' => false,
        ]);

        return $rideRequest->fresh();
    }

    /**
     * Find drivers using Redis GEORADIUS command.
     */
    private function findViaRedisGeo(
        float $lat,
        float $lng,
        string $vehicleType,
        float $radiusKm,
        int $limit
    ): Collection {
        $results = Redis::georadius(
            self::GEO_KEY,
            $lng,
            $lat,
            $radiusKm,
            'km',
            'WITHCOORD',
            'WITHDIST',
            'ASC',
            'COUNT',
            $limit * 3
        );

        if (empty($results)) {
            return collect();
        }

        $driverIds = array_map(function ($item) {
            return is_array($item) ? $item[0] : $item;
        }, $results);

        return Driver::query()
            ->whereIn('id', $driverIds)
            ->where('is_online', true)
            ->where('is_available', true)
            ->where('status', 'active')
            ->whereHas('activeVehicle', function ($query) use ($vehicleType) {
                $query->where('type', $this->mapVehicleType($vehicleType));
            })
            ->with('user:id,name,phone,avatar_url')
            ->with('activeVehicle:id,driver_id,plate_number,type,make,model,color')
            ->limit($limit)
            ->get();
    }

    /**
     * Fallback: find drivers using Haversine formula in SQL.
     */
    private function findViaDatabase(
        float $lat,
        float $lng,
        string $vehicleType,
        float $radiusKm,
        int $limit
    ): Collection {
        $earthRadiusKm = 6371;

        return Driver::query()
            ->selectRaw("
                drivers.*,
                ({$earthRadiusKm} * acos(
                    cos(radians(?)) * cos(radians(current_lat)) *
                    cos(radians(current_lng) - radians(?)) +
                    sin(radians(?)) * sin(radians(current_lat))
                )) AS distance_km
            ", [$lat, $lng, $lat])
            ->where('is_online', true)
            ->where('is_available', true)
            ->where('status', 'active')
            ->whereNotNull('current_lat')
            ->whereNotNull('current_lng')
            ->whereHas('activeVehicle', function ($query) use ($vehicleType) {
                $query->where('type', $this->mapVehicleType($vehicleType));
            })
            ->having('distance_km', '<=', $radiusKm)
            ->orderBy('distance_km')
            ->with('user:id,name,phone,avatar_url')
            ->with('activeVehicle:id,driver_id,plate_number,type,make,model,color')
            ->limit($limit)
            ->get();
    }

    /**
     * Map ride vehicle type to vehicle table type.
     */
    private function mapVehicleType(string $rideType): string
    {
        return match ($rideType) {
            'moto'    => 'motorcycle',
            'economy' => 'car',
            'comfort' => 'car',
            'xl'      => 'van',
            default   => 'car',
        };
    }
}
