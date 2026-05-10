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
        Schema::table('drivers', function (Blueprint $table) {
            $table->string('id_card_front_url')->after('license_class')->nullable();
            $table->string('id_card_back_url')->after('id_card_front_url')->nullable();
            $table->string('driver_photo_url')->after('id_card_back_url')->nullable();
            $table->timestamp('verified_at')->after('status')->nullable();
            $table->text('verification_notes')->after('verified_at')->nullable();
            $table->text('rejection_reason')->after('verification_notes')->nullable();
            
            // Re-define status to be clearer or just update comment if needed.
            // Current values in previous migration: pending_verification | active | suspended | deactivated
            // We'll stick with these but use 'pending_verification' as the gateway.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('drivers', function (Blueprint $table) {
            $table->dropColumn([
                'id_card_front_url',
                'id_card_back_url',
                'driver_photo_url',
                'verified_at',
                'verification_notes',
                'rejection_reason',
            ]);
        });
    }
};
