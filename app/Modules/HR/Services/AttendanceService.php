<?php

namespace App\Modules\HR\Services;

use App\Modules\HR\Models\AttendanceRecord;
use App\Modules\HR\Models\Employee;
use Carbon\Carbon;

class AttendanceService
{
    /**
     * Record clock-in event.
     */
    public function clockIn(Employee $employee, float $lat = null, float $lng = null): AttendanceRecord
    {
        $today = Carbon::today();

        // Check if already clocked in today
        $existing = AttendanceRecord::where('employee_id', $employee->id)
            ->where('date', $today->toDateString())
            ->first();

        if ($existing && $existing->clock_in) {
            throw new \Exception("Already clocked in for today.");
        }

        return AttendanceRecord::create([
            'employee_id' => $employee->id,
            'date' => $today->toDateString(),
            'clock_in' => now(),
            'status' => 'present',
            'lat_in' => $lat,
            'lng_in' => $lng,
        ]);
    }

    /**
     * Record clock-out event.
     */
    public function clockOut(Employee $employee): AttendanceRecord
    {
        $record = AttendanceRecord::where('employee_id', $employee->id)
            ->whereDate('date', Carbon::today())
            ->firstOrFail();

        if ($record->clock_out) {
            throw new \Exception("Already clocked out for today.");
        }

        $record->update(['clock_out' => now()]);
        return $record;
    }
}
