<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Convert columns to JSON for i18n support
        // PostgreSQL can cast text to jsonb easily
        
        // 1. CMS Pages (Title, Meta Description)
        Schema::table('cms_pages', function (Blueprint $table) {
            $table->json('title_translations')->nullable();
            $table->json('meta_description_translations')->nullable();
        });

        // Migrate existing data for cms_pages
        DB::table('cms_pages')->get()->each(function ($page) {
            DB::table('cms_pages')->where('id', $page->id)->update([
                'title_translations' => json_encode(['en' => $page->title]),
                'meta_description_translations' => $page->meta_description ? json_encode(['en' => $page->meta_description]) : null,
            ]);
        });

        Schema::table('cms_pages', function (Blueprint $table) {
            $table->dropColumn(['title', 'meta_description']);
            $table->renameColumn('title_translations', 'title');
            $table->renameColumn('meta_description_translations', 'meta_description');
        });

        // 2. CMS Blocks (Content, Link Text)
        Schema::table('cms_blocks', function (Blueprint $table) {
            $table->json('content_translations')->nullable();
            $table->json('link_text_translations')->nullable();
        });

        // Migrate existing data for cms_blocks
        DB::table('cms_blocks')->get()->each(function ($block) {
            DB::table('cms_blocks')->where('id', $block->id)->update([
                'content_translations' => $block->content ? json_encode(['en' => $block->content]) : null,
                'link_text_translations' => $block->link_text ? json_encode(['en' => $block->link_text]) : null,
            ]);
        });

        Schema::table('cms_blocks', function (Blueprint $table) {
            $table->dropColumn(['content', 'link_text']);
            $table->renameColumn('content_translations', 'content');
            $table->renameColumn('link_text_translations', 'link_text');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Simple revert to text/string if needed, but JSON is more flexible
        Schema::table('cms_pages', function (Blueprint $table) {
            $table->string('title_temp')->nullable();
            $table->text('meta_description_temp')->nullable();
        });

        Schema::table('cms_blocks', function (Blueprint $table) {
            $table->text('content_temp')->nullable();
            $table->string('link_text_temp')->nullable();
        });
        
        // (Manual data migration back to single field if needed)

        Schema::table('cms_pages', function (Blueprint $table) {
            $table->dropColumn(['title', 'meta_description']);
            $table->renameColumn('title_temp', 'title');
            $table->renameColumn('meta_description_temp', 'meta_description');
        });

        Schema::table('cms_blocks', function (Blueprint $table) {
            $table->dropColumn(['content', 'link_text']);
            $table->renameColumn('content_temp', 'content');
            $table->renameColumn('link_text_temp', 'link_text');
        });
    }
};
