@extends('admin.layout')
@section('title', 'Global Entity Node Matrix')
@section('content')

<div class="mb-8 flex items-center justify-between">
    <div>
        <h2 class="text-2xl font-black text-brand tracking-tight">User Matrix Control</h2>
        <p class="text-brand-muted font-medium mt-1">Personnel oversight of the WADEXPRO distributed human node network.</p>
    </div>
    <button class="px-6 py-3 bg-brand text-white font-bold rounded-lg hover:bg-brand-light transition flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
        Provision New Entity
    </button>
</div>

<!-- Stats Bar -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-white p-6 rounded-lg border border-gray-100 shadow-sm flex items-center gap-4">
        <div class="w-12 h-12 bg-accent/10 rounded-lg flex items-center justify-center text-accent">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
        </div>
        <div>
            <p class="text-[10px] font-black text-brand-muted uppercase tracking-widest">Total Nodes</p>
            <p class="text-2xl font-black text-brand tracking-tight">1,248</p>
        </div>
    </div>
    <div class="bg-white p-6 rounded-lg border border-gray-100 shadow-sm flex items-center gap-4">
        <div class="w-12 h-12 bg-green-50 rounded-lg flex items-center justify-center text-green-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
        </div>
        <div>
            <p class="text-[10px] font-black text-brand-muted uppercase tracking-widest">Active Sessions</p>
            <p class="text-2xl font-black text-brand tracking-tight">842</p>
        </div>
    </div>
    <div class="bg-white p-6 rounded-lg border border-gray-100 shadow-sm flex items-center gap-4">
        <div class="w-12 h-12 bg-brand/5 rounded-lg flex items-center justify-center text-brand">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
        </div>
        <div>
            <p class="text-[10px] font-black text-brand-muted uppercase tracking-widest">Revoked Access</p>
            <p class="text-2xl font-black text-brand tracking-tight">14</p>
        </div>
    </div>
</div>

<!-- Table Area -->
<div class="bg-white rounded-lg border border-gray-100 shadow-sm overflow-hidden">
    <div class="p-6 border-b border-gray-50 flex items-center justify-between bg-surface/30">
        <div class="flex items-center gap-4">
            <select class="bg-white border border-gray-100 rounded-lg px-4 py-2 text-sm font-bold outline-none focus:ring-2 focus:ring-accent/20">
                <option>All User Types</option>
                <option>Super Admin</option>
                <option>Driver</option>
                <option>Customer</option>
            </select>
            <select class="bg-white border border-gray-100 rounded-lg px-4 py-2 text-sm font-bold outline-none focus:ring-2 focus:ring-accent/20">
                <option>Active Status</option>
                <option>Deactivated</option>
            </select>
        </div>
        <p class="text-xs font-bold text-brand-muted">Showing 50 of 1,248 entries</p>
    </div>
    
    <table class="w-full text-left">
        <thead>
            <tr class="bg-surface/10 border-b border-gray-50">
                <th class="px-6 py-4 text-[11px] font-black text-brand-muted uppercase tracking-widest">Identity / Node</th>
                <th class="px-6 py-4 text-[11px] font-black text-brand-muted uppercase tracking-widest">Type</th>
                <th class="px-6 py-4 text-[11px] font-black text-brand-muted uppercase tracking-widest">Clearance</th>
                <th class="px-6 py-4 text-[11px] font-black text-brand-muted uppercase tracking-widest">State</th>
                <th class="px-6 py-4 text-[11px] font-black text-brand-muted uppercase tracking-widest text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            <!-- Mock Row 1 -->
            <tr class="hover:bg-surface/30 transition-colors group">
                <td class="px-6 py-5">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-brand text-accent rounded-lg flex items-center justify-center font-black text-sm">SA</div>
                        <div>
                            <p class="text-sm font-bold text-brand">Super Admin</p>
                            <p class="text-xs text-brand-muted mt-0.5">admin@wadexpro.com</p>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-5 uppercase text-[10px] font-black tracking-widest text-brand-muted">Executive</td>
                <td class="px-6 py-5">
                    <span class="px-2 py-1 bg-brand text-accent text-[10px] font-black rounded-lg uppercase">Root Level 5</span>
                </td>
                <td class="px-6 py-5">
                    <div class="flex items-center gap-2">
                        <div class="w-2 h-2 bg-green-500 rounded-full shadow-[0_0_8px_rgba(34,197,94,0.4)]"></div>
                        <span class="text-xs font-bold text-brand uppercase tracking-tighter">Connected</span>
                    </div>
                </td>
                <td class="px-6 py-5 text-right">
                    <button class="p-2 text-gray-300 hover:text-brand transition"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h.01M12 12h.01M19 12h.01M6 12a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0z"/></svg></button>
                </td>
            </tr>
            <!-- Mock Row 2 -->
            <tr class="hover:bg-surface/30 transition-colors group">
                <td class="px-6 py-5">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-surface text-brand rounded-lg flex items-center justify-center font-black text-sm uppercase">JK</div>
                        <div>
                            <p class="text-sm font-bold text-brand">John Kwesi</p>
                            <p class="text-xs text-brand-muted mt-0.5">j.kwesi@logistics.node</p>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-5 uppercase text-[10px] font-black tracking-widest text-brand-muted">Driver</td>
                <td class="px-6 py-5">
                    <span class="px-2 py-1 bg-surface text-brand text-[10px] font-black rounded-lg uppercase">Level 1 (KYC)</span>
                </td>
                <td class="px-6 py-5">
                    <div class="flex items-center gap-2">
                        <div class="w-2 h-2 bg-gray-300 rounded-full"></div>
                        <span class="text-xs font-bold text-brand-muted uppercase tracking-tighter">Inactive</span>
                    </div>
                </td>
                <td class="px-6 py-5 text-right">
                    <button class="p-2 text-gray-300 hover:text-brand transition"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h.01M12 12h.01M19 12h.01M6 12a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0z"/></svg></button>
                </td>
            </tr>
        </tbody>
    </table>
    
    <div class="p-6 bg-surface/10 border-t border-gray-50 flex items-center justify-between">
        <button class="text-sm font-bold text-brand-muted hover:text-brand flex items-center gap-2 transition"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg> Previous Batch</button>
        <div class="flex items-center gap-3">
            <button class="w-10 h-10 rounded-lg bg-brand text-accent font-black text-sm">1</button>
            <button class="w-10 h-10 rounded-lg hover:bg-surface font-bold text-sm">2</button>
            <button class="w-10 h-10 rounded-lg hover:bg-surface font-bold text-sm">3</button>
            <span class="text-gray-300">...</span>
            <button class="w-10 h-10 rounded-lg hover:bg-surface font-bold text-sm">25</button>
        </div>
        <button class="text-sm font-bold text-brand-muted hover:text-brand flex items-center gap-2 transition">Next Batch <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></button>
    </div>
</div>

@endsection
