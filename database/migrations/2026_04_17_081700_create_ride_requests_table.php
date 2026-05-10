<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ride_requests', function (Blueprint $table) {
            $table->uuid('id')->primary();
            
            // Relationships
            $table->uuid('customer_id')->constrained('users');
            $table->uuid('driver_id')->nullable()->constrained('drivers');
            
            // Route Details
            $table->string('pickup_address');
            $table->decimal('pickup_lat', 10, 8);
            $table->decimal('pickup_lng', 11, 8);
            
            $table->string('dropoff_address');
            $table->decimal('dropoff_lat', 10, 8);
            $table->decimal('dropoff_lng', 11, 8);
            
            // Ride Options
            $table->enum('vehicle_type', ['economy', 'comfort', 'moto', 'xl'])->default('economy');
            
            // State Machine
            $table->enum('status', [
                'pending', 
                'searching', 
                'driver_assigned', 
                'driver_arrived', 
                'in_progress', 
                'completed', 
                'cancelled'
            ])->default('pending');
            
            // Pricing Metrics
            $table->decimal('estimated_price', 10, 2);
            $table->decimal('final_price', 10, 2)->nullable();
            $table->decimal('surge_multiplier', 4, 2)->default(1.0);
            $table->string('promo_code')->nullable();
            $table->decimal('discount_amount', 10, 2)->default(0);
            
            // Context Tracking
            $table->integer('estimated_duration_minutes')->nullable();
            $table->decimal('estimated_distance_km', 8, 2)->nullable();

            // Scheduler
            $table->timestamp('scheduled_at')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes for query performance
            $table->index(['customer_id', 'status']);
            $table->index(['driver_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ride_requests');
    }
};
