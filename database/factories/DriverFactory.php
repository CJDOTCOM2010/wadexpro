<?php

namespace Database\Factories;

use App\Modules\Logistics\Models\Driver;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Modules\Logistics\Models\Driver>
 */
class DriverFactory extends Factory
{
    protected $model = Driver::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'license_number' => 'DL-' . fake()->unique()->numerify('########'),
            'license_expires_at' => fake()->dateTimeBetween('+1 year', '+5 years'),
            'license_class' => 'B',
            'is_online' => true,
            'is_available' => true,
            'status' => 'active',
            'verified_at' => now(),
        ];
    }
}
