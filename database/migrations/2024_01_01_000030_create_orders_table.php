<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('reference', 25)->unique();  // WAD-2024-000001
            $table->uuid('customer_id');
            $table->foreign('customer_id')->references('id')->on('users');
            $table->uuid('driver_id')->nullable();
            $table->foreign('driver_id')->references('id')->on('drivers')->nullOnDelete();

            $table->string('status', 50)->default('pending');
            // pending|confirmed|assigned|picked_up|in_transit|delivered|cancelled|failed

            $table->string('priority', 20)->default('normal');
            // normal | express | scheduled

            $table->timestamp('scheduled_at')->nullable();

            // Pickup details
            $table->text('pickup_address');
            $table->decimal('pickup_lat', 10, 8);
            $table->decimal('pickup_lng', 11, 8);
            $table->string('pickup_contact_name')->nullable();
            $table->string('pickup_contact_phone', 20)->nullable();

            // Package details
            $table->string('package_description')->nullable();
            $table->decimal('package_weight_kg', 8, 2)->nullable();
            $table->string('package_size', 20)->nullable(); // small|medium|large

            // Payment
            $table->string('payment_method', 50)->nullable();
            // cash | paystack | flutterwave | stripe | wallet
            $table->string('payment_status', 30)->default('pending');
            $table->string('payment_gateway_ref')->nullable();

            // Financials
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('delivery_fee', 12, 2)->default(0);
            $table->decimal('tax_amount', 12, 2)->default(0);
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->string('currency', 3)->default('GHS');

            // Route / timing
            $table->integer('estimated_duration_seconds')->nullable();
            $table->integer('actual_duration_seconds')->nullable();
            $table->decimal('estimated_distance_km', 10, 3)->nullable();
            $table->decimal('actual_distance_km', 10, 3)->nullable();

            $table->text('notes')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->jsonb('metadata')->default('{}');  // extensible without schema changes

            // Lifecycle timestamps
            $table->timestamp('assigned_at')->nullable();
            $table->timestamp('picked_up_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamps();

            $table->index('customer_id');
            $table->index('driver_id');
            $table->index('status');
            $table->index('payment_status');
            $table->index('created_at');
            $table->index('reference');
        });

        Schema::create('order_stops', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('order_id');
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->unsignedSmallInteger('sequence');
            $table->text('address');
            $table->decimal('lat', 10, 8);
            $table->decimal('lng', 11, 8);
            $table->string('contact_name')->nullable();
            $table->string('contact_phone', 20)->nullable();
            $table->string('stop_type', 20)->default('delivery'); // pickup | delivery
            $table->string('status', 20)->default('pending');
            $table->text('notes')->nullable();
            $table->timestamp('arrived_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index(['order_id', 'sequence']);
        });

        Schema::create('tracking_events', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('order_id')->nullable();
            $table->uuid('driver_id');
            $table->foreign('driver_id')->references('id')->on('drivers');
            $table->string('event_type', 50)->default('location_update');
            // location_update | status_change | eta_recalculated | stop_arrived | stop_completed
            $table->decimal('lat', 10, 8)->nullable();
            $table->decimal('lng', 11, 8)->nullable();
            $table->decimal('speed_kmh', 5, 1)->nullable();
            $table->smallInteger('bearing')->nullable();
            $table->jsonb('metadata')->nullable();
            $table->timestamp('recorded_at')->useCurrent();

            $table->index(['order_id', 'recorded_at']);
            $table->index(['driver_id', 'recorded_at']);
        });

        Schema::create('ratings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('order_id')->unique();
            $table->uuid('rated_by');
            $table->foreign('rated_by')->references('id')->on('users');
            $table->uuid('rated_user');
            $table->foreign('rated_user')->references('id')->on('users');
            $table->unsignedTinyInteger('score'); // 1-5
            $table->text('comment')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ratings');
        Schema::dropIfExists('tracking_events');
        Schema::dropIfExists('order_stops');
        Schema::dropIfExists('orders');
    }
};
