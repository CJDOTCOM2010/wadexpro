<?php

namespace App\Modules\Logistics\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Logistics\Models\RideRequest;
use App\Modules\Logistics\Services\SurgeService;
use App\Modules\Logistics\Services\PromotionService;
use App\Modules\Logistics\Services\RideMatchingService;
use App\Modules\Logistics\Services\FareCalculationService;
use App\Modules\Logistics\Services\SafetyGuardService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class RideRequestController extends Controller
{
    private SurgeService $surgeService;
    private PromotionService $promotionService;
    private RideMatchingService $matchingService;
    private FareCalculationService $fareService;
    private SafetyGuardService $safetyService;

    public function __construct(
        SurgeService $surgeService, 
        PromotionService $promotionService,
        RideMatchingService $matchingService,
        FareCalculationService $fareService,
        SafetyGuardService $safetyService
    ) {
        $this->surgeService = $surgeService;
        $this->promotionService = $promotionService;
        $this->matchingService = $matchingService;
        $this->fareService = $fareService;
        $this->safetyService = $safetyService;
    }


    /**
     * Calculate an internal mock estimation for ride pricing.
     */
    public function estimate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pickup_lat'   => 'required|numeric',
            'pickup_lng'   => 'required|numeric',
            'dropoff_lat'  => 'required|numeric',
            'dropoff_lng'  => 'required|numeric',
            'actual_distance_km' => 'nullable|numeric',
            'vehicle_type' => ['required', Rule::in(['economy', 'comfort', 'moto', 'xl'])],
            'promo_code'   => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Precise estimation via centralized FareCalculationService
        $estimate = $this->fareService->estimate(
            $lat1,
            $lng1,
            $lat2,
            $lng2,
            $request->vehicle_type,
            $request->filled('actual_distance_km') ? (float) $request->actual_distance_km : null,
            null,
            $request->promo_code
        );

        return response()->json($estimate);
    }

    /**
     * Persist the request to the PostgreSQL Database.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pickup_address'  => 'required|string|max:255',
            'pickup_lat'    => 'required|numeric',
            'pickup_lng'    => 'required|numeric',
            'dropoff_address' => 'required|string|max:255',
            'dropoff_lat'   => 'required|numeric',
            'dropoff_lng'   => 'required|numeric',
            'actual_distance_km' => 'nullable|numeric',
            'vehicle_type'  => ['required', Rule::in(['economy', 'comfort', 'moto', 'xl'])],
            'promo_code'    => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Precise calculation via centralized FareCalculationService
        $estimate = $this->fareService->estimate(
            (float) $request->pickup_lat,
            (float) $request->pickup_lng,
            (float) $request->dropoff_lat,
            (float) $request->dropoff_lng,
            $request->vehicle_type,
            $request->filled('actual_distance_km') ? (float) $request->actual_distance_km : null,
            null,
            $request->promo_code
        );

        $ride = RideRequest::create([
            'customer_id'            => $request->user()->id,
            'pickup_address'         => $request->pickup_address,
            'pickup_lat'             => (float) $request->pickup_lat, 
            'pickup_lng'             => (float) $request->pickup_lng,
            'dropoff_address'        => $request->dropoff_address,
            'dropoff_lat'            => (float) $request->dropoff_lat,
            'dropoff_lng'            => (float) $request->dropoff_lng,
            'vehicle_type'           => $request->vehicle_type,
            'estimated_price'        => $estimate['total'],
            'estimated_distance_km'  => $estimate['distance_km'],
            'surge_multiplier'       => $estimate['surge_multiplier'],
            'promo_code'             => $request->promo_code,
            'discount_amount'        => $estimate['discount'],
            'status'                 => 'searching',
        ]);

        if ($request->filled('promo_code')) {
            // Logically handle promo increment via service if needed
        }

        // Dispatch to nearby drivers
        $this->matchingService->dispatchToNearbyDrivers($ride);

        // Safety Integrity Audit
        $this->safetyService->performFraudAudit($ride);

        return response()->json([
            'message' => 'Ride request initiated successfully.',
            'data'    => $ride->load('customer:id,name,phone')
        ], 201);
    }

    /**
     * Retrieve a specific ride request status.
     */
    public function show($id)
    {
        $ride = RideRequest::with(['driver.user', 'driver.activeVehicle'])->findOrFail($id);

        return response()->json([
            'status' => $ride->status,
            'driver' => $ride->driver ? [
                'name'    => $ride->driver->user->name,
                'avatar'  => $ride->driver->user->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($ride->driver->user->name),
                'rating'  => '4.9', // Mock rating for now
                'vehicle' => $ride->driver->activeVehicle->make . ' ' . $ride->driver->activeVehicle->model,
                'plate'   => $ride->driver->activeVehicle->plate_number,
                'color'   => $ride->driver->activeVehicle->color ?? 'Black',
            ] : null,
            'data' => $ride
        ]);
    }

    /**
     * Internal Math Simulator
     */
    private function calculateHaversineDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371; // km
        $latFrom = deg2rad($lat1);
        $lonFrom = deg2rad($lon1);
        $latTo   = deg2rad($lat2);
        $lonTo   = deg2rad($lon2);
        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;
        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) + cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
        return $angle * $earthRadius;
    }
}
