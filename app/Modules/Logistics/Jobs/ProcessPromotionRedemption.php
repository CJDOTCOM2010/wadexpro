<?php

namespace App\Modules\Logistics\Jobs;

use App\Modules\Logistics\Models\Promotion;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessPromotionRedemption implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(protected string $promoId)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Promotion::where('id', $this->promoId)->increment('times_used');
    }
}
