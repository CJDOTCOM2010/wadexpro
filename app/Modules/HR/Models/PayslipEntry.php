<?php

namespace App\Modules\HR\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class PayslipEntry extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'payroll_run_id',
        'employee_id',
        'gross_pay',
        'basic_salary',
        'allowances',
        'deductions',
        'tax_deducted',
        'social_security_deducted',
        'net_pay',
        'currency',
        'payment_status',
        'transaction_id',
        'paid_at',
    ];

    protected $casts = [
        'allowances' => 'json',
        'deductions' => 'json',
        'gross_pay' => 'decimal:2',
        'basic_salary' => 'decimal:2',
        'tax_deducted' => 'decimal:2',
        'social_security_deducted' => 'decimal:2',
        'net_pay' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    public function payrollRun()
    {
        return $this->belongsTo(PayrollRun::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
