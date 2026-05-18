<?php

namespace App\Modules\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\CronJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class CronJobController extends Controller
{
    public function index()
    {
        $cronJobs = CronJob::orderBy('created_at', 'desc')->get();
        $scheduleOptions = CronJob::getScheduleOptions();
        $defaultJobs = CronJob::getDefaultCronJobs();

        return view('admin.settings.cron', compact('cronJobs', 'scheduleOptions', 'defaultJobs'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:cron_jobs,name',
            'command' => 'required|string|max:500',
            'schedule' => 'required|string|max:50',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['status'] = 'pending';
        $validated['next_run'] = now()->addDay();

        CronJob::create($validated);

        return back()->with('success', 'Cron job created successfully.');
    }

    public function update(Request $request, $id)
    {
        $cronJob = CronJob::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255|unique:cron_jobs,name,'.$id,
            'command' => 'sometimes|string|max:500',
            'schedule' => 'sometimes|string|max:50',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
        ]);

        if (isset($validated['is_active'])) {
            $validated['is_active'] = $request->boolean('is_active', true);
        }

        $cronJob->update($validated);

        return back()->with('success', 'Cron job updated successfully.');
    }

    public function destroy($id)
    {
        $cronJob = CronJob::findOrFail($id);
        $cronJob->delete();

        return back()->with('success', 'Cron job deleted successfully.');
    }

    public function toggle($id)
    {
        $cronJob = CronJob::findOrFail($id);
        $cronJob->is_active = ! $cronJob->is_active;
        $cronJob->save();

        $status = $cronJob->is_active ? 'enabled' : 'disabled';

        return back()->with('success', "Cron job {$status}.");
    }

    public function runNow($id)
    {
        $cronJob = CronJob::findOrFail($id);

        try {
            $cronJob->update([
                'status' => 'running',
                'last_run' => now(),
            ]);

            $startTime = microtime(true);
            $exitCode = Artisan::call($cronJob->command);
            $duration = round(microtime(true) - $startTime, 2);

            $cronJob->update([
                'status' => $exitCode === 0 ? 'completed' : 'failed',
                'last_output' => "Exit code: {$exitCode}, Duration: {$duration}s",
            ]);

            return back()->with('success', "Command executed. Exit code: {$exitCode}, Duration: {$duration}s");
        } catch (\Exception $e) {
            Log::error('Cron job execution failed: '.$e->getMessage());
            $cronJob->update([
                'status' => 'failed',
                'last_output' => $e->getMessage(),
            ]);

            return back()->with('error', 'Command execution failed: '.$e->getMessage());
        }
    }

    public function generateCrontab()
    {
        $activeJobs = CronJob::where('is_active', true)->get();
        $lines = [];

        $lines[] = '# WADEXPRO CRON JOBS';
        $lines[] = '# Generated at: '.now()->toDateTimeString();
        $lines[] = '';

        foreach ($activeJobs as $job) {
            $cron = CronJob::getScheduleCron($job->schedule);
            $lines[] = "# {$job->name}";
            $lines[] = "# Description: {$job->description}";
            $lines[] = "{$cron} php ".base_path('artisan')." {$job->command}";
            $lines[] = '';
        }

        return response()->json([
            'crontab' => implode("\n", $lines),
            'jobs_count' => $activeJobs->count(),
        ]);
    }

    public function installDefaults(Request $request)
    {
        $defaults = CronJob::getDefaultCronJobs();

        foreach ($defaults as $job) {
            if (! CronJob::where('name', $job['name'])->exists()) {
                CronJob::create(array_merge($job, [
                    'status' => 'pending',
                    'next_run' => now()->addDay(),
                ]));
            }
        }

        return back()->with('success', 'Default cron jobs installed.');
    }
}
