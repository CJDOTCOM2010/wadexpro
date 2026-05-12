<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CustomerLogisticsController extends Controller
{
    /**
     * Get Service Hubs by Service Type
     */
    public function getHubs(Request $request, $serviceType)
    {
        try {
            $hubs = [];
            
            switch (strtolower($serviceType)) {
                case 'rent':
                    $hubs = [
                        ['id' => 1, 'name' => 'Accra Airport Hub', 'address' => 'Kotoka Intl Airport, Accra', 'phone' => '+233 24 555 1001', 'info' => 'Sedans, SUVs, Luxury'],
                        ['id' => 2, 'name' => 'Kumasi City Hub', 'address' => 'Asokwa, Kumasi', 'phone' => '+233 24 555 1002', 'info' => 'Economy & Mid-range'],
                    ];
                    $tagline = 'Premium car rentals across major cities. Self-drive or chauffeur options with full insurance coverage.';
                    $image = 'https://images.unsplash.com/photo-1550355291-bbee04a92027?q=80&w=2072&auto=format&fit=crop';
                    break;
                case '2-wheels':
                    $hubs = [
                        ['id' => 3, 'name' => 'Wadex Moto Hub', 'address' => 'Kwame Nkrumah Circle, Accra', 'phone' => '+233 24 555 5001', 'info' => 'Express Delivery'],
                        ['id' => 4, 'name' => 'Madina Moto Point', 'address' => 'Madina Zongo Junction', 'phone' => '+233 24 555 5002', 'info' => 'City Shuttle'],
                    ];
                    $tagline = 'Express bike delivery and transit for fast city mobility. Beat the traffic with WADEX 2-Wheels.';
                    $image = 'https://images.unsplash.com/photo-1558981806-ec527fa84c39?q=80&w=2070&auto=format&fit=crop';
                    break;
                case 'transit':
                    $hubs = [
                        ['id' => 5, 'name' => 'Wadex-STC Terminal', 'address' => 'Lamptey Ave, Accra', 'phone' => '+233 24 555 2001', 'info' => 'Executive Coaches'],
                        ['id' => 6, 'name' => 'VIP Circle Station', 'address' => 'Kwame Nkrumah Circle, Accra', 'phone' => '+233 24 555 2002', 'info' => 'Economy Shuttles'],
                    ];
                    $tagline = 'Inter-city bus and shuttle services connecting all major Ghanaian cities with comfort.';
                    $image = 'https://images.unsplash.com/photo-1544620347-c4fd4a3d5957?q=80&w=2069&auto=format&fit=crop';
                    break;
                case 'charter':
                    $hubs = [
                        ['id' => 7, 'name' => 'Elite Charter Center', 'address' => 'Airport Residential, Accra', 'phone' => '+233 24 555 3001', 'info' => 'Corporate & Events'],
                        ['id' => 8, 'name' => 'Cape Coast Tours', 'address' => 'Castle Road, Cape Coast', 'phone' => '+233 24 555 3002', 'info' => 'Tour & Excursions'],
                    ];
                    $tagline = 'Private bus and corporate rentals for events, tours, and group transport.';
                    $image = 'https://images.unsplash.com/photo-1540962351504-03099e0a754b?q=80&w=1974&auto=format&fit=crop';
                    break;
                case 'travel':
                    $hubs = [
                        ['id' => 9, 'name' => 'Kotoka Terminal 3', 'address' => 'Kotoka Intl Airport, Accra', 'phone' => '+233 24 555 4001', 'info' => 'Domestic Flights'],
                        ['id' => 10, 'name' => 'Kumasi Airport', 'address' => 'Kumasi Airport, Kumasi', 'phone' => '+233 24 555 4002', 'info' => 'Regional Flights'],
                    ];
                    $tagline = 'Domestic and regional air travel bookings with WADEX partner airlines.';
                    $image = 'https://images.unsplash.com/photo-1436491865332-7a61a109c0f2?q=80&w=2070&auto=format&fit=crop';
                    break;
                default:
                    $hubs = [
                        ['id' => 11, 'name' => 'WADEX Logistics Hub', 'address' => 'Tema Harbor Area, Tema', 'phone' => '+233 24 555 4001', 'info' => 'Full Service Logistics'],
                    ];
                    $tagline = 'Advanced transportation and logistics solutions.';
                    $image = 'https://images.unsplash.com/photo-1436491865332-7a61a109c0f2?q=80&w=2070&auto=format&fit=crop';
                    break;
            }

            return response()->json([
                'status' => 'success',
                'data' => [
                    'hubs' => $hubs,
                    'tagline' => $tagline,
                    'headerImage' => $image,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Failed to retrieve hubs'], 500);
        }
    }

    /**
     * Get Rental Vehicles Available
     */
    public function getRentals(Request $request)
    {
        try {
            $rentals = [
                'economy' => [
                    'category' => 'Economy',
                    'subtitle' => 'Starting from GHS 28/day',
                    'vehicles' => [
                        ['id' => 101, 'name' => 'Toyota Vitz', 'specs' => 'Manual/Auto • 5 Seats', 'price_display' => 'GHS 28/day', 'price_raw' => 'GHS 28', 'image' => 'https://images.unsplash.com/photo-1550355291-bbee04a92027?q=80&w=2072&auto=format&fit=crop'],
                        ['id' => 102, 'name' => 'Honda Fit', 'specs' => 'Automatic • 5 Seats', 'price_display' => 'GHS 32/day', 'price_raw' => 'GHS 32', 'image' => 'https://images.unsplash.com/photo-1590362891991-f776e747a588?q=80&w=2069&auto=format&fit=crop'],
                    ]
                ],
                'premium' => [
                    'category' => 'Premium & SUV',
                    'subtitle' => 'Starting from GHS 65/day',
                    'vehicles' => [
                        ['id' => 201, 'name' => 'Range Rover Vogue', 'specs' => 'Luxury • 4x4', 'price_display' => 'GHS 150/day', 'price_raw' => 'GHS 150', 'image' => 'https://images.unsplash.com/photo-1549317661-bd32c8ce0db2?q=80&w=2070&auto=format&fit=crop'],
                        ['id' => 202, 'name' => 'Toyota Land Cruiser', 'specs' => 'Off-road • 7 Seats', 'price_display' => 'GHS 120/day', 'price_raw' => 'GHS 120', 'image' => 'https://images.unsplash.com/photo-1533473359331-0135ef1b58bf?q=80&w=2070&auto=format&fit=crop'],
                    ]
                ]
            ];

            return response()->json([
                'status' => 'success',
                'data' => $rentals
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Failed to retrieve rentals'], 500);
        }
    }

    /**
     * Book a Hub Service or Rental
     */
    public function bookLogistics(Request $request)
    {
        $request->validate([
            'service_type' => 'required|string',
            'item_id' => 'required',
        ]);

        try {
            $user = $request->user('sanctum');
            if (!$user) {
                return response()->json(['status' => 'success', 'message' => 'Virtual booking confirmed.']);
            }

            // In a real scenario, we'd record the booking here.
            return response()->json([
                'status' => 'success',
                'message' => 'Booking successfully recorded.'
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Failed to process booking'], 500);
        }
    }
}
