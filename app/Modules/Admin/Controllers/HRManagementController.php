<?php

namespace App\Modules\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Modules\Admin\Models\Role;
use App\Modules\Admin\Models\StaffProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class HRManagementController extends Controller
{
    /**
     * Staff registry listing.
     */
    public function index(Request $request)
    {
        try {
            $query = User::whereIn('user_type', ['admin', 'support', 'staff', 'manager', 'employee'])
                ->with('staffProfile')
                ->latest();

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            }

            if ($request->filled('role')) {
                $query->where('user_type', $request->role);
            }

            $staff = $query->paginate(20)->withQueryString();

            $stats = [
                'total'    => User::whereIn('user_type', ['admin', 'support', 'staff', 'manager', 'employee'])->count(),
                'admins'   => User::where('user_type', 'admin')->count(),
                'support'  => User::where('user_type', 'support')->count(),
            ];

            $roles = Role::orderBy('name')->get();

            return view('admin.hr_management', compact('staff', 'stats', 'roles'));
        } catch (\Exception $e) {
            return back()->with('error', 'Error loading HR Management: ' . $e->getMessage());
        }
    }

    /**
     * Show the professional staff onboarding form.
     */
    public function create()
    {
        $roles = Role::orderBy('name')->get();
        return view('admin.hr_onboarding', compact('roles'));
    }

    /**
     * Process the full staff onboarding submission.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            // Personal
            'first_name'      => 'required|string|max:100',
            'last_name'       => 'required|string|max:100',
            'middle_name'     => 'nullable|string|max:100',
            'date_of_birth'   => 'nullable|date|before:today',
            'gender'          => 'nullable|in:male,female,other',
            'marital_status'  => 'nullable|in:single,married,divorced,widowed',
            'nationality'     => 'nullable|string|max:100',
            'id_type'         => 'nullable|string|max:50',
            'id_number'       => 'nullable|string|max:50',
            // Contact
            'email'           => 'required|email|unique:users,email',
            'phone'           => 'nullable|string|max:20',
            'personal_email'  => 'nullable|email|max:255',
            'personal_phone'  => 'nullable|string|max:20',
            'residential_address' => 'nullable|string|max:500',
            'city'            => 'nullable|string|max:100',
            'state_province'  => 'nullable|string|max:100',
            'postal_code'     => 'nullable|string|max:20',
            'country'         => 'nullable|string|max:100',
            // Emergency
            'emergency_name'  => 'nullable|string|max:200',
            'emergency_phone' => 'nullable|string|max:20',
            'emergency_relationship' => 'nullable|string|max:50',
            'emergency_address' => 'nullable|string|max:500',
            // Employment
            'role'            => 'required|string|exists:roles,name',
            'job_title'       => 'nullable|string|max:200',
            'department'      => 'nullable|string|max:100',
            'employment_type' => 'nullable|in:full_time,part_time,contract,intern',
            'hire_date'       => 'nullable|date',
            'probation_end'   => 'nullable|date|after:hire_date',
            'work_location'   => 'nullable|string|max:200',
            'reporting_to'    => 'nullable|string|max:200',
            'salary_grade'    => 'nullable|string|max:30',
            'base_salary'     => 'nullable|numeric|min:0',
            'pay_frequency'   => 'nullable|in:weekly,bi_weekly,monthly',
            // Banking
            'bank_name'       => 'nullable|string|max:200',
            'account_name'    => 'nullable|string|max:200',
            'account_number'  => 'nullable|string|max:30',
            'sort_code'       => 'nullable|string|max:20',
            'tax_id'          => 'nullable|string|max:30',
            // Documents
            'cv_file'         => 'nullable|file|mimes:pdf,doc,docx|max:5120',
            'id_document'     => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'proof_of_address'=> 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'photo'           => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
            // Notes
            'notes'           => 'nullable|string|max:2000',
        ]);

        $tempPassword = Str::random(12);
        $employeeId = 'WDX-' . strtoupper(Str::random(6));

        // Create the user account
        $user = User::create([
            'name'       => $data['first_name'] . ' ' . $data['last_name'],
            'email'      => $data['email'],
            'phone'      => $data['phone'] ?? null,
            'user_type'  => 'employee',
            'password'   => Hash::make($tempPassword),
            'is_active'  => true,
        ]);

        // Assign the role
        $role = Role::where('name', $data['role'])->first();
        if ($role) {
            $user->roles()->syncWithoutDetaching([$role->id]);
        }

        // Handle file uploads
        $cvPath = $request->hasFile('cv_file') ? $request->file('cv_file')->store('staff/cv', 'public') : null;
        $idDocPath = $request->hasFile('id_document') ? $request->file('id_document')->store('staff/id_docs', 'public') : null;
        $proofPath = $request->hasFile('proof_of_address') ? $request->file('proof_of_address')->store('staff/proof', 'public') : null;
        $photoPath = $request->hasFile('photo') ? $request->file('photo')->store('staff/photos', 'public') : null;

        // Create the staff profile
        StaffProfile::create([
            'user_id'          => $user->id,
            'first_name'       => $data['first_name'],
            'last_name'        => $data['last_name'],
            'middle_name'      => $data['middle_name'] ?? null,
            'date_of_birth'    => $data['date_of_birth'] ?? null,
            'gender'           => $data['gender'] ?? null,
            'marital_status'   => $data['marital_status'] ?? null,
            'nationality'      => $data['nationality'] ?? null,
            'id_type'          => $data['id_type'] ?? null,
            'id_number'        => $data['id_number'] ?? null,
            'personal_email'   => $data['personal_email'] ?? null,
            'personal_phone'   => $data['personal_phone'] ?? null,
            'residential_address' => $data['residential_address'] ?? null,
            'city'             => $data['city'] ?? null,
            'state_province'   => $data['state_province'] ?? null,
            'postal_code'      => $data['postal_code'] ?? null,
            'country'          => $data['country'] ?? null,
            'emergency_name'   => $data['emergency_name'] ?? null,
            'emergency_phone'  => $data['emergency_phone'] ?? null,
            'emergency_relationship' => $data['emergency_relationship'] ?? null,
            'emergency_address' => $data['emergency_address'] ?? null,
            'employee_id'      => $employeeId,
            'job_title'        => $data['job_title'] ?? null,
            'department'       => $data['department'] ?? null,
            'employment_type'  => $data['employment_type'] ?? 'full_time',
            'hire_date'        => $data['hire_date'] ?? now(),
            'probation_end'    => $data['probation_end'] ?? null,
            'work_location'    => $data['work_location'] ?? null,
            'reporting_to'     => $data['reporting_to'] ?? null,
            'salary_grade'     => $data['salary_grade'] ?? null,
            'base_salary'      => $data['base_salary'] ?? null,
            'pay_frequency'    => $data['pay_frequency'] ?? 'monthly',
            'bank_name'        => $data['bank_name'] ?? null,
            'account_name'     => $data['account_name'] ?? null,
            'account_number'   => $data['account_number'] ?? null,
            'sort_code'        => $data['sort_code'] ?? null,
            'tax_id'           => $data['tax_id'] ?? null,
            'cv_path'          => $cvPath,
            'id_document_path' => $idDocPath,
            'proof_of_address_path' => $proofPath,
            'photo_path'       => $photoPath,
            'onboarding_status' => 'complete',
            'notes'            => $data['notes'] ?? null,
        ]);

        if ($photoPath) {
            $user->update(['avatar_url' => '/storage/' . $photoPath]);
        }

        return redirect()->route('orchestrator.hr')->with('success', "Staff account created for {$user->name} (ID: {$employeeId}). Role: {$role->label}. Temp password: {$tempPassword}");
    }

    /**
     * Update a staff member's role.
     */
    public function updateRole(Request $request, $id)
    {
        $request->validate(['role' => 'required|string|exists:roles,name']);
        $user = User::findOrFail($id);
        $role = Role::where('name', $request->role)->first();
        if ($role) {
            $user->roles()->sync([$role->id]);
        }
        return back()->with('success', "Role updated to '{$role->label}' for {$user->name}.");
    }

    /**
     * Deactivate a staff account.
     */
    public function deactivate($id)
    {
        $user = User::findOrFail($id);
        if ($user->email === config('orchestrator.super_admin_email')) {
            return back()->with('error', 'Cannot deactivate the primary Super Admin account.');
        }
        $user->update(['is_active' => false]);
        return back()->with('success', "{$user->name}'s account has been deactivated.");
    }

    /**
     * Reactivate a staff account.
     */
    public function activate($id)
    {
        $user = User::findOrFail($id);
        $user->update(['is_active' => true]);
        return back()->with('success', "{$user->name}'s account has been reactivated.");
    }

    /**
     * Force-reset a staff member's password.
     */
    public function resetPassword($id)
    {
        $user = User::findOrFail($id);
        $tempPassword = Str::random(12);
        $user->update(['password' => Hash::make($tempPassword)]);
        return back()->with('success', "Password reset for {$user->name}. New temporary password: {$tempPassword}");
    }
}
