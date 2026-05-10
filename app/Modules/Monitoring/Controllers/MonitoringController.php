<?php

namespace App\Modules\Monitoring\Controllers;

use App\Core\Traits\ApiResponse;
use App\Modules\Monitoring\Models\ActivityLog;
use App\Modules\Monitoring\Models\SystemAlert;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class MonitoringController extends Controller
{
    use ApiResponse;

    /**
     * View audit logs.
     */
    public function auditLogs(Request $request)
    {
        $logs = ActivityLog::with('user')->latest('created_at')->paginate(50);
        return $this->paginated($logs, 'Audit logs retrieved.');
    }

    /**
     * View active system alerts.
     */
    public function alerts()
    {
        $alerts = SystemAlert::where('is_resolved', false)->get();
        return $this->success($alerts, 'System alerts retrieved.');
    }

    /**
     * Resolve a system alert.
     */
    public function resolveAlert(Request $request, string $id)
    {
        $alert = SystemAlert::findOrFail($id);
        
        $alert->update([
            'is_resolved' => true,
            'resolved_by' => $request->user()->id,
            'resolved_at' => now(),
            'resolution_notes' => $request->notes,
        ]);

        return $this->success(null, 'Alert resolved.');
    }
}
