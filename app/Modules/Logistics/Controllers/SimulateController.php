<?php

namespace App\Modules\Logistics\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Logistics\Models\RideRequest;
use App\Modules\Logistics\Models\Driver;
use App\Modules\Logistics\Services\RideMatchingService;
use Illuminate\Http\Request;

class SimulateController extends Controller
{
    public function __construct()
    {
        if (app()->isProduction()) {
            abort(403, 'Simulation tools are disabled in production for security reasons.');
        }
    }

    /**
     * Simulate a driver accepting a ride request.
     */
    public function acceptRide($rideId)
    {
        $ride = RideRequest::findOrFail($rideId);

        if ($ride->status !== 'searching') {
            return response()->json(['message' => 'Ride is not in searching state.'], 400);
        }

        // Find any available driver to simulate acceptance
        $driver = Driver::where('is_available', true)->where('is_online', true)->first();

        if (!$driver) {
            // Fallback: Just pick any driver if none are "online" in dev
            $driver = Driver::first();
        }

        if (!$driver) {
            return response()->json(['message' => 'No drivers found in database to simulate acceptance.'], 404);
        }

        // Assign driver
        $ride->update([
            'driver_id' => $driver->id,
            'status'    => 'driver_assigned',
        ]);

        return response()->json([
            'message' => 'Simulation: Driver ' . $driver->id . ' has accepted the ride.',
            'ride_status' => $ride->status,
            'driver' => [
                'name' => $driver->user->name ?? 'Simulation Driver',
                'plate' => $driver->activeVehicle->plate_number ?? 'MOCK-123'
            ]
        ]);
    }

    /**
     * Simulate a trip completion and financial settlement.
     */
    public function completeRide($rideId)
    {
        $ride = RideRequest::findOrFail($rideId);

        if ($ride->status !== 'driver_assigned') {
            return response()->json(['message' => 'Only assigned rides can be completed.'], 400);
        }

        // Transition status
        $ride->update(['status' => 'completed']);

        // Trigger financial settlement via WalletService
        $walletService = app(\App\Modules\Logistics\Services\WalletService::class);
        $walletService->settleRide($ride);

        return response()->json([
            'message' => 'Simulation: Ride completed and settled.',
            'ride_status' => $ride->status,
            'payment_status' => $ride->payment_status,
        ]);
    }

    /**
     * Simulate an SOS emergency trigger.
     */
    public function triggerSOS($rideId)
    {
        $ride = RideRequest::findOrFail($rideId);
        
        $sosService = app(\App\Modules\Logistics\Services\SosService::class);
        
        $result = $sosService->trigger(
            $ride->customer_id,
            $ride->pickup_lat, // Current location simulation
            $ride->pickup_lng,
            $ride->id
        );

        return response()->json([
            'message' => 'Simulation: SOS Triggered.',
            'sos_id' => $result['id'],
            'status' => $result['status']
        ]);
    }
}
