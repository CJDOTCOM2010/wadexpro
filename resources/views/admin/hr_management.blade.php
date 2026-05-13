@extends('admin.layout')
@section('title', 'HR & Staff Registry')
@section('content')

<div class="mb-8 flex items-center justify-between">
    <div>
        <h2 class="text-2xl font-black text-brand tracking-tight">Staff & Roles</h2>
        <p class="text-brand-muted font-medium mt-1">Manage system administrators, support agents, and access controls.</p>
    </div>
    <div class="flex gap-4">
        <a href="{{ route('orchestrator.hr.create') }}" class="px-6 py-3 bg-brand text-white font-bold rounded-lg hover:bg-brand-light transition flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
            Onboard New Staff
        </a>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
    <div class="bg-white p-4 rounded-lg border border-gray-100 shadow-sm flex items-center justify-between">
        <div>
            <p class="text-[10px] font-black text-brand-muted uppercase tracking-widest">Total Staff</p>
            <p class="text-xl font-bold text-brand mt-0.5">{{ $stats['total'] }}</p>
        </div>
        <div class="w-10 h-10 rounded-full bg-brand/5 text-brand flex items-center justify-center">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
        </div>
    </div>
    <div class="bg-white p-4 rounded-lg border border-gray-100 shadow-sm flex items-center justify-between">
        <div>
            <p class="text-[10px] font-black text-brand-muted uppercase tracking-widest">System Admins</p>
            <p class="text-xl font-bold text-brand mt-0.5">{{ $stats['admins'] }}</p>
        </div>
        <div class="w-10 h-10 rounded-full bg-red-50 text-red-600 flex items-center justify-center">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
        </div>
    </div>
    <div class="bg-white p-4 rounded-lg border border-gray-100 shadow-sm flex items-center justify-between">
        <div>
            <p class="text-[10px] font-black text-brand-muted uppercase tracking-widest">Support Agents</p>
            <p class="text-xl font-bold text-brand mt-0.5">{{ $stats['support'] }}</p>
        </div>
        <div class="w-10 h-10 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
        </div>
    </div>
</div>

<div class="bg-white rounded-lg border border-gray-100 shadow-sm overflow-hidden">
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

<!-- Add Modal -->
<div id="add-modal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-lg font-black text-brand">Add Staff Member</h3>
            <button onclick="document.getElementById('add-modal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
        </div>
        <form action="{{ route('orchestrator.hr.store') }}" method="POST">
            @csrf
            <div class="space-y-4 mb-6">
                <div>
                    <label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-1">Full Name</label>
                    <input type="text" name="name" required class="w-full bg-surface border border-gray-200 rounded p-2 text-sm focus:ring-2 focus:ring-brand/20 outline-none">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-1">Email Address</label>
                    <input type="email" name="email" required class="w-full bg-surface border border-gray-200 rounded p-2 text-sm focus:ring-2 focus:ring-brand/20 outline-none">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-1">Role</label>
                    <select name="role" required class="w-full bg-surface border border-gray-200 rounded p-2 text-sm focus:ring-2 focus:ring-brand/20 outline-none cursor-pointer">
                        <option value="">-- Select Role --</option>
                        @foreach($roles as $role)
                        <option value="{{ $role->name }}">{{ $role->label ?? $role->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-1">Department (Optional)</label>
                    <input type="text" name="department" class="w-full bg-surface border border-gray-200 rounded p-2 text-sm focus:ring-2 focus:ring-brand/20 outline-none">
                </div>
            </div>
            <div class="flex justify-end pt-4 border-t border-gray-100">
                <button type="submit" class="px-6 py-2.5 bg-brand text-white font-bold rounded shadow-sm hover:bg-brand-light transition">Send Invitation</button>
            </div>
        </form>
    </div>
</div>

@endsection
