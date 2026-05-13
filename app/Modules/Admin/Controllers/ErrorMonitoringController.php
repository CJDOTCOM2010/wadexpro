<?php

namespace App\Modules\Admin\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class ErrorMonitoringController extends Controller
{
    /**
     * Display the error monitoring dashboard.
     */
    public function index(Request $request)
    {
        $logFile = storage_path('logs/laravel.log');
        $logs = [];

        if (File::exists($logFile)) {
            $logContent = File::get($logFile);
            $logs = $this->parseLogs($logContent);
        }

        // Filter functionality
        $level = $request->query('level');
        if ($level) {
            $logs = array_filter($logs, fn($log) => strtolower($log['level']) === strtolower($level));
        }

        // Pagination manually since it's an array
        $page = request()->get('page', 1);
        $perPage = 50;
        $offset = ($page - 1) * $perPage;
        
        $totalLogs = count($logs);
        $pagedLogs = array_slice($logs, $offset, $perPage);
        
        $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
            $pagedLogs,
            $totalLogs,
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        // Stats
        $stats = [
            'total' => $totalLogs,
            'errors' => count(array_filter($logs, fn($l) => in_array($l['level'], ['ERROR', 'CRITICAL', 'EMERGENCY', 'ALERT']))),
            'warnings' => count(array_filter($logs, fn($l) => $l['level'] === 'WARNING')),
            'info' => count(array_filter($logs, fn($l) => in_array($l['level'], ['INFO', 'NOTICE', 'DEBUG']))),
        ];

        return view('admin.error_monitoring', compact('paginator', 'stats'));
    }

    /**
     * Clear the log file.
     */
    public function clear()
    {
        $logFile = storage_path('logs/laravel.log');
        
        if (File::exists($logFile)) {
            File::put($logFile, '');
        }

        return back()->with('success', 'System error logs have been flushed successfully.');
    }

    /**
     * Basic parsing of Laravel log format.
     */
    private function parseLogs(string $logContent): array
    {
        $pattern = '/\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\] ([\w\.]+)\.([A-Z]+): (.*)/';
        $logs = [];

        // Split by lines that start with the date format
        $entries = preg_split('/(?=\[\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}\])/', $logContent);

        foreach ($entries as $entry) {
            $entry = trim($entry);
            if (empty($entry)) {
                continue;
            }

            // Get the first line
            $lines = explode("\n", $entry);
            $firstLine = $lines[0];

            if (preg_match($pattern, $firstLine, $matches)) {
                $logs[] = [
                    'timestamp' => $matches[1],
                    'environment' => $matches[2],
                    'level' => $matches[3],
                    'message' => $matches[4],
                    'stack_trace' => implode("\n", array_slice($lines, 1))
                ];
            }
        }

        // Reverse to show newest first
        return array_reverse($logs);
    }
}
