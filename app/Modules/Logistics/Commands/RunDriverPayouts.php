<?php

namespace App\Modules\Logistics\Commands;

use Illuminate\Console\Command;
use App\Modules\Logistics\Models\Driver;
use App\Modules\Logistics\Services\PayoutService;

class RunDriverPayouts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payouts:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Aggregates un-paid deliveries and initiates payouts for all active drivers.';

    /**
     * Execute the console command.
     */
    public function handle(PayoutService $payoutService)
    {
        $this->info('Starting automated driver payout aggregation...');

        $drivers = Driver::where('status', 'active')->get();
        $generatedCount = 0;

        foreach ($drivers as $driver) {
            $result = $payoutService->calculateDriverPayout($driver);
            
            if ($result['success']) {
                $this->line("Payout generated for Driver {$driver->id}: {$result['amount']} " . ($result['currency'] ?? 'GHS') . " across {$result['orders_count']} deliveries.");
                $generatedCount++;
            }
        }

        $this->info("Completed. Generated {$generatedCount} payouts.");
        return Command::SUCCESS;
    }
}
