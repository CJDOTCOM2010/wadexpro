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
        Schema::create('analytics_snapshots', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('metric_name'); // e.g., revenue, ride_volume, avg_completion_time
            $table->decimal('metric_value', 16, 4);
            
            $table->string('dimension_type')->nullable(); // e.g., ORGANIZATION, REGION, DRIVER
            $table->uuid('dimension_id')->nullable();
            
            $table->enum('period', ['HOURLY', 'DAILY', 'WEEKLY', 'MONTHLY']);
            $table->timestamp('start_at');
            $table->json('metadata')->nullable(); // Stores breakdown like [economy => 50, comfort => 20]
            
            $table->timestamps();

            $table->index(['metric_name', 'period', 'start_at']);
            $table->index(['dimension_type', 'dimension_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('analytics_snapshots');
    }
};
