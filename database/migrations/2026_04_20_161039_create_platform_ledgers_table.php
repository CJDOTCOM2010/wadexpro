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
        Schema::create('platform_ledgers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('ride_request_id')->nullable();
            $table->foreign('ride_request_id')->references('id')->on('ride_requests')->onDelete('set null');
            $table->uuid('transaction_id')->nullable();
            $table->enum('type', ['commission', 'fee', 'adjustment', 'tax']);
            $table->decimal('amount', 16, 4);
            $table->string('currency', 3)->default('GHS');
            $table->string('description')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['type', 'created_at']);
            $table->index('ride_request_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('platform_ledgers');
    }
};
