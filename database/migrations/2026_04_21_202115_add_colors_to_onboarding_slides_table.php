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
        Schema::table('onboarding_slides', function (Blueprint $table) {
            $table->string('bg_color')->nullable()->after('image_url');
            $table->string('text_color')->nullable()->after('bg_color');
            $table->string('button_color')->nullable()->after('text_color');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('onboarding_slides', function (Blueprint $table) {
            $table->dropColumn(['bg_color', 'text_color', 'button_color']);
        });
    }
};
