<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Modules\Logistics\Models\Order;
use App\Modules\Logistics\Models\OrderStop;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

class LogisticsOrchestrationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test Linear Sequence Enforcement.
     * A driver cannot complete a later stop before the earlier ones are settled.
     */
    public function test_sequence_enforcement_prevents_skipping_stops(): void
    {
        $user = User::factory()->create(['user_type' => 'driver']);
        $driver = \App\Modules\Logistics\Models\Driver::factory()->create(['user_id' => $user->id]);
        Sanctum::actingAs($user->fresh());

        // Create an order with 3 stops
        $order = Order::factory()->create(['driver_id' => $driver->id, 'status' => 'en_route']);
        
        $stop1 = OrderStop::factory()->create(['order_id' => $order->id, 'sequence' => 1, 'status' => 'pending']);
        $stop2 = OrderStop::factory()->create(['order_id' => $order->id, 'sequence' => 2, 'status' => 'pending']);
        $stop3 = OrderStop::factory()->create(['order_id' => $order->id, 'sequence' => 3, 'status' => 'pending']);

        // Attempting to complete Stop 2 while Stop 1 is PENDING
        $response = $this->patchJson("/api/v1/logistics/driver/stops/{$stop2->id}/status", [
            'status' => 'arrived'
        ]);

        // Should fail with a business logic error (422 Unprocessable or 403 Forbidden)
        $response->assertStatus(422);
        $response->assertJsonFragment(['message' => 'Cannot update this stop until the previous stop is completed.']);

        // Now arrive at Stop 1
        $this->patchJson("/api/v1/logistics/driver/stops/{$stop1->id}/status", ['status' => 'arrived'])->assertStatus(200);
        
        // Progress Stop 1 to 'delivered'
        $this->patchJson("/api/v1/logistics/driver/stops/{$stop1->id}/status", ['status' => 'delivered'])->assertStatus(200);

        // Now Stop 2 should be accessible
        $this->patchJson("/api/v1/logistics/driver/stops/{$stop2->id}/status", ['status' => 'arrived'])->assertStatus(200);
    }
}
