<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            $table->string('action', 100);
            // created | updated | deleted | login | logout | exported | toggled | etc.
            $table->string('model_type', 150)->nullable();
            $table->uuid('model_id')->nullable();
            $table->jsonb('old_values')->nullable();
            $table->jsonb('new_values')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('module', 100)->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['user_id', 'created_at']);
            $table->index(['model_type', 'model_id']);
            $table->index(['action', 'created_at']);
            $table->index('module');
        });

        Schema::create('system_alerts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type', 100);
            // driver_offline | payment_failed | system_error | low_balance | etc.
            $table->string('severity', 20)->default('info');
            // info | warning | critical
            $table->string('title');
            $table->text('message');
            $table->boolean('is_resolved')->default(false);
            $table->uuid('resolved_by')->nullable();
            $table->foreign('resolved_by')->references('id')->on('users')->nullOnDelete();
            $table->timestamp('resolved_at')->nullable();
            $table->text('resolution_notes')->nullable();
            $table->jsonb('metadata')->default('{}');
            $table->timestamps();

            $table->index(['severity', 'is_resolved']);
            $table->index('type');
            $table->index('created_at');
        });

        Schema::create('system_notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('type', 100);
            // order_update | payment | chat | system | driver_assigned | etc.
            $table->string('title');
            $table->text('body');
            $table->jsonb('data')->default('{}');
            $table->string('action_url')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->string('channel', 30)->default('in_app');
            // in_app | push | email | sms
            $table->boolean('is_sent')->default(false);
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'is_read']);
            $table->index(['user_id', 'created_at']);
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('system_notifications');
        Schema::dropIfExists('system_alerts');
        Schema::dropIfExists('activity_logs');
    }
};
