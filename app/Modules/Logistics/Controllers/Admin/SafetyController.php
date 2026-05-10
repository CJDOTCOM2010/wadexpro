<?php

namespace App\Modules\Logistics\Controllers\Admin;

use App\Core\Traits\ApiResponse;
use App\Http\Controllers\Controller;
use App\Modules\Logistics\Models\SafetyAlert;
use Illuminate\Http\Request;

class SafetyController extends Controller
{
    use ApiResponse;

    /**
     * List safety alerts.
     */
    public function alerts(Request $request)
    {
        $status = $request->input('status', 'PENDING');
        
        $alerts = SafetyAlert::with(['ride.customer', 'ride.driver.user'])
            ->when($status, fn($q) => $q->where('status', $status))
            ->orderByRaw("FIELD(severity, 'CRITICAL', 'HIGH', 'MEDIUM', 'LOW')")
            ->orderByDesc('created_at')
            ->paginate(20);

        return $this->success($alerts, 'Safety alerts retrieved.');
    }

    /**
     * Get statistics for the Safety HUD.
     */
    public function stats()
    {
        $stats = [
            'pending_critical' => SafetyAlert::where('status', 'PENDING')->where('severity', 'CRITICAL')->count(),
            'pending_high'     => SafetyAlert::where('status', 'PENDING')->where('severity', 'HIGH')->count(),
            'total_today'      => SafetyAlert::where('created_at', '>', now()->startOfDay())->count(),
            'active_sos'       => SafetyAlert::where('status', 'PENDING')->where('type', 'SOS')->count(),
        ];

        return $this->success($stats, 'Safety stats retrieved.');
    }

    /**
     * Resolve a safety alert.
     */
    public function resolve(Request $request, string $id)
    {
        $alert = SafetyAlert::findOrFail($id);
        
        $validated = $request->validate([
            'status'           => 'required|in:RESOLVED,DISMISSED,INVESTIGATING',
            'notes'            => 'required|string|min:5',
        ]);

        $alert->update([
            'status'           => $validated['status'],
            'resolution_notes' => $validated['notes'],
            'resolved_by'      => $request->user()->id,
            'resolved_at'      => now(),
        ]);

        return $this->success($alert, 'Safety alert updated.');
    }

    /**
     * Get detailed info for a single alert.
     */
    public function show(string $id)
    {
        $alert = SafetyAlert::with([
            'ride.customer', 
            'ride.driver.user', 
            'ride.driver.vehicle'
        ])->findOrFail($id);

        return $this->success($alert, 'Alert details retrieved.');
    }
}
