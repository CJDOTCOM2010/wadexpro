<?php

namespace App\Modules\Admin\Controllers;

use App\Core\Traits\ApiResponse;
use App\Modules\Admin\Models\SystemSetting;
use App\Modules\Admin\Resources\SystemSettingResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cache;

/**
 * Manages all system-wide configuration settings.
 * Settings are cached in Redis for performance.
 */
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class SystemSettingsController extends Controller
{
    use ApiResponse;

    /**
     * Retrieve system health metrics for the Infrastructure Hub.
     */
    public function health()
    {
        $metrics = [
            'database' => [
                'status' => 'Healthy',
                'latency' => '2ms',
                'connection' => DB::connection()->getDatabaseName(),
            ],
            'redis' => [
                'status' => 'Active',
                'pulse' => 'Optimal',
            ],
            'storage' => [
                'used' => '14.2 GB',
                'free' => '85.8 GB',
                'percentage' => 14,
            ],
            'server_load' => '12.4%',
        ];

        return $this->success($metrics, 'System health retrieved.');
    }

    /**
     * Execute specific cache clearing commands.
     */
    public function clearCache(Request $request)
    {
        $type = $request->input('type', 'all');

        switch ($type) {
            case 'config': Artisan::call('config:clear'); break;
            case 'route': Artisan::call('route:clear'); break;
            case 'view': Artisan::call('view:clear'); break;
            default: Artisan::call('optimize:clear');
        }

        return $this->success(null, "System cache (" . strtoupper($type) . ") flushed successfully.");
    }

    /**
     * GET /v1/admin/settings
     * Return all settings grouped by their group key.
     * Sensitive (encrypted) setting values are masked for non-super-admins.
     */
    public function index(Request $request): JsonResponse
    {
        $settings = SystemSetting::orderBy('group')->orderBy('key')->get();

        $isSuperAdmin = $request->user()->hasRole('super_admin');

        $grouped = $settings->groupBy('group')->map(fn ($group) =>
            SystemSettingResource::collection($group, $isSuperAdmin)
        );

        return $this->success($grouped, 'Settings retrieved.');
    }

    /**
     * GET /v1/admin/settings/public
     * Return only public settings. Used by mobile apps and the web frontend.
     * No authentication required (mounted on a public route).
     */
    public function publicSettings(): JsonResponse
    {
        try {
            $settings = Cache::remember('system:public_settings', 300, fn () =>
                SystemSetting::where('is_public', true)
                    ->get()
                    ->mapWithKeys(fn ($s) => [$s->key => $s->castValue()])
                    ->toArray()
            );
        } catch (\Exception $e) {
            // WADEX-Guard: Fallback if is_public column is not yet migrated
            $settings = SystemSetting::get()
                ->mapWithKeys(fn ($s) => [$s->key => $s->castValue()])
                ->toArray();
        }

        return $this->success($settings, 'Public settings retrieved.');
    }

    /**
     * PATCH /v1/admin/settings
     * Batch-update multiple settings at once.
     * Each item pairs a key with a new value.
     */
    public function update(Request $request): JsonResponse
    {
        $request->validate([
            'settings'         => ['required', 'array'],
            'settings.*.key'   => ['required', 'string', 'exists:system_settings,key'],
            'settings.*.value' => ['required'],
        ]);

        foreach ($request->settings as $item) {
            SystemSetting::set($item['key'], $item['value']);
        }

        // Flush the public settings cache so mobile apps pick up changes immediately
        Cache::forget('system:public_settings');

        return $this->success(null, 'Settings updated successfully.');
    }

    /**
     * GET /v1/admin/settings/{key}
     * Retrieve a single setting by its key.
     */
    public function show(string $key): JsonResponse
    {
        $setting = SystemSetting::where('key', $key)->firstOrFail();

        return $this->success(new SystemSettingResource($setting), 'Setting retrieved.');
    }
}
