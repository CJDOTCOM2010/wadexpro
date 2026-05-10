<?php

namespace Database\Factories;

use App\Modules\Logistics\Models\OrderStop;
use App\Modules\Logistics\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Modules\Logistics\Models\OrderStop>
 */
class OrderStopFactory extends Factory
{
    protected $model = OrderStop::class;

    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'sequence' => 1,
            'address' => fake()->address(),
            'lat' => fake()->latitude(5.5, 5.7),
            'lng' => fake()->longitude(-0.25, -0.1),
            'stop_type' => 'delivery',
            'status' => 'pending',
        ];
    }
}
