<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('splash_screens', function (Blueprint $table) {
            $table->boolean('show_bg_color')->default(true)->after('show_app_name');
            $table->float('bg_color_opacity', 3, 2)->default(0.70)->after('show_bg_color');
            $table->boolean('show_accent_color')->default(true)->after('bg_color_opacity');
            $table->float('accent_color_opacity', 3, 2)->default(0.30)->after('show_accent_color');
        });
    }

    public function down(): void
    {
        Schema::table('splash_screens', function (Blueprint $table) {
            $table->dropColumn([
                'show_bg_color',
                'bg_color_opacity',
                'show_accent_color',
                'accent_color_opacity',
            ]);
        });
    }
};
