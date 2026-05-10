<?php

namespace App\Modules\Logistics\Services;

use App\Modules\Logistics\Models\RideRequest;
use App\Modules\Logistics\Models\SafetyAlert;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SafetyGuardService
{
    /**
     * Audit a ride request for potential fraud using heuristics.
     */
    public function performFraudAudit(RideRequest $ride): void
    {
        $riskScore = 0;
        $anomalies = [];

        // 1. Account Age Heuristic
        $customer = $ride->customer;
        if ($customer && $customer->created_at->diffInHours() < 2) {
            $riskScore += 30;
            $anomalies[] = 'NEW_ACCOUNT';
        }

        // 2. Cancellation Velocity
        $cancellations = RideRequest::where('customer_id', $ride->customer_id)
            ->where('status', 'cancelled')
            ->where('created_at', '>', now()->subHours(24))
            ->count();
        if ($cancellations > 5) {
            $riskScore += 40;
            $anomalies[] = 'HIGH_CANCELLATION_VELOCITY';
        }

        // 3. Distance Anomaly (Pickup vs Current Location)
        // If pickup is > 50km from user's current GPS at booking (optional check)

        if ($riskScore >= 70) {
            $this->triggerAlert($ride, 'FRAUD', 'HIGH', [
                'risk_score' => $riskScore,
                'anomalies'  => $anomalies
            ]);
        }
    }

    /**
     * Monitor real-time telemetry for route deviations.
     */
    public function monitorTelemetry(RideRequest $ride, float $currentLat, float $currentLng): void
    {
        if ($ride->status !== 'in_progress' || empty($ride->polyline)) {
            return;
        }

        $points = is_string($ride->polyline) ? json_decode($ride->polyline, true) : $ride->polyline;
        if (empty($points)) return;

        // Calculate distance from current point to the nearest point in the polyline
        $minDistance = 999999;
        foreach ($points as $point) {
            $dist = $this->haversine($currentLat, $currentLng, $point[0], $point[1]);
            if ($dist < $minDistance) $minDistance = $dist;
        }

        // Thresholds: Urban (400m), Rural (800m)
        $threshold = 0.4; // 400 meters
        
        if ($minDistance > $threshold) {
            $this->triggerAlert($ride, 'DEVIATION', 'MEDIUM', [
                'deviation_km' => round($minDistance, 3),
                'threshold_km' => $threshold,
                'location'     => ['lat' => $currentLat, 'lng' => $currentLng]
            ]);
        }
    }

    /**
     * Create and broadcast a safety alert.
     */
    public function triggerAlert(RideRequest $ride, string $type, string $severity, array $metadata): void
    {
        // Prevent duplicate spam alerts for same ride/type in short interval
        $exists = SafetyAlert::where('ride_id', $ride->id)
            ->where('type', $type)
            ->where('status', 'PENDING')
            ->where('created_at', '>', now()->subMinutes(5))
            ->exists();

        if ($exists) return;

        $alert = SafetyAlert::create([
            'ride_id'  => $ride->id,
            'type'     => $type,
            'severity' => $severity,
            'metadata' => $metadata,
            'status'   => 'PENDING'
        ]);

        // Real-time broadcast to Safety HUD
        event(new \App\Events\Logistics\SafetyAlertTriggered([
            'id'       => $alert->id,
            'ride_id'  => $ride->id,
            'type'     => $type,
            'severity' => $severity,
            'metadata' => $metadata,
            'customer_name' => $ride->customer?->name,
            'driver_name'   => $ride->driver?->user?->name,
            'created_at'    => $alert->created_at->toIso8601String()
        ]));

        Log::warning("WADEX-Guard Alert [{$type}]: Ride {$ride->id} - {$severity} Severity.");
    }

    private function haversine(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadius = 6371;
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
        $a = sin($dLat / 2) ** 2 + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) ** 2;
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $earthRadius * $c;
    }
}
