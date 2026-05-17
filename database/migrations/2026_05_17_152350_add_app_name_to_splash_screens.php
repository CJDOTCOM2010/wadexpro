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
            $table->string('app_name')->nullable()->after('tagline');
            $table->boolean('show_app_name')->default(true)->after('show_tagline');
        });
    }

    public function down(): void
    {
        Schema::table('splash_screens', function (Blueprint $table) {
            $table->dropColumn(['app_name', 'show_app_name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('splash_screens', function (Blueprint $table) {
            //
        });
    }
};
