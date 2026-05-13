<?php

namespace App\Modules\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Admin\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

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
        
        if (Storage::disk($disk)->exists($directory)) {
            $allFiles = Storage::disk($disk)->files($directory);
            $allDirs = Storage::disk($disk)->directories($directory);
            
            foreach ($allFiles as $file) {
                $files[] = [
                    'name' => basename($file),
                    'path' => $file,
                    'url' => Storage::disk($disk)->url($file),
                    'size' => $this->formatBytes(Storage::disk($disk)->size($file)),
                    'last_modified' => date('Y-m-d H:i:s', Storage::disk($disk)->lastModified($file)),
                    'extension' => pathinfo($file, PATHINFO_EXTENSION),
                    'is_image' => in_array(strtolower(pathinfo($file, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp']),
                ];
            }
            
            foreach ($allDirs as $dir) {
                $directories[] = [
                    'name' => basename($dir),
                    'path' => $dir,
                ];
            }
        }

        // Storage Stats
        $stats = [
            'disk' => $disk,
            'total_files' => count($files),
            'total_dirs' => count($directories),
            'storage_driver' => config("filesystems.disks.{$disk}.driver"),
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
            'disk' => 'nullable|string'
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
            'disk' => 'required|string'
        ]);

        if (Storage::disk($request->disk)->exists($request->path)) {
            Storage::disk($request->disk)->delete($request->path);
            return back()->with('success', 'Asset deleted successfully.');
        }

        return back()->with('error', 'Asset not found.');
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
        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}
