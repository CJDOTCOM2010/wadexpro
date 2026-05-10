<?php

namespace App\Modules\HR\Controllers;

use App\Core\Traits\ApiResponse;
use App\Modules\HR\Models\Employee;
use App\Modules\HR\Services\AttendanceService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class AttendanceController extends Controller
{
    use ApiResponse;

    public function __construct(private AttendanceService $attendanceService)
    {
    }

    /**
     * List all attendance records for admin oversight.
     */
    public function index()
    {
        $records = \App\Modules\HR\Models\AttendanceRecord::with('employee.user')
            ->orderBy('date', 'desc')
            ->paginate(30);
            
        return $this->paginated($records, 'Attendance history retrieved.');
    }

    /**
     * Records a clock-in for the current authenticated employee.
     */
    public function clockIn(Request $request)
    {
        $employee = Employee::where('user_id', $request->user()->id)->where('is_active', true)->first();

        if (!$employee) {
            return $this->error('Employee record not found or inactive.', 404);
        }

        try {
            $record = $this->attendanceService->clockIn($employee, $request->lat, $request->lng);
            return $this->success($record, 'Clock-in recorded successfully.');
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 400);
        }
    }

    /**
     * Records a clock-out for the current authenticated employee.
     */
    public function clockOut(Request $request)
    {
        $employee = Employee::where('user_id', $request->user()->id)->first();

        if (!$employee) {
            return $this->error('Employee record not found.', 404);
        }

        try {
            $record = $this->attendanceService->clockOut($employee);
            return $this->success($record, 'Clock-out recorded successfully.');
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 400);
        }
    }
}
