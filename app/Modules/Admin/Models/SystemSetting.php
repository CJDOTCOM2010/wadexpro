<?php

namespace App\Modules\Admin\Models;

use App\Core\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

/**
 * Key-value system configuration store.
 * Supports string, integer, boolean, JSON, and encrypted types.
 * The Super Admin edits these from the Settings panel — no code changes required.
 */
class SystemSetting extends Model
{
    use HasUuid;

    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
        'label',
        'tooltip',
        'is_public',
        'is_encrypted',
    ];

    protected function casts(): array
    {
        return [
            'is_public'    => 'boolean',
            'is_encrypted' => 'boolean',
        ];
    }

    // -------------------------------------------------------------------------
    // Static helpers
    // -------------------------------------------------------------------------

    /**
     * Retrieve a setting value by key with type casting applied.
     * Returns $default if the key does not exist.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        try {
            $setting = static::where('key', $key)->first();

            if (!$setting) {
                return $default;
            }

            return $setting->castValue();
        } catch (\Exception $e) {
            return $default;
        }
    }

    /**
     * Set or upsert a setting value by key.
     */
    public static function set(string $key, mixed $value): static
    {
        return static::updateOrCreate(
            ['key' => $key],
            ['value' => is_array($value) ? json_encode($value) : (string) $value]
        );
    }

    /**
     * Return the value cast to the declared type.
     */
    public function castValue(): mixed
    {
        $raw = $this->is_encrypted ? Crypt::decryptString($this->value) : $this->value;

        return match ($this->type) {
            'integer' => (int) $raw,
            'boolean' => filter_var($raw, FILTER_VALIDATE_BOOLEAN),
            'json'    => json_decode($raw, true),
            default   => $raw,
        };
    }
}
