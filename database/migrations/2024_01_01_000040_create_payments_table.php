<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wallets', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id')->unique();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->decimal('balance', 14, 4)->default(0.0000);
            $table->string('currency', 3)->default('GHS');
            $table->boolean('is_frozen')->default(false);
            $table->timestamps();
        });

        Schema::create('transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('reference', 100)->unique();
            $table->uuid('order_id')->nullable();
            $table->uuid('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->string('type', 50)->default('payment');
            // payment | payout | refund | wallet_topup | wallet_withdrawal | commission
            $table->string('gateway', 50)->nullable();
            // paystack | flutterwave | stripe | google_pay | wallet | cash
            $table->string('gateway_ref')->nullable();
            $table->decimal('amount', 14, 4);
            $table->string('currency', 3);
            $table->decimal('exchange_rate', 14, 6)->default(1.000000);
            $table->decimal('amount_in_base_currency', 14, 4)->nullable();
            $table->string('status', 30)->default('pending');
            // pending | processing | completed | failed | reversed
            $table->text('failure_reason')->nullable();
            $table->jsonb('metadata')->default('{}');
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index('status');
            $table->index('gateway_ref');
            $table->index('type');
            $table->index('created_at');
        });

        Schema::create('driver_payouts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('driver_id');
            $table->foreign('driver_id')->references('id')->on('drivers');
            $table->date('period_start');
            $table->date('period_end');
            $table->decimal('gross_amount', 12, 2)->default(0);
            $table->decimal('commission_rate', 5, 2)->default(0);
            $table->decimal('commission_amount', 12, 2)->default(0);
            $table->decimal('net_amount', 12, 2)->default(0);
            $table->string('currency', 3)->default('GHS');
            $table->string('status', 30)->default('pending');
            // pending | processing | paid | failed
            $table->uuid('transaction_id')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('driver_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('driver_payouts');
        Schema::dropIfExists('transactions');
        Schema::dropIfExists('wallets');
    }
};
