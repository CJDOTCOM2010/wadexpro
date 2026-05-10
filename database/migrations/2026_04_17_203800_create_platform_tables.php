<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // -----------------------------------------------------------------------
        // Promo Codes
        // -----------------------------------------------------------------------
        Schema::create('promo_codes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code', 50)->unique();
            $table->string('description')->nullable();
            $table->enum('type', ['percentage', 'fixed'])->default('percentage');
            $table->decimal('value', 10, 2);
            $table->decimal('max_discount', 10, 2)->nullable();
            $table->decimal('min_order_amount', 10, 2)->default(0);
            $table->string('currency', 3)->default('GHS');
            $table->integer('max_uses')->nullable();
            $table->integer('max_uses_per_user')->default(1);
            $table->integer('times_used')->default(0);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->json('applicable_vehicle_types')->nullable();
            $table->json('applicable_regions')->nullable();
            $table->timestamps();

            $table->index('code');
            $table->index(['is_active', 'expires_at']);
        });

        Schema::create('promo_code_uses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('promo_code_id');
            $table->foreign('promo_code_id')->references('id')->on('promo_codes')->onDelete('cascade');
            $table->uuid('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->uuid('ride_request_id')->nullable();
            $table->decimal('discount_applied', 10, 2);
            $table->timestamps();

            $table->index(['promo_code_id', 'user_id']);
        });

        // -----------------------------------------------------------------------
        // Surge Pricing Zones
        // -----------------------------------------------------------------------
        Schema::create('surge_zones', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name', 100);
            $table->string('description')->nullable();
            $table->decimal('center_lat', 10, 8);
            $table->decimal('center_lng', 11, 8);
            $table->decimal('radius_km', 8, 2)->default(5.00);
            $table->json('polygon_coordinates')->nullable();
            $table->decimal('current_multiplier', 4, 2)->default(1.00);
            $table->decimal('min_multiplier', 4, 2)->default(1.00);
            $table->decimal('max_multiplier', 4, 2)->default(5.00);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['is_active', 'center_lat', 'center_lng']);
        });

        Schema::create('surge_rules', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('surge_zone_id');
            $table->foreign('surge_zone_id')->references('id')->on('surge_zones')->onDelete('cascade');
            $table->integer('demand_threshold');
            $table->integer('supply_threshold');
            $table->decimal('multiplier', 4, 2);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('surge_zone_id');
        });

        // -----------------------------------------------------------------------
        // SOS Emergency Events
        // -----------------------------------------------------------------------
        Schema::create('sos_events', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->uuid('ride_request_id')->nullable();
            $table->decimal('lat', 10, 8);
            $table->decimal('lng', 11, 8);
            $table->enum('status', ['triggered', 'acknowledged', 'resolved', 'false_alarm'])->default('triggered');
            $table->text('notes')->nullable();
            $table->uuid('resolved_by')->nullable();
            $table->timestamp('acknowledged_at')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index('ride_request_id');
        });

        // -----------------------------------------------------------------------
        // CMS — Pages, Sections, Blocks
        // -----------------------------------------------------------------------
        if (!Schema::hasTable('cms_pages')) {
            Schema::create('cms_pages', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('title');
                $table->string('slug')->unique();
                $table->text('meta_description')->nullable();
                $table->string('meta_keywords')->nullable();
                $table->enum('status', ['published', 'draft', 'archived'])->default('draft');
                $table->string('template')->default('default');
                $table->string('region', 10)->nullable();
                $table->integer('sort_order')->default(0);
                $table->timestamps();

                $table->index(['slug', 'status']);
                $table->index('region');
            });
        }

        if (!Schema::hasTable('cms_sections')) {
            Schema::create('cms_sections', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->uuid('page_id');
                $table->foreign('page_id')->references('id')->on('cms_pages')->onDelete('cascade');
                $table->string('type', 50);
                $table->string('title')->nullable();
                $table->integer('sort_order')->default(0);
                $table->boolean('is_visible')->default(true);
                $table->json('settings')->nullable();
                $table->timestamps();

                $table->index(['page_id', 'sort_order']);
            });
        }

        if (!Schema::hasTable('cms_blocks')) {
            Schema::create('cms_blocks', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->uuid('section_id');
                $table->foreign('section_id')->references('id')->on('cms_sections')->onDelete('cascade');
                $table->string('type', 50);
                $table->string('key', 100)->nullable();
                $table->text('content')->nullable();
                $table->string('media_url')->nullable();
                $table->string('link_url')->nullable();
                $table->string('link_text')->nullable();
                $table->integer('sort_order')->default(0);
                $table->json('properties')->nullable();
                $table->timestamps();

                $table->index(['section_id', 'sort_order']);
            });
        }

        // -----------------------------------------------------------------------
        // Admin Activity Logs
        // -----------------------------------------------------------------------
        Schema::create('admin_activity_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->string('action', 100);
            $table->string('resource_type', 100)->nullable();
            $table->uuid('resource_id')->nullable();
            $table->text('description')->nullable();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
            $table->index(['resource_type', 'resource_id']);
            $table->index('action');
        });

        // -----------------------------------------------------------------------
        // Device Tokens (FCM Push Notifications)
        // -----------------------------------------------------------------------
        Schema::create('device_tokens', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('token');
            $table->enum('platform', ['android', 'ios', 'web'])->default('android');
            $table->string('device_id')->nullable();
            $table->string('device_name')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'token']);
            $table->index('user_id');
        });

        // -----------------------------------------------------------------------
        // OTP Verifications
        // -----------------------------------------------------------------------
        Schema::create('otp_verifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('identifier');
            $table->enum('channel', ['sms', 'email'])->default('sms');
            $table->string('code', 6);
            $table->enum('purpose', ['login', 'registration', 'password_reset', 'ride_verification'])->default('login');
            $table->boolean('is_verified')->default(false);
            $table->timestamp('expires_at');
            $table->integer('attempts')->default(0);
            $table->timestamps();

            $table->index(['identifier', 'purpose', 'is_verified']);
            $table->index('expires_at');
        });

        // -----------------------------------------------------------------------
        // Driver Documents (license photos, insurance, vehicle docs)
        // -----------------------------------------------------------------------
        Schema::create('driver_documents', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('driver_id');
            $table->foreign('driver_id')->references('id')->on('drivers')->onDelete('cascade');
            $table->enum('type', [
                'drivers_license_front',
                'drivers_license_back',
                'vehicle_insurance',
                'vehicle_registration',
                'profile_photo',
                'national_id',
                'other'
            ]);
            $table->string('file_path');
            $table->string('file_name');
            $table->string('mime_type', 50)->nullable();
            $table->integer('file_size')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('rejection_reason')->nullable();
            $table->uuid('reviewed_by')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();

            $table->index(['driver_id', 'type']);
            $table->index('status');
        });

        // -----------------------------------------------------------------------
        // Wallet Transactions (separate from gateway transactions)
        // -----------------------------------------------------------------------
        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('wallet_id');
            $table->foreign('wallet_id')->references('id')->on('wallets')->onDelete('cascade');
            $table->enum('type', ['credit', 'debit']);
            $table->decimal('amount', 14, 4);
            $table->decimal('balance_after', 14, 4);
            $table->string('description');
            $table->string('reference', 100)->nullable();
            $table->uuid('related_transaction_id')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['wallet_id', 'created_at']);
            $table->index('reference');
        });

        // -----------------------------------------------------------------------
        // Scheduled Rides
        // -----------------------------------------------------------------------
        Schema::create('scheduled_rides', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('customer_id');
            $table->foreign('customer_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('pickup_address');
            $table->decimal('pickup_lat', 10, 8);
            $table->decimal('pickup_lng', 11, 8);
            $table->string('dropoff_address');
            $table->decimal('dropoff_lat', 10, 8);
            $table->decimal('dropoff_lng', 11, 8);
            $table->enum('vehicle_type', ['economy', 'comfort', 'moto', 'xl'])->default('economy');
            $table->decimal('estimated_price', 10, 2);
            $table->timestamp('scheduled_at');
            $table->enum('status', ['pending', 'dispatched', 'cancelled'])->default('pending');
            $table->uuid('ride_request_id')->nullable();
            $table->timestamps();

            $table->index(['customer_id', 'scheduled_at']);
            $table->index(['status', 'scheduled_at']);
        });

        // -----------------------------------------------------------------------
        // Ratings (if not already created)
        // -----------------------------------------------------------------------
        if (!Schema::hasTable('ratings')) {
            Schema::create('ratings', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->uuid('ride_request_id')->nullable();
                $table->uuid('order_id')->nullable();
                $table->uuid('rater_id');
                $table->foreign('rater_id')->references('id')->on('users');
                $table->uuid('rated_user_id');
                $table->foreign('rated_user_id')->references('id')->on('users');
                $table->tinyInteger('score');
                $table->text('comment')->nullable();
                $table->timestamps();

                $table->index('ride_request_id');
                $table->index('rated_user_id');
            });
        }

        // -----------------------------------------------------------------------
        // System Settings (key-value runtime configuration)
        // -----------------------------------------------------------------------
        if (!Schema::hasTable('system_settings')) {
            Schema::create('system_settings', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('group', 50)->default('general');
                $table->string('key', 100);
                $table->text('value')->nullable();
                $table->string('type', 20)->default('string');
                $table->text('description')->nullable();
                $table->timestamps();

                $table->unique(['group', 'key']);
                $table->index('group');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('scheduled_rides');
        Schema::dropIfExists('wallet_transactions');
        Schema::dropIfExists('driver_documents');
        Schema::dropIfExists('otp_verifications');
        Schema::dropIfExists('device_tokens');
        Schema::dropIfExists('admin_activity_logs');
        Schema::dropIfExists('cms_blocks');
        Schema::dropIfExists('cms_sections');
        Schema::dropIfExists('cms_pages');
        Schema::dropIfExists('sos_events');
        Schema::dropIfExists('surge_rules');
        Schema::dropIfExists('surge_zones');
        Schema::dropIfExists('promo_code_uses');
        Schema::dropIfExists('promo_codes');
        Schema::dropIfExists('ratings');
        Schema::dropIfExists('system_settings');
    }
};
