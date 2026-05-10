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
        Schema::table('splash_screens', function (Blueprint $table) {
            $table->boolean('show_logo')->default(true)->after('tagline');
            $table->boolean('show_background')->default(true)->after('show_logo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('splash_screens', function (Blueprint $table) {
            $table->dropColumn(['show_logo', 'show_background']);
        });
    }
};
