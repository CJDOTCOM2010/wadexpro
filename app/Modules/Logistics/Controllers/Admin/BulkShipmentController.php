<?php

namespace App\Modules\Logistics\Controllers\Admin;

use App\Core\Traits\ApiResponse;
use App\Http\Controllers\Controller;
use App\Modules\Logistics\Models\Organization;
use App\Modules\Logistics\Services\BulkOrderService;
use Illuminate\Http\Request;

class BulkShipmentController extends Controller
{
    use ApiResponse;

    public function __construct(private BulkOrderService $bulkService) {}

    /**
     * Process a bulk shipment upload for an organization.
     */
    public function store(Request $request, string $orgId)
    {
        $org = Organization::findOrFail($orgId);
        
        $validated = $request->validate([
            'batch' => 'required|array|min:1|max:50', // Capped at 50 for stability
            'batch.*.pickup_address' => 'required|string',
            'batch.*.pickup_lat'     => 'required|numeric',
            'batch.*.pickup_lng'     => 'required|numeric',
            'batch.*.stops'          => 'required|array|min:1',
            'batch.*.package_description' => 'required|string',
            'batch.*.priority'       => 'nullable|in:standard,express,overnight',
        ]);

        $results = $this->bulkService->processBatch($org, $validated['batch']);

        return $this->success($results, 'Bulk shipment processing completed.');
    }
}
