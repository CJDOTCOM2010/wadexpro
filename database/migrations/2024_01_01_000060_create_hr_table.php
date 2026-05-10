<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id')->nullable()->unique();
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            $table->string('employee_code', 20)->unique();
            $table->string('department', 100)->nullable();
            $table->string('position', 100)->nullable();
            $table->date('hire_date');
            $table->string('employment_type', 50)->default('full_time');
            // full_time | part_time | contract | intern
            $table->decimal('base_salary', 14, 2)->default(0);
            $table->string('salary_currency', 3)->default('GHS');
            $table->string('bank_name', 100)->nullable();
            $table->string('bank_account', 50)->nullable();
            $table->string('bank_branch', 100)->nullable();
            $table->string('tax_id', 50)->nullable();
            $table->string('social_security_number', 50)->nullable();
            $table->boolean('is_active')->default(true);
            $table->date('termination_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('department');
            $table->index('is_active');
        });

        Schema::create('attendance_records', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('employee_id');
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            $table->date('date');
            $table->time('clock_in')->nullable();
            $table->time('clock_out')->nullable();
            $table->integer('break_minutes')->default(0);
            $table->string('status', 20)->default('present');
            // present | absent | late | half_day | leave | public_holiday
            $table->decimal('lat_in', 10, 8)->nullable();
            $table->decimal('lng_in', 11, 8)->nullable();
            $table->text('notes')->nullable();
            $table->uuid('approved_by')->nullable();
            $table->timestamps();

            $table->unique(['employee_id', 'date']);
            $table->index(['employee_id', 'date']);
        });

        Schema::create('leave_requests', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('employee_id');
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            $table->string('leave_type', 50)->default('annual');
            // annual | sick | maternity | paternity | unpaid | study | bereavement
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('days_count', 5, 1)->default(0);
            $table->text('reason');
            $table->string('status', 20)->default('pending');
            // pending | approved | rejected | cancelled
            $table->uuid('approved_by')->nullable();
            $table->text('approver_notes')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();

            $table->index(['employee_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leave_requests');
        Schema::dropIfExists('attendance_records');
        Schema::dropIfExists('employees');
    }
};
