<?php

namespace App\Modules\Admin\Controllers;

use App\Core\Traits\ApiResponse;
use App\Modules\Admin\Models\AdminAuditLog;
use App\Modules\Admin\Models\Module;
use App\Modules\Admin\Resources\ModuleResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ModuleManagerController extends Controller
{
    use ApiResponse;

    public function index(): JsonResponse
    {
        $modules = Module::orderBy('name')->get();

        return $this->success(
            ModuleResource::collection($modules),
            'Modules retrieved.'
        );
    }

    public function toggle(Request $request, string $slug): JsonResponse
    {
        $request->validate([
            'is_enabled' => ['required', 'boolean'],
        ]);

        $module = Module::where('slug', $slug)->firstOrFail();

        $previousState = $module->is_enabled;
        $module->update(['is_enabled' => (bool) $request->is_enabled]);

        AdminAuditLog::log(
            'module_toggle',
            "Module '{$module->name}' ".($module->is_enabled ? 'enabled' : 'disabled'),
            [
                'module_slug' => $slug,
                'previous_state' => $previousState,
                'new_state' => $module->is_enabled,
            ]
        );

        return $this->success(
            new ModuleResource($module),
            "Module '{$module->name}' ".($module->is_enabled ? 'enabled' : 'disabled').'.'
        );
    }

    public function updateConfig(Request $request, string $slug): JsonResponse
    {
        $request->validate([
            'config' => ['required', 'array'],
        ]);

        $module = Module::where('slug', $slug)->firstOrFail();
        $module->update(['config' => $request->config]);

        AdminAuditLog::log(
            'module_config_update',
            "Updated configuration for module '{$module->name}'",
            ['module_slug' => $slug, 'config' => $request->config]
        );

        return $this->success(
            new ModuleResource($module),
            'Module configuration updated.'
        );
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'slug' => ['required', 'string', 'unique:modules,slug'],
            'name' => ['required', 'string'],
            'description' => ['nullable', 'string'],
            'version' => ['nullable', 'string'],
        ]);

        $module = Module::create([
            'slug' => $request->slug,
            'name' => $request->name,
            'description' => $request->description,
            'version' => $request->version ?? '1.0.0',
            'is_enabled' => true,
            'config' => [],
        ]);

        AdminAuditLog::log(
            'module_create',
            "Created new module '{$module->name}'",
            ['module_slug' => $module->slug]
        );

        return $this->success(
            new ModuleResource($module),
            "Module '{$module->name}' created successfully."
        );
    }

    public function destroy(string $slug): JsonResponse
    {
        $module = Module::where('slug', $slug)->firstOrFail();
        $moduleName = $module->name;
        $module->delete();

        AdminAuditLog::log(
            'module_delete',
            "Deleted module '{$moduleName}'",
            ['module_slug' => $slug]
        );

        return $this->success(null, "Module '{$moduleName}' deleted successfully.");
    }
}
