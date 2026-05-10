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
            $table->string('button_type')->default('action_below_text')->after('button_text');
        });
    }

    public function down(): void
    {
        Schema::table('onboarding_slides', function (Blueprint $table) {
            $table->dropColumn('button_type');
        });
    }
};
