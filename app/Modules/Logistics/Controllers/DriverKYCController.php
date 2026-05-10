<?php

namespace App\Modules\Logistics\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Logistics\Models\Driver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class DriverKYCController extends Controller
{
    use \App\Core\Traits\ApiResponse;

    /**
     * Get the current KYC status of the authenticated driver.
     */
    public function getStatus(Request $request)
    {
        $driver = $request->user()->driver;

        if (!$driver) {
            return $this->error('Driver profile not found.', 404);
        }

        return $this->success([
            'status'           => $driver->status,
            'is_verified'      => $driver->status === 'active',
            'is_pending'       => $driver->status === 'pending_verification',
            'documents'        => [
                'id_card_front' => $driver->id_card_front_url ? asset('storage/' . $driver->id_card_front_url) : null,
                'id_card_back'  => $driver->id_card_back_url ? asset('storage/' . $driver->id_card_back_url) : null,
                'driver_photo'  => $driver->driver_photo_url ? asset('storage/' . $driver->driver_photo_url) : null,
            ],
            'rejection_reason' => $driver->rejection_reason,
            'verified_at'      => $driver->verified_at,
        ], 'KYC status retrieved.');
    }

    /**
     * Upload KYC documents for review.
     */
    public function uploadDocuments(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'license_number'     => 'required|string|max:100',
            'license_expires_at' => 'required|date',
            'license_class'      => 'required|string|max:10',
            'id_card_front'      => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
            'id_card_back'       => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
            'driver_photo'       => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
        ]);

        if ($validator->fails()) {
            return $this->error('Validation failed.', 422, $validator->errors());
        }

        $user = $request->user();
        $driver = $user->driver ?? Driver::create(['user_id' => $user->id]);

        // Update basic info
        $driver->license_number     = $request->license_number;
        $driver->license_expires_at = $request->license_expires_at;
        $driver->license_class      = $request->license_class;

        // Process Uploads
        if ($request->hasFile('id_card_front')) {
            $driver->id_card_front_url = $request->file('id_card_front')->store('kyc/id_cards', 'public');
        }

        if ($request->hasFile('id_card_back')) {
            $driver->id_card_back_url = $request->file('id_card_back')->store('kyc/id_cards', 'public');
        }

        if ($request->hasFile('driver_photo')) {
            $driver->driver_photo_url = $request->file('driver_photo')->store('kyc/photos', 'public');
        }

        // Set status to pending review if core docs are present
        if ($driver->id_card_front_url && $driver->driver_photo_url && $driver->status !== 'active') {
            $driver->status = 'pending_verification';
        }

        $driver->save();

        return $this->success([
            'status' => $driver->status,
            'driver' => $driver
        ], 'KYC documents uploaded successfully and are pending review.');
    }
}
