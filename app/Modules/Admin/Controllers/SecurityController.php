<?php

namespace App\Modules\Admin\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Modules\Security\Models\FraudDetection;
use App\Modules\Security\Models\BlockedDevice;
use App\Models\AuditLog;
use Illuminate\Support\Facades\DB;

class SecurityController extends Controller
{
    public function index(Request $request)
    {
        // KPIs
        $stats = [
            'open_alerts' => FraudDetection::where('status', 'open')->count(),
            'high_risk'   => FraudDetection::where('status', 'open')->highRisk()->count(),
            'blocked'     => BlockedDevice::active()->count(),
        ];

        // Fraud Alerts
        $alertsQuery = FraudDetection::with('user')->orderBy('created_at', 'desc');
        if ($request->filled('status')) {
            $alertsQuery->where('status', $request->status);
        } else {
            $alertsQuery->whereIn('status', ['open', 'under_review']);
        }
        $fraudAlerts = $alertsQuery->paginate(10, ['*'], 'alerts_page');

        // Blocked Devices
        $blockedDevices = BlockedDevice::with(['user', 'blockedBy'])
            ->orderBy('created_at', 'desc')
            ->paginate(10, ['*'], 'devices_page');

        // Recent Audit Logs (System Access)
        $auditLogs = AuditLog::with('user')
            ->whereIn('event', ['login', 'logout', 'failed_login', 'permission_granted', 'role_changed'])
            ->orderBy('logged_at', 'desc')
            ->limit(15)
            ->get();

        return view('admin.security', compact('stats', 'fraudAlerts', 'blockedDevices', 'auditLogs'));
    }

    public function resolveAlert(Request $request, string $id)
    {
        $request->validate([
            'resolution_notes' => 'required|string|max:1000',
            'status'           => 'required|in:resolved,false_positive',
        ]);

        $alert = FraudDetection::findOrFail($id);
        $alert->update([
            'status'           => $request->status,
            'resolution_notes' => $request->resolution_notes,
            'resolved_by'      => auth('admin')->id(),
            'resolved_at'      => now(),
        ]);

        // Automatically log this action
        AuditLog::create([
            'auditable_type' => FraudDetection::class,
            'auditable_id'   => $alert->id,
            'user_id'        => auth('admin')->id(),
            'user_type'      => 'admin',
            'event'          => 'fraud_alert_resolved',
            'new_values'     => ['status' => $request->status],
            'ip_address'     => $request->ip(),
            'user_agent'     => $request->userAgent(),
            'url'            => $request->url(),
            'logged_at'      => now(),
        ]);

        return back()->with('success', 'Fraud alert has been successfully resolved.');
    }

    public function blockDevice(Request $request)
    {
        $request->validate([
            'device_fingerprint' => 'required|string|max:255',
            'reason'             => 'required|string|max:500',
        ]);

        $device = BlockedDevice::create([
            'device_fingerprint' => $request->device_fingerprint,
            'reason'             => $request->reason,
            'blocked_by'         => auth('admin')->id(),
            'blocked_at'         => now(),
            'is_active'          => true,
            // Assuming form might pass user_id and device_type, optional here
            'user_id'            => $request->user_id ?? null,
            'device_type'        => $request->device_type ?? 'unknown',
        ]);

        AuditLog::create([
            'auditable_type' => BlockedDevice::class,
            'auditable_id'   => $device->id,
            'user_id'        => auth('admin')->id(),
            'user_type'      => 'admin',
            'event'          => 'device_blocked',
            'new_values'     => ['fingerprint' => $request->device_fingerprint],
            'ip_address'     => $request->ip(),
            'user_agent'     => $request->userAgent(),
            'url'            => $request->url(),
            'logged_at'      => now(),
        ]);

        return back()->with('success', 'Device fingerprint has been blocked permanently.');
    }

    public function unblockDevice(Request $request, string $id)
    {
        $device = BlockedDevice::findOrFail($id);
        $device->update(['is_active' => false]);

        AuditLog::create([
            'auditable_type' => BlockedDevice::class,
            'auditable_id'   => $device->id,
            'user_id'        => auth('admin')->id(),
            'user_type'      => 'admin',
            'event'          => 'device_unblocked',
            'ip_address'     => $request->ip(),
            'user_agent'     => $request->userAgent(),
            'url'            => $request->url(),
            'logged_at'      => now(),
        ]);

        return back()->with('success', 'Device fingerprint has been unblocked.');
    }
}
