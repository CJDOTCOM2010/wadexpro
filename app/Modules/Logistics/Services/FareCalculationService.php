<?php

namespace App\Modules\Logistics\Services;

use App\Modules\Logistics\Models\RideRequest;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class FareCalculationService
{
    /**
     * Vehicle type pricing configuration.
     * In production, these are loaded from system_settings table.
     *
     * @var array<string, array{base_fare: float, per_km: float, per_minute: float, minimum_fare: float, booking_fee: float}>
     */
    public function __construct(
        private RegionService $regionService
    ) {
    }

    /**
     * Calculate a complete fare estimate for a ride.
     */
    public function estimate(
        float $pickupLat,
        float $pickupLng,
        float $dropoffLat,
        float $dropoffLng,
        string $vehicleType = 'economy',
        ?float $routeDistanceKm = null,
        ?int $routeDurationMinutes = null,
        ?string $promoCode = null
    ): array {
        // Find Region and Rates
        $region = $this->regionService->findRegionByCoords($pickupLat, $pickupLng);
        $rates = $region 
            ? $this->regionService->getRates($region->id, $vehicleType)
            : $this->getDefaultRates($vehicleType);

        $currency = $region ? $region->currency_code : 'GHS';

        // Use client-supplied route distance or fallback to Haversine with correction factor
        $distanceKm = $routeDistanceKm ?? $this->estimateRoadDistance($pickupLat, $pickupLng, $dropoffLat, $dropoffLng);
        $durationMinutes = $routeDurationMinutes ?? $this->estimateDuration($distanceKm);

        // Base fare components
        $baseFare = $rates['base_fare'];
        $distanceCharge = $distanceKm * $rates['per_km'];
        $timeCharge = $durationMinutes * $rates['per_minute'];
        $bookingFee = $rates['booking_fee'];

        $subtotal = $baseFare + $distanceCharge + $timeCharge + $bookingFee;

        // Surge pricing
        $surgeMultiplier = $this->getSurgeMultiplier($pickupLat, $pickupLng);
        $surgeAmount = ($surgeMultiplier > 1.0) ? $subtotal * ($surgeMultiplier - 1.0) : 0.0;

        $subtotalWithSurge = $subtotal + $surgeAmount;

        // Promo discount
        $discount = 0.0;
        if ($promoCode) {
            $discount = $this->calculatePromoDiscount($promoCode, $subtotalWithSurge);
        }

        // Enforce minimum fare
        $total = max($subtotalWithSurge - $discount, $rates['minimum_fare']);

        // Estimated driver arrival time (based on average nearby driver distance)
        $estimatedArrivalMinutes = $this->estimateDriverArrival($pickupLat, $pickupLng);

        return [
            'region_id'               => $region?->id,
            'vehicle_type'             => $vehicleType,
            'distance_km'             => round($distanceKm, 2),
            'duration_minutes'        => $durationMinutes,
            'base_fare'               => round($baseFare, 2),
            'distance_charge'         => round($distanceCharge, 2),
            'time_charge'             => round($timeCharge, 2),
            'booking_fee'             => round($bookingFee, 2),
            'surge_multiplier'        => round($surgeMultiplier, 2),
            'surge_amount'            => round($surgeAmount, 2),
            'subtotal'                => round($subtotalWithSurge, 2),
            'discount'                => round($discount, 2),
            'total'                   => round($total, 2),
            'currency'                => $currency,
            'estimated_arrival_minutes' => $estimatedArrivalMinutes,
        ];
    }

    /**
     * Calculate multi-vehicle estimates for the ride booking screen.
     *
     * @return array<string, array>
     */
    public function estimateAllVehicles(
        float $pickupLat,
        float $pickupLng,
        float $dropoffLat,
        float $dropoffLng,
        ?float $routeDistanceKm = null,
        ?int $routeDurationMinutes = null
    ): array {
        $estimates = [];

        foreach (array_keys($this->vehicleRates) as $vehicleType) {
            $estimates[$vehicleType] = $this->estimate(
                $pickupLat,
                $pickupLng,
                $dropoffLat,
                $dropoffLng,
                $vehicleType,
                $routeDistanceKm,
                $routeDurationMinutes
            );
        }

        return $estimates;
    }

    /**
     * Calculate the final fare for a completed ride using actual distance/time.
     */
    public function calculateFinalFare(RideRequest $ride, float $actualDistanceKm, int $actualDurationMinutes): float
    {
        $rates = $this->getRatesForVehicle($ride->vehicle_type);

        $baseFare = $rates['base_fare'];
        $distanceCharge = $actualDistanceKm * $rates['per_km'];
        $timeCharge = $actualDurationMinutes * $rates['per_minute'];
        $bookingFee = $rates['booking_fee'];

        $total = $baseFare + $distanceCharge + $timeCharge + $bookingFee;

        return round(max($total, $rates['minimum_fare']), 2);
    }

    /**
     * Get surge multiplier for a given location.
     */
    private function getSurgeMultiplier(float $lat, float $lng): float
    {
        $cacheKey = "surge:{$this->roundCoord($lat)}:{$this->roundCoord($lng)}";

        return Cache::remember($cacheKey, 120, function () use ($lat, $lng) {
            $zone = DB::table('surge_zones')
                ->where('is_active', true)
                ->whereRaw("
                    (6371 * acos(
                        cos(radians(?)) * cos(radians(center_lat)) *
                        cos(radians(center_lng) - radians(?)) +
                        sin(radians(?)) * sin(radians(center_lat))
                    )) <= radius_km
                ", [$lat, $lng, $lat])
                ->orderByRaw("
                    (6371 * acos(
                        cos(radians(?)) * cos(radians(center_lat)) *
                        cos(radians(center_lng) - radians(?)) +
                        sin(radians(?)) * sin(radians(center_lat))
                    ))
                ", [$lat, $lng, $lat])
                ->first();

            return $zone ? (float) $zone->current_multiplier : 1.0;
        });
    }

    /**
     * Calculate promo code discount.
     */
    private function calculatePromoDiscount(string $code, float $subtotal): float
    {
        $promo = DB::table('promo_codes')
            ->where('code', strtoupper($code))
            ->where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->where(function ($query) {
                $query->whereNull('starts_at')
                    ->orWhere('starts_at', '<=', now());
            })
            ->where(function ($query) {
                $query->whereNull('max_uses')
                    ->orWhereRaw('times_used < max_uses');
            })
            ->first();

        if (!$promo) {
            return 0.0;
        }

        if ($subtotal < $promo->min_order_amount) {
            return 0.0;
        }

        $discount = $promo->type === 'percentage'
            ? $subtotal * ($promo->value / 100)
            : $promo->value;

        if ($promo->max_discount) {
            $discount = min($discount, $promo->max_discount);
        }

        return round($discount, 2);
    }

    /**
     * Haversine with road distance correction factor (1.3x straight-line).
     */
    private function estimateRoadDistance(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadius = 6371;
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2) ** 2 + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) ** 2;
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        $straightLineKm = $earthRadius * $c;

        // Road correction factor: roads are typically 1.3x straight-line distance
        return $straightLineKm * 1.3;
    }

    /**
     * Estimate drive duration based on distance and average city speed.
     */
    private function estimateDuration(float $distanceKm): int
    {
        $avgCitySpeedKmh = 25;
        return (int) ceil(($distanceKm / $avgCitySpeedKmh) * 60);
    }

    /**
     * Estimate how many minutes until a driver can arrive at pickup.
     */
    private function estimateDriverArrival(float $lat, float $lng): int
    {
        $nearbyCount = DB::table('drivers')
            ->where('is_online', true)
            ->where('is_available', true)
            ->where('status', 'active')
            ->whereNotNull('current_lat')
            ->whereRaw("
                (6371 * acos(
                    cos(radians(?)) * cos(radians(current_lat)) *
                    cos(radians(current_lng) - radians(?)) +
                    sin(radians(?)) * sin(radians(current_lat))
                )) <= 5
            ", [$lat, $lng, $lat])
            ->count();

        return match (true) {
            $nearbyCount >= 10 => 2,
            $nearbyCount >= 5  => 4,
            $nearbyCount >= 2  => 6,
            $nearbyCount >= 1  => 10,
            default            => 15,
        };
    }

    /**
     * Round coordinate to 3 decimal places for cache key grouping.
     */
    private function roundCoord(float $coord): string
    {
        return number_format(round($coord, 3), 3);
    }

    /**
     * Get default rates if no region is detected.
     */
    private function getDefaultRates(string $vehicleType): array
    {
        $defaults = [
            'moto' => [
                'base_fare'    => 2.00,
                'per_km'       => 0.80,
                'per_minute'   => 0.10,
                'minimum_fare' => 3.00,
                'booking_fee'  => 0.50,
            ],
            'economy' => [
                'base_fare'    => 4.00,
                'per_km'       => 1.50,
                'per_minute'   => 0.15,
                'minimum_fare' => 5.00,
                'booking_fee'  => 1.00,
            ]
        ];

        return $defaults[$vehicleType] ?? $defaults['economy'];
    }
}
