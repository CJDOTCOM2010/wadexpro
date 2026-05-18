<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('backup_settings', function (Blueprint $table) {
            $table->id();
            $table->boolean('auto_backup_enabled')->default(false);
            $table->string('frequency')->default('daily'); // daily, weekly, monthly
            $table->string('backup_type')->default('all'); // all, only-db, only-files
            $table->time('scheduled_time')->default('02:00:00');
            $table->string('day_of_week')->nullable(); // for weekly (monday-sunday)
            $table->integer('day_of_month')->nullable(); // for monthly (1-28)
            $table->integer('retention_days')->default(30);
            $table->boolean('notify_on_success')->default(true);
            $table->boolean('notify_on_failure')->default(true);
            $table->string('email')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('backup_settings');
    }
};
