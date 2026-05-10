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
            $table->string('trip_profile')->default('PERSONAL')->after('billing_source'); // e.g., PERSONAL, BUSINESS, FAMILY
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->string('trip_profile')->default('PERSONAL')->after('billing_source');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('trip_profile');
        });

        Schema::table('ride_requests', function (Blueprint $table) {
            $table->dropColumn('trip_profile');
        });
    }
};
