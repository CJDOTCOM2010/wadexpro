<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Events\DriverLocationUpdated;

class TelemetryController extends Controller
{
    /**
     * Update driver location and broadcast to the Operations Map.
     */
    public function updateLocation(Request $request)
    {
        $validated = $request->validate([
            'driverId' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'heading' => 'nullable|numeric',
            'isBusy' => 'nullable|boolean',
        ]);

        broadcast(new DriverLocationUpdated(
            $validated['driverId'],
            $validated['latitude'],
            $validated['longitude'],
            $validated['heading'] ?? 0,
            $validated['isBusy'] ?? false
        ));

        return response()->json([
            'success' => true,
            'message' => 'Telemetry broadcasted successfully.',
            'data' => $validated,
        ]);
    }
}
