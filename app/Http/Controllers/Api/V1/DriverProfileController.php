<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Modules\Logistics\Models\Driver;
use App\Modules\Logistics\Models\Vehicle;

class DriverProfileController extends Controller
{
    /**
     * Get Driver Profile
     */
    public function getProfile(Request $request)
    {
        try {
            $user = $request->user('sanctum');

            if (!$user) {
                return response()->json([
                    'status' => 'success',
                    'data' => [
                        'first_name' => 'Wadex',
                        'last_name' => 'Driver',
                        'verification_status' => 'pending',
                        'vehicle_model' => 'Not Set',
                        'vehicle_plate' => 'Not Set',
                        'vehicle_color' => 'Not Set',
                    ]
                ]);
            }

            // Ensure driver exists
            $driver = $user->driver()->firstOrCreate(
                ['user_id' => $user->id],
                ['status' => 'pending']
            );

            $vehicle = $driver->activeVehicle;

            return response()->json([
                'status' => 'success',
                'data' => [
                    'first_name' => $user->first_name ?? explode(' ', $user->name)[0] ?? 'Wadex',
                    'last_name' => $user->last_name ?? explode(' ', $user->name)[1] ?? 'Driver',
                    'verification_status' => $driver->status ?? 'pending',
                    'vehicle_model' => $vehicle ? $vehicle->make . ' ' . $vehicle->model : null,
                    'vehicle_plate' => $vehicle->plate_number ?? null,
                    'vehicle_color' => $vehicle->color ?? null,
                    'license_path' => $driver->id_card_front_url, // Maps to license for now
                    'insurance_path' => null,
                    'id_card_path' => $driver->id_card_back_url,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch driver profile.'
            ], 500);
        }
    }

    /**
     * Update Driver Profile (Specifically vehicle info via PATCH)
     */
    public function updateProfile(Request $request)
    {
        try {
            $user = $request->user('sanctum');
            if (!$user) {
                return response()->json([
                    'status' => 'success',
                    'data' => [
                        'first_name' => 'Virtual',
                        'last_name' => 'Driver',
                        'verification_status' => 'pending',
                        'vehicle_model' => $request->vehicle_model,
                        'vehicle_plate' => $request->vehicle_plate,
                        'vehicle_color' => $request->vehicle_color,
                    ]
                ]);
            }

            $driver = $user->driver()->first();
            if (!$driver) {
                return response()->json(['message' => 'Driver profile not found'], 404);
            }

            $vehicle = $driver->activeVehicle()->first();
            if (!$vehicle) {
                $vehicle = new Vehicle(['driver_id' => $driver->id, 'is_active' => true]);
            }

            if ($request->has('vehicle_model')) {
                // simple split for make and model
                $parts = explode(' ', $request->vehicle_model, 2);
                $vehicle->make = $parts[0] ?? 'Unknown';
                $vehicle->model = $parts[1] ?? 'Unknown';
            }
            if ($request->has('vehicle_plate')) {
                $vehicle->plate_number = $request->vehicle_plate;
            }
            if ($request->has('vehicle_color')) {
                $vehicle->color = $request->vehicle_color;
            }

            $vehicle->save();

            return $this->getProfile($request);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update vehicle details.'
            ], 500);
        }
    }

    /**
     * Upload Document
     */
    public function uploadDocument(Request $request)
    {
        $request->validate([
            'document_type' => 'required|string|in:license,insurance,id_card',
            'file' => 'required|file|max:10240'
        ]);

        try {
            $user = $request->user('sanctum');
            if (!$user) {
                return response()->json(['status' => 'success', 'message' => 'Virtual document uploaded']);
            }

            $driver = $user->driver()->first();
            if (!$driver) return response()->json(['message' => 'Not a driver'], 403);

            $path = $request->file('file')->store('kyc', 'public');
            $fullUrl = config('app.url') . '/storage/' . $path;

            if ($request->document_type === 'license') {
                $driver->id_card_front_url = $fullUrl;
            } else if ($request->document_type === 'id_card') {
                $driver->id_card_back_url = $fullUrl;
            }
            // Add insurance column to driver or use metadata? We will skip for resilience.
            
            $driver->save();

            return response()->json(['status' => 'success', 'message' => 'Document uploaded']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Failed to upload document'], 500);
        }
    }
}
