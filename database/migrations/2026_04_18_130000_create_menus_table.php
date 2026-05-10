<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('menus', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('location')->default('header'); // header, footer, mobile
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('menu_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('menu_id')->constrained('menus')->cascadeOnDelete();
            $table->uuid('parent_id')->nullable();
            $table->string('label');
            $table->string('url')->nullable();
            $table->string('icon')->nullable();          // SVG icon name or class
            $table->string('image_url')->nullable();      // Mega menu image
            $table->text('description')->nullable();       // Sub-text under label
            $table->string('group_label')->nullable();     // Column heading in mega menu
            $table->string('type')->default('link');        // link, group, divider, cta_button
            $table->string('target')->default('_self');     // _self, _blank
            $table->string('css_class')->nullable();        // Custom styling
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->index(['menu_id', 'parent_id', 'sort_order']);
        });

        Schema::table('menu_items', function (Blueprint $table) {
            $table->foreign('parent_id')->references('id')->on('menu_items')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menu_items');
        Schema::dropIfExists('menus');
    }
};
