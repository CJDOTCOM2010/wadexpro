<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('staff_profiles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id')->unique();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();

            // Personal Information
            $table->string('first_name');
            $table->string('last_name');
            $table->string('middle_name')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('gender', 20)->nullable();
            $table->string('marital_status', 30)->nullable();
            $table->string('nationality')->nullable();
            $table->string('id_type', 50)->nullable(); // NIN, Passport, Voter's Card
            $table->string('id_number', 50)->nullable();

            // Contact Details
            $table->string('personal_email')->nullable();
            $table->string('personal_phone', 20)->nullable();
            $table->text('residential_address')->nullable();
            $table->string('city')->nullable();
            $table->string('state_province')->nullable();
            $table->string('postal_code', 20)->nullable();
            $table->string('country')->nullable();

            // Emergency Contact
            $table->string('emergency_name')->nullable();
            $table->string('emergency_phone', 20)->nullable();
            $table->string('emergency_relationship', 50)->nullable();
            $table->text('emergency_address')->nullable();

            // Employment Details
            $table->string('employee_id', 30)->nullable()->unique();
            $table->string('job_title')->nullable();
            $table->string('department')->nullable();
            $table->string('employment_type', 30)->default('full_time'); // full_time, part_time, contract, intern
            $table->date('hire_date')->nullable();
            $table->date('probation_end')->nullable();
            $table->string('work_location')->nullable();
            $table->string('reporting_to')->nullable(); // Manager name or ID
            $table->string('salary_grade', 30)->nullable();
            $table->decimal('base_salary', 12, 2)->nullable();
            $table->string('pay_frequency', 20)->default('monthly'); // weekly, bi-weekly, monthly

            // Banking / Payment
            $table->string('bank_name')->nullable();
            $table->string('account_name')->nullable();
            $table->string('account_number', 30)->nullable();
            $table->string('sort_code', 20)->nullable();
            $table->string('tax_id', 30)->nullable(); // TIN

            // Documents (file paths stored)
            $table->string('cv_path')->nullable();
            $table->string('id_document_path')->nullable();
            $table->string('proof_of_address_path')->nullable();
            $table->string('offer_letter_path')->nullable();
            $table->string('photo_path')->nullable();

            // Status
            $table->string('onboarding_status', 30)->default('pending'); // pending, complete, on_hold
            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staff_profiles');
    }
};
