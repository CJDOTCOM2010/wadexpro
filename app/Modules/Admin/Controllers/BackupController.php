<?php

namespace App\Modules\Admin\Controllers;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

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

            return view('admin.settings.backup', compact('backups'));
        } catch (\Throwable $e) {
            Log::error('Backup Index Critical Error: '.$e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return response('Critical System Error: '.$e->getMessage(), 500);
        }
    }

    public function create(Request $request)
    {
        $option = $request->get('option', 'all'); // 'all', 'only-db', 'only-files'

        try {
            $exitCode = 0;
            if ($option === 'only-db') {
                $exitCode = Artisan::call('backup:run', ['--only-db' => true]);
            } elseif ($option === 'only-files') {
                $exitCode = Artisan::call('backup:run', ['--only-files' => true]);
            } else {
                $exitCode = Artisan::call('backup:run');
            }

            $output = Artisan::output();

            if ($exitCode !== 0) {
                // Determine if it's the pg_dump issue
                if (str_contains($output, 'pg_dump') || str_contains($output, 'dump process failed')) {
                    return back()->with('error', 'Database backup failed: Your server does not have PostgreSQL utilities (pg_dump) installed to backup the remote Supabase database. Please use the Supabase Dashboard for database backups, or select "Media Vault (Files only)" to backup your uploaded files.');
                }
                return back()->with('error', 'Backup failed with exit code ' . $exitCode . '. Check system logs.');
            }

            return back()->with('success', 'Backup completed successfully and is now securely stored in your vault.');
        } catch (\Exception $e) {
            Log::error('Backup Error: '.$e->getMessage());

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
