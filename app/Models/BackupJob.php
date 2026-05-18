<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class BackupJob extends Model
{
    protected $table = 'backup_jobs';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id', 'type', 'status', 'progress', 'current_step',
        'tables_total', 'tables_done', 'rows_dumped',
        'file_name', 'file_size', 'error_message',
        'started_at', 'completed_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'progress' => 'integer',
        'tables_total' => 'integer',
        'tables_done' => 'integer',
        'rows_dumped' => 'integer',
        'file_size' => 'integer',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::creating(fn($m) => $m->id = $m->id ?: (string) Str::uuid());
    }

    public function updateProgress(int $progress, string $step, array $extra = []): void
    {
        $this->update(array_merge([
            'progress' => $progress,
            'current_step' => $step,
        ], $extra));
    }

    public function markRunning(): void
    {
        $this->update(['status' => 'running', 'started_at' => now(), 'progress' => 1, 'current_step' => 'Starting…']);
    }

    public function markCompleted(string $fileName, int $fileSize): void
    {
        $this->update([
            'status' => 'completed',
            'progress' => 100,
            'current_step' => 'Done',
            'file_name' => $fileName,
            'file_size' => $fileSize,
            'completed_at' => now(),
        ]);
    }

    public function markFailed(string $error): void
    {
        $this->update([
            'status' => 'failed',
            'current_step' => 'Failed',
            'error_message' => $error,
            'completed_at' => now(),
        ]);
    }

    public function markCancelled(): void
    {
        $this->update([
            'status' => 'failed',
            'current_step' => 'Cancelled',
            'error_message' => 'Cancelled by admin',
            'completed_at' => now(),
        ]);
    }

    public function fileSizeHuman(): string
    {
        $bytes = $this->file_size;
        foreach (['B', 'KB', 'MB', 'GB'] as $unit) {
            if ($bytes < 1024) return round($bytes, 2) . ' ' . $unit;
            $bytes /= 1024;
        }
        return round($bytes, 2) . ' TB';
    }
}
