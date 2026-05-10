<?php

namespace App\Modules\Logistics\Services;

use App\Modules\Logistics\Models\Driver;
use App\Modules\Logistics\Models\TrackingEvent;
use Illuminate\Support\Facades\Log;

class AntifraudService
{
    /**
     * Thresholds for GPS plausibility checks
     */
    private const MAX_SPEED_KMH = 140.0; // Max reasonable commercial vehicle speed
    private const MAX_DISTANCE_CHANGE_THRESHOLD = 5.0; // km per update (at 60s interval)

    /**
     * Validate a GPS update against historical data to detect spoofing or 'teleportation'.
     */
    public function validateGpsUpdate(Driver $driver, float $newLat, float $newLng, ?float $speedKmh = null): array
    {
        if (!$driver->current_lat || !$driver->current_lng) {
            return ['valid' => true, 'reason' => 'initial_ping'];
        }

        $timeSinceLastPing = $driver->last_location_at ? now()->diffInSeconds($driver->last_location_at) : 0;
        
        // 1. Basic Speed Check (if reported by device)
        if ($speedKmh !== null && $speedKmh > self::MAX_SPEED_KMH) {
            return ['valid' => false, 'reason' => 'excessive_speed_reported', 'details' => "Speed: {$speedKmh}km/h"];
        }

        // 2. Teleportation / Distance-Time Plausibility Check
        $distanceKm = $this->calculateDistance(
            $driver->current_lat, 
            $driver->current_lng, 
            $newLat, 
            $newLng
        );

        if ($timeSinceLastPing > 0) {
            $calculatedSpeed = ($distanceKm / ($timeSinceLastPing / 3600));
            
            if ($calculatedSpeed > self::MAX_SPEED_KMH && $distanceKm > 0.5) {
                Log::warning("Antifraud Triggered: Driver {$driver->id} suspected of GPS spoofing.", [
                    'distance' => $distanceKm,
                    'time_gap' => $timeSinceLastPing,
                    'calculated_speed' => $calculatedSpeed
                ]);
                return ['valid' => false, 'reason' => 'teleportation_detected', 'details' => "Speed: {$calculatedSpeed}km/h over {$distanceKm}km"];
            }
        }

        return ['valid' => true, 'reason' => 'passed'];
    }

    private function calculateDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371; // km
        $latFrom = deg2rad($lat1);
        $lonFrom = deg2rad($lon1);
        $latTo = deg2rad($lat2);
        $lonTo = deg2rad($lon2);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
            cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));

        return $angle * $earthRadius;
    }
}
