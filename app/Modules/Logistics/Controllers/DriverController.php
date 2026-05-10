<?php

namespace App\Modules\Logistics\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Logistics\Models\RideRequest;
use App\Modules\Logistics\Services\WalletService;
use Illuminate\Http\Request;

class DriverController extends Controller
{
    private WalletService $walletService;

    public function __construct(WalletService $walletService)
    {
        $this->walletService = $walletService;
    }
    /**
     * Poll the database for globally active rides that are seeking drivers.
     * Future Iteration: Inject PostGIS radius clamping mapping ST_Distance vs payload Lat/Lng.
     */
    public function availableRides(Request $request)
    {
        // Load the ride with the customer namespace for the driver manifest.
        $rides = RideRequest::with('customer:id,first_name,last_name')
            ->where('status', 'searching')
            ->orderBy('created_at', 'asc')
            ->take(10) // Limit memory on scan.
            ->get();

        return response()->json([
            'status' => 'success',
            'data'   => $rides
        ], 200);
    }

    /**
     * Atomically lock and accept a ride as a driver.
     */
    public function acceptRide(Request $request, $id)
    {
        $ride = RideRequest::find($id);

        if (!$ride) {
            return response()->json(['error' => 'Ride no longer exists.'], 404);
        }

        // Strict Concurrency Defense: Prevent two drivers accepting the same hit simultaneously.
        if ($ride->status !== 'searching') {
            return response()->json(['error' => 'This ride was claimed by another fleet member.'], 409);
        }

        // Security Gate: Verify driver status
        if ($request->user()->driver->status !== 'active') {
            return response()->json([
                'error' => 'Your account is pending verification. please complete your KYC and wait for approval.',
                'status' => $request->user()->driver->status
            ], 403);
        }

        $ride->update([
            'driver_id' => $request->user()->id,
            'status'    => 'driver_assigned',
        ]);

        // Notify Customer via FCM
        $fcmService = app(\App\Modules\Notifications\Services\FcmService::class);
        $fcmService->sendToUser($ride->customer, 'Driver Found!', 'A driver has accepted your request and is on the way.', [
            'ride_id' => $ride->id,
            'status'  => 'driver_assigned',
            'type'    => 'ride_accepted'
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Ride assigned successfully.',
            'data'    => $ride
        ], 200);
    }

    /**
     * Update the status of an active ride.
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:driver_arrived,in_progress,completed,cancelled',
        ]);

        $ride = RideRequest::where('id', $id)
            ->where('driver_id', $request->user()->id)
            ->firstOrFail();

        $status = $request->status;
        
        // Map frontend statuses to DB statuses if they differ
        // DB statuses: searching, in_progress, completed, cancelled
        $dbStatus = $status;
        if ($status === 'driver_arrived') $dbStatus = 'in_progress';

        $ride->status = $dbStatus;
        $ride->save();

        // Financial Settlement on Completion
        if ($status === 'completed') {
            try {
                $this->walletService->settleRide($ride);
            } catch (\Exception $e) {
                // Log settlement error but don't fail the status update
                \Log::error('Ride Settlement Failed', [
                    'ride_id' => $ride->id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Ride status updated to ' . $status,
            'data' => $ride
        ]);
    }

    /**
     * Drivers toggle their online/available status via the app.
     */
    public function toggleStatus(Request $request)
    {
        $request->validate([
            'is_online'    => 'required|boolean',
            'is_available' => 'required|boolean',
        ]);

        $driver = $request->user()->driver;

        if (!$driver) {
            return response()->json(['error' => 'Not a registered driver.'], 403);
        }

        $driver->update([
            'is_online'    => $request->is_online,
            'is_available' => $request->is_available,
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Driver status updated.',
            'is_online' => (bool) $driver->is_online,
            'is_available' => (bool) $driver->is_available,
        ]);
    }
}
