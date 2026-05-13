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
        Schema::create('notification_templates', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('event_name', 100);
            $table->string('channel', 20); // email, sms, whatsapp, push
            $table->string('subject')->nullable();
            $table->text('content');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Ensure we don't have multiple templates for the same event and channel combination
            $table->unique(['event_name', 'channel']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_templates');
    }
};
