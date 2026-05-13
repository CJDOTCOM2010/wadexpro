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
        Schema::table('wallet_transactions', function (Blueprint $table) {
            if (!Schema::hasColumn('wallet_transactions', 'user_id')) {
                $table->uuid('user_id')->nullable();
            }
            if (!Schema::hasColumn('wallet_transactions', 'category')) {
                $table->string('category', 40)->default('general');
            }
            if (!Schema::hasColumn('wallet_transactions', 'balance_before')) {
                $table->decimal('balance_before', 14, 4)->default(0);
            }
            if (!Schema::hasColumn('wallet_transactions', 'status')) {
                $table->string('status', 20)->default('completed');
            }
            if (!Schema::hasColumn('wallet_transactions', 'performed_by')) {
                $table->uuid('performed_by')->nullable();
            }
            if (!Schema::hasColumn('wallet_transactions', 'transacted_at')) {
                $table->timestamp('transacted_at')->useCurrent();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wallet_transactions', function (Blueprint $table) {
            $columns = ['user_id', 'category', 'balance_before', 'status', 'performed_by', 'transacted_at'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('wallet_transactions', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
