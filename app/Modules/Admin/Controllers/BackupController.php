<?php

namespace App\Modules\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Services\DatabaseDumper;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class BackupController extends Controller
{
    /**
     * Display a listing of backups.
     */
    public function index()
    {
        try {
            $diskName = config('backup.backup.destination.disks')[0] ?? 'local';
            $disk = Storage::disk($diskName);
            $backupName = config('backup.backup.name', 'Laravel');

            $files = [];
            try {
                $files = $disk->allFiles($backupName);
            } catch (\Exception $e) {
                Log::warning('Backup directory scan failed: '.$e->getMessage());
            }

            $backups = [];
            foreach ($files as $file) {
                if (substr($file, -4) == '.zip') {
                    try {
                        $backups[] = [
                            'file_path' => $file,
                            'file_name' => str_replace($backupName.'/', '', $file),
                            'file_size' => $this->formatBytes($disk->size($file)),
                            'last_modified' => Carbon::createFromTimestamp($disk->lastModified($file))->toDateTimeString(),
                            'age' => Carbon::createFromTimestamp($disk->lastModified($file))->diffForHumans(),
                        ];
                    } catch (\Exception $e) {
                        Log::warning('Failed to process backup file: '.$file.' - '.$e->getMessage());
                    }
                }
            }

            // Sort by date descending
            usort($backups, function ($a, $b) {
                return ($b['last_modified'] ?? '') <=> ($a['last_modified'] ?? '');
            });

            // Get live database stats for the UI
            $dbStats = $this->getDatabaseStats();

            return view('admin.settings.backup', compact('backups', 'dbStats'));
        } catch (\Throwable $e) {
            Log::error('Backup Index Critical Error: '.$e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return response('Critical System Error: '.$e->getMessage(), 500);
        }
    }

    /**
     * Create a new backup.
     *
     * Uses three strategies:
     *   - "all"        → Full system (custom DB dump + files)
     *   - "only-db"    → Database only (custom PHP-based dump, no pg_dump needed)
     *   - "only-files"  → Media/files only (spatie backup:run --only-files)
     */
    public function create(Request $request)
    {
        $option = $request->get('option', 'all'); // 'all', 'only-db', 'only-files'

        try {
            if ($option === 'only-files') {
                // Files-only backup uses spatie (no database involved)
                $exitCode = Artisan::call('backup:run', ['--only-files' => true]);
                if ($exitCode !== 0) {
                    return back()->with('error', 'File backup failed with exit code ' . $exitCode . '. Check system logs.');
                }
                return back()->with('success', 'Media vault backup completed successfully.');
            }

            // For "only-db" or "all", use the custom PHP-based database dumper
            $timestamp = now()->format('Y-m-d-H-i-s');
            $backupName = config('backup.backup.name', 'Laravel');
            $diskName = config('backup.backup.destination.disks')[0] ?? 'local';
            $disk = Storage::disk($diskName);

            // Ensure the backup directory exists
            $backupDir = $backupName;
            if (!$disk->directoryExists($backupDir)) {
                $disk->makeDirectory($backupDir);
            }

            // Step 1: Generate SQL dump using our custom PHP dumper
            $tempDir = storage_path('app/backup-temp');
            if (!is_dir($tempDir)) {
                mkdir($tempDir, 0755, true);
            }

            $sqlFile = $tempDir . '/database-dump.sql';
            $dumper = new DatabaseDumper($sqlFile);
            $dumper->dump();

            // Step 2: Create a ZIP archive
            if ($option === 'only-db') {
                $zipFileName = "{$backupDir}/{$timestamp}-database.zip";
            } else {
                $zipFileName = "{$backupDir}/{$timestamp}-full-system.zip";
            }

            $tempZipPath = $tempDir . '/backup.zip';
            $zip = new ZipArchive();

            if ($zip->open($tempZipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
                throw new \RuntimeException('Failed to create ZIP archive.');
            }

            // Always add the database dump
            $zip->addFile($sqlFile, 'db-dumps/database-dump.sql');

            // If "all", also include application files
            if ($option === 'all') {
                $this->addDirectoryToZip($zip, storage_path('app/public'), 'storage/app/public');
            }

            $zip->close();

            // Step 3: Move the ZIP to the backup disk
            $disk->put($zipFileName, file_get_contents($tempZipPath));

            // Step 4: Clean up temp files
            @unlink($sqlFile);
            @unlink($tempZipPath);

            $sizeFormatted = $this->formatBytes($disk->size($zipFileName));

            return back()->with('success', "Backup completed successfully! ({$sizeFormatted}) — All database tables, sequences, indexes, and constraints have been captured.");

        } catch (\Exception $e) {
            Log::error('Backup Error: '.$e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->with('error', 'Failed to execute backup: '.$e->getMessage());
        }
    }

    /**
     * Download a backup.
     */
    public function download($fileName)
    {
        $disk = Storage::disk(config('backup.backup.destination.disks')[0]);
        $filePath = config('backup.backup.name').'/'.$fileName;

        if ($disk->exists($filePath)) {
            return $disk->download($filePath);
        }

        return back()->with('error', 'Backup file not found.');
    }

    /**
     * Delete a backup.
     */
    public function delete($fileName)
    {
        $disk = Storage::disk(config('backup.backup.destination.disks')[0]);
        $filePath = config('backup.backup.name').'/'.$fileName;

        if ($disk->exists($filePath)) {
            $disk->delete($filePath);

            return back()->with('success', 'Backup deleted successfully.');
        }

        return back()->with('error', 'Backup file not found.');
    }

    /**
     * Clean up old backups based on strategy.
     */
    public function clean()
    {
        try {
            Artisan::call('backup:clean');

            return back()->with('success', 'Cleanup process completed successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Cleanup failed: '.$e->getMessage());
        }
    }

    /**
     * Get live database statistics for display in the UI.
     */
    protected function getDatabaseStats(): array
    {
        try {
            $tableCount = \DB::select("
                SELECT count(*) as cnt
                FROM information_schema.tables
                WHERE table_schema = 'public' AND table_type = 'BASE TABLE'
            ");

            $dbSize = \DB::selectOne("SELECT pg_size_pretty(pg_database_size(current_database())) as size");

            $totalRows = 0;
            $tables = \DB::select("
                SELECT table_name
                FROM information_schema.tables
                WHERE table_schema = 'public' AND table_type = 'BASE TABLE'
            ");
            foreach ($tables as $t) {
                try {
                    $rowCount = \DB::selectOne("SELECT count(*) as cnt FROM \"{$t->table_name}\"");
                    $totalRows += $rowCount->cnt ?? 0;
                } catch (\Exception $e) {
                    // Skip tables we can't read
                }
            }

            return [
                'table_count' => $tableCount[0]->cnt ?? 0,
                'db_size' => $dbSize->size ?? 'Unknown',
                'total_rows' => number_format($totalRows),
                'connection' => config('database.default'),
                'host' => config('database.connections.pgsql.host', 'URL-based'),
            ];
        } catch (\Exception $e) {
            Log::warning('Could not fetch database stats: ' . $e->getMessage());
            return [
                'table_count' => '—',
                'db_size' => '—',
                'total_rows' => '—',
                'connection' => config('database.default'),
                'host' => 'Unknown',
            ];
        }
    }

    /**
     * Recursively add a directory to a ZipArchive.
     */
    protected function addDirectoryToZip(ZipArchive $zip, string $realPath, string $zipPath): void
    {
        if (!is_dir($realPath)) return;

        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($realPath, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($files as $file) {
            if ($file->isFile()) {
                $filePath = $file->getRealPath();
                $relativePath = $zipPath . '/' . substr($filePath, strlen($realPath) + 1);
                $zip->addFile($filePath, $relativePath);
            }
        }
    }

    /**
     * Helper to format bytes.
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);

        return round($bytes, $precision).' '.$units[$pow];
    }
}
