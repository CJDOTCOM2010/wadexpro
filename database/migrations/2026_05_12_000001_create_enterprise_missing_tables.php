<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ─── OTP VERIFICATIONS ────────────────────────────────────────────────
        if (!Schema::hasTable('otp_verifications')) {
            Schema::create('otp_verifications', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->foreignUuid('user_id')->nullable()->constrained()->nullOnDelete();
                $table->string('phone')->nullable()->index();
                $table->string('email')->nullable()->index();
                $table->string('otp_code', 10);
                $table->string('type', 30)->default('login');
                $table->timestamp('expires_at');
                $table->timestamp('verified_at')->nullable();
                $table->unsignedSmallInteger('attempts')->default(0);
                $table->string('ip_address', 45)->nullable();
                $table->string('device_fingerprint')->nullable();
                $table->timestamps();
            });
        }

        // ─── AUDIT LOGS ───────────────────────────────────────────────────────
        if (!Schema::hasTable('audit_logs')) {
            Schema::create('audit_logs', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->nullableUuidMorphs('auditable');
                $table->foreignUuid('user_id')->nullable()->constrained()->nullOnDelete();
                $table->string('user_type', 20)->nullable();
                $table->string('event', 50);
                $table->json('old_values')->nullable();
                $table->json('new_values')->nullable();
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->string('url')->nullable();
                $table->json('tags')->nullable();
                $table->timestamp('logged_at')->useCurrent();
                $table->index(['user_id', 'event']);
                $table->index('logged_at');
            });
        }

        // ─── DRIVER DOCUMENTS ─────────────────────────────────────────────────
        if (!Schema::hasTable('driver_documents')) {
            Schema::create('driver_documents', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->foreignUuid('driver_id')->constrained('drivers')->cascadeOnDelete();
                $table->string('document_type', 50);
                $table->string('document_number')->nullable();
                $table->text('file_url');
                $table->text('back_file_url')->nullable();
                $table->string('status', 20)->default('pending');
                $table->text('rejection_reason')->nullable();
                $table->date('issued_at')->nullable();
                $table->date('expires_at')->nullable();
                $table->foreignUuid('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('reviewed_at')->nullable();
                $table->json('metadata')->nullable();
                $table->timestamps();
                $table->index(['driver_id', 'status']);
                $table->index('expires_at');
            });
        }

        // ─── VEHICLE TYPES ────────────────────────────────────────────────────
        if (!Schema::hasTable('vehicle_types')) {
            Schema::create('vehicle_types', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('name', 80);
                $table->string('slug', 80)->unique();
                $table->text('description')->nullable();
                $table->string('icon_url')->nullable();
                $table->string('image_url')->nullable();
                $table->decimal('base_fare', 10, 2)->default(0);
                $table->decimal('per_km_rate', 10, 2)->default(0);
                $table->decimal('per_minute_rate', 10, 2)->default(0);
                $table->decimal('min_fare', 10, 2)->default(0);
                $table->unsignedTinyInteger('capacity')->default(4);
                $table->decimal('max_weight_kg', 8, 2)->nullable();
                $table->boolean('is_active')->default(true);
                $table->json('service_types')->nullable();
                $table->unsignedSmallInteger('sort_order')->default(0);
                $table->timestamps();
            });
        }

        // ─── VEHICLE DOCUMENTS ────────────────────────────────────────────────
        if (!Schema::hasTable('vehicle_documents')) {
            Schema::create('vehicle_documents', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->foreignUuid('vehicle_id')->constrained('vehicles')->cascadeOnDelete();
                $table->string('document_type', 50);
                $table->string('document_number')->nullable();
                $table->text('file_url');
                $table->string('status', 20)->default('pending');
                $table->text('rejection_reason')->nullable();
                $table->date('issued_at')->nullable();
                $table->date('expires_at')->nullable();
                $table->foreignUuid('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('reviewed_at')->nullable();
                $table->timestamps();
                $table->index(['vehicle_id', 'status']);
            });
        }

        // ─── RIDE CANCELLATIONS ───────────────────────────────────────────────
        if (!Schema::hasTable('ride_cancellations')) {
            Schema::create('ride_cancellations', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->foreignUuid('order_id')->constrained('orders')->cascadeOnDelete();
                $table->foreignUuid('cancelled_by')->nullable()->constrained('users')->nullOnDelete();
                $table->string('cancelled_by_type', 20);
                $table->string('reason_code', 60)->nullable();
                $table->text('reason_text')->nullable();
                $table->boolean('penalty_applied')->default(false);
                $table->decimal('penalty_amount', 10, 2)->default(0);
                $table->string('penalty_charged_to', 20)->default('none');
                $table->timestamp('cancelled_at')->useCurrent();
                $table->timestamps();
                $table->index(['order_id', 'cancelled_by_type']);
            });
        }

        // ─── WALLET TRANSACTIONS ──────────────────────────────────────────────
        if (!Schema::hasTable('wallet_transactions')) {
            Schema::create('wallet_transactions', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->foreignUuid('wallet_id')->constrained('wallets')->cascadeOnDelete();
                $table->foreignUuid('user_id')->constrained()->cascadeOnDelete();
                $table->string('type', 10);
                $table->string('category', 40);
                $table->decimal('amount', 12, 2);
                $table->decimal('balance_before', 12, 2);
                $table->decimal('balance_after', 12, 2);
                $table->string('reference')->unique();
                $table->text('description')->nullable();
                $table->json('metadata')->nullable();
                $table->string('status', 20)->default('completed');
                $table->foreignUuid('performed_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('transacted_at')->useCurrent();
                $table->timestamps();
                $table->index(['wallet_id', 'type']);
                $table->index('transacted_at');
            });
        }

        // ─── PUSH NOTIFICATIONS ───────────────────────────────────────────────
        if (!Schema::hasTable('push_notifications')) {
            Schema::create('push_notifications', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->foreignUuid('user_id')->nullable()->constrained()->nullOnDelete();
                $table->string('user_type', 20)->nullable();
                $table->string('title');
                $table->text('body');
                $table->json('data')->nullable();
                $table->string('channel', 20)->default('fcm');
                $table->text('device_token')->nullable();
                $table->string('topic')->nullable();
                $table->string('status', 20)->default('queued');
                $table->text('failure_reason')->nullable();
                $table->timestamp('sent_at')->nullable();
                $table->timestamp('delivered_at')->nullable();
                $table->timestamp('opened_at')->nullable();
                $table->timestamps();
                $table->index(['user_id', 'status']);
            });
        }

        // ─── FRAUD DETECTIONS ─────────────────────────────────────────────────
        if (!Schema::hasTable('fraud_detections')) {
            Schema::create('fraud_detections', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->foreignUuid('user_id')->nullable()->constrained()->nullOnDelete();
                $table->string('user_type', 20)->nullable();
                $table->string('event_type', 60);
                $table->unsignedTinyInteger('risk_score')->default(0);
                $table->string('risk_level', 20)->default('low');
                $table->json('details')->nullable();
                $table->string('ip_address', 45)->nullable();
                $table->string('device_fingerprint')->nullable();
                $table->foreignUuid('order_id')->nullable()->constrained('orders')->nullOnDelete();
                $table->string('status', 20)->default('open');
                $table->foreignUuid('resolved_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('resolved_at')->nullable();
                $table->text('resolution_notes')->nullable();
                $table->boolean('auto_flagged')->default(true);
                $table->timestamps();
                $table->index(['user_id', 'risk_level', 'status']);
            });
        }

        // ─── BLOCKED DEVICES ──────────────────────────────────────────────────
        if (!Schema::hasTable('blocked_devices')) {
            Schema::create('blocked_devices', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('device_fingerprint')->index();
                $table->string('device_type', 20)->nullable();
                $table->foreignUuid('user_id')->nullable()->constrained()->nullOnDelete();
                $table->foreignUuid('blocked_by')->nullable()->constrained('users')->nullOnDelete();
                $table->string('reason');
                $table->text('notes')->nullable();
                $table->timestamp('blocked_at')->useCurrent();
                $table->timestamp('expires_at')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // ─── SUPPORT TICKETS ──────────────────────────────────────────────────
        if (!Schema::hasTable('support_tickets')) {
            Schema::create('support_tickets', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('ticket_number', 20)->unique();
                $table->foreignUuid('user_id')->constrained()->cascadeOnDelete();
                $table->string('user_type', 20)->default('customer');
                $table->foreignUuid('order_id')->nullable()->constrained('orders')->nullOnDelete();
                $table->string('subject');
                $table->string('category', 40)->default('general');
                $table->string('priority', 20)->default('medium');
                $table->string('status', 30)->default('open');
                $table->foreignUuid('assigned_to')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignUuid('resolved_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('resolved_at')->nullable();
                $table->timestamp('closed_at')->nullable();
                $table->timestamp('first_response_at')->nullable();
                $table->unsignedTinyInteger('satisfaction_rating')->nullable();
                $table->text('internal_notes')->nullable();
                $table->timestamps();
                $table->index(['status', 'priority']);
                $table->index('assigned_to');
            });
        }

        // ─── TICKET REPLIES ───────────────────────────────────────────────────
        if (!Schema::hasTable('ticket_replies')) {
            Schema::create('ticket_replies', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->foreignUuid('ticket_id')->constrained('support_tickets')->cascadeOnDelete();
                $table->foreignUuid('sender_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('sender_type', 20)->default('customer');
                $table->text('message');
                $table->json('attachments')->nullable();
                $table->boolean('is_internal')->default(false);
                $table->timestamps();
                $table->index('ticket_id');
            });
        }

        // ─── BLOG POSTS ───────────────────────────────────────────────────────
        if (!Schema::hasTable('blog_posts')) {
            Schema::create('blog_posts', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('title');
                $table->string('slug')->unique();
                $table->text('excerpt')->nullable();
                $table->longText('content')->nullable();
                $table->string('cover_image_url')->nullable();
                $table->foreignUuid('author_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('category', 60)->nullable();
                $table->json('tags')->nullable();
                $table->string('status', 20)->default('draft');
                $table->boolean('is_featured')->default(false);
                $table->string('meta_title')->nullable();
                $table->text('meta_description')->nullable();
                $table->timestamp('published_at')->nullable();
                $table->unsignedInteger('view_count')->default(0);
                $table->timestamps();
                $table->index(['status', 'published_at']);
            });
        }

        // ─── FAQS ─────────────────────────────────────────────────────────────
        if (!Schema::hasTable('faqs')) {
            Schema::create('faqs', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->text('question');
                $table->longText('answer');
                $table->string('category', 50)->default('general');
                $table->string('audience', 20)->default('all');
                $table->boolean('is_active')->default(true);
                $table->unsignedSmallInteger('sort_order')->default(0);
                $table->timestamps();
                $table->index(['audience', 'category', 'is_active']);
            });
        }

        // ─── BANNERS ──────────────────────────────────────────────────────────
        if (!Schema::hasTable('banners')) {
            Schema::create('banners', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('title');
                $table->string('image_url');
                $table->string('link_url')->nullable();
                $table->string('link_target', 10)->default('_self');
                $table->string('placement', 40)->default('home');
                $table->string('audience', 20)->default('all');
                $table->boolean('is_active')->default(true);
                $table->timestamp('starts_at')->nullable();
                $table->timestamp('ends_at')->nullable();
                $table->unsignedSmallInteger('sort_order')->default(0);
                $table->timestamps();
                $table->index(['placement', 'audience', 'is_active']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('banners');
        Schema::dropIfExists('faqs');
        Schema::dropIfExists('blog_posts');
        Schema::dropIfExists('ticket_replies');
        Schema::dropIfExists('support_tickets');
        Schema::dropIfExists('blocked_devices');
        Schema::dropIfExists('fraud_detections');
        Schema::dropIfExists('push_notifications');
        Schema::dropIfExists('wallet_transactions');
        Schema::dropIfExists('ride_cancellations');
        Schema::dropIfExists('vehicle_documents');
        Schema::dropIfExists('vehicle_types');
        Schema::dropIfExists('driver_documents');
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('otp_verifications');
    }
};
