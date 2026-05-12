@extends('admin.layout')
@section('title', 'HR & Staff Registry')
@section('content')

<div class="mb-8 flex items-center justify-between">
    <div>
        <h2 class="text-2xl font-black text-brand tracking-tight">Staff & Roles</h2>
        <p class="text-brand-muted font-medium mt-1">Manage system administrators, support agents, and access controls.</p>
    </div>
    <div class="flex gap-4">
        <button class="px-6 py-3 bg-brand text-white font-bold rounded-lg hover:bg-brand-light transition flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
            Add Staff Member
        </button>
    </div>
</div>

<div class="bg-white rounded-lg border border-gray-100 shadow-sm overflow-hidden">
    <div class="p-6 border-b border-gray-50">
        <div class="relative w-full md:w-96">
            <input type="text" placeholder="Search staff by name or email..." class="w-full bg-surface border border-gray-100 rounded-lg pl-10 pr-4 py-2.5 text-sm font-medium outline-none focus:ring-2 focus:ring-brand/20 transition-all">
            <svg class="w-4 h-4 text-gray-400 absolute left-4 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-surface/30 border-b border-gray-50">
                    <th class="px-6 py-4 text-[10px] font-black text-brand-muted uppercase tracking-widest whitespace-nowrap">Staff Member</th>
                    <th class="px-6 py-4 text-[10px] font-black text-brand-muted uppercase tracking-widest whitespace-nowrap">Role</th>
                    <th class="px-6 py-4 text-[10px] font-black text-brand-muted uppercase tracking-widest whitespace-nowrap">Department</th>
                    <th class="px-6 py-4 text-[10px] font-black text-brand-muted uppercase tracking-widest whitespace-nowrap">Last Login</th>
                    <th class="px-6 py-4 text-[10px] font-black text-brand-muted uppercase tracking-widest text-right whitespace-nowrap">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                <!-- Row 1 -->
                <tr class="hover:bg-surface/20 transition-colors group">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-full bg-brand text-white font-bold flex items-center justify-center shrink-0 shadow-sm">
                                SA
                            </div>
                            <div>
                                <p class="text-sm font-bold text-brand flex items-center gap-2">Super Admin <svg class="w-3 h-3 text-accent" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg></p>
                                <p class="text-[10px] text-brand-muted mt-0.5 font-mono">admin@wadexpro.com</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 bg-brand text-white text-[9px] font-black uppercase tracking-widest rounded shadow-sm">Super Admin</span>
                    </td>
                    <td class="px-6 py-4 text-xs font-bold text-brand">All Departments</td>
                    <td class="px-6 py-4 text-xs text-brand-muted">Active Now</td>
                    <td class="px-6 py-4 text-right">
                        <span class="text-[10px] text-gray-400 italic">Protected</span>
                    </td>
                </tr>
                <!-- Row 2 -->
                <tr class="hover:bg-surface/20 transition-colors group">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-700 font-bold flex items-center justify-center shrink-0 border border-blue-200">
                                MA
                            </div>
                            <div>
                                <p class="text-sm font-bold text-brand">Michael Addo</p>
                                <p class="text-[10px] text-brand-muted mt-0.5 font-mono">michael.a@wadexpro.com</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 bg-surface border border-gray-200 text-brand text-[9px] font-black uppercase tracking-widest rounded">Support Lead</span>
                    </td>
                    <td class="px-6 py-4 text-xs font-bold text-brand">Customer Support</td>
                    <td class="px-6 py-4 text-xs text-brand-muted">2 hours ago</td>
                    <td class="px-6 py-4 text-right">
                        <button class="text-accent font-bold text-xs hover:text-accent-light transition">Manage</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

@endsection
