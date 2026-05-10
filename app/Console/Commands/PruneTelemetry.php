<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Modules\Logistics\Models\TrackingEvent;
use Illuminate\Support\Facades\Log;

class PruneTelemetry extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wadex:prune-telemetry {--days=3 : Retention period in days}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Prune old GPS telemetry data to prevent database bloat.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = $this->option('days');
        $cutoff = now()->subDays($days);

        $this->info("Pruning telemetry data older than $days days (Cutoff: $cutoff)...");

        $count = TrackingEvent::where('recorded_at', '<', $cutoff)->delete();

        $this->info("Successfully removed $count telemetry records.");
        
        Log::info('WADEX Telemetry Pruning Complete', [
            'records_removed' => $count,
            'retention_days' => $days
        ]);

        return Command::SUCCESS;
    }
}
