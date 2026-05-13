<?php

namespace App\Modules\Admin\Models;

use App\Core\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class StaffProfile extends Model
{
    use HasUuid;

    protected $fillable = [
        'user_id',
        // Personal
        'first_name', 'last_name', 'middle_name', 'date_of_birth', 'gender',
        'marital_status', 'nationality', 'id_type', 'id_number',
        // Contact
        'personal_email', 'personal_phone', 'residential_address',
        'city', 'state_province', 'postal_code', 'country',
        // Emergency
        'emergency_name', 'emergency_phone', 'emergency_relationship', 'emergency_address',
        // Employment
        'employee_id', 'job_title', 'department', 'employment_type',
        'hire_date', 'probation_end', 'work_location', 'reporting_to',
        'salary_grade', 'base_salary', 'pay_frequency',
        // Banking
        'bank_name', 'account_name', 'account_number', 'sort_code', 'tax_id',
        // Documents
        'cv_path', 'id_document_path', 'proof_of_address_path', 'offer_letter_path', 'photo_path',
        // Status
        'onboarding_status', 'notes',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'hire_date' => 'date',
        'probation_end' => 'date',
        'base_salary' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}
