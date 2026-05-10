<?php

namespace App\Modules\Logistics\Services;

use App\Modules\Logistics\Models\Order;
use App\Modules\Logistics\Models\Organization;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BulkOrderService
{
    public function __construct(private OrderService $orderService) {}

    /**
     * Process a batch of delivery orders for an organization.
     */
    public function processBatch(Organization $org, array $batchData): array
    {
        $results = [
            'success_count' => 0,
            'failed_count'  => 0,
            'orders'        => [],
            'errors'        => []
        ];

        DB::transaction(function () use ($org, $batchData, &$results) {
            foreach ($batchData as $index => $data) {
                try {
                    // Enrich data with organization context
                    $data['organization_id'] = $org->id;
                    $data['billing_source']  = 'CORPORATE';
                    
                    // Use existing OrderService for core logic (address verification, stop creation)
                    $order = $this->orderService->createOrder($data);
                    
                    $results['orders'][] = $order->id;
                    $results['success_count']++;
                } catch (\Exception $e) {
                    $results['failed_count']++;
                    $results['errors'][] = [
                        'row'   => $index + 1,
                        'error' => $e->getMessage()
                    ];
                    Log::error("Bulk Order Failed (Row ".($index+1)."): " . $e->getMessage());
                }
            }
        });

        return $results;
    }
}
