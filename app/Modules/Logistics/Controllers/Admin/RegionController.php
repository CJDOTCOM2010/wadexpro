<?php

namespace App\Modules\Logistics\Controllers\Admin;

use App\Core\Traits\ApiResponse;
use App\Http\Controllers\Controller;
use App\Modules\Logistics\Models\Region;
use App\Modules\Logistics\Models\RegionRate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RegionController extends Controller
{
    use ApiResponse;

    /**
     * List all regions.
     */
    public function index()
    {
        $regions = Region::with('rates')->get();
        return $this->success($regions, 'Regions retrieved successfully.');
    }

    /**
     * Store a new region.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|unique:regions,slug',
            'currency_code' => 'required|string|size:3',
            'currency_symbol' => 'required|string|max:10',
            'tax_percentage' => 'required|numeric|min:0|max:100',
            'timezone' => 'required|string',
            'boundary' => 'nullable|array',
        ]);

        $region = Region::create($validated);

        return $this->success($region, 'Region created successfully.', 201);
    }

    /**
     * Get a single region with its rates.
     */
    public function show(string $id)
    {
        $region = Region::with('rates')->findOrFail($id);
        return $this->success($region, 'Region details retrieved.');
    }

    /**
     * Update a region.
     */
    public function update(Request $request, string $id)
    {
        $region = Region::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'string|max:255',
            'currency_code' => 'string|size:3',
            'currency_symbol' => 'string|max:10',
            'tax_percentage' => 'numeric|min:0|max:100',
            'timezone' => 'string',
            'boundary' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        $region->update($validated);

        return $this->success($region, 'Region updated successfully.');
    }

    /**
     * Sync rates for a region.
     */
    public function syncRates(Request $request, string $id)
    {
        $region = Region::findOrFail($id);
        $rates = $request->input('rates', []);

        DB::transaction(function () use ($region, $rates) {
            foreach ($rates as $rateData) {
                RegionRate::updateOrCreate(
                    [
                        'region_id'    => $region->id,
                        'vehicle_type' => $rateData['vehicle_type'],
                    ],
                    [
                        'base_fare'    => $rateData['base_fare'],
                        'per_km'       => $rateData['per_km'],
                        'per_minute'   => $rateData['per_minute'],
                        'minimum_fare' => $rateData['minimum_fare'],
                        'booking_fee'  => $rateData['booking_fee'],
                    ]
                );
            }
        });

        return $this->success($region->load('rates'), 'Rates synced successfully.');
    }

    /**
     * Delete a region.
     */
    public function destroy(string $id)
    {
        $region = Region::findOrFail($id);
        $region->delete();

        return $this->success(null, 'Region deleted successfully.');
    }
}
