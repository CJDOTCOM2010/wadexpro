<?php

namespace Database\Seeders;

use App\Modules\Logistics\Models\VehicleType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class VehicleTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            [
                'name' => 'Wadex Economy',
                'description' => 'Affordable, everyday rides.',
                'base_fare' => 10.00,
                'per_km_rate' => 2.50,
                'per_minute_rate' => 0.50,
                'min_fare' => 15.00,
                'capacity' => 4,
                'service_types' => ['ride'],
                'sort_order' => 1,
            ],
            [
                'name' => 'Wadex Comfort',
                'description' => 'Newer cars with extra legroom.',
                'base_fare' => 15.00,
                'per_km_rate' => 3.50,
                'per_minute_rate' => 0.75,
                'min_fare' => 20.00,
                'capacity' => 4,
                'service_types' => ['ride'],
                'sort_order' => 2,
            ],
            [
                'name' => 'Wadex XL',
                'description' => 'Spacious rides for groups of up to 6.',
                'base_fare' => 25.00,
                'per_km_rate' => 5.00,
                'per_minute_rate' => 1.00,
                'min_fare' => 35.00,
                'capacity' => 6,
                'service_types' => ['ride'],
                'sort_order' => 3,
            ],
            [
                'name' => 'Wadex Moto',
                'description' => 'Fastest way to beat the traffic.',
                'base_fare' => 5.00,
                'per_km_rate' => 1.50,
                'per_minute_rate' => 0.25,
                'min_fare' => 8.00,
                'capacity' => 1,
                'service_types' => ['ride'],
                'sort_order' => 4,
            ],
            [
                'name' => 'Wadex Courier',
                'description' => 'Instant delivery for packages.',
                'base_fare' => 8.00,
                'per_km_rate' => 2.00,
                'per_minute_rate' => 0.40,
                'min_fare' => 12.00,
                'capacity' => 0,
                'max_weight_kg' => 20.00,
                'service_types' => ['courier'],
                'sort_order' => 5,
            ],
        ];

        foreach ($types as $type) {
            VehicleType::firstOrCreate(
                ['slug' => Str::slug($type['name'])],
                array_merge($type, [
                    'id' => Str::uuid()->toString(),
                    'is_active' => true,
                ])
            );
        }
    }
}
