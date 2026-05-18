<?php

namespace App\Console\Commands;

use App\Jobs\RunDatabaseBackup;
use App\Models\BackupJob;
use App\Models\BackupSetting;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class RunScheduledBackup extends Command
{
    protected $signature = 'backup:scheduled';

    protected $description = 'Run automatic backup based on schedule settings';

    public function handle(): int
    {
        $settings = BackupSetting::getSettings();

        if (! $settings->auto_backup_enabled) {
            $this->info('Auto backup is disabled.');

            return self::SUCCESS;
        }

        $this->info('Starting scheduled backup...');

        try {
            $option = $settings->backup_type;

            if ($option === 'only-files') {
                $exitCode = Artisan::call('backup:run', ['--only-files' => true]);
                if ($exitCode !== 0) {
                    $this->error('File backup failed.');
                    Log::error('Scheduled file backup failed');

                    return self::FAILURE;
                }
                $this->info('File backup completed successfully.');
            } else {
                $job = BackupJob::create([
                    'type' => $option,
                    'status' => 'pending',
                    'progress' => 0,
                    'current_step' => 'Scheduled backup queued...',
                ]);

                RunDatabaseBackup::dispatch($job->id, $option);

                $this->info("Backup job {$job->id} created and queued.");
            }

            $this->cleanupOldBackups($settings->retention_days);

            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Scheduled backup failed: '.$e->getMessage());
            Log::error('Scheduled backup failed: '.$e->getMessage());

            return self::FAILURE;
        }
    }

    protected function cleanupOldBackups(int $retentionDays): void
    {
        try {
            $diskName = config('backup.backup.destination.disks')[0] ?? 'local';
            $disk = Storage::disk($diskName);
            $backupName = config('backup.backup.name', 'Wadexpro');

            $files = $disk->allFiles($backupName);
            $cutoff = now()->subDays($retentionDays);

            $deleted = 0;
            foreach ($files as $file) {
                if (substr($file, -4) === '.zip') {
                    $modified = Carbon::createFromTimestamp($disk->lastModified($file));
                    if ($modified->lt($cutoff)) {
                        $disk->delete($file);
                        $deleted++;
                    }
                }
            }

            if ($deleted > 0) {
                $this->info("Cleaned up {$deleted} old backup(s).");
            }
        } catch (\Exception $e) {
            $this->warn('Could not cleanup old backups: '.$e->getMessage());
        }
    }
}
