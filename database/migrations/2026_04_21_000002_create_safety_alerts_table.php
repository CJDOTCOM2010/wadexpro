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
        Schema::create('safety_alerts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('ride_id')->nullable();
            $table->foreign('ride_id')->references('id')->on('ride_requests')->onDelete('cascade');
            
            $table->enum('type', ['FRAUD', 'DEVIATION', 'SOS', 'SYSTEM_ANOMALY']);
            $table->enum('severity', ['LOW', 'MEDIUM', 'HIGH', 'CRITICAL'])->default('LOW');
            
            $table->json('metadata')->nullable(); // Stores deviation distance, fraud scores, etc.
            $table->enum('status', ['PENDING', 'INVESTIGATING', 'RESOLVED', 'DISMISSED'])->default('PENDING');
            
            $table->uuid('resolved_by')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->text('resolution_notes')->nullable();
            
            $table->timestamps();

            $table->index(['type', 'severity']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('safety_alerts');
    }
};
