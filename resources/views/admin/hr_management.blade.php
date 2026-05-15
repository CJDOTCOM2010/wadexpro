@extends('admin.layout')
@section('title', 'HR & Staff Registry')
@section('content')

<!-- Error Alert -->
@if(session('error'))
<div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl flex items-center gap-3">
    <svg class="w-5 h-5 text-red-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
    <p class="text-sm font-medium text-red-700">{{ session('error') }}</p>
</div>
@endif

<!-- Success Alert -->
@if(session('success'))
<div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl flex items-center gap-3">
    <svg class="w-5 h-5 text-green-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    <p class="text-sm font-medium text-green-700">{{ session('success') }}</p>
</div>
@endif

<div class="mb-8 flex items-center justify-between">
    <div>
        <h2 class="text-2xl font-black text-brand tracking-tight">Staff & Roles</h2>
        <p class="text-brand-muted font-medium mt-1">Manage system administrators, support agents, and access controls.</p>
    </div>
    <div class="flex gap-4">
        <button onclick="document.getElementById('onboard-modal').classList.remove('hidden')" class="px-6 py-3 bg-brand text-white font-bold rounded-xl hover:bg-brand-light transition flex items-center gap-2 shadow-lg shadow-brand/20">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
            Onboard New Staff
        </button>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
    <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-center justify-between hover:shadow-md transition-all group">
        <div>
            <p class="text-[10px] font-black text-brand-muted uppercase tracking-widest">Total Staff</p>
            <p class="text-2xl font-black text-brand mt-0.5">{{ $stats['total'] ?? 0 }}</p>
        </div>
        <div class="w-12 h-12 rounded-2xl bg-brand/5 text-brand flex items-center justify-center group-hover:bg-brand group-hover:text-white transition-all">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
        </div>
    </div>
    <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-center justify-between hover:shadow-md transition-all group">
        <div>
            <p class="text-[10px] font-black text-brand-muted uppercase tracking-widest">System Admins</p>
            <p class="text-2xl font-black text-brand mt-0.5">{{ $stats['admins'] ?? 0 }}</p>
        </div>
        <div class="w-12 h-12 rounded-2xl bg-red-50 text-red-600 flex items-center justify-center group-hover:bg-red-500 group-hover:text-white transition-all">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
        </div>
    </div>
    <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-center justify-between hover:shadow-md transition-all group">
        <div>
            <p class="text-[10px] font-black text-brand-muted uppercase tracking-widest">Support Agents</p>
            <p class="text-2xl font-black text-brand mt-0.5">{{ $stats['support'] ?? 0 }}</p>
        </div>
        <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center group-hover:bg-blue-500 group-hover:text-white transition-all">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
        </div>
    </div>
</div>

