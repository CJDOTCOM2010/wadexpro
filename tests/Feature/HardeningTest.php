<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

class HardeningTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test SOS Rate Limiting (3 requests per 5 minutes).
     */
    public function test_sos_is_throttled_after_3_requests(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        // First 3 requests should succeed (assuming 200/201 depending on your controller)
        for ($i = 0; $i < 3; $i++) {
            $response = $this->postJson('/api/v1/logistics/sos', [
                'lat' => 5.6037,
                'lng' => -0.1870,
                'type' => 'emergency'
            ]);
            
            // Check for success status (either 200 or 201)
            if (!in_array($response->status(), [200, 201, 202])) {
                $response->dump();
            }
            $this->assertTrue(in_array($response->status(), [200, 201, 202]));
        }

        // 4th request should be throttled
        $response = $this->postJson('/api/v1/logistics/sos', [
            'lat' => 5.6037,
            'lng' => -0.1870,
            'type' => 'emergency'
        ]);

        $response->assertStatus(429);
    }

    public function test_telemetry_is_throttled_after_12_requests(): void
    {
        $user = User::factory()
            ->has(\App\Modules\Logistics\Models\Driver::factory(), 'driver')
            ->create(['user_type' => 'driver']);
        
        Sanctum::actingAs($user->load('driver'));

        // First 12 requests
        for ($i = 0; $i < 12; $i++) {
            $response = $this->postJson('/api/v1/logistics/driver/location', [
                'lat' => 5.6037,
                'lng' => -0.1870,
                'bearing' => 90.0
            ]);
            
            $response->assertStatus(200);
        }

        // 13th request should be throttled
        $response = $this->postJson('/api/v1/logistics/driver/location', [
            'lat' => 5.6037,
            'lng' => -0.1870,
            'bearing' => 90.5
        ]);

        $response->assertStatus(429);
    }
}
