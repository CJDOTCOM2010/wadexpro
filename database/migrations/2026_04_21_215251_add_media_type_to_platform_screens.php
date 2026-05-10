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
            $table->string('media_type')->default('image')->after('image_url'); // image, video
        });

        Schema::table('splash_screens', function (Blueprint $table) {
            $table->string('logo_media_type')->default('image')->after('logo_url');
            $table->string('background_media_type')->default('image')->after('background_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('onboarding_slides', function (Blueprint $table) {
            $table->dropColumn('media_type');
        });

        Schema::table('splash_screens', function (Blueprint $table) {
            $table->dropColumn(['logo_media_type', 'background_media_type']);
        });
    }
};
