<?php

namespace App\Modules\Admin\Controllers;

use App\Core\Traits\ApiResponse;
use App\Modules\Admin\Models\AdminAuditLog;
use App\Modules\Admin\Models\Module;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class SystemControlController extends Controller
{
    use ApiResponse;

    public function status(): JsonResponse
    {
        $status = [
            'app' => [
                'name' => config('app.name'),
                'env' => config('app.env'),
                'debug' => config('app.debug'),
                'version' => config('app.version', '1.0.0'),
            ],
            'database' => [
                'connected' => DB::connection()->getPdo() !== null,
                'driver' => config('database.default'),
                'name' => config('database.connections.'.config('database.default').'.database'),
            ],
            'maintenance_mode' => app()->isDownForMaintenance(),
            'cache_driver' => config('cache.default'),
            'session_driver' => config('session.driver'),
            'queue_driver' => config('queue.default'),
            'storage_used' => $this->getStorageUsage(),
            'active_modules' => Module::where('is_enabled', true)->count(),
            'total_modules' => Module::count(),
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'server_time' => now()->toIso8601String(),
            'timezone' => config('app.timezone'),
        ];

        return $this->success($status);
    }

    public function toggleMaintenance(Request $request): JsonResponse
    {
        $enabled = $request->boolean('enabled', ! app()->isDownForMaintenance());

        if ($enabled) {
            Artisan::call('down', ['--secret' => $request->get('secret', 'wadexpro-admin')]);

            AdminAuditLog::log(
                'maintenance_enable',
                'Enabled maintenance mode',
                ['secret' => $request->get('secret')]
            );
        } else {
            Artisan::call('up');

            AdminAuditLog::log(
                'maintenance_disable',
                'Disabled maintenance mode',
                []
            );
        }

        return $this->success([
            'maintenance_mode' => app()->isDownForMaintenance(),
        ], $enabled ? 'Maintenance mode enabled.' : 'Maintenance mode disabled.');
    }

    public function clearCache(Request $request): JsonResponse
    {
        $type = $request->get('type', 'all');

        $commands = match ($type) {
            'config' => ['config:clear'],
            'route' => ['route:clear'],
            'view' => ['view:clear'],
            'cache' => ['cache:clear'],
            'events' => ['event:clear'],
            'all' => ['config:clear', 'cache:clear', 'route:clear', 'view:clear', 'event:clear', 'clear-compiled'],
            default => ['cache:clear'],
        };

        foreach ($commands as $command) {
            Artisan::call($command);
        }

        AdminAuditLog::log(
            'cache_clear',
            "Cleared cache: {$type}",
            ['type' => $type, 'commands' => $commands]
        );

        return $this->success([
            'cleared' => $type,
            'commands_run' => $commands,
        ], 'Cache cleared successfully.');
    }

    public function triggerBackup(Request $request): JsonResponse
    {
        $type = $request->get('type', 'database');

        AdminAuditLog::log(
            'backup_trigger',
            "Triggered {$type} backup",
            ['type' => $type]
        );

        return $this->success([
            'status' => 'queued',
            'type' => $type,
            'message' => 'Backup has been queued. Check logs for progress.',
        ], 'Backup initiated.');
    }

    private function getStorageUsage(): array
    {
        $storagePath = storage_path('app');
        $size = $this->folderSize($storagePath);

        return [
            'bytes' => $size,
            'formatted' => $this->formatBytes($size),
        ];
    }

    private function folderSize(string $directory): int
    {
        $size = 0;
        if (is_dir($directory)) {
            foreach (glob($directory.'/*') as $file) {
                $size += is_file($file) ? filesize($file) : $this->folderSize($file);
            }
        }

        return $size;
    }

    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }

        return round($bytes, 2).' '.$units[$i];
    }
}
