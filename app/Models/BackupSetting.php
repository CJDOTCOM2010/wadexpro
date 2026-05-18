<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class BackupSetting extends Model
{
    protected $table = 'backup_settings';

    protected $fillable = [
        'auto_backup_enabled',
        'frequency',
        'backup_type',
        'scheduled_time',
        'day_of_week',
        'day_of_month',
        'retention_days',
        'notify_on_success',
        'notify_on_failure',
        'email',
    ];

    protected $casts = [
        'auto_backup_enabled' => 'boolean',
        'notify_on_success' => 'boolean',
        'notify_on_failure' => 'boolean',
        'retention_days' => 'integer',
        'day_of_month' => 'integer',
    ];

    public static function getSettings(): self
    {
        $settings = self::first();
        if (! $settings) {
            $settings = self::create([
                'auto_backup_enabled' => false,
                'frequency' => 'daily',
                'backup_type' => 'all',
                'scheduled_time' => '02:00:00',
                'retention_days' => 30,
                'notify_on_success' => true,
                'notify_on_failure' => true,
            ]);
        }

        return $settings;
    }

    public function isDueForBackup(): bool
    {
        if (! $this->auto_backup_enabled) {
            return false;
        }

        $now = now();
        $scheduledTime = Carbon::parse($this->scheduled_time);

        return match ($this->frequency) {
            'daily' => $now->format('H:i') === $scheduledTime->format('H:i'),
            'weekly' => $now->format('l') === ($this->day_of_week ?? 'Monday')
                && $now->format('H:i') === $scheduledTime->format('H:i'),
            'monthly' => $now->day === ($this->day_of_month ?? 1)
                && $now->format('H:i') === $scheduledTime->format('H:i'),
            default => false,
        };
    }
}