<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="p-6 border-b border-gray-50 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <form action="{{ route('orchestrator.hr') }}" method="GET" class="relative w-full md:w-96 flex">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search staff by name or email..." class="w-full bg-surface border border-gray-100 rounded-l-lg pl-10 pr-4 py-2 text-sm font-medium outline-none focus:ring-2 focus:ring-brand/20 transition-all">
            <svg class="w-4 h-4 text-gray-400 absolute left-4 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <button type="submit" class="bg-brand text-white px-4 rounded-r-lg font-bold text-sm">Search</button>
        </form>
        <form action="{{ route('orchestrator.hr') }}" method="GET">
            <select name="role" onchange="this.form.submit()" class="bg-surface border border-gray-100 rounded-lg px-4 py-2 text-sm font-bold text-brand outline-none cursor-pointer">
                <option value="">All Roles</option>
                <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admins</option>
                <option value="support" {{ request('role') == 'support' ? 'selected' : '' }}>Support</option>
                <option value="manager" {{ request('role') == 'manager' ? 'selected' : '' }}>Managers</option>
            </select>
            @if(request('search')) <input type="hidden" name="search" value="{{ request('search') }}"> @endif
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-surface/30 border-b border-gray-50">
                    <th class="px-6 py-4 text-[10px] font-black text-brand-muted uppercase tracking-widest whitespace-nowrap">Staff Member</th>
                    <th class="px-6 py-4 text-[10px] font-black text-brand-muted uppercase tracking-widest whitespace-nowrap">Role</th>
                    <th class="px-6 py-4 text-[10px] font-black text-brand-muted uppercase tracking-widest whitespace-nowrap">Department</th>
                    <th class="px-6 py-4 text-[10px] font-black text-brand-muted uppercase tracking-widest whitespace-nowrap">Status</th>
                    <th class="px-6 py-4 text-[10px] font-black text-brand-muted uppercase tracking-widest text-right whitespace-nowrap">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($staff as $member)
                <tr class="hover:bg-surface/20 transition-colors group {{ $member->status == 'inactive' ? 'opacity-50' : '' }}">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-full {{ $member->user_type == 'admin' ? 'bg-brand text-white' : 'bg-surface border border-gray-200 text-brand' }} font-bold flex items-center justify-center shrink-0 shadow-sm">
                                {{ strtoupper(substr($member->name, 0, 2)) }}
                            </div>
                            <div>
                                <p class="text-sm font-bold text-brand flex items-center gap-2">
                                    {{ $member->name }}
                                    @if($member->user_type == 'admin')
                                    <svg class="w-3 h-3 text-accent" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                    @endif
                                </p>
                                <p class="text-[10px] text-brand-muted mt-0.5 font-mono">{{ $member->email }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        @if($member->user_type == 'admin')
                            <span class="px-2 py-1 bg-brand text-white text-[9px] font-black uppercase tracking-widest rounded shadow-sm">System Admin</span>
                        @elseif($member->user_type == 'support')
                            <span class="px-2 py-1 bg-surface border border-gray-200 text-brand text-[9px] font-black uppercase tracking-widest rounded">Support</span>
                        @else
                            <span class="px-2 py-1 bg-surface border border-gray-200 text-brand text-[9px] font-black uppercase tracking-widest rounded">{{ ucfirst($member->user_type) }}</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-xs font-bold text-brand">{{ $member->department ?? 'General' }}</td>
                    <td class="px-6 py-4 text-xs">
                        @if($member->status == 'active')
                            <span class="text-green-600 font-bold">Active</span>
                        @else
                            <span class="text-red-500 font-bold">Deactivated</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right">
                        @if($member->email === config('orchestrator.super_admin_email'))
                            <span class="text-[10px] text-gray-400 italic">Protected Root Account</span>
                        @else
                            <div class="flex items-center justify-end gap-3">
                                @if($member->status == 'active')
                                    <form action="{{ route('orchestrator.hr.deactivate', $member->id) }}" method="POST" class="inline" onsubmit="return confirm('Deactivate this staff member?');">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="text-red-500 font-bold text-xs hover:text-red-700 transition">Deactivate</button>
                                    </form>
                                @else
                                    <form action="{{ route('orchestrator.hr.activate', $member->id) }}" method="POST" class="inline">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="text-green-500 font-bold text-xs hover:text-green-700 transition">Activate</button>
                                    </form>
                                @endif
                                
                                <form action="{{ route('orchestrator.hr.reset-password', $member->id) }}" method="POST" class="inline" onsubmit="return confirm('Generate a new temporary password for this user?');">
                                    @csrf
                                    <button type="submit" class="text-brand font-bold text-xs hover:text-brand-light transition">Reset Pwd</button>
                                </form>
                            </div>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-gray-500">No staff members found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="p-4 border-t border-gray-50">
        {{ $staff->links() }}
    </div>
</div>

<!-- Onboard Staff Modal -->
<div id="onboard-modal" class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4" x-data="{ step: 1 }">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-5xl max-h-[92vh] overflow-y-auto">
        <div class="sticky top-0 bg-white z-10 px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <div>
                <h3 class="text-lg font-black text-brand">Staff Onboarding</h3>
                <p class="text-[10px] text-brand-muted font-bold uppercase tracking-widest">Step <span x-text="step"></span> of 7</p>
            </div>
            <button onclick="document.getElementById('onboard-modal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
        </div>
        {{-- Progress Bar --}}
        <div class="w-full bg-gray-100 h-1"><div class="bg-accent h-1 transition-all" :style="'width:' + (step/7*100) + '%'"></div></div>

        <form action="{{ route('orchestrator.hr.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="p-6">
                {{-- Step 1: Personal --}}
                <div x-show="step===1">
                    <h4 class="text-sm font-black text-brand mb-4">👤 Personal Information</h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div><label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-1">First Name *</label><input type="text" name="first_name" required placeholder="Enter first name" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-accent/20 outline-none"><p class="text-[9px] text-gray-400 mt-0.5">ℹ️ Legal first name as on ID</p></div>
                        <div><label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-1">Middle Name</label><input type="text" name="middle_name" placeholder="Enter middle name (optional)" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-accent/20 outline-none"><p class="text-[9px] text-gray-400 mt-0.5">ℹ️ Optional middle name</p></div>
                        <div><label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-1">Last Name *</label><input type="text" name="last_name" required placeholder="Enter surname" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-accent/20 outline-none"><p class="text-[9px] text-gray-400 mt-0.5">ℹ️ Legal surname as on ID</p></div>
                        <div><label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-1">Date of Birth</label><input type="date" name="date_of_birth" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-accent/20 outline-none"><p class="text-[9px] text-gray-400 mt-0.5">ℹ️ Must be 18+ years old</p></div>
                        <div><label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-1">Gender</label><select name="gender" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-accent/20 outline-none"><option value="">-- Select gender --</option><option value="male">Male</option><option value="female">Female</option><option value="other">Other</option></select><p class="text-[9px] text-gray-400 mt-0.5">ℹ️ As stated on official ID</p></div>
                        <div><label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-1">Marital Status</label><select name="marital_status" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-accent/20 outline-none"><option value="">--</option><option value="single">Single</option><option value="married">Married</option><option value="divorced">Divorced</option><option value="widowed">Widowed</option></select></div>
                        <div><label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-1">Nationality</label><input type="text" name="nationality" placeholder="e.g. Nigerian" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-accent/20 outline-none"></div>
                        <div><label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-1">ID Type</label><select name="id_type" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-accent/20 outline-none"><option value="">--</option><option value="NIN">NIN</option><option value="Passport">Passport</option><option value="Voters Card">Voter's Card</option><option value="Drivers License">Driver's License</option></select></div>
                        <div><label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-1">ID Number</label><input type="text" name="id_number" placeholder="e.g. 12345678901" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-accent/20 outline-none"><p class="text-[9px] text-gray-400 mt-0.5">ℹ️ Enter your government-issued ID number</p></div>
                    </div>
                </div>
                {{-- Step 2: Contact --}}
                <div x-show="step===2" x-cloak>
                    <h4 class="text-sm font-black text-brand mb-4">📞 Contact Details</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div><label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-1">Work Email *</label><input type="email" name="email" required placeholder="name@wadexpro.com" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-accent/20 outline-none"><p class="text-[9px] text-gray-400 mt-0.5">ℹ️ This will be used for system login</p></div>
                        <div><label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-1">Work Phone</label><input type="tel" name="phone" placeholder="+234 800 000 0000" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-accent/20 outline-none"><p class="text-[9px] text-gray-400 mt-0.5">ℹ️ Company-issued or primary work phone</p></div>
                        <div><label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-1">Personal Email</label><input type="email" name="personal_email" placeholder="personal@gmail.com" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-accent/20 outline-none"><p class="text-[9px] text-gray-400 mt-0.5">ℹ️ Alternative contact email</p></div>
                        <div><label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-1">Personal Phone</label><input type="tel" name="personal_phone" placeholder="+234 900 000 0000" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-accent/20 outline-none"><p class="text-[9px] text-gray-400 mt-0.5">ℹ️ Personal mobile number</p></div>
                        <div class="md:col-span-2"><label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-1">Residential Address</label><textarea name="residential_address" rows="2" placeholder="Enter full street address, house number, and area" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-accent/20 outline-none resize-none"></textarea><p class="text-[9px] text-gray-400 mt-0.5">ℹ️ Current home address for HR records</p></div>
                        <div><label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-1">City</label><input type="text" name="city" placeholder="e.g. Lagos" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-accent/20 outline-none"></div>
                        <div><label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-1">State / Province</label><input type="text" name="state_province" placeholder="e.g. Lagos State" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-accent/20 outline-none"></div>
                        <div><label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-1">Postal Code</label><input type="text" name="postal_code" placeholder="e.g. 100001" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-accent/20 outline-none"></div>
                        <div><label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-1">Country</label><input type="text" name="country" value="Nigeria" placeholder="e.g. Nigeria" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-accent/20 outline-none"></div>
                    </div>
                </div>
                {{-- Step 3: Emergency --}}
                <div x-show="step===3" x-cloak>
                    <h4 class="text-sm font-black text-brand mb-4">🚨 Emergency Contact</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div><label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-1">Full Name</label><input type="text" name="emergency_name" placeholder="e.g. Jane Doe" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-accent/20 outline-none"><p class="text-[9px] text-gray-400 mt-0.5">ℹ️ Person to contact in case of emergency</p></div>
                        <div><label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-1">Phone Number</label><input type="tel" name="emergency_phone" placeholder="+234 800 000 0000" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-accent/20 outline-none"><p class="text-[9px] text-gray-400 mt-0.5">ℹ️ Reachable phone number</p></div>
                        <div><label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-1">Relationship</label><select name="emergency_relationship" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-accent/20 outline-none"><option value="">-- Select relationship --</option><option value="spouse">Spouse</option><option value="parent">Parent</option><option value="sibling">Sibling</option><option value="child">Child</option><option value="friend">Friend</option><option value="other">Other</option></select><p class="text-[9px] text-gray-400 mt-0.5">ℹ️ How they are related to the employee</p></div>
                        <div><label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-1">Address</label><input type="text" name="emergency_address" placeholder="Contact person's address" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-accent/20 outline-none"><p class="text-[9px] text-gray-400 mt-0.5">ℹ️ Home address of emergency contact</p></div>
                    </div>
                </div>
                {{-- Step 4: Employment --}}
                <div x-show="step===4" x-cloak>
                    <h4 class="text-sm font-black text-brand mb-4">💼 Employment Details</h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div><label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-1">System Role *</label><select name="role" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-accent/20 outline-none"><option value="">-- Select --</option>@foreach($roles as $r)<option value="{{ $r->name }}">{{ $r->label ?? $r->name }}</option>@endforeach</select></div>
                        <div><label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-1">Job Title</label><input type="text" name="job_title" placeholder="e.g. Fleet Coordinator" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-accent/20 outline-none"></div>
                        <div><label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-1">Department</label><input type="text" name="department" placeholder="e.g. Operations" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-accent/20 outline-none"></div>
                        <div><label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-1">Employment Type</label><select name="employment_type" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-accent/20 outline-none"><option value="full_time">Full-Time</option><option value="part_time">Part-Time</option><option value="contract">Contract</option><option value="intern">Intern</option></select></div>
                        <div><label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-1">Hire Date</label><input type="date" name="hire_date" value="{{ date('Y-m-d') }}" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-accent/20 outline-none"></div>
                        <div><label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-1">Probation End</label><input type="date" name="probation_end" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-accent/20 outline-none"></div>
                        <div><label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-1">Work Location</label><input type="text" name="work_location" placeholder="e.g. Lagos HQ" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-accent/20 outline-none"></div>
                        <div><label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-1">Reports To</label><input type="text" name="reporting_to" placeholder="e.g. John Smith (Manager)" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-accent/20 outline-none"><p class="text-[9px] text-gray-400 mt-0.5">ℹ️ Direct supervisor or manager name</p></div>
                        <div><label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-1">Salary Grade</label><input type="text" name="salary_grade" placeholder="e.g. Grade 5" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-accent/20 outline-none"><p class="text-[9px] text-gray-400 mt-0.5">ℹ️ Company salary grade level</p></div>
                        <div><label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-1">Base Salary (₦)</label><input type="number" name="base_salary" step="0.01" placeholder="e.g. 250000" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-accent/20 outline-none"><p class="text-[9px] text-gray-400 mt-0.5">ℹ️ Gross monthly salary in Naira</p></div>
                        <div><label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-1">Pay Frequency</label><select name="pay_frequency" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-accent/20 outline-none"><option value="monthly">Monthly</option><option value="bi_weekly">Bi-Weekly</option><option value="weekly">Weekly</option></select><p class="text-[9px] text-gray-400 mt-0.5">ℹ️ How often employee is paid</p></div>
                    </div>
                </div>
                {{-- Step 5: Banking --}}
                <div x-show="step===5" x-cloak>
                    <h4 class="text-sm font-black text-brand mb-4">🏦 Banking & Payment</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div><label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-1">Bank Name</label><input type="text" name="bank_name" placeholder="e.g. First Bank, GTBank" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-accent/20 outline-none"><p class="text-[9px] text-gray-400 mt-0.5">ℹ️ Employee's salary bank</p></div>
                        <div><label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-1">Account Name</label><input type="text" name="account_name" placeholder="e.g. John Doe" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-accent/20 outline-none"><p class="text-[9px] text-gray-400 mt-0.5">ℹ️ Name on the bank account</p></div>
                        <div><label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-1">Account Number</label><input type="text" name="account_number" placeholder="e.g. 0123456789" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-accent/20 outline-none"><p class="text-[9px] text-gray-400 mt-0.5">ℹ️ 10-digit bank account number</p></div>
                        <div><label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-1">Sort Code / Routing</label><input type="text" name="sort_code" placeholder="e.g. 011" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-accent/20 outline-none"><p class="text-[9px] text-gray-400 mt-0.5">ℹ️ Bank sort code or routing number</p></div>
                        <div><label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-1">Tax ID (TIN)</label><input type="text" name="tax_id" placeholder="e.g. 12345678-0001" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-accent/20 outline-none"><p class="text-[9px] text-gray-400 mt-0.5">ℹ️ Tax Identification Number from FIRS</p></div>
                    </div>
                </div>
                {{-- Step 6: Documents --}}
                <div x-show="step===6" x-cloak>
                    <h4 class="text-sm font-black text-brand mb-4">📎 Document Uploads</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="border border-dashed border-gray-200 rounded-lg p-4 text-center hover:border-accent/50 transition"><p class="text-xs font-black text-brand mb-1">📸 Photo</p><input type="file" name="photo" accept="image/*" class="w-full text-xs"><p class="text-[9px] text-gray-400 mt-1">JPG/PNG, max 2MB</p></div>
                        <div class="border border-dashed border-gray-200 rounded-lg p-4 text-center hover:border-accent/50 transition"><p class="text-xs font-black text-brand mb-1">📄 CV/Resume</p><input type="file" name="cv_file" accept=".pdf,.doc,.docx" class="w-full text-xs"><p class="text-[9px] text-gray-400 mt-1">PDF/DOC, max 5MB</p></div>
                        <div class="border border-dashed border-gray-200 rounded-lg p-4 text-center hover:border-accent/50 transition"><p class="text-xs font-black text-brand mb-1">🪪 Government ID</p><input type="file" name="id_document" accept=".pdf,.jpg,.jpeg,.png" class="w-full text-xs"><p class="text-[9px] text-gray-400 mt-1">PDF/Image, max 5MB</p></div>
                        <div class="border border-dashed border-gray-200 rounded-lg p-4 text-center hover:border-accent/50 transition"><p class="text-xs font-black text-brand mb-1">🏠 Proof of Address</p><input type="file" name="proof_of_address" accept=".pdf,.jpg,.jpeg,.png" class="w-full text-xs"><p class="text-[9px] text-gray-400 mt-1">Utility bill, max 5MB</p></div>
                    </div>
                    <div class="mt-4"><label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-1">HR Notes</label><textarea name="notes" rows="2" placeholder="Any additional notes about this employee (internal use only)..." class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-accent/20 outline-none resize-none"></textarea><p class="text-[9px] text-gray-400 mt-0.5">ℹ️ Internal notes visible only to HR staff</p></div>
                </div>
                {{-- Step 7: Confirm --}}
                <div x-show="step===7" x-cloak>
                    <h4 class="text-sm font-black text-brand mb-4">✅ Review & Submit</h4>
                    <div class="bg-surface/50 rounded-lg p-4 border border-gray-100 text-sm text-brand-muted mb-4">A <strong>temporary password</strong> and <strong>Employee ID (WDX-XXXXXX)</strong> will be auto-generated upon submission.</div>
                    <div class="bg-amber-50 border border-amber-200 rounded-lg p-3 text-sm text-amber-700 font-medium">⚠️ This will create a new user account with the assigned system role. Verify all details are correct.</div>
                </div>
            </div>

            {{-- Footer Navigation --}}
            <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between">
                <button type="button" x-show="step > 1" @click="step--" class="px-5 py-2 bg-gray-50 text-brand font-bold rounded-lg hover:bg-gray-100 transition text-sm">← Back</button>
                <div x-show="step <= 1"></div>
                <div class="flex gap-2">
                    <button type="button" @click="document.getElementById('onboard-modal').classList.add('hidden')" class="px-5 py-2 text-brand-muted font-bold text-sm hover:text-brand transition">Cancel</button>
                    <button type="button" x-show="step < 7" @click="step++" class="px-5 py-2 bg-brand text-white font-bold rounded-lg hover:bg-brand-light transition text-sm">Next →</button>
                    <button type="submit" x-show="step === 7" x-cloak class="px-6 py-2 bg-accent text-white font-black rounded-lg hover:bg-accent/90 transition text-sm uppercase tracking-widest shadow-lg">🚀 Create Account</button>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection

