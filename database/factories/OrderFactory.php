<?php

namespace Database\Factories;

use App\Modules\Logistics\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Modules\Logistics\Models\Order>
 */
class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        return [
            'reference' => 'WAD-' . fake()->year() . '-' . fake()->unique()->numerify('######'),
            'customer_id' => User::factory(),
            'status' => 'pending',
            'priority' => 'medium',
            'pickup_address' => fake()->address(),
            'pickup_lat' => fake()->latitude(5.5, 5.7),
            'pickup_lng' => fake()->longitude(-0.25, -0.1),
            'package_description' => fake()->sentence(),
            'total_amount' => fake()->randomFloat(2, 50, 500),
            'currency' => 'GHS',
        ];
    }
}
