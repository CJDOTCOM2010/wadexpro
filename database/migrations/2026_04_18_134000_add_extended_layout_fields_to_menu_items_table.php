<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('menu_items', function (Blueprint $table) {
            $table->string('layout')->default('standard')->after('type'); // standard, extended_grid
            $table->string('badge_text')->nullable()->after('description');
            $table->string('badge_color')->nullable()->after('badge_text');
            $table->json('meta_data')->nullable()->after('css_class'); // For price, rating, date
        });
    }

    public function down(): void
    {
        Schema::table('menu_items', function (Blueprint $table) {
            $table->dropColumn(['layout', 'badge_text', 'badge_color', 'meta_data']);
        });
    }
};
