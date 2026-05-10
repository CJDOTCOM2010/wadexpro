<?php

namespace App\Modules\Logistics\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Redis;

class LiveMapController extends Controller
{
    use \App\Core\Traits\ApiResponse;

    /**
     * Fetch all online driver positions from Redis.
     * This powers the Admin Live Map with millisecond-accuracy data.
     */
    public function index(): JsonResponse
    {
        // 1. Get all active driver IDs from the high-performance Redis Geospatial key
        $driverIds = Redis::zRange('drivers:locations', 0, -1);
        
        if (empty($driverIds)) {
            return $this->success([], 'No online drivers found.');
        }

        $positions = Redis::geoPos('drivers:locations', ...$driverIds);
        
        $data = [];
        foreach ($driverIds as $index => $id) {
            if ($pos = $positions[$index]) {
                // Fetch basic driver profile from Redis cache (seeded on login/status update)
                $profile = Redis::hGetAll("driver:profile:$id");
                
                $data[] = [
                    'driverId'    => $id,
                    'lat'         => (float) $pos[1],
                    'lng'         => (float) $pos[0],
                    'name'        => $profile['name'] ?? 'Driver ' . substr($id, 0, 4),
                    'vehicleType' => $profile['vehicle_type'] ?? 'standard',
                    'status'      => $profile['status'] ?? 'online',
                    'lastUpdate'  => (int) ($profile['updated_at'] ?? now()->timestamp),
                    'heading'     => (float) ($profile['heading'] ?? 0),
                ];
            }
        }

        return $this->success($data, 'Live driver positions retrieved.');
    }
}
