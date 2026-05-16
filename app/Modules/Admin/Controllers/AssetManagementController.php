<?php

namespace App\Modules\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Admin\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class AssetManagementController extends Controller
{
    /**
     * Display the asset management dashboard.
     */
    public function index(Request $request)
    {
        $disk = $request->get('disk', 'public');
        $directory = $request->get('path', '');

        // Ensure path is safe
        $directory = str_replace(['..', './'], '', $directory);

        $files = [];
        $directories = [];
        $storageDriver = 'Unknown';

        try {
            $storageDriver = config("filesystems.disks.{$disk}.driver") ?? 'unknown';

            if (Storage::disk($disk)->exists($directory)) {
                $allFiles = Storage::disk($disk)->files($directory);
                $allDirs = Storage::disk($disk)->directories($directory);

                foreach ($allFiles as $file) {
                    try {
                        $files[] = [
                            'name' => basename($file),
                            'path' => $file,
                            'url' => Storage::disk($disk)->url($file),
                            'size' => $this->formatBytes(Storage::disk($disk)->size($file)),
                            'last_modified' => date('Y-m-d H:i:s', Storage::disk($disk)->lastModified($file)),
                            'extension' => pathinfo($file, PATHINFO_EXTENSION),
                            'is_image' => in_array(strtolower(pathinfo($file, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp']),
                        ];
                    } catch (\Exception $e) {
                        // Gracefully degrade if file metadata is missing on remote Supabase/S3 disk
                        $files[] = [
                            'name' => basename($file),
                            'path' => $file,
                            'url' => '#',
                            'size' => 'Unknown',
                            'last_modified' => 'Unknown',
                            'extension' => pathinfo($file, PATHINFO_EXTENSION),
                            'is_image' => in_array(strtolower(pathinfo($file, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp']),
                        ];
                    }
                }

                foreach ($allDirs as $dir) {
                    $directories[] = [
                        'name' => basename($dir),
                        'path' => $dir,
                    ];
                }
            }
        } catch (\Exception $e) {
            // Handle unreachable cloud storage gracefully (returns empty lists)
        }

        // Storage Stats
        $stats = [
            'disk' => $disk,
            'total_files' => count($files),
            'total_dirs' => count($directories),
            'storage_driver' => $storageDriver,
        ];

        return view('admin.settings.assets', compact('files', 'directories', 'directory', 'stats', 'disk'));
    }

    /**
     * Handle file uploads.
     */
    public function upload(Request $request)
    {
        $request->validate([
            'files.*' => 'required|file|max:10240', // 10MB limit
            'path' => 'nullable|string',
            'disk' => 'nullable|string',
        ]);

        $disk = $request->get('disk', 'public');
        $path = $request->get('path', 'uploads');

        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $file->store($path, $disk);
            }
        }

        return back()->with('success', 'Assets uploaded successfully.');
    }

    /**
     * Delete an asset.
     */
    public function delete(Request $request)
    {
        $request->validate([
            'path' => 'required|string',
            'disk' => 'required|string',
        ]);

        if (Storage::disk($request->disk)->exists($request->path)) {
            Storage::disk($request->disk)->delete($request->path);

            return back()->with('success', 'Asset deleted successfully.');
        }

        return back()->with('error', 'Asset not found.');
    }

    public function createFolder(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'path' => 'nullable|string',
            'disk' => 'nullable|string',
        ]);

        $disk = $request->get('disk', 'public');
        $folderPath = trim($request->get('path', '').'/'.$request->name, '/');

        if (Storage::disk($disk)->exists($folderPath)) {
            return back()->with('error', 'Folder already exists.');
        }

        Storage::disk($disk)->makeDirectory($folderPath);

        return back()->with('success', "Folder '{$request->name}' created.");
    }

    public function rename(Request $request)
    {
        $request->validate([
            'old_path' => 'required|string',
            'new_name' => 'required|string|max:255',
            'disk' => 'nullable|string',
        ]);

        $disk = $request->get('disk', 'public');
        $dir = dirname($request->old_path);
        $newPath = ($dir === '.' ? '' : $dir).'/'.$request->new_name;
        $newPath = ltrim($newPath, '/');

        if (! Storage::disk($disk)->exists($request->old_path)) {
            return back()->with('error', 'File or folder not found.');
        }

        if (Storage::disk($disk)->exists($newPath)) {
            return back()->with('error', 'A file or folder with that name already exists.');
        }

        Storage::disk($disk)->move($request->old_path, $newPath);

        return back()->with('success', "Renamed to '{$request->new_name}'.");
    }

    /**
     * Update storage configuration.
     */
    public function updateConfig(Request $request)
    {
        $settings = $request->input('settings', []);

        foreach ($settings as $key => $value) {
            SystemSetting::set($key, $value);
        }

        return back()->with('success', 'Storage configuration updated.');
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
