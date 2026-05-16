@extends('admin.layout')
@section('title', 'HR & Staff Registry')
@section('content')

@if(session('error'))
<div class="mb-4 p-3.5 bg-red-50 border border-red-200 rounded-lg flex items-center gap-2.5">
    <svg class="w-4 h-4 text-red-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    <p class="text-sm font-medium text-red-700">{{ session('success') }}</p>
</div>
@endif
@if(session('success'))
<div class="mb-4 p-3.5 bg-green-50 border border-green-200 rounded-lg flex items-center gap-2.5">
    <svg class="w-4 h-4 text-green-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    <p class="text-sm font-medium text-green-700">{{ session('success') }}</p>
</div>
@endif

<div x-data="hrManager()" class="max-w-6xl mx-auto">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-black text-brand tracking-tight">Staff & Roles</h2>
            <p class="text-sm text-brand-muted font-medium mt-0.5">Manage system administrators, support agents, and access controls.</p>
        </div>
        <button @click="onboardStep = 1; showOnboard = true" class="px-5 py-2.5 bg-brand text-white rounded-lg text-xs font-bold hover:bg-brand-light transition-colors flex items-center gap-2 shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
            Onboard Staff
        </button>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="bg-white border border-gray-100 rounded-xl p-4 flex items-center gap-3.5">
            <div class="w-10 h-10 bg-brand/5 rounded-lg flex items-center justify-center text-brand shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
            <div>
                <p class="text-lg font-black text-brand">{{ $stats['total'] ?? 0 }}</p>
                <p class="text-[10px] font-bold text-brand-muted uppercase tracking-wider">Total Staff</p>
            </div>
        </div>
        <div class="bg-white border border-gray-100 rounded-xl p-4 flex items-center gap-3.5">
            <div class="w-10 h-10 bg-red-50 rounded-lg flex items-center justify-center text-red-600 shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
            </div>
            <div>
                <p class="text-lg font-black text-brand">{{ $stats['admins'] ?? 0 }}</p>
                <p class="text-[10px] font-bold text-brand-muted uppercase tracking-wider">Admins</p>
            </div>
        </div>
        <div class="bg-white border border-gray-100 rounded-xl p-4 flex items-center gap-3.5">
            <div class="w-10 h-10 bg-blue-50 rounded-lg flex items-center justify-center text-blue-600 shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <p class="text-lg font-black text-brand">{{ $stats['support'] ?? 0 }}</p>
                <p class="text-[10px] font-bold text-brand-muted uppercase tracking-wider">Support</p>
            </div>
        </div>
    </div>

    {{-- Staff Table --}}
    <div class="bg-white border border-gray-100 rounded-xl overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-surface/20">
            <form action="{{ route('orchestrator.hr') }}" method="GET" class="flex items-center gap-3 flex-1">
                <div class="relative flex-1 max-w-xs">
                    <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name or email..." class="w-full bg-white border border-gray-200 rounded-lg pl-9 pr-3 py-2 text-sm outline-none focus:ring-2 focus:ring-accent/20 transition-shadow">
                </div>
                <select name="role" onchange="this.form.submit()" class="bg-white border border-gray-200 rounded-lg px-3 py-2 text-xs font-bold outline-none focus:ring-2 focus:ring-accent/20">
                    <option value="">All Roles</option>
                    <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admins</option>
                    <option value="support" {{ request('role') == 'support' ? 'selected' : '' }}>Support</option>
                    <option value="manager" {{ request('role') == 'manager' ? 'selected' : '' }}>Managers</option>
                </select>
                @if(request('search'))<input type="hidden" name="search" value="{{ request('search') }}">@endif
                @if(request('search') || request('role'))
                <a href="{{ route('orchestrator.hr') }}" class="text-xs font-bold text-brand-muted hover:text-brand shrink-0">Clear</a>
                @endif
            </form>
        </div>

        <div class="divide-y divide-gray-50">
            @forelse($staff as $member)
            @php $isSuper = $member->email === config('orchestrator.super_admin_email'); @endphp
            <div class="px-5 py-4 hover:bg-surface/20 transition-colors {{ $member->status === 'inactive' || !$member->is_active ? 'opacity-50' : '' }}">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-lg flex items-center justify-center font-bold text-xs shrink-0 {{ $member->user_type === 'admin' ? 'bg-brand text-accent' : 'bg-surface text-brand border border-gray-100' }}">
                        {{ strtoupper(substr($member->name, 0, 2)) }}
                    </div>
                    <div class="flex-1 min-w-0 grid grid-cols-1 lg:grid-cols-4 gap-2 lg:gap-4">
                        <div>
                            <p class="text-sm font-bold text-brand truncate flex items-center gap-1.5">
                                {{ $member->name }}
                                @if($member->user_type === 'admin')
                                <svg class="w-3 h-3 text-accent shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                @endif
                            </p>
                            <p class="text-[10px] text-brand-muted font-mono">{{ $member->email }}</p>
                        </div>
                        <div>
                            <span class="px-2 py-0.5 text-[9px] font-bold rounded {{ $member->user_type === 'admin' ? 'bg-brand text-accent' : 'bg-surface border border-gray-100 text-brand' }} uppercase">
                                {{ $member->user_type === 'admin' ? 'Admin' : ucfirst($member->user_type) }}
                            </span>
                        </div>
                        <div>
                            <span class="text-xs font-bold text-brand">{{ $member->department ?? 'General' }}</span>
                        </div>
                        <div class="flex items-center justify-between lg:justify-end gap-2">
                            @if($member->is_active)
                            <span class="text-[10px] font-bold text-green-600 flex items-center gap-1"><span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span> Active</span>
                            @else
                            <span class="text-[10px] font-bold text-red-500 flex items-center gap-1"><span class="w-1.5 h-1.5 bg-red-500 rounded-full"></span> Inactive</span>
                            @endif
                            @if(!$isSuper)
                            <div class="flex items-center gap-1">
                                <button @click="openRoleModal('{{ $member->id }}', '{{ $member->name }}', '{{ $member->user_type }}')" class="px-2 py-1 text-[10px] font-bold text-accent hover:bg-accent/5 rounded-lg transition-colors">Role</button>
                                @if($member->is_active)
                                <form action="{{ route('orchestrator.hr.deactivate', $member->id) }}" method="POST" class="inline" onsubmit="return confirm('Deactivate this staff member?')">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="px-2 py-1 text-[10px] font-bold text-red-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors">Deactivate</button>
                                </form>
                                @else
                                <form action="{{ route('orchestrator.hr.activate', $member->id) }}" method="POST" class="inline">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="px-2 py-1 text-[10px] font-bold text-green-500 hover:text-green-600 hover:bg-green-50 rounded-lg transition-colors">Activate</button>
                                </form>
                                @endif
                                <form action="{{ route('orchestrator.hr.reset-password', $member->id) }}" method="POST" class="inline" onsubmit="return confirm('Generate a new temporary password for this user?')">
                                    @csrf
                                    <button type="submit" class="px-2 py-1 text-[10px] font-bold text-brand-muted hover:text-brand hover:bg-surface rounded-lg transition-colors">Reset Pwd</button>
                                </form>
                            </div>
                            @else
                            <span class="text-[10px] text-gray-400 italic">Protected</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="flex flex-col items-center justify-center py-16 text-brand-muted">
                <svg class="w-14 h-14 mb-3 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <p class="text-sm font-bold">No staff found</p>
                <p class="text-xs mt-1">Onboard your first staff member to get started.</p>
            </div>
            @endforelse
        </div>

        @if($staff->hasPages())
        <div class="px-5 py-3 border-t border-gray-100 bg-surface/20">{{ $staff->links() }}</div>
        @endif
    </div>

    {{-- Role Modal --}}
    <div x-show="showRole" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-brand/50 backdrop-blur-sm" @click="showRole = false"></div>
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-sm relative z-10" @click.outside="showRole = false">
            <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 bg-accent/10 rounded-lg flex items-center justify-center text-accent">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-brand">Change Role</h3>
                        <p class="text-xs text-brand-muted" x-text="'Update role for ' + roleMember"></p>
                    </div>
                </div>
                <button @click="showRole = false" class="w-7 h-7 bg-surface rounded-lg flex items-center justify-center text-brand-muted hover:text-brand transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form method="POST" :action="'{{ route('orchestrator.hr.role', ['id' => '__ID__']) }}'.replace('__ID__', roleId)" class="p-6 space-y-4">
                @csrf @method('PATCH')
                <div>
                    <label class="text-[10px] font-bold text-brand-muted uppercase tracking-wider mb-1.5 block">New Role</label>
                    <select name="role" required class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium outline-none focus:ring-2 focus:ring-accent/20 transition-shadow">
                        @foreach($roles as $r)
                        <option value="{{ $r->name }}" :selected="roleCurrent === '{{ $r->name }}'">{{ $r->label ?? $r->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex justify-end gap-2 pt-2 border-t border-gray-100">
                    <button type="button" @click="showRole = false" class="px-4 py-2 text-xs font-bold text-brand-muted hover:text-brand transition-colors">Cancel</button>
                    <button type="submit" class="px-5 py-2 bg-brand text-white rounded-lg text-xs font-bold hover:bg-brand-light transition-colors">Update Role</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Onboard Modal --}}
    <div x-show="showOnboard" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-brand/50 backdrop-blur-sm" @click="showOnboard = false"></div>
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-4xl relative z-10 max-h-[90vh] overflow-y-auto" @click.outside="showOnboard = false">
            <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between sticky top-0 bg-white">
                <div>
                    <h3 class="text-base font-bold text-brand">Staff Onboarding</h3>
                    <p class="text-xs text-brand-muted">Step <span x-text="onboardStep"></span> of 7</p>
                </div>
                <button @click="showOnboard = false" class="w-7 h-7 bg-surface rounded-lg flex items-center justify-center text-brand-muted hover:text-brand transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="h-1 bg-gray-100"><div class="h-full bg-accent transition-all" :style="'width:' + (onboardStep / 7 * 100) + '%'"></div></div>

            <form action="{{ route('orchestrator.hr.store') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
                @csrf
                {{-- Step 1: Personal --}}
                <div x-show="onboardStep === 1" class="space-y-4">
                    <p class="text-sm font-bold text-brand">Personal Information</p>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div><label class="text-[10px] font-bold text-brand-muted uppercase tracking-wider mb-1.5 block">First Name *</label><input type="text" name="first_name" required placeholder="First name" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium outline-none focus:ring-2 focus:ring-accent/20 transition-shadow"></div>
                        <div><label class="text-[10px] font-bold text-brand-muted uppercase tracking-wider mb-1.5 block">Middle Name</label><input type="text" name="middle_name" placeholder="Middle name" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium outline-none focus:ring-2 focus:ring-accent/20 transition-shadow"></div>
                        <div><label class="text-[10px] font-bold text-brand-muted uppercase tracking-wider mb-1.5 block">Last Name *</label><input type="text" name="last_name" required placeholder="Surname" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium outline-none focus:ring-2 focus:ring-accent/20 transition-shadow"></div>
                        <div><label class="text-[10px] font-bold text-brand-muted uppercase tracking-wider mb-1.5 block">Date of Birth</label><input type="date" name="date_of_birth" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium outline-none focus:ring-2 focus:ring-accent/20 transition-shadow"></div>
                        <div><label class="text-[10px] font-bold text-brand-muted uppercase tracking-wider mb-1.5 block">Gender</label><select name="gender" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium outline-none focus:ring-2 focus:ring-accent/20"><option value="">Select</option><option value="male">Male</option><option value="female">Female</option></select></div>
                        <div><label class="text-[10px] font-bold text-brand-muted uppercase tracking-wider mb-1.5 block">Nationality</label><input type="text" name="nationality" placeholder="e.g. Ghanaian" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium outline-none focus:ring-2 focus:ring-accent/20 transition-shadow"></div>
                    </div>
                </div>
                {{-- Step 2: Contact --}}
                <div x-show="onboardStep === 2" x-cloak class="space-y-4">
                    <p class="text-sm font-bold text-brand">Contact Details</p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div><label class="text-[10px] font-bold text-brand-muted uppercase tracking-wider mb-1.5 block">Work Email *</label><input type="email" name="email" required placeholder="name@wadexpro.com" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium outline-none focus:ring-2 focus:ring-accent/20 transition-shadow"></div>
                        <div><label class="text-[10px] font-bold text-brand-muted uppercase tracking-wider mb-1.5 block">Work Phone</label><input type="text" name="phone" placeholder="+233 xx xxx xxxx" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium outline-none focus:ring-2 focus:ring-accent/20 transition-shadow"></div>
                        <div><label class="text-[10px] font-bold text-brand-muted uppercase tracking-wider mb-1.5 block">Personal Email</label><input type="email" name="personal_email" placeholder="personal@email.com" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium outline-none focus:ring-2 focus:ring-accent/20 transition-shadow"></div>
                        <div><label class="text-[10px] font-bold text-brand-muted uppercase tracking-wider mb-1.5 block">Personal Phone</label><input type="text" name="personal_phone" placeholder="+233 xx xxx xxxx" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium outline-none focus:ring-2 focus:ring-accent/20 transition-shadow"></div>
                        <div class="md:col-span-2"><label class="text-[10px] font-bold text-brand-muted uppercase tracking-wider mb-1.5 block">Address</label><textarea name="residential_address" rows="2" placeholder="Full address" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium outline-none focus:ring-2 focus:ring-accent/20 transition-shadow resize-none"></textarea></div>
                    </div>
                </div>
                {{-- Step 3: Emergency --}}
                <div x-show="onboardStep === 3" x-cloak class="space-y-4">
                    <p class="text-sm font-bold text-brand">Emergency Contact</p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div><label class="text-[10px] font-bold text-brand-muted uppercase tracking-wider mb-1.5 block">Full Name</label><input type="text" name="emergency_name" placeholder="e.g. Jane Doe" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium outline-none focus:ring-2 focus:ring-accent/20 transition-shadow"></div>
                        <div><label class="text-[10px] font-bold text-brand-muted uppercase tracking-wider mb-1.5 block">Phone</label><input type="text" name="emergency_phone" placeholder="+233 xx xxx xxxx" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium outline-none focus:ring-2 focus:ring-accent/20 transition-shadow"></div>
                        <div><label class="text-[10px] font-bold text-brand-muted uppercase tracking-wider mb-1.5 block">Relationship</label><select name="emergency_relationship" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium outline-none focus:ring-2 focus:ring-accent/20"><option value="">Select</option><option value="spouse">Spouse</option><option value="parent">Parent</option><option value="sibling">Sibling</option><option value="friend">Friend</option></select></div>
                        <div><label class="text-[10px] font-bold text-brand-muted uppercase tracking-wider mb-1.5 block">Address</label><input type="text" name="emergency_address" placeholder="Contact address" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium outline-none focus:ring-2 focus:ring-accent/20 transition-shadow"></div>
                    </div>
                </div>
                {{-- Step 4: Employment --}}
                <div x-show="onboardStep === 4" x-cloak class="space-y-4">
                    <p class="text-sm font-bold text-brand">Employment Details</p>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div><label class="text-[10px] font-bold text-brand-muted uppercase tracking-wider mb-1.5 block">System Role *</label><select name="role" required class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium outline-none focus:ring-2 focus:ring-accent/20"><option value="">Select</option>@foreach($roles as $r)<option value="{{ $r->name }}">{{ $r->label ?? $r->name }}</option>@endforeach</select></div>
                        <div><label class="text-[10px] font-bold text-brand-muted uppercase tracking-wider mb-1.5 block">Job Title</label><input type="text" name="job_title" placeholder="e.g. Fleet Manager" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium outline-none focus:ring-2 focus:ring-accent/20 transition-shadow"></div>
                        <div><label class="text-[10px] font-bold text-brand-muted uppercase tracking-wider mb-1.5 block">Department</label><input type="text" name="department" placeholder="e.g. Operations" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium outline-none focus:ring-2 focus:ring-accent/20 transition-shadow"></div>
                        <div><label class="text-[10px] font-bold text-brand-muted uppercase tracking-wider mb-1.5 block">Employment Type</label><select name="employment_type" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium outline-none focus:ring-2 focus:ring-accent/20"><option value="full_time">Full-Time</option><option value="part_time">Part-Time</option><option value="contract">Contract</option></select></div>
                        <div><label class="text-[10px] font-bold text-brand-muted uppercase tracking-wider mb-1.5 block">Hire Date</label><input type="date" name="hire_date" value="{{ date('Y-m-d') }}" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium outline-none focus:ring-2 focus:ring-accent/20 transition-shadow"></div>
                        <div><label class="text-[10px] font-bold text-brand-muted uppercase tracking-wider mb-1.5 block">Salary Grade</label><input type="text" name="salary_grade" placeholder="e.g. Grade 5" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium outline-none focus:ring-2 focus:ring-accent/20 transition-shadow"></div>
                    </div>
                </div>
                {{-- Step 5: Banking --}}
                <div x-show="onboardStep === 5" x-cloak class="space-y-4">
                    <p class="text-sm font-bold text-brand">Banking & Payment</p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div><label class="text-[10px] font-bold text-brand-muted uppercase tracking-wider mb-1.5 block">Bank Name</label><input type="text" name="bank_name" placeholder="e.g. Access Bank" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium outline-none focus:ring-2 focus:ring-accent/20 transition-shadow"></div>
                        <div><label class="text-[10px] font-bold text-brand-muted uppercase tracking-wider mb-1.5 block">Account Name</label><input type="text" name="account_name" placeholder="e.g. John Doe" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium outline-none focus:ring-2 focus:ring-accent/20 transition-shadow"></div>
                        <div><label class="text-[10px] font-bold text-brand-muted uppercase tracking-wider mb-1.5 block">Account Number</label><input type="text" name="account_number" placeholder="0123456789" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium outline-none focus:ring-2 focus:ring-accent/20 transition-shadow"></div>
                        <div><label class="text-[10px] font-bold text-brand-muted uppercase tracking-wider mb-1.5 block">Tax ID (TIN)</label><input type="text" name="tax_id" placeholder="12345678-0001" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium outline-none focus:ring-2 focus:ring-accent/20 transition-shadow"></div>
                    </div>
                </div>
                {{-- Step 6: Documents --}}
                <div x-show="onboardStep === 6" x-cloak class="space-y-4">
                    <p class="text-sm font-bold text-brand">Documents</p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="p-4 border-2 border-dashed border-gray-200 rounded-lg text-center"><p class="text-xs font-bold text-brand mb-2">Photo</p><input type="file" name="photo" accept="image/*" class="text-xs"></div>
                        <div class="p-4 border-2 border-dashed border-gray-200 rounded-lg text-center"><p class="text-xs font-bold text-brand mb-2">CV / Resume</p><input type="file" name="cv_file" accept=".pdf,.doc,.docx" class="text-xs"></div>
                        <div class="p-4 border-2 border-dashed border-gray-200 rounded-lg text-center"><p class="text-xs font-bold text-brand mb-2">Government ID</p><input type="file" name="id_document" accept=".pdf,.jpg,.png" class="text-xs"></div>
                        <div class="p-4 border-2 border-dashed border-gray-200 rounded-lg text-center"><p class="text-xs font-bold text-brand mb-2">Proof of Address</p><input type="file" name="proof_of_address" accept=".pdf,.jpg,.png" class="text-xs"></div>
                    </div>
                </div>
                {{-- Step 7: Confirm --}}
                <div x-show="onboardStep === 7" x-cloak class="space-y-4">
                    <p class="text-sm font-bold text-brand">Review & Submit</p>
                    <div class="p-4 bg-surface rounded-lg border border-gray-100 text-xs text-brand-muted">A temporary password and Employee ID will be auto-generated upon submission.</div>
                    <div class="p-4 bg-amber-50 border border-amber-200 rounded-lg text-xs text-amber-700 font-bold">This will create a new user account with the assigned role. Verify all details are correct.</div>
                </div>

                {{-- Footer --}}
                <div class="flex justify-between pt-4 border-t border-gray-100">
                    <button type="button" x-show="onboardStep > 1" @click="onboardStep--" class="px-4 py-2 bg-surface text-brand-muted rounded-lg text-xs font-bold hover:bg-gray-100 transition-colors">Back</button>
                    <div></div>
                    <div class="flex gap-2">
                        <button type="button" @click="showOnboard = false" class="px-4 py-2 text-xs font-bold text-brand-muted hover:text-brand transition-colors">Cancel</button>
                        <button type="button" x-show="onboardStep < 7" @click="onboardStep++" class="px-5 py-2 bg-brand text-white rounded-lg text-xs font-bold hover:bg-brand-light transition-colors">Next</button>
                        <button type="submit" x-show="onboardStep === 7" class="px-5 py-2 bg-brand text-white rounded-lg text-xs font-bold hover:bg-brand-light transition-colors">Create Account</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function hrManager() {
    return {
        showOnboard: false, onboardStep: 1,
        showRole: false, roleId: '', roleMember: '', roleCurrent: '',
        openRoleModal(id, name, current) { this.roleId = id; this.roleMember = name; this.roleCurrent = current; this.showRole = true; }
    };
}
</script>
<style>[x-cloak] { display: none !important; }</style>
@endsection