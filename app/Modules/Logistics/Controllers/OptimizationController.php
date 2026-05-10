<?php

namespace App\Modules\Logistics\Controllers;

use App\Core\Traits\ApiResponse;
use App\Modules\Logistics\Services\OptimizationService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class OptimizationController extends Controller
{
    use ApiResponse;

    public function __construct(private OptimizationService $optimizationService)
    {
    }

    /**
     * Allow clients to request an optimized sequence of stops.
     */
    public function optimize(Request $request)
    {
        $validated = $request->validate([
            'pickup_lat' => 'required|numeric',
            'pickup_lng' => 'required|numeric',
            'stops' => 'required|array|min:2',
            'stops.*.id' => 'nullable|string', // Temporary IDs or DB IDs
            'stops.*.lat' => 'required|numeric',
            'stops.*.lng' => 'required|numeric',
        ]);

        $optimizedStops = $this->optimizationService->optimizeRoute(
            $validated['pickup_lat'],
            $validated['pickup_lng'],
            $validated['stops']
        );

        return $this->success([
            'original_count' => count($validated['stops']),
            'optimized_stops' => $optimizedStops
        ], 'Route optimized successfully.');
    }
}
