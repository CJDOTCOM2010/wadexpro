<?php

namespace App\Modules\Logistics\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Modules\Logistics\Models\SurgeZone;
use App\Modules\Logistics\Models\SurgeRule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SurgeManagementController extends Controller
{
    use \App\Core\Traits\ApiResponse;

    /**
     * List all surge zones.
     */
    public function index()
    {
        return $this->success(SurgeZone::with('rules')->get(), 'Surge zones retrieved.');
    }

    /**
     * Store a new surge zone.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'           => 'required|string|max:255',
            'center_lat'      => 'required|numeric',
            'center_lng'      => 'required|numeric',
            'radius_km'       => 'required|numeric|min:0.5',
            'min_multiplier'  => 'required|numeric|min:1.0',
            'max_multiplier'  => 'required|numeric|gte:min_multiplier',
        ]);

        if ($validator->fails()) {
            return $this->error('Validation failed.', 422, $validator->errors());
        }

        $zone = SurgeZone::create($request->all());

        return $this->success($zone, 'Surge zone created successfully.', 201);
    }

    /**
     * Show detailed zone info + rules.
     */
    public function show($id)
    {
        $zone = SurgeZone::with('rules')->findOrFail($id);
        return $this->success($zone, 'Surge zone details retrieved.');
    }

    /**
     * Update a surge zone.
     */
    public function update(Request $request, $id)
    {
        $zone = SurgeZone::findOrFail($id);
        $zone->update($request->all());

        return $this->success($zone, 'Surge zone updated successfully.');
    }

    /**
     * Delete a surge zone.
     */
    public function destroy($id)
    {
        $zone = SurgeZone::findOrFail($id);
        $zone->delete();

        return $this->success(null, 'Surge zone deleted successfully.');
    }

    /**
     * Manage rules for a specific zone.
     */
    public function syncRules(Request $request, $id)
    {
        $zone = SurgeZone::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'rules' => 'required|array',
            'rules.*.demand_threshold' => 'required|integer',
            'rules.*.supply_threshold' => 'required|integer',
            'rules.*.multiplier'       => 'required|numeric|min:1.0',
        ]);

        if ($validator->fails()) {
            return $this->error('Validation failed.', 422, $validator->errors());
        }

        DB::transaction(function () use ($zone, $request) {
            $zone->rules()->delete();
            foreach ($request->rules as $ruleData) {
                $zone->rules()->create($ruleData);
            }
        });

        return $this->success($zone->load('rules'), 'Surge rules synchronized successfully.');
    }
}
