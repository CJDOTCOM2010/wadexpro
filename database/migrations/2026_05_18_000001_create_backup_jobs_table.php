<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('backup_jobs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type')->default('database'); // database, files, all
            $table->string('status')->default('pending'); // pending, running, completed, failed
            $table->integer('progress')->default(0); // 0-100
            $table->string('current_step')->nullable(); // human-readable step name
            $table->integer('tables_total')->default(0);
            $table->integer('tables_done')->default(0);
            $table->bigInteger('rows_dumped')->default(0);
            $table->string('file_name')->nullable();
            $table->bigInteger('file_size')->default(0);
            $table->text('error_message')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('backup_jobs');
    }
};
