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
        Schema::table('ride_requests', function (Blueprint $table) {
            $table->uuid('organization_id')->nullable()->after('customer_id');
            $table->foreign('organization_id')->references('id')->on('organizations')->onDelete('set null');
            $table->enum('billing_source', ['PERSONAL', 'CORPORATE'])->default('PERSONAL')->after('payment_method');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->uuid('organization_id')->nullable()->after('customer_id');
            $table->foreign('organization_id')->references('id')->on('organizations')->onDelete('set null');
            $table->enum('billing_source', ['PERSONAL', 'CORPORATE'])->default('PERSONAL')->after('payment_method');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['organization_id', 'billing_source']);
        });

        Schema::table('ride_requests', function (Blueprint $table) {
            $table->dropColumn(['organization_id', 'billing_source']);
        });
    }
};
