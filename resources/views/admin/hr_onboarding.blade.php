@extends('admin.layout')
@section('title', 'Staff Onboarding')
@section('content')
<div x-data="{ step: 1 }">
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-black text-brand tracking-tight">Staff Onboarding Form</h2>
            <p class="text-brand-muted font-medium mt-1 text-sm">Complete all sections to create a new employee account.</p>
        </div>
        <a href="{{ route('orchestrator.hr') }}" class="text-brand-muted hover:text-brand font-bold text-sm flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg> Back
        </a>
    </div>

    @if($errors->any())
    <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">
        <ul class="list-disc pl-5 font-medium">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
    @endif

    {{-- Step Indicator --}}
    <div class="flex items-center gap-2 mb-8 overflow-x-auto pb-2">
        @foreach(['Personal Info', 'Contact Details', 'Emergency Contact', 'Employment', 'Banking', 'Documents', 'Review'] as $i => $label)
        <button @click="step = {{ $i+1 }}" :class="step === {{ $i+1 }} ? 'bg-brand text-white shadow-lg' : step > {{ $i+1 }} ? 'bg-accent/10 text-accent' : 'bg-gray-50 text-brand-muted'"
            class="px-4 py-2 rounded-lg text-[10px] font-black uppercase tracking-widest whitespace-nowrap transition flex items-center gap-1.5">
            <span class="w-5 h-5 rounded-full flex items-center justify-center text-[9px] border" :class="step > {{ $i+1 }} ? 'bg-accent text-white border-accent' : step === {{ $i+1 }} ? 'bg-white text-brand border-white/30' : 'border-gray-200'">
                <template x-if="step > {{ $i+1 }}">✓</template>
                <template x-if="step <= {{ $i+1 }}">{{ $i+1 }}</template>
            </span>
            {{ $label }}
        </button>
        @endforeach
    </div>

    <form action="{{ route('orchestrator.hr.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm">

            {{-- STEP 1: Personal --}}
            <div x-show="step===1" class="p-6 lg:p-8">
                <h3 class="text-base font-black text-brand mb-6 flex items-center gap-2"><span class="w-8 h-8 bg-brand/10 text-brand rounded-lg flex items-center justify-center text-xs font-black">1</span> Personal Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    <div><label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-1.5">First Name <span class="text-red-500">*</span></label><input type="text" name="first_name" value="{{ old('first_name') }}" required class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium focus:ring-2 focus:ring-accent/20 outline-none"></div>
                    <div><label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-1.5">Middle Name</label><input type="text" name="middle_name" value="{{ old('middle_name') }}" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium focus:ring-2 focus:ring-accent/20 outline-none"></div>
                    <div><label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-1.5">Last Name <span class="text-red-500">*</span></label><input type="text" name="last_name" value="{{ old('last_name') }}" required class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium focus:ring-2 focus:ring-accent/20 outline-none"></div>
                    <div><label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-1.5">Date of Birth</label><input type="date" name="date_of_birth" value="{{ old('date_of_birth') }}" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium focus:ring-2 focus:ring-accent/20 outline-none"></div>
                    <div><label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-1.5">Gender</label><select name="gender" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium focus:ring-2 focus:ring-accent/20 outline-none"><option value="">--</option><option value="male">Male</option><option value="female">Female</option><option value="other">Other</option></select></div>
                    <div><label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-1.5">Marital Status</label><select name="marital_status" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium focus:ring-2 focus:ring-accent/20 outline-none"><option value="">--</option><option value="single">Single</option><option value="married">Married</option><option value="divorced">Divorced</option><option value="widowed">Widowed</option></select></div>
                    <div><label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-1.5">Nationality</label><input type="text" name="nationality" value="{{ old('nationality') }}" placeholder="e.g. Nigerian" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium focus:ring-2 focus:ring-accent/20 outline-none"></div>
                    <div><label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-1.5">ID Type</label><select name="id_type" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium focus:ring-2 focus:ring-accent/20 outline-none"><option value="">--</option><option value="NIN">National ID (NIN)</option><option value="Passport">International Passport</option><option value="Voters Card">Voter's Card</option><option value="Drivers License">Driver's License</option></select></div>
                    <div><label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-1.5">ID Number</label><input type="text" name="id_number" value="{{ old('id_number') }}" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium focus:ring-2 focus:ring-accent/20 outline-none"></div>
                </div>
            </div>

            {{-- STEP 2: Contact --}}
            <div x-show="step===2" x-cloak class="p-6 lg:p-8">
                <h3 class="text-base font-black text-brand mb-6 flex items-center gap-2"><span class="w-8 h-8 bg-brand/10 text-brand rounded-lg flex items-center justify-center text-xs font-black">2</span> Contact Details</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div><label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-1.5">Work Email <span class="text-red-500">*</span></label><input type="email" name="email" value="{{ old('email') }}" required class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium focus:ring-2 focus:ring-accent/20 outline-none"></div>
                    <div><label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-1.5">Work Phone</label><input type="tel" name="phone" value="{{ old('phone') }}" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium focus:ring-2 focus:ring-accent/20 outline-none"></div>
                    <div><label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-1.5">Personal Email</label><input type="email" name="personal_email" value="{{ old('personal_email') }}" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium focus:ring-2 focus:ring-accent/20 outline-none"></div>
                    <div><label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-1.5">Personal Phone</label><input type="tel" name="personal_phone" value="{{ old('personal_phone') }}" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium focus:ring-2 focus:ring-accent/20 outline-none"></div>
                    <div class="md:col-span-2"><label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-1.5">Residential Address</label><textarea name="residential_address" rows="2" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium focus:ring-2 focus:ring-accent/20 outline-none resize-none">{{ old('residential_address') }}</textarea></div>
                    <div><label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-1.5">City</label><input type="text" name="city" value="{{ old('city') }}" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium focus:ring-2 focus:ring-accent/20 outline-none"></div>
                    <div><label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-1.5">State / Province</label><input type="text" name="state_province" value="{{ old('state_province') }}" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium focus:ring-2 focus:ring-accent/20 outline-none"></div>
                    <div><label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-1.5">Postal Code</label><input type="text" name="postal_code" value="{{ old('postal_code') }}" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium focus:ring-2 focus:ring-accent/20 outline-none"></div>
                    <div><label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-1.5">Country</label><input type="text" name="country" value="{{ old('country', 'Nigeria') }}" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium focus:ring-2 focus:ring-accent/20 outline-none"></div>
                </div>
            </div>

            {{-- STEP 3: Emergency --}}
            <div x-show="step===3" x-cloak class="p-6 lg:p-8">
                <h3 class="text-base font-black text-brand mb-6 flex items-center gap-2"><span class="w-8 h-8 bg-red-50 text-red-500 rounded-lg flex items-center justify-center text-xs font-black">3</span> Emergency Contact</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div><label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-1.5">Full Name</label><input type="text" name="emergency_name" value="{{ old('emergency_name') }}" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium focus:ring-2 focus:ring-accent/20 outline-none"></div>
                    <div><label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-1.5">Phone Number</label><input type="tel" name="emergency_phone" value="{{ old('emergency_phone') }}" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium focus:ring-2 focus:ring-accent/20 outline-none"></div>
                    <div><label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-1.5">Relationship</label><select name="emergency_relationship" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium focus:ring-2 focus:ring-accent/20 outline-none"><option value="">--</option><option value="spouse">Spouse</option><option value="parent">Parent</option><option value="sibling">Sibling</option><option value="child">Child</option><option value="friend">Friend</option><option value="other">Other</option></select></div>
                    <div><label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-1.5">Address</label><input type="text" name="emergency_address" value="{{ old('emergency_address') }}" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium focus:ring-2 focus:ring-accent/20 outline-none"></div>
                </div>
            </div>

            {{-- STEP 4: Employment --}}
            <div x-show="step===4" x-cloak class="p-6 lg:p-8">
                <h3 class="text-base font-black text-brand mb-6 flex items-center gap-2"><span class="w-8 h-8 bg-accent/10 text-accent rounded-lg flex items-center justify-center text-xs font-black">4</span> Employment Details</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    <div><label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-1.5">System Role <span class="text-red-500">*</span></label><select name="role" required class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium focus:ring-2 focus:ring-accent/20 outline-none"><option value="">-- Select Role --</option>@foreach($roles as $r)<option value="{{ $r->name }}">{{ $r->label ?? $r->name }}</option>@endforeach</select></div>
                    <div><label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-1.5">Job Title</label><input type="text" name="job_title" value="{{ old('job_title') }}" placeholder="e.g. Fleet Coordinator" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium focus:ring-2 focus:ring-accent/20 outline-none"></div>
                    <div><label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-1.5">Department</label><input type="text" name="department" value="{{ old('department') }}" placeholder="e.g. Operations" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium focus:ring-2 focus:ring-accent/20 outline-none"></div>
                    <div><label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-1.5">Employment Type</label><select name="employment_type" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium focus:ring-2 focus:ring-accent/20 outline-none"><option value="full_time">Full-Time</option><option value="part_time">Part-Time</option><option value="contract">Contract</option><option value="intern">Intern</option></select></div>
                    <div><label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-1.5">Hire Date</label><input type="date" name="hire_date" value="{{ old('hire_date', date('Y-m-d')) }}" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium focus:ring-2 focus:ring-accent/20 outline-none"></div>
                    <div><label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-1.5">Probation End</label><input type="date" name="probation_end" value="{{ old('probation_end') }}" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium focus:ring-2 focus:ring-accent/20 outline-none"></div>
                    <div><label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-1.5">Work Location</label><input type="text" name="work_location" value="{{ old('work_location') }}" placeholder="e.g. Lagos HQ" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium focus:ring-2 focus:ring-accent/20 outline-none"></div>
                    <div><label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-1.5">Reports To</label><input type="text" name="reporting_to" value="{{ old('reporting_to') }}" placeholder="Manager name" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium focus:ring-2 focus:ring-accent/20 outline-none"></div>
                    <div><label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-1.5">Salary Grade</label><input type="text" name="salary_grade" value="{{ old('salary_grade') }}" placeholder="e.g. Grade 5" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium focus:ring-2 focus:ring-accent/20 outline-none"></div>
                    <div><label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-1.5">Base Salary (₦)</label><input type="number" name="base_salary" step="0.01" value="{{ old('base_salary') }}" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium focus:ring-2 focus:ring-accent/20 outline-none"></div>
                    <div><label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-1.5">Pay Frequency</label><select name="pay_frequency" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium focus:ring-2 focus:ring-accent/20 outline-none"><option value="monthly">Monthly</option><option value="bi_weekly">Bi-Weekly</option><option value="weekly">Weekly</option></select></div>
                </div>
            </div>

            {{-- STEP 5: Banking --}}
            <div x-show="step===5" x-cloak class="p-6 lg:p-8">
                <h3 class="text-base font-black text-brand mb-6 flex items-center gap-2"><span class="w-8 h-8 bg-emerald-50 text-emerald-600 rounded-lg flex items-center justify-center text-xs font-black">5</span> Banking & Payment</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div><label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-1.5">Bank Name</label><input type="text" name="bank_name" value="{{ old('bank_name') }}" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium focus:ring-2 focus:ring-accent/20 outline-none"></div>
                    <div><label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-1.5">Account Name</label><input type="text" name="account_name" value="{{ old('account_name') }}" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium focus:ring-2 focus:ring-accent/20 outline-none"></div>
                    <div><label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-1.5">Account Number</label><input type="text" name="account_number" value="{{ old('account_number') }}" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium focus:ring-2 focus:ring-accent/20 outline-none"></div>
                    <div><label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-1.5">Sort Code / Routing</label><input type="text" name="sort_code" value="{{ old('sort_code') }}" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium focus:ring-2 focus:ring-accent/20 outline-none"></div>
                    <div><label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-1.5">Tax ID (TIN)</label><input type="text" name="tax_id" value="{{ old('tax_id') }}" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium focus:ring-2 focus:ring-accent/20 outline-none"></div>
                </div>
            </div>

            {{-- STEP 6: Documents --}}
            <div x-show="step===6" x-cloak class="p-6 lg:p-8">
                <h3 class="text-base font-black text-brand mb-6 flex items-center gap-2"><span class="w-8 h-8 bg-purple-50 text-purple-600 rounded-lg flex items-center justify-center text-xs font-black">6</span> Document Uploads</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="border border-dashed border-gray-200 rounded-lg p-5 text-center hover:border-accent/50 transition">
                        <p class="text-xs font-black text-brand mb-2">📸 Passport Photo</p>
                        <input type="file" name="photo" accept="image/*" class="w-full text-xs text-brand-muted">
                        <p class="text-[9px] text-gray-400 mt-1">JPG/PNG, max 2MB</p>
                    </div>
                    <div class="border border-dashed border-gray-200 rounded-lg p-5 text-center hover:border-accent/50 transition">
                        <p class="text-xs font-black text-brand mb-2">📄 CV / Resume</p>
                        <input type="file" name="cv_file" accept=".pdf,.doc,.docx" class="w-full text-xs text-brand-muted">
                        <p class="text-[9px] text-gray-400 mt-1">PDF/DOC, max 5MB</p>
                    </div>
                    <div class="border border-dashed border-gray-200 rounded-lg p-5 text-center hover:border-accent/50 transition">
                        <p class="text-xs font-black text-brand mb-2">🪪 Government ID</p>
                        <input type="file" name="id_document" accept=".pdf,.jpg,.jpeg,.png" class="w-full text-xs text-brand-muted">
                        <p class="text-[9px] text-gray-400 mt-1">PDF/Image, max 5MB</p>
                    </div>
                    <div class="border border-dashed border-gray-200 rounded-lg p-5 text-center hover:border-accent/50 transition">
                        <p class="text-xs font-black text-brand mb-2">🏠 Proof of Address</p>
                        <input type="file" name="proof_of_address" accept=".pdf,.jpg,.jpeg,.png" class="w-full text-xs text-brand-muted">
                        <p class="text-[9px] text-gray-400 mt-1">Utility bill or bank statement, max 5MB</p>
                    </div>
                </div>
                <div class="mt-6">
                    <label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-1.5">HR Notes (Internal)</label>
                    <textarea name="notes" rows="3" placeholder="Any additional notes about this employee..." class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium focus:ring-2 focus:ring-accent/20 outline-none resize-none">{{ old('notes') }}</textarea>
                </div>
            </div>

            {{-- STEP 7: Review --}}
            <div x-show="step===7" x-cloak class="p-6 lg:p-8">
                <h3 class="text-base font-black text-brand mb-6 flex items-center gap-2"><span class="w-8 h-8 bg-green-50 text-green-600 rounded-lg flex items-center justify-center text-xs font-black">✓</span> Review & Submit</h3>
                <div class="bg-surface/50 rounded-lg p-6 border border-gray-100 mb-6">
                    <p class="text-sm text-brand-muted">Please review all sections before submitting. A <strong>temporary password</strong> will be auto-generated and displayed after account creation. The employee's <strong>Employee ID</strong> (WDX-XXXXXX) will also be assigned automatically.</p>
                </div>
                <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 text-sm text-amber-700 font-medium">
                    ⚠️ By submitting, a new user account will be created and the selected system role will be assigned. Ensure all information is accurate.
                </div>
            </div>

            {{-- Navigation --}}
            <div class="px-6 lg:px-8 py-5 border-t border-gray-100 flex items-center justify-between">
                <button type="button" x-show="step > 1" @click="step--" class="px-6 py-2.5 bg-gray-50 text-brand font-bold rounded-lg hover:bg-gray-100 transition text-sm">← Previous</button>
                <div x-show="step <= 1"></div>
                <div class="flex gap-3">
                    <button type="button" x-show="step < 7" @click="step++" class="px-6 py-2.5 bg-brand text-white font-bold rounded-lg hover:bg-brand-light transition text-sm">Next →</button>
                    <button type="submit" x-show="step === 7" class="px-8 py-2.5 bg-accent text-white font-black rounded-lg hover:bg-accent/90 transition text-sm uppercase tracking-widest shadow-lg">
                        🚀 Create Staff Account
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
