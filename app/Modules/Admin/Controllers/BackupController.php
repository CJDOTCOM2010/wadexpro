<?php

namespace App\Modules\Admin\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Carbon\Carbon;

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
            
            $files = $disk->allFiles($backupName);
            
            $backups = [];
            foreach ($files as $file) {
                if (substr($file, -4) == '.zip') {
                    $backups[] = [
                        'file_path' => $file,
                        'file_name' => str_replace($backupName . '/', '', $file),
                        'file_size' => $this->formatBytes($disk->size($file)),
                        'last_modified' => Carbon::createFromTimestamp($disk->lastModified($file))->toDateTimeString(),
                        'age' => Carbon::createFromTimestamp($disk->lastModified($file))->diffForHumans(),
                    ];
                }
            }

            // Sort by date descending
            usort($backups, function($a, $b) {
                return ($b['last_modified'] ?? '') <=> ($a['last_modified'] ?? '');
            });

            return view('admin.settings.backup', compact('backups'));
        } catch (\Exception $e) {
            Log::error('Backup Index Error: ' . $e->getMessage());
            return view('admin.settings.backup', ['backups' => []]);
        }
    }

    /**
     * Create a new backup.
     */
    public function create(Request $request)
    {
        $option = $request->get('option', 'all'); // 'all', 'only-db', 'only-files'
        
        try {
            if ($option === 'only-db') {
                Artisan::queue('backup:run', ['--only-db' => true]);
            } elseif ($option === 'only-files') {
                Artisan::queue('backup:run', ['--only-files' => true]);
            } else {
                Artisan::queue('backup:run');
            }

            return back()->with('success', 'Backup process started in the background. It will appear here shortly.');
        } catch (\Exception $e) {
            Log::error('Backup Error: ' . $e->getMessage());
            return back()->with('error', 'Failed to start backup: ' . $e->getMessage());
        }
    }

    /**
     * Download a backup.
     */
    public function download($fileName)
    {
        $disk = Storage::disk(config('backup.backup.destination.disks')[0]);
        $filePath = config('backup.backup.name') . '/' . $fileName;

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
        $filePath = config('backup.backup.name') . '/' . $fileName;

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
            return back()->with('error', 'Cleanup failed: ' . $e->getMessage());
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
        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}
