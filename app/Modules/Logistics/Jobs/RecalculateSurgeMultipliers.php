<?php

namespace App\Modules\Logistics\Jobs;

use App\Modules\Logistics\Models\SurgeZone;
use App\Modules\Logistics\Services\SurgeService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class RecalculateSurgeMultipliers implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(SurgeService $surgeService): void
    {
        $zones = SurgeZone::where('is_active', true)->get();

        foreach ($zones as $zone) {
            try {
                // Clear cache first to ensure a fresh recalculation
                $surgeService->flushZoneCache($zone->id);
                
                // Recalculate
                $multiplier = $surgeService->recalculateSurge($zone->id);
                
                if ($multiplier > 1.0) {
                    Log::info("Surge Active in [{$zone->name}]: {$multiplier}x");
                }
            } catch (\Exception $e) {
                Log::error("Failed to recalculate surge for zone [{$zone->name}]: " . $e->getMessage());
            }
        }
    }
}
