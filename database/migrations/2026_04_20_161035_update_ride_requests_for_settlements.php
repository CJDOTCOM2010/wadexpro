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
            $table->enum('payment_status', ['unpaid', 'settled', 'failed', 'pending'])->default('unpaid')->after('status');
            $table->decimal('commission_amount', 14, 4)->default(0)->after('final_price');
            $table->decimal('driver_earnings', 14, 4)->default(0)->after('commission_amount');
            $table->decimal('tax_amount', 14, 4)->default(0)->after('driver_earnings');
            $table->string('payment_reference')->nullable()->after('payment_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ride_requests', function (Blueprint $table) {
            $table->dropColumn(['payment_status', 'commission_amount', 'driver_earnings', 'tax_amount', 'payment_reference']);
        });
    }
};
