<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('modules', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('slug', 100)->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('is_enabled')->default(true);
            $table->string('version', 20)->nullable();
            $table->jsonb('config')->default('{}');
            $table->timestamps();
        });

        Schema::create('system_settings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type', 30)->default('string');
            // Values: string | integer | boolean | json | encrypted
            $table->string('group', 100)->default('general');
            $table->string('label')->nullable();
            $table->text('tooltip')->nullable();   // Shown as ? help in UI
            $table->boolean('is_public')->default(false); // Exposed to mobile API
            $table->boolean('is_encrypted')->default(false);
            $table->timestamps();

            $table->index('group');
            $table->index('is_public');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('system_settings');
        Schema::dropIfExists('modules');
    }
};
