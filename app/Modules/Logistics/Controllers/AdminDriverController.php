<?php

namespace App\Modules\Logistics\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Logistics\Models\Driver;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminDriverController extends Controller
{
    use \App\Core\Traits\ApiResponse;

    /**
     * List drivers with filtering and pagination.
     */
    public function index(Request $request)
    {
        $query = Driver::with('user');

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        return $this->success($query->paginate($request->get('per_page', 20)), 'Drivers retrieved.');
    }

    /**
     * Show detailed KYC information for a driver.
     */
    public function show($id)
    {
        $driver = Driver::with('user', 'vehicles')->findOrFail($id);

        return $this->success([
            'id' => $driver->id,
            'user_name' => $driver->user->name,
            'user_email' => $driver->user->email,
            'user_phone' => $driver->user->phone,
            'license' => [
                'number' => $driver->license_number,
                'class' => $driver->license_class,
                'expires_at' => $driver->license_expires_at,
            ],
            'documents' => [
                'id_card_front' => $driver->id_card_front_url ? asset('storage/' . $driver->id_card_front_url) : null,
                'id_card_back' => $driver->id_card_back_url ? asset('storage/' . $driver->id_card_back_url) : null,
                'profile_photo' => $driver->driver_photo_url ? asset('storage/' . $driver->driver_photo_url) : null,
            ],
            'status' => $driver->status,
            'rejection_reason' => $driver->rejection_reason,
            'verified_at' => $driver->verified_at,
            'created_at' => $driver->created_at,
        ], 'Driver details retrieved.');
    }

    /**
     * Approve a driver after document review.
     */
    public function approve($id)
    {
        $driver = Driver::findOrFail($id);
        
        $driver->update([
            'status' => 'active',
            'verified_at' => now(),
            'rejection_reason' => null
        ]);

        return $this->success([
            'status' => 'active'
        ], 'Driver approved successfully.');
    }

    /**
     * Reject a driver application with a reason.
     */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|max:500'
        ]);

        $driver = Driver::findOrFail($id);
        
        $driver->update([
            'status' => 'pending_verification', 
            'rejection_reason' => $request->reason,
            'verified_at' => null
        ]);

        return $this->success([
            'status' => $driver->status
        ], 'Driver application rejected. The driver has been notified to re-upload documents.');
    }

    /**
     * Suspend an active driver.
     */
    public function suspend(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|max:500'
        ]);

        $driver = Driver::findOrFail($id);
        
        $driver->update([
            'status' => 'suspended',
            'rejection_reason' => $request->reason
        ]);

        return $this->success([
            'status' => 'suspended'
        ], 'Driver account suspended.');
    }
}
