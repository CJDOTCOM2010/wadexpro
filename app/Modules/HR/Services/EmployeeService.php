<?php

namespace App\Modules\HR\Services;

use App\Models\User;
use App\Modules\HR\Models\Employee;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EmployeeService
{
    /**
     * Onboard a new employee from an existing User or create a new one.
     */
    public function onboard(array $data): Employee
    {
        return DB::transaction(function () use ($data) {
            $user = null;
            if (isset($data['user_id'])) {
                $user = User::findOrFail($data['user_id']);
            } else {
                // Create minimal user if not exists
                $user = User::create([
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'password' => bcrypt(Str::random(16)),
                    'user_type' => 'employee',
                ]);
            }

            return Employee::create([
                'user_id' => $user->id,
                'employee_code' => $data['employee_code'] ?? 'EMP-' . strtoupper(Str::random(6)),
                'department' => $data['department'],
                'position' => $data['position'],
                'hire_date' => $data['hire_date'] ?? now(),
                'employment_type' => $data['employment_type'] ?? 'full_time',
                'base_salary' => $data['base_salary'] ?? 0,
                'salary_currency' => $data['salary_currency'] ?? 'GHS',
                'bank_name' => $data['bank_name'] ?? null,
                'bank_account' => $data['bank_account'] ?? null,
                'bank_branch' => $data['bank_branch'] ?? null,
                'tax_id' => $data['tax_id'] ?? null,
                'social_security_number' => $data['social_security_number'] ?? null,
                'is_active' => true,
            ]);
        });
    }

    /**
     * Terminate or deactivate an employee.
     */
    public function terminate(string $employeeId, string $reason = ''): bool
    {
        $employee = Employee::findOrFail($employeeId);
        return $employee->update([
            'is_active' => false,
            'termination_date' => now(),
            'notes' => trim($employee->notes . "\nTerminated on " . now()->toDateString() . ": " . $reason),
        ]);
    }
}
