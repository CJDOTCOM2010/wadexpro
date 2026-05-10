<?php

namespace App\Modules\Admin\Services;

use Illuminate\Support\Facades\DB;

class ModuleManagementService
{
    /**
     * Get all modules with their status.
     *
     * @return array<string, array{name: string, enabled: bool, slug: string}>
     */
    public function getAllModules(): array
    {
        $configModules = config('modules', []);
        $dbModules = DB::table('modules')->pluck('is_enabled', 'slug')->toArray();

        $modules = [];
        foreach ($configModules as $slug => $config) {
            $modules[$slug] = [
                'slug'    => $slug,
                'name'    => $config['name'],
                'enabled' => isset($dbModules[$slug])
                    ? (bool) $dbModules[$slug]
                    : $config['enabled'],
            ];
        }

        return $modules;
    }

    /**
     * Enable a module.
     */
    public function enableModule(string $slug): bool
    {
        $configModules = config('modules', []);

        if (!isset($configModules[$slug])) {
            return false;
        }

        DB::table('modules')->updateOrInsert(
            ['slug' => $slug],
            [
                'is_enabled'  => true,
                'updated_at'  => now(),
            ]
        );

        return true;
    }

    /**
     * Disable a module.
     */
    public function disableModule(string $slug): bool
    {
        // Prevent disabling critical modules
        $critical = ['logistics', 'payments', 'notifications'];

        if (in_array($slug, $critical, true)) {
            return false;
        }

        DB::table('modules')->updateOrInsert(
            ['slug' => $slug],
            [
                'is_enabled'  => false,
                'updated_at'  => now(),
            ]
        );

        return true;
    }

    /**
     * Toggle a module's enabled state.
     */
    public function toggleModule(string $slug): array
    {
        $modules = $this->getAllModules();

        if (!isset($modules[$slug])) {
            return ['success' => false, 'message' => 'Module not found.'];
        }

        $currentState = $modules[$slug]['enabled'];

        if ($currentState) {
            $result = $this->disableModule($slug);
        } else {
            $result = $this->enableModule($slug);
        }

        if (!$result) {
            return ['success' => false, 'message' => 'Cannot toggle this module. It may be a critical system module.'];
        }

        return [
            'success' => true,
            'enabled' => !$currentState,
            'message' => $currentState ? 'Module disabled.' : 'Module enabled.',
        ];
    }
}
