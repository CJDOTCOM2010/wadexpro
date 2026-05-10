<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('drivers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id')->unique();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('license_number', 100)->nullable()->unique();
            $table->date('license_expires_at')->nullable();
            $table->string('license_class', 10)->nullable();
            $table->boolean('is_online')->default(false);
            $table->boolean('is_available')->default(false);
            $table->decimal('current_lat', 10, 8)->nullable();
            $table->decimal('current_lng', 11, 8)->nullable();
            $table->timestamp('last_location_at')->nullable();
            $table->decimal('rating', 3, 2)->default(5.00);
            $table->integer('total_deliveries')->default(0);
            $table->integer('total_cancellations')->default(0);
            $table->string('status', 30)->default('pending_verification');
            // Values: pending_verification | active | suspended | deactivated
            $table->timestamps();

            $table->index(['is_online', 'is_available']);
            $table->index('status');
        });

        Schema::create('vehicles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('driver_id');
            $table->foreign('driver_id')->references('id')->on('drivers')->onDelete('cascade');
            $table->string('plate_number', 20)->unique();
            $table->string('type', 50)->default('motorcycle');
            // Values: motorcycle | car | van | truck | mini_truck
            $table->string('make', 100)->nullable();
            $table->string('model', 100)->nullable();
            $table->smallInteger('year')->nullable();
            $table->decimal('max_weight_kg', 8, 2)->nullable();
            $table->string('color', 50)->nullable();
            $table->string('insurance_number', 100)->nullable();
            $table->date('insurance_expires_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('driver_id');
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicles');
        Schema::dropIfExists('drivers');
    }
};
