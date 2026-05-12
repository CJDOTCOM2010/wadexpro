<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Modules\Admin\Models\SystemSetting;
use Illuminate\Http\Request;

class OrchestratorController extends Controller
{
    /**
     * Display the Global Operations Map.
     */
    public function index()
    {
        // Mock data to prevent errors during rendering
        $liveNodes = 1245; 
        
        // Use standard objects for mock data to match Blade access (e.g. $alert->ride->reference)
        $sosAlerts = collect([
            (object) [
                'ride' => (object) ['reference' => 'RIDE-8A7B9C'],
                'created_at' => now()->subMinutes(2),
            ],
            (object) [
                'ride' => (object) ['reference' => 'RIDE-9D2E1F'],
                'created_at' => now()->subMinutes(15),
            ]
        ]);
        
        $highValueOrders = collect([
            (object) [
                'reference' => 'ORDER-X1Y2Z3',
                'total_amount' => 850.00,
                'created_at' => now()->subMinutes(5),
            ]
        ]);
        
        $recentDeployments = collect([
            (object) [
                'id' => 'NODE-1A2B3C4D5E6F',
                'user' => (object) ['name' => 'Kojo Mensah'],
                'last_location_at' => now()->subSeconds(30),
            ],
            (object) [
                'id' => 'NODE-9Z8Y7X6W5V4U',
                'user' => (object) ['name' => 'Abena Osei'],
                'last_location_at' => now()->subMinutes(1),
            ]
        ]);

        $google_maps_api_key = SystemSetting::get('google_maps_api_key');

        return view('admin.operations_map', compact(
            'liveNodes', 'sosAlerts', 'highValueOrders', 'recentDeployments', 'google_maps_api_key'
        ));
    }

    /**
     * Display the Tactical Dispatcher Grid.
     */
    public function dispatcher()
    {
        $stats = [
            'fleet_load' => 78,
            'active_rides' => 842,
            'online_nodes' => 1083,
        ];
        
        $priorityOrders = collect([
            (object) [
                'reference' => 'INT-VIP-001',
                'priority' => 'urgent',
                'pickup_address' => 'Kotoka International Airport, T3',
            ],
            (object) [
                'reference' => 'INT-MED-042',
                'priority' => 'high',
                'pickup_address' => 'Korle-Bu Teaching Hospital',
            ],
        ]);
        
        $activeDrivers = collect();
        for ($i = 0; $i < 45; $i++) {
            $activeDrivers->push((object) ['id' => uniqid('NODE-')]);
        }

        $google_maps_api_key = SystemSetting::get('google_maps_api_key');

        return view('admin.dispatcher', compact('stats', 'priorityOrders', 'activeDrivers', 'google_maps_api_key'));
    }
}
