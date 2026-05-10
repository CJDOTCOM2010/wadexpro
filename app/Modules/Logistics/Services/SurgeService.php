<?php

namespace App\Modules\Logistics\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class SurgeService
{
    /**
     * Calculate and update surge multiplier for a zone based on real-time supply/demand.
     */
    public function recalculateSurge(string $zoneId): float
    {
        return Cache::remember("surge_multiplier_{$zoneId}", 60, function () use ($zoneId) {
            $zone = DB::table('surge_zones')->where('id', $zoneId)->where('is_active', true)->first();

            if (!$zone) {
                return 1.0;
            }

            // Count active ride requests in this zone (demand)
            $demand = DB::table('ride_requests')
                ->whereIn('status', ['pending', 'searching'])
                ->whereRaw("
                    (6371 * acos(
                        cos(radians(?)) * cos(radians(pickup_lat)) *
                        cos(radians(pickup_lng) - radians(?)) +
                        sin(radians(?)) * sin(radians(pickup_lat))
                    )) <= ?
                ", [$zone->center_lat, $zone->center_lng, $zone->center_lat, $zone->radius_km])
                ->count();

            // Count available drivers in this zone (supply)
            $supply = DB::table('drivers')
                ->where('is_online', true)
                ->where('is_available', true)
                ->where('status', 'active')
                ->whereNotNull('current_lat')
                ->whereRaw("
                    (6371 * acos(
                        cos(radians(?)) * cos(radians(current_lat)) *
                        cos(radians(current_lng) - radians(?)) +
                        sin(radians(?)) * sin(radians(current_lat))
                    )) <= ?
                ", [$zone->center_lat, $zone->center_lng, $zone->center_lat, $zone->radius_km])
                ->count();

            // Find matching surge rule
            $rule = DB::table('surge_rules')
                ->where('surge_zone_id', $zoneId)
                ->where('is_active', true)
                ->where('demand_threshold', '<=', $demand)
                ->where('supply_threshold', '>=', $supply)
                ->orderByDesc('multiplier')
                ->first();

            $multiplier = $rule ? (float) $rule->multiplier : 1.0;

            // Clamp to zone limits
            $multiplier = max((float) $zone->min_multiplier, min($multiplier, (float) $zone->max_multiplier));

            // Update zone's current multiplier for reporting
            DB::table('surge_zones')
                ->where('id', $zoneId)
                ->update(['current_multiplier' => $multiplier, 'updated_at' => now()]);

            return $multiplier;
        });
    }

    /**
     * Find the best multiplier for a specific location.
     */
    public function getMultiplierAtLocation(float $lat, float $lng): float
    {
        $zone = DB::table('surge_zones')
            ->where('is_active', true)
            ->whereRaw("
                (6371 * acos(
                    cos(radians(?)) * cos(radians(center_lat)) *
                    cos(radians(center_lng) - radians(?)) +
                    sin(radians(?)) * sin(radians(center_lat))
                )) <= radius_km
            ", [$lat, $lng, $lat])
            ->first();

        if (!$zone) {
            return 1.0;
        }

        return $this->recalculateSurge($zone->id);
    }

    /**
     * Flush the cache for a specific zone (e.g. after rule update).
     */
    public function flushZoneCache(string $zoneId): void
    {
        Cache::forget("surge_multiplier_{$zoneId}");
    }
}
