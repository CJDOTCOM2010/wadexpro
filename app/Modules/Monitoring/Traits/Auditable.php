<?php

namespace App\Modules\Monitoring\Traits;

use App\Modules\Monitoring\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;

trait Auditable
{
    public static function bootAuditable()
    {
        static::created(function (Model $model) {
            self::logActivity('created', $model);
        });

        static::updated(function (Model $model) {
            self::logActivity('updated', $model);
        });

        static::deleted(function (Model $model) {
            self::logActivity('deleted', $model);
        });
    }

    protected static function logActivity(string $action, Model $model)
    {
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'model_type' => get_class($model),
            'model_id' => $model->id,
            'old_values' => $action === 'updated' ? array_intersect_key($model->getOriginal(), $model->getDirty()) : null,
            'new_values' => $action === 'created' || $action === 'updated' ? $model->getAttributes() : null,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'module' => self::getModuleName($model),
        ]);
    }

    private static function getModuleName(Model $model): string
    {
        $class = get_class($model);
        if (str_contains($class, 'App\\Modules\\')) {
            return explode('\\', $class)[2] ?? 'System';
        }
        return 'Core';
    }
}
