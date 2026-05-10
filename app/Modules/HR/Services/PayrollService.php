<?php

namespace App\Modules\HR\Services;

use App\Modules\HR\Models\Employee;
use App\Modules\HR\Models\PayrollRun;
use App\Modules\HR\Models\PayslipEntry;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PayrollService
{
    /**
     * Run payroll for a specific date range.
     */
    public function run(Carbon $start, Carbon $end, array $metadata = []): PayrollRun
    {
        return DB::transaction(function () use ($start, $end, $metadata) {
            $payrollRun = PayrollRun::create([
                'period_start' => $start,
                'period_end' => $end,
                'run_date' => now(),
                'status' => 'draft',
                'currency' => 'GHS',
                'created_by' => auth()->id(),
                'notes' => $metadata['notes'] ?? null,
            ]);

            $employees = Employee::where('is_active', true)->get();
            $totalGross = 0;
            $totalNet = 0;
            $totalDeductions = 0;

            foreach ($employees as $employee) {
                // Simplified calculation logic for MVP
                $basic = $employee->base_salary;
                $taxRate = 0.15; // 15% flat tax for MVP
                $ssnitRate = 0.055; // 5.5% for SSNIT
                
                $taxAmount = $basic * $taxRate;
                $socialSecurity = $basic * $ssnitRate;
                $net = $basic - ($taxAmount + $socialSecurity);

                PayslipEntry::create([
                    'payroll_run_id' => $payrollRun->id,
                    'employee_id' => $employee->id,
                    'basic_salary' => $basic,
                    'gross_pay' => $basic,
                    'tax_deducted' => $taxAmount,
                    'social_security_deducted' => $socialSecurity,
                    'net_pay' => $net,
                    'currency' => $employee->salary_currency,
                    'allowances' => [],
                    'deductions' => [
                        ['name' => 'Income Tax', 'amount' => $taxAmount],
                        ['name' => 'Social Security', 'amount' => $socialSecurity]
                    ]
                ]);

                $totalGross += $basic;
                $totalNet += $net;
                $totalDeductions += ($taxAmount + $socialSecurity);
            }

            $payrollRun->update([
                'total_gross' => $totalGross,
                'total_net' => $totalNet,
                'total_deductions' => $totalDeductions,
            ]);

            return $payrollRun;
        });
    }
}
