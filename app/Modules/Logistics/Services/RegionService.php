<?php

namespace App\Modules\Logistics\Services;

use App\Modules\Logistics\Models\Region;
use App\Modules\Logistics\Models\RegionRate;
use Illuminate\Support\Facades\Cache;

class RegionService
{
    /**
     * Find which region a coordinate belongs to.
     */
    public function findRegionByCoords(float $lat, float $lng): ?Region
    {
        $regions = Cache::remember('active_regions_polygons', 3600, function () {
            return Region::where('is_active', true)->get();
        });

        foreach ($regions as $region) {
            if ($this->isPointInPolygon($lat, $lng, $region->boundary)) {
                return $region;
            }
        }

        return null;
    }

    /**
     * Get rates for a specific region and vehicle type.
     */
    public function getRates(string $regionId, string $vehicleType): array
    {
        return Cache::remember("region_rates:{$regionId}:{$vehicleType}", 3600, function () use ($regionId, $vehicleType) {
            $rate = RegionRate::where('region_id', $regionId)
                ->where('vehicle_type', $vehicleType)
                ->first();

            if (!$rate) {
                // Fallback to a default if provided or first available
                $rate = RegionRate::where('region_id', $regionId)->first();
            }

            return $rate ? $rate->toArray() : [];
        });
    }

    /**
     * Ray-Casting Algorithm for Point-in-Polygon detection.
     */
    private function isPointInPolygon(float $lat, float $lng, ?array $polygon): bool
    {
        if (!$polygon || !isset($polygon['coordinates'][0])) {
            return false;
        }

        $points = $polygon['coordinates'][0]; // Standard GeoJSON [ [lng, lat], ... ]
        $verticesCount = count($points);
        $inside = false;

        for ($i = 0, $j = $verticesCount - 1; $i < $verticesCount; $j = $i++) {
            $xi = $points[$i][0]; $yi = $points[$i][1];
            $xj = $points[$j][0]; $yj = $points[$j][1];

            $intersect = (($yi > $lat) != ($yj > $lat))
                && ($lng < ($xj - $xi) * ($lat - $yi) / ($yj - $yi) + $xi);
            
            if ($intersect) $inside = !$inside;
        }

        return $inside;
    }
}
