<?php

namespace App\Modules\Admin\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SystemSettingsService
{
    /**
     * Cache TTL in seconds.
     */
    private const CACHE_TTL = 600;

    /**
     * Get a setting value by group and key.
     */
    public function get(string $group, string $key, mixed $default = null): mixed
    {
        $cacheKey = "setting:{$group}:{$key}";

        $value = Cache::remember($cacheKey, self::CACHE_TTL, function () use ($group, $key) {
            $setting = DB::table('system_settings')
                ->where('group', $group)
                ->where('key', $key)
                ->first();

            return $setting ? $this->castValue($setting->value, $setting->type) : '__NULL__';
        });

        return $value === '__NULL__' ? $default : $value;
    }

    /**
     * Get all settings for a group.
     *
     * @return array<string, mixed>
     */
    public function getGroup(string $group): array
    {
        $cacheKey = "settings_group:{$group}";

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($group) {
            $settings = DB::table('system_settings')
                ->where('group', $group)
                ->get();

            $result = [];
            foreach ($settings as $setting) {
                $result[$setting->key] = $this->castValue($setting->value, $setting->type);
            }

            return $result;
        });
    }

    /**
     * Set a setting value.
     */
    public function set(string $group, string $key, mixed $value, string $type = 'string', ?string $description = null): void
    {
        $existing = DB::table('system_settings')
            ->where('group', $group)
            ->where('key', $key)
            ->first();

        $stringValue = is_array($value) || is_object($value) ? json_encode($value) : (string) $value;

        if ($existing) {
            DB::table('system_settings')
                ->where('id', $existing->id)
                ->update([
                    'value'      => $stringValue,
                    'type'       => $type,
                    'updated_at' => now(),
                ]);
        } else {
            DB::table('system_settings')->insert([
                'id'          => (string) Str::uuid(),
                'group'       => $group,
                'key'         => $key,
                'value'       => $stringValue,
                'type'        => $type,
                'description' => $description,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        }

        // Clear caches
        Cache::forget("setting:{$group}:{$key}");
        Cache::forget("settings_group:{$group}");
    }

    /**
     * Bulk set settings for a group.
     *
     * @param array<string, mixed> $settings
     */
    public function setGroup(string $group, array $settings): void
    {
        foreach ($settings as $key => $value) {
            $type = match (true) {
                is_bool($value)   => 'boolean',
                is_int($value)    => 'integer',
                is_float($value)  => 'float',
                is_array($value)  => 'json',
                default           => 'string',
            };

            $this->set($group, $key, $value, $type);
        }
    }

    /**
     * Get all settings grouped by group name (for admin UI).
     *
     * @return array<string, array>
     */
    public function getAllGrouped(): array
    {
        $settings = DB::table('system_settings')
            ->orderBy('group')
            ->orderBy('key')
            ->get();

        $grouped = [];
        foreach ($settings as $setting) {
            $grouped[$setting->group][] = [
                'id'          => $setting->id,
                'key'         => $setting->key,
                'value'       => $this->castValue($setting->value, $setting->type),
                'type'        => $setting->type,
                'description' => $setting->description,
            ];
        }

        return $grouped;
    }

    /**
     * Cast stored string value to proper PHP type.
     */
    private function castValue(?string $value, string $type): mixed
    {
        if ($value === null) {
            return null;
        }

        return match ($type) {
            'boolean'  => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'integer'  => (int) $value,
            'float'    => (float) $value,
            'json'     => json_decode($value, true),
            default    => $value,
        };
    }
}
