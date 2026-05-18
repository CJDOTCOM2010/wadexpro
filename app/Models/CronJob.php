<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class CronJob extends Model
{
    protected $table = 'cron_jobs';

    protected $fillable = [
        'name',
        'command',
        'schedule',
        'description',
        'is_active',
        'last_run',
        'next_run',
        'status',
        'last_output',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_run' => 'datetime',
        'next_run' => 'datetime',
    ];

    public static function getDefaultCronJobs(): array
    {
        return [
            [
                'name' => 'Scheduled Backup',
                'command' => 'backup:scheduled',
                'schedule' => 'daily',
                'description' => 'Run automatic backup based on schedule settings',
                'is_active' => false,
            ],
            [
                'name' => 'Database Cleanup',
                'command' => 'backup:clean',
                'schedule' => 'weekly',
                'description' => 'Clean up old backups based on retention policy',
                'is_active' => false,
            ],
            [
                'name' => 'Queue Worker',
                'command' => 'queue:work --stop-when-empty',
                'schedule' => 'every-minute',
                'description' => 'Process queued jobs',
                'is_active' => false,
            ],
            [
                'name' => 'Health Check',
                'command' => 'inspire',
                'schedule' => 'hourly',
                'description' => 'System health monitoring',
                'is_active' => false,
            ],
        ];
    }

    public static function getScheduleOptions(): array
    {
        return [
            'every-minute' => 'Every Minute',
            'every-five-minutes' => 'Every 5 Minutes',
            'every-ten-minutes' => 'Every 10 Minutes',
            'every-fifteen-minutes' => 'Every 15 Minutes',
            'every-thirty-minutes' => 'Every 30 Minutes',
            'hourly' => 'Hourly',
            'daily' => 'Daily (Midnight)',
            'daily-at-1am' => 'Daily at 1 AM',
            'daily-at-2am' => 'Daily at 2 AM',
            'daily-at-3am' => 'Daily at 3 AM',
            'daily-at-6am' => 'Daily at 6 AM',
            'weekly' => 'Weekly (Sunday)',
            'weekly-monday' => 'Weekly (Monday)',
            'monthly' => 'Monthly (1st)',
        ];
    }

    public static function getScheduleCron(string $option): string
    {
        return match ($option) {
            'every-minute' => '* * * * *',
            'every-five-minutes' => '*/5 * * * *',
            'every-ten-minutes' => '*/10 * * * *',
            'every-fifteen-minutes' => '*/15 * * * *',
            'every-thirty-minutes' => '*/30 * * * *',
            'hourly' => '0 * * * *',
            'daily' => '0 0 * * *',
            'daily-at-1am' => '0 1 * * *',
            'daily-at-2am' => '0 2 * * *',
            'daily-at-3am' => '0 3 * * *',
            'daily-at-6am' => '0 6 * * *',
            'weekly' => '0 0 * * 0',
            'weekly-monday' => '0 0 * * 1',
            'monthly' => '0 0 1 * *',
            default => '0 0 * * *',
        };
    }

    public function calculateNextRun(): void
    {
        $cron = self::getScheduleCron($this->schedule);
        $this->next_run = Carbon::now()->addDay(); // Placeholder - actual calculation would use a cron parser
    }
}
