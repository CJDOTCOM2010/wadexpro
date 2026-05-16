<?php

namespace App\Modules\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Modules\Admin\Models\Module;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class InfrastructureController extends Controller
{
    /**
     * Display the Infrastructure / Cache management dashboard.
     */
    public function infrastructure()
    {
        $modules = Module::orderBy('name')->get();

        return view('admin.infrastructure', compact('modules'));
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
            return back()->with('error', 'Command execution failed: '.$e->getMessage());
        }
    }

    public function toggleModule($id)
    {
        $module = Module::findOrFail($id);
        $module->update(['is_enabled' => ! $module->is_enabled]);

        return back()->with('success', "Module '{$module->name}' ".($module->is_enabled ? 'enabled' : 'disabled').'.');
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
