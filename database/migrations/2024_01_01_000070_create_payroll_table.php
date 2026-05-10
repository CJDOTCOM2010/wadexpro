<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payroll_runs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->date('period_start');
            $table->date('period_end');
            $table->date('run_date');
            $table->string('status', 20)->default('draft');
            // draft | processing | approved | paid | cancelled
            $table->decimal('total_gross', 14, 2)->default(0);
            $table->decimal('total_deductions', 14, 2)->default(0);
            $table->decimal('total_net', 14, 2)->default(0);
            $table->string('currency', 3)->default('GHS');
            $table->uuid('created_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            $table->uuid('approved_by')->nullable();
            $table->foreign('approved_by')->references('id')->on('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index(['period_start', 'period_end']);
        });

        Schema::create('payslip_entries', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('payroll_run_id');
            $table->foreign('payroll_run_id')->references('id')->on('payroll_runs')->onDelete('cascade');
            $table->uuid('employee_id');
            $table->foreign('employee_id')->references('id')->on('employees');
            $table->decimal('gross_pay', 12, 2)->default(0);
            $table->decimal('basic_salary', 12, 2)->default(0);
            $table->jsonb('allowances')->default('[]');
            // [{"name": "Transport", "amount": 200.00}, ...]
            $table->jsonb('deductions')->default('[]');
            // [{"name": "Tax", "amount": 50.00, "type": "statutory"}, ...]
            $table->decimal('tax_deducted', 12, 2)->default(0);
            $table->decimal('social_security_deducted', 12, 2)->default(0);
            $table->decimal('net_pay', 12, 2)->default(0);
            $table->string('currency', 3)->default('GHS');
            $table->string('payment_status', 20)->default('pending');
            // pending | paid | failed
            $table->uuid('transaction_id')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->unique(['payroll_run_id', 'employee_id']);
            $table->index('employee_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payslip_entries');
        Schema::dropIfExists('payroll_runs');
    }
};
