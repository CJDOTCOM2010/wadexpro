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
        if (!Schema::hasTable('referrals')) {
            Schema::create('referrals', function (Blueprint $table) {
                $table->uuid('id')->primary();
                
                $table->uuid('inviter_id'); 
                $table->foreign('inviter_id')->references('id')->on('users')->onDelete('cascade');
                
                $table->uuid('referee_id');
                $table->foreign('referee_id')->references('id')->on('users')->onDelete('cascade');
                
                $table->string('referral_code'); // The code used
                $table->enum('status', ['PENDING', 'COMPLETED', 'EXPIRED'])->default('PENDING');
                
                $table->decimal('reward_amount', 12, 2)->default(0);
                $table->timestamp('completed_at')->nullable();
                
                $table->timestamps();

                $table->unique('referee_id'); // A user can only be referred once
                $table->index('status');
            });
        }

        // Add referral_code to users table
        if (Schema::hasTable('users') && !Schema::hasColumn('users', 'referral_code')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('referral_code')->unique()->nullable()->after('email');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('referral_code');
        });
        Schema::dropIfExists('referrals');
    }
};
