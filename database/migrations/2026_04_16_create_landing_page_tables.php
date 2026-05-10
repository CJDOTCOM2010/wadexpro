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
        // 1. Countries/Regions Supported by the Platform
        Schema::create('regions', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // e.g., gh, ng, gb
            $table->string('name');
            $table->string('currency_code', 3);
            $table->string('language_default_code', 5)->default('en');
            $table->string('flag_url')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. Dynamic Landing Page Content Fragments
        Schema::create('landing_page_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('region_id')->constrained()->onDelete('cascade');
            $table->string('lang_code', 5)->default('en'); // e.g., en, fr
            $table->string('section_key'); // e.g., hero, service_ride, service_drive, footer
            $table->json('content'); // stores title, subtitle, image_url, cta_links, etc.
            $table->integer('sort_order')->default(0);
            $table->boolean('is_published')->default(true);
            $table->timestamps();

            $table->unique(['region_id', 'lang_code', 'section_key'], 'section_region_lang_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('landing_page_sections');
        Schema::dropIfExists('regions');
    }
};
