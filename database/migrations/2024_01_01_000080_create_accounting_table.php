<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Create chart_of_accounts WITHOUT the self-referential FK first
        Schema::create('chart_of_accounts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code', 20)->unique();
            // 1000=Assets, 2000=Liabilities, 3000=Equity, 4000=Revenue, 5000=Expenses
            $table->string('name');
            $table->string('account_type', 50)->default('asset');
            // asset | liability | equity | revenue | expense
            $table->uuid('parent_id')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_system')->default(false);
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index('account_type');
            $table->index('parent_id');
        });

        // Add self-referential FK after PK is established (PostgreSQL requirement)
        Schema::table('chart_of_accounts', function (Blueprint $table) {
            $table->foreign('parent_id')->references('id')->on('chart_of_accounts')->nullOnDelete();
        });

        Schema::create('journal_entries', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('reference', 100)->unique();
            $table->text('description')->nullable();
            $table->date('entry_date');
            $table->string('source_type', 50)->nullable();
            // order | payroll | expense | invoice | manual | refund
            $table->uuid('source_id')->nullable();
            $table->boolean('is_posted')->default(false);
            $table->boolean('is_reversed')->default(false);
            $table->uuid('reversal_of')->nullable();
            $table->uuid('created_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            $table->timestamp('posted_at')->nullable();
            $table->timestamps();

            $table->index(['source_type', 'source_id']);
            $table->index('entry_date');
            $table->index('is_posted');
        });

        Schema::create('journal_lines', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('journal_id');
            $table->foreign('journal_id')->references('id')->on('journal_entries')->onDelete('cascade');
            $table->uuid('account_id');
            $table->foreign('account_id')->references('id')->on('chart_of_accounts');
            $table->decimal('debit', 16, 4)->default(0.0000);
            $table->decimal('credit', 16, 4)->default(0.0000);
            $table->string('currency', 3)->default('GHS');
            $table->decimal('exchange_rate', 14, 6)->default(1.000000);
            $table->text('description')->nullable();
            $table->timestamps();
            // Application-layer constraint: sum(debit) = sum(credit) per journal_id

            $table->index('journal_id');
            $table->index('account_id');
        });

        Schema::create('invoices', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('invoice_number', 50)->unique();
            $table->uuid('customer_id');
            $table->foreign('customer_id')->references('id')->on('users');
            $table->uuid('order_id')->nullable();
            $table->date('issue_date');
            $table->date('due_date');
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('tax_rate', 5, 2)->default(0);
            $table->decimal('tax_amount', 12, 2)->default(0);
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->decimal('amount_paid', 12, 2)->default(0);
            $table->string('currency', 3)->default('GHS');
            $table->string('status', 20)->default('draft');
            // draft | sent | partially_paid | paid | overdue | cancelled | void
            $table->string('pdf_url')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('customer_id');
            $table->index('status');
            $table->index('due_date');
        });

        Schema::create('invoice_line_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('invoice_id');
            $table->foreign('invoice_id')->references('id')->on('invoices')->onDelete('cascade');
            $table->text('description');
            $table->decimal('quantity', 10, 3)->default(1.000);
            $table->string('unit', 30)->nullable();
            $table->decimal('unit_price', 12, 2)->default(0);
            $table->decimal('line_total', 12, 2)->default(0);
            $table->smallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('expenses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('category', 100);
            $table->text('description');
            $table->decimal('amount', 12, 2);
            $table->string('currency', 3)->default('GHS');
            $table->date('expense_date');
            $table->uuid('submitted_by')->nullable();
            $table->foreign('submitted_by')->references('id')->on('users')->nullOnDelete();
            $table->uuid('approved_by')->nullable();
            $table->foreign('approved_by')->references('id')->on('users')->nullOnDelete();
            $table->string('receipt_url')->nullable();
            $table->string('status', 20)->default('pending');
            // pending | approved | rejected | reimbursed
            $table->uuid('journal_entry_id')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('expense_date');
            $table->index('category');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expenses');
        Schema::dropIfExists('invoice_line_items');
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('journal_lines');
        Schema::dropIfExists('journal_entries');
        Schema::dropIfExists('chart_of_accounts');
    }
};
