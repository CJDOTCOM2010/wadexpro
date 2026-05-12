<?php

namespace App\Modules\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Logistics\Models\Driver;
use App\Modules\Logistics\Models\Vehicle;
use App\Modules\Logistics\Models\VehicleType;
use App\Modules\Logistics\Models\DriverDocument;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DriverManagementController extends Controller
{
    /**
     * Driver Registry — main listing page.
     */
    public function index(Request $request)
    {
        $query = Driver::with(['user', 'activeVehicle'])
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $drivers = $query->paginate(15)->withQueryString();

        $stats = [
            'total'    => Driver::count(),
            'active'   => Driver::where('status', 'active')->count(),
            'pending'  => Driver::where('status', 'pending_verification')->count(),
            'suspended'=> Driver::where('status', 'suspended')->count(),
        ];

        return view('admin.driver_management', compact('drivers', 'stats'));
    }

    /**
     * KYC Document Approvals queue.
     */
    public function documents(Request $request)
    {
        // Queue: drivers awaiting verification
        $queue = Driver::with('user')
            ->whereIn('status', ['pending_verification', 'pending'])
            ->latest()
            ->get();

        // Currently selected driver (default to first in queue)
        $selectedId = $request->get('driver', optional($queue->first())->id);
        $selected   = $selectedId
            ? Driver::with(['user', 'vehicles', 'documents'])->find($selectedId)
            : null;

        return view('admin.driver_documents', compact('queue', 'selected'));
    }

    /**
     * Approve a driver — marks status active and timestamps verification.
     */
    public function approve(Request $request, $id)
    {
        $driver = Driver::findOrFail($id);

        $driver->update([
            'status'           => 'active',
            'verified_at'      => now(),
            'rejection_reason' => null,
        ]);

        return back()->with('success', "Driver {$driver->user->name} has been approved and activated.");
    }

    /**
     * Reject / request re-upload.
     */
    public function reject(Request $request, $id)
    {
        $request->validate(['reason' => 'required|string|max:500']);

        $driver = Driver::findOrFail($id);
        $driver->update([
            'status'           => 'pending_verification',
            'rejection_reason' => $request->reason,
            'verified_at'      => null,
        ]);

        return back()->with('success', "Driver notified to re-upload documents.");
    }

    /**
     * Suspend an active driver.
     */
    public function suspend(Request $request, $id)
    {
        $request->validate(['reason' => 'required|string|max:500']);

        $driver = Driver::findOrFail($id);
        $driver->update([
            'status'           => 'suspended',
            'rejection_reason' => $request->reason,
        ]);

        return back()->with('success', "Driver account has been suspended.");
    }

    /**
     * Reactivate a suspended driver.
     */
    public function activate($id)
    {
        $driver = Driver::findOrFail($id);
        $driver->update([
            'status'           => 'active',
            'rejection_reason' => null,
        ]);

        return back()->with('success', "Driver account has been reactivated.");
    }
}
