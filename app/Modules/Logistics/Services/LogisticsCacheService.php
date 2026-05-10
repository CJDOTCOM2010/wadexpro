<?php

namespace App\Modules\Logistics\Services;

use App\Modules\Logistics\Models\Region;
use App\Modules\Logistics\Models\VehicleType;
use Illuminate\Support\Facades\Cache;

class LogisticsCacheService
{
    private const TTL = 3600; // 1 hour

    /**
     * Get all active regions with rates, leveraging cache to offload DB lookups.
     */
    public function getCachedRegions()
    {
        return Cache::remember('logistics_regions_active', self::TTL, function () {
            return Region::where('is_active', true)->with('rates')->get();
        });
    }

    /**
     * Get all vehicle types from cache.
     */
    public function getCachedVehicleTypes()
    {
        return Cache::remember('logistics_vehicle_types', self::TTL, function () {
            return VehicleType::orderBy('sequence')->get();
        });
    }

    /**
     * Reset the logistics cache when rates or regions change.
     */
    public function flushLogisticsStaticData(): void
    {
        Cache::forget('logistics_regions_active');
        Cache::forget('logistics_vehicle_types');
    }

    /**
     * Cache real-time surge multipliers for 2 minutes to prevent DB hammering.
     */
    public function getSurgeMultiplier(string $regionId): float
    {
        return Cache::remember("surge_mult_{$regionId}", 120, function () {
            // Logic to fetch from DB or Dynamic logic
            return 1.0; 
        });
    }
}
