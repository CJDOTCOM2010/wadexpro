<?php

namespace App\Modules\HR\Models;

use App\Models\User;
use App\Modules\Monitoring\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Employee extends Model
{
    use HasFactory, HasUuids, Auditable;

    protected $fillable = [
        'user_id',
        'employee_code',
        'department',
        'position',
        'hire_date',
        'employment_type',
        'base_salary',
        'salary_currency',
        'bank_name',
        'bank_account',
        'bank_branch',
        'tax_id',
        'social_security_number',
        'is_active',
        'termination_date',
        'notes',
    ];

    protected $casts = [
        'hire_date' => 'date',
        'termination_date' => 'date',
        'is_active' => 'boolean',
        'base_salary' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function attendanceRecords()
    {
        return $this->hasMany(AttendanceRecord::class);
    }

    public function leaveRequests()
    {
        return $this->hasMany(LeaveRequest::class);
    }

    public function payslips()
    {
        return $this->hasMany(PayslipEntry::class);
    }
}
