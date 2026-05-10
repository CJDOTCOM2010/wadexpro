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
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            
            $table->string('provider'); // card, momo, wallet
            $table->string('provider_id')->nullable(); // masked_id or masked_phone
            $table->string('gateway_token')->nullable(); // encrypted token
            
            $table->string('brand')->nullable(); // visa, mastercard, mtn, airteltigo
            $table->string('last_four', 4)->nullable();
            $table->boolean('is_default')->default(false);
            
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'is_default']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_methods');
    }
};
