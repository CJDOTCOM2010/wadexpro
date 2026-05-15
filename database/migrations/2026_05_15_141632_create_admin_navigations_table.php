<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admin_navigations', function (Blueprint $table) {
            $table->id();
            $table->string('section');
            $table->string('label');
            $table->string('route')->nullable();
            $table->text('icon')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_visible')->default(true);
            $table->string('permission')->nullable();
            $table->string('badge')->nullable();
            $table->timestamps();

            $table->index('section');
            $table->index('is_visible');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_navigations');
    }
};