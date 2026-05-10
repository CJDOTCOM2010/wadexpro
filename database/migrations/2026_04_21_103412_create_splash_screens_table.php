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
        Schema::create('splash_screens', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->enum('app_type', ['customer', 'driver'])->unique();
            $table->string('tagline')->default('Move. Deliver. Thrive.');
            $table->string('logo_url')->nullable();
            $table->string('background_url')->nullable();
            $table->string('bg_color')->default('#000B1E');
            $table->string('secondary_color')->default('#FFB800');
            $table->integer('duration_ms')->default(3000);
            $table->boolean('show_ripple')->default(true);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('splash_screens');
    }
};
