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
        return; // Schema conflicts with pre-existing BigInt regions table
        if (!Schema::hasTable('regions')) {
            Schema::create('regions', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('name');
                $table->string('slug')->unique();
                $table->string('currency_code', 3);
                $table->string('currency_symbol', 10);
                $table->decimal('tax_percentage', 5, 2)->default(0.00);
                $table->string('timezone')->default('UTC');
                $table->json('boundary')->nullable(); // GeoJSON Polygon
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->index('is_active');
            });
        }

        if (!Schema::hasTable('region_rates')) {
            Schema::create('region_rates', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->uuid('region_id');
                $table->foreign('region_id')->references('id')->on('regions')->onDelete('cascade');
                $table->string('vehicle_type');
                $table->decimal('base_fare', 10, 2);
                $table->decimal('per_km', 10, 2);
                $table->decimal('per_minute', 10, 2);
                $table->decimal('minimum_fare', 10, 2);
                $table->decimal('booking_fee', 10, 2);
                $table->timestamps();

                $table->unique(['region_id', 'vehicle_type']);
            });
        }

        // Add region_id to existing tables
        if (!Schema::hasColumn('users', 'region_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->uuid('region_id')->nullable()->after('id');
                $table->foreign('region_id')->references('id')->on('regions');
            });
        }

        if (!Schema::hasColumn('drivers', 'region_id')) {
            Schema::table('drivers', function (Blueprint $table) {
                $table->uuid('region_id')->nullable()->after('id');
                $table->foreign('region_id')->references('id')->on('regions');
            });
        }

        if (Schema::hasTable('ride_requests') && !Schema::hasColumn('ride_requests', 'region_id')) {
            Schema::table('ride_requests', function (Blueprint $table) {
                $table->uuid('region_id')->nullable()->after('id');
                $table->foreign('region_id')->references('id')->on('regions');
            });
        }

        if (Schema::hasTable('orders') && !Schema::hasColumn('orders', 'region_id')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->uuid('region_id')->nullable()->after('id');
                $table->foreign('region_id')->references('id')->on('regions');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['region_id']);
            $table->dropColumn('region_id');
        });

        Schema::table('ride_requests', function (Blueprint $table) {
            $table->dropForeign(['region_id']);
            $table->dropColumn('region_id');
        });

        Schema::table('drivers', function (Blueprint $table) {
            $table->dropForeign(['region_id']);
            $table->dropColumn('region_id');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['region_id']);
            $table->dropColumn('region_id');
        });

        Schema::dropIfExists('region_rates');
        Schema::dropIfExists('regions');
    }
};
