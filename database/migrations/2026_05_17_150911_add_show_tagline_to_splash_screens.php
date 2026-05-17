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
            $table->boolean('show_tagline')->default(true)->after('show_background');
        });
    }

    public function down(): void
    {
        Schema::table('splash_screens', function (Blueprint $table) {
            $table->dropColumn('show_tagline');
        });
    }
};
