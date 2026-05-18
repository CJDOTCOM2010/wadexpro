<?php

namespace App\Jobs;

use App\Models\BackupJob;
use App\Services\DatabaseDumper;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class RunDatabaseBackup implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * No timeout — large databases can take a long time.
     */
    public int $timeout = 0;
    public int $tries = 1;

    public function __construct(
        protected string $backupJobId,
        protected string $type = 'database'
    ) {}

    public function handle(): void
    {
        // Remove all PHP execution limits
        set_time_limit(0);
        ini_set('memory_limit', '1G');

        $job = BackupJob::findOrFail($this->backupJobId);
        $job->markRunning();

        try {
            $backupName = config('backup.backup.name', 'Laravel');
            $diskName = config('backup.backup.destination.disks')[0] ?? 'local';
            $disk = Storage::disk($diskName);
            $timestamp = now()->format('Y-m-d-H-i-s');
            $tempDir = storage_path('app/backup-temp');

            if (!is_dir($tempDir)) {
                mkdir($tempDir, 0755, true);
            }

            if (!$disk->directoryExists($backupName)) {
                $disk->makeDirectory($backupName);
            }

            if ($this->type === 'only-files') {
                $this->runFilesBackup($job, $disk, $backupName, $timestamp, $tempDir);
            } else {
                $this->runDatabaseBackup($job, $disk, $backupName, $timestamp, $tempDir);
            }

        } catch (\Throwable $e) {
            Log::error('BackupJob failed: ' . $e->getMessage(), [
                'job_id' => $this->backupJobId,
                'trace' => $e->getTraceAsString(),
            ]);
            $job->markFailed($e->getMessage());
        }
    }

    protected function runDatabaseBackup(BackupJob $job, $disk, string $backupName, string $timestamp, string $tempDir): void
    {
        $suffix = $this->type === 'all' ? 'full-system' : 'database';
        $sqlFile = $tempDir . "/wadexpro-dump-{$timestamp}.sql";
        $tempZip = $tempDir . "/wadexpro-{$suffix}-{$timestamp}.zip";
        $zipFileName = "{$backupName}/wadexpro-{$suffix}-{$timestamp}.zip";

        // Step 1: Dump the database
        $job->updateProgress(5, 'Connecting to database…');

        $dumper = new DatabaseDumper($sqlFile);

        // Pass a progress callback so we can update the UI in real time
        $dumper->onProgress(function (int $progress, string $step, array $stats) use ($job) {
            $job->updateProgress($progress, $step, [
                'tables_total' => $stats['tables_total'] ?? 0,
                'tables_done' => $stats['tables_done'] ?? 0,
                'rows_dumped' => $stats['rows_dumped'] ?? 0,
            ]);
        });

        $job->updateProgress(10, 'Analysing database schema…');
        $dumper->dump();

        $dumpSize = filesize($sqlFile);
        $job->updateProgress(80, "SQL dump complete (" . $this->formatBytes($dumpSize) . ") — creating archive…");

        // Step 2: Package into ZIP
        $zip = new ZipArchive();
        if ($zip->open($tempZip, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new \RuntimeException('Failed to create ZIP archive.');
        }

        $zip->addFile($sqlFile, "db-dumps/wadexpro-full-database.sql");

        // If full-system, add storage files too
        if ($this->type === 'all') {
            $job->updateProgress(85, 'Adding media files to archive…');
            $storagePath = storage_path('app/public');
            if (is_dir($storagePath)) {
                $this->addDirToZip($zip, $storagePath, 'storage/app/public');
            }
        }

        $zip->close();

        $job->updateProgress(92, 'Uploading archive to storage…');

        // Step 3: Move to final storage disk
        $zipHandle = fopen($tempZip, 'rb');
        $disk->put($zipFileName, $zipHandle);
        if (is_resource($zipHandle)) fclose($zipHandle);

        // Clean up temp files
        @unlink($sqlFile);
        @unlink($tempZip);

        $finalSize = $disk->size($zipFileName);
        $fileName = basename($zipFileName);

        $job->markCompleted($fileName, $finalSize);

        Log::info('BackupJob completed', [
            'job_id' => $this->backupJobId,
            'file' => $fileName,
            'size' => $this->formatBytes($finalSize),
        ]);
    }

    protected function runFilesBackup(BackupJob $job, $disk, string $backupName, string $timestamp, string $tempDir): void
    {
        $job->updateProgress(10, 'Running file backup…');
        $exitCode = Artisan::call('backup:run', ['--only-files' => true]);
        if ($exitCode !== 0) {
            throw new \RuntimeException('File backup failed with exit code: ' . $exitCode);
        }
        $job->markCompleted('files-backup.zip', 0);
    }

    protected function addDirToZip(ZipArchive $zip, string $realPath, string $zipPath): void
    {
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($realPath, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::LEAVES_ONLY
        );
        foreach ($files as $file) {
            if ($file->isFile()) {
                $relative = $zipPath . '/' . substr($file->getRealPath(), strlen($realPath) + 1);
                $zip->addFile($file->getRealPath(), $relative);
            }
        }
    }

    protected function formatBytes(int $bytes): string
    {
        foreach (['B', 'KB', 'MB', 'GB', 'TB'] as $unit) {
            if ($bytes < 1024) return round($bytes, 2) . ' ' . $unit;
            $bytes /= 1024;
        }
        return round($bytes, 2) . ' TB';
    }
}
