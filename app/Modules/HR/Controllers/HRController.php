<?php

namespace App\Modules\HR\Controllers;

use App\Core\Traits\ApiResponse;
use App\Modules\HR\Models\Employee;
use App\Modules\HR\Models\LeaveRequest;
use App\Modules\HR\Services\EmployeeService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class HRController extends Controller
{
    use ApiResponse;

    public function __construct(private EmployeeService $employeeService)
    {
    }

    /**
     * List all employees with basic filtering.
     */
    public function index()
    {
        $employees = Employee::with('user')->paginate(15);
        return $this->paginated($employees, 'Employee directory retrieved.');
    }

    /**
     * Register a new employee.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'department' => 'required|string',
            'position' => 'required|string',
            'base_salary' => 'required|numeric',
        ]);

        $employee = $this->employeeService->onboard($validated);
        return $this->success($employee, 'Employee onboarded successfully.');
    }

    /**
     * Submit a leave request.
     */
    public function requestLeave(Request $request)
    {
        $employee = Employee::where('user_id', $request->user()->id)->firstOrFail();

        $validated = $request->validate([
            'leave_type' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string',
        ]);

        $leave = LeaveRequest::create(array_merge($validated, [
            'employee_id' => $employee->id,
            'status' => 'pending',
            'days_count' => 1.0, // Should be calculated in production!
        ]));

        return $this->success($leave, 'Leave request submitted.');
    }
}
