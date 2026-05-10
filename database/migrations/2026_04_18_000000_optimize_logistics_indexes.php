<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations to optimize logistics performance via composite indexing.
     */
    public function up(): void
    {
        Schema::table('ride_requests', function (Blueprint $table) {
            // New performance optimization for the dispatcher dashboard
            $table->index(['status', 'created_at']);
        });

        Schema::table('drivers', function (Blueprint $table) {
            // High-speed fleet discovery
            $table->index(['is_available', 'last_location_at']);
        });

        Schema::table('orders', function (Blueprint $table) {
            // Composite index for customer history filtering
            $table->index(['customer_id', 'status']);
        });

        Schema::table('tracking_events', function (Blueprint $table) {
            // Optimize time-series pruning and retrieval
            $table->index(['recorded_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ride_requests', function (Blueprint $table) {
            $table->dropIndex(['status', 'created_at']);
        });

        Schema::table('drivers', function (Blueprint $table) {
            $table->dropIndex(['is_available', 'last_location_at']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['customer_id', 'status']);
        });

        Schema::table('tracking_events', function (Blueprint $table) {
            $table->dropIndex(['recorded_at']);
        });
    }
};
