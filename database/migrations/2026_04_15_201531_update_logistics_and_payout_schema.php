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
        Schema::table('orders', function (Blueprint $table) {
            $table->uuid('payout_id')->nullable()->index();
        });

        Schema::table('driver_payouts', function (Blueprint $table) {
            $table->unsignedInteger('total_deliveries')->default(0);
            $table->jsonb('metadata')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->string('reference')->nullable()->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('driver_payouts', function (Blueprint $table) {
            $table->dropColumn(['total_deliveries', 'metadata', 'processed_at', 'reference']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('payout_id');
        });
    }
};
