<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cron_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('command');
            $table->string('schedule')->default('daily'); // cron expression or frequency
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_run')->nullable();
            $table->timestamp('next_run')->nullable();
            $table->string('status')->default('pending'); // pending, running, completed, failed
            $table->text('last_output')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cron_jobs');
    }
};
