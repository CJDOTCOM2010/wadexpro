<?php

namespace App\Modules\Admin\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use App\Models\AuditLog;

class InfrastructureController extends Controller
{
    /**
     * Display the Infrastructure / Cache management dashboard.
     */
    public function infrastructure()
    {
        return view('admin.infrastructure');
    }

    /**
     * Execute artisan commands for cache management.
     */
    public function cacheCommand(Request $request)
    {
        $command = $request->input('command');

        try {
            switch ($command) {
                case 'optimize':
                    Artisan::call('optimize:clear');
                    $msg = 'Aggressive Cache Clear successful.';
                    break;
                case 'config':
                    Artisan::call('config:cache');
                    $msg = 'Configuration Cache rebuilt successfully.';
                    break;
                case 'route':
                    Artisan::call('route:clear');
                    $msg = 'Routing path cache flushed.';
                    break;
                case 'nuclear':
                    Artisan::call('cache:clear');
                    $msg = 'Node sessions wiped and cache cleared.';
                    break;
                default:
                    return back()->with('error', 'Unknown command.');
            }
            
            return back()->with('success', $msg);
        } catch (\Exception $e) {
            return back()->with('error', 'Command execution failed: ' . $e->getMessage());
        }
    }

    /**
     * Display the Module Hardening & Third-Party integration dashboard.
     */
    public function modules()
    {
        // Load some recent audit logs to populate the dynamic log ticker
        $auditLogs = class_exists(AuditLog::class) 
            ? AuditLog::orderBy('created_at', 'desc')->take(5)->get() 
            : [];
            
        return view('admin.module_hardening', compact('auditLogs'));
    }
}
