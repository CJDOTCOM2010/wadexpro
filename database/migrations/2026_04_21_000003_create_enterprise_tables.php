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
        Schema::create('organizations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('logo_url')->nullable();
            
            $table->string('tax_id')->nullable();
            $table->string('billing_email')->nullable();
            $table->text('billing_address')->nullable();
            
            $table->decimal('balance', 14, 4)->default(0); // For Prepaid
            $table->decimal('credit_limit', 14, 4)->default(0); // For Postpaid
            $table->enum('billing_type', ['PREPAID', 'POSTPAID'])->default('PREPAID');
            $table->enum('billing_cycle', ['WEEKLY', 'MONTHLY', 'QUARTERLY'])->default('MONTHLY');
            
            $table->boolean('is_active')->default(true);
            $table->json('settings')->nullable(); // For custom enterprise rules (e.g., allowed vehicle types)
            
            $table->timestamps();
            $table->softDeletes();

            $table->index('billing_type');
            $table->index('is_active');
        });

        Schema::create('organization_members', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('organization_id');
            $table->foreign('organization_id')->references('id')->on('organizations')->onDelete('cascade');
            
            $table->uuid('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            
            $table->enum('role', ['ORG_ADMIN', 'ORG_MANAGER', 'ORG_STAFF'])->default('ORG_STAFF');
            $table->boolean('can_use_org_wallet')->default(true);
            
            $table->timestamps();

            $table->unique(['organization_id', 'user_id']);
            $table->index('role');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organization_members');
        Schema::dropIfExists('organizations');
    }
};
