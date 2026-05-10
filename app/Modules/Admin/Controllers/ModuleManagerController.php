<?php

namespace App\Modules\Admin\Controllers;

use App\Core\Support\ModuleRegistry;
use App\Core\Traits\ApiResponse;
use App\Modules\Admin\Models\Module;
use App\Modules\Admin\Resources\ModuleResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

/**
 * Manages the enable/disable state of all registered modules.
 * Only accessible by super_admin role.
 */
class ModuleManagerController extends Controller
{
    use ApiResponse;

    // Registry removed temporarily to fix binding resolution exception
    // public function __construct(private readonly ModuleRegistry $registry)
    // {
    // }

    /**
     * GET /v1/admin/modules
     * List all modules with their current enabled state.
     */
    public function index(): JsonResponse
    {
        $modules = Module::orderBy('name')->get();

        return $this->success(
            ModuleResource::collection($modules),
            'Modules retrieved.'
        );
    }

    /**
     * PATCH /v1/admin/modules/{slug}/toggle
     * Enable or disable a module. Flushes the ModuleRegistry cache.
     */
    public function toggle(Request $request, string $slug): JsonResponse
    {
        $request->validate([
            'is_enabled' => ['required', 'boolean'],
        ]);

        $module = $this->registry->toggle($slug, (bool) $request->is_enabled);

        return $this->success(
            new ModuleResource($module),
            "Module '{$module->name}' " . ($module->is_enabled ? 'enabled' : 'disabled') . '.'
        );
    }

    /**
     * PATCH /v1/admin/modules/{slug}/config
     * Update a module's runtime configuration JSON without code changes.
     */
    public function updateConfig(Request $request, string $slug): JsonResponse
    {
        $request->validate([
            'config' => ['required', 'array'],
        ]);

        $module = Module::where('slug', $slug)->firstOrFail();
        $module->update(['config' => $request->config]);

        return $this->success(
            new ModuleResource($module),
            "Module configuration updated."
        );
    }
}
