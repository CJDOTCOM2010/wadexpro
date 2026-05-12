<?php

namespace App\Modules\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Logistics\Models\VehicleType;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class VehicleTypeController extends Controller
{
    /**
     * List all vehicle types.
     */
    public function index()
    {
        $vehicleTypes = VehicleType::withCount(['vehicles' => function ($q) {
            $q->where('is_active', true);
        }])->orderBy('sort_order')->get();

        return view('admin.vehicle_types', compact('vehicleTypes'));
    }

    /**
     * Store a new vehicle type.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'             => 'required|string|max:80',
            'description'      => 'nullable|string',
            'base_fare'        => 'required|numeric|min:0',
            'per_km_rate'      => 'required|numeric|min:0',
            'per_minute_rate'  => 'required|numeric|min:0',
            'min_fare'         => 'required|numeric|min:0',
            'capacity'         => 'required|integer|min:1|max:20',
            'max_weight_kg'    => 'nullable|numeric|min:0',
            'service_types'    => 'nullable|array',
            'sort_order'       => 'nullable|integer|min:0',
        ]);

        $data['slug']      = Str::slug($data['name']);
        $data['is_active'] = true;

        VehicleType::create($data);

        return back()->with('success', "Vehicle type '{$data['name']}' created successfully.");
    }

    /**
     * Update an existing vehicle type.
     */
    public function update(Request $request, $id)
    {
        $vehicleType = VehicleType::findOrFail($id);

        $data = $request->validate([
            'name'             => 'required|string|max:80',
            'description'      => 'nullable|string',
            'base_fare'        => 'required|numeric|min:0',
            'per_km_rate'      => 'required|numeric|min:0',
            'per_minute_rate'  => 'required|numeric|min:0',
            'min_fare'         => 'required|numeric|min:0',
            'capacity'         => 'required|integer|min:1|max:20',
            'max_weight_kg'    => 'nullable|numeric|min:0',
            'service_types'    => 'nullable|array',
            'sort_order'       => 'nullable|integer|min:0',
        ]);

        $vehicleType->update($data);

        return back()->with('success', "Vehicle type updated successfully.");
    }

    /**
     * Toggle active / inactive status.
     */
    public function toggle($id)
    {
        $vehicleType = VehicleType::findOrFail($id);
        $vehicleType->update(['is_active' => !$vehicleType->is_active]);

        $state = $vehicleType->is_active ? 'enabled' : 'disabled';
        return back()->with('success', "Vehicle type {$state}.");
    }

    /**
     * Delete a vehicle type.
     */
    public function destroy($id)
    {
        $vehicleType = VehicleType::findOrFail($id);
        $vehicleType->delete();

        return back()->with('success', "Vehicle type deleted.");
    }
}
