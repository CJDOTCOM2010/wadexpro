@extends('admin.layout')
@section('title', 'Asset Registry')
@section('content')

<div class="mb-8 flex items-center justify-between">
    <div>
        <h2 class="text-2xl font-black text-brand tracking-tight">Asset Registry</h2>
        <p class="text-brand-muted font-medium mt-1">Management and telemetry of all physical transport and delivery vehicles.</p>
    </div>
    <button class="px-6 py-3 bg-brand text-white font-bold rounded-lg hover:bg-brand-light transition flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
        Register New Asset
    </button>
</div>

<!-- Stats Bar -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-white p-6 rounded-lg border border-gray-100 shadow-sm flex items-center gap-4">
        <div class="w-12 h-12 bg-accent/10 rounded-lg flex items-center justify-center text-accent">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
        </div>
        <div>
            <p class="text-[10px] font-black text-brand-muted uppercase tracking-widest">Total Fleet Assets</p>
            <p class="text-2xl font-black text-brand tracking-tight">854</p>
        </div>
    </div>
    <div class="bg-white p-6 rounded-lg border border-gray-100 shadow-sm flex items-center gap-4">
        <div class="w-12 h-12 bg-green-50 rounded-lg flex items-center justify-center text-green-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
        </div>
        <div>
            <p class="text-[10px] font-black text-brand-muted uppercase tracking-widest">Active & Available</p>
            <p class="text-2xl font-black text-brand tracking-tight">612</p>
        </div>
    </div>
    <div class="bg-white p-6 rounded-lg border border-gray-100 shadow-sm flex items-center gap-4">
        <div class="w-12 h-12 bg-red-50 rounded-lg flex items-center justify-center text-red-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        </div>
        <div>
            <p class="text-[10px] font-black text-brand-muted uppercase tracking-widest">Maintenance Required</p>
            <p class="text-2xl font-black text-brand tracking-tight">42</p>
        </div>
    </div>
</div>

<!-- Table Area -->
<div class="bg-white rounded-lg border border-gray-100 shadow-sm overflow-hidden">
    <div class="p-6 border-b border-gray-50 flex items-center justify-between bg-surface/30">
        <div class="flex items-center gap-4">
            <select class="bg-white border border-gray-100 rounded-lg px-4 py-2 text-sm font-bold outline-none focus:ring-2 focus:ring-accent/20">
                <option>All Asset Types</option>
                <option>Motorcycle</option>
                <option>Car (Standard)</option>
                <option>Van (Cargo)</option>
                <option>Truck</option>
            </select>
            <select class="bg-white border border-gray-100 rounded-lg px-4 py-2 text-sm font-bold outline-none focus:ring-2 focus:ring-accent/20">
                <option>Operational Status</option>
                <option>Active</option>
                <option>In Maintenance</option>
                <option>Decommissioned</option>
            </select>
        </div>
        <div class="flex gap-2">
            <input type="text" placeholder="Search by Plate or ID..." class="bg-white border border-gray-100 rounded-lg px-4 py-2 text-sm font-medium outline-none focus:ring-2 focus:ring-accent/20 w-64">
        </div>
    </div>
    
    <table class="w-full text-left">
        <thead>
            <tr class="bg-surface/10 border-b border-gray-50">
                <th class="px-6 py-4 text-[11px] font-black text-brand-muted uppercase tracking-widest">Asset Details</th>
                <th class="px-6 py-4 text-[11px] font-black text-brand-muted uppercase tracking-widest">Category</th>
                <th class="px-6 py-4 text-[11px] font-black text-brand-muted uppercase tracking-widest">Assigned Node</th>
                <th class="px-6 py-4 text-[11px] font-black text-brand-muted uppercase tracking-widest">Health</th>
                <th class="px-6 py-4 text-[11px] font-black text-brand-muted uppercase tracking-widest text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            <!-- Mock Row 1 -->
            <tr class="hover:bg-surface/30 transition-colors group">
                <td class="px-6 py-5">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-brand text-accent rounded-lg flex items-center justify-center font-black text-sm uppercase">CAR</div>
                        <div>
                            <p class="text-sm font-bold text-brand">Toyota Corolla 2021</p>
                            <p class="text-xs text-brand-muted mt-0.5">Plate: GX-1244-21 • ID: AST-992</p>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-5 uppercase text-[10px] font-black tracking-widest text-brand-muted">Standard Ride</td>
                <td class="px-6 py-5">
                    <div class="flex items-center gap-2">
                        <div class="w-6 h-6 bg-surface rounded-full flex items-center justify-center text-[8px] font-black text-brand">JK</div>
                        <span class="text-xs font-bold text-brand">John Kwesi</span>
                    </div>
                </td>
                <td class="px-6 py-5">
                    <div class="flex items-center gap-2">
                        <div class="w-2 h-2 bg-green-500 rounded-full shadow-[0_0_8px_rgba(34,197,94,0.4)]"></div>
                        <span class="text-xs font-bold text-brand uppercase tracking-tighter">Optimal</span>
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
                        <div class="w-10 h-10 bg-surface text-brand rounded-lg flex items-center justify-center font-black text-sm uppercase">VAN</div>
                        <div>
                            <p class="text-sm font-bold text-brand">Ford Transit Custom</p>
                            <p class="text-xs text-brand-muted mt-0.5">Plate: GR-5912-19 • ID: AST-401</p>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-5 uppercase text-[10px] font-black tracking-widest text-brand-muted">Heavy Cargo</td>
                <td class="px-6 py-5">
                    <span class="text-xs font-bold text-brand-muted italic">Unassigned</span>
                </td>
                <td class="px-6 py-5">
                    <div class="flex items-center gap-2">
                        <div class="w-2 h-2 bg-accent rounded-full shadow-[0_0_8px_rgba(248,184,3,0.4)]"></div>
                        <span class="text-xs font-bold text-brand uppercase tracking-tighter">Due for Service</span>
                    </div>
                </td>
                <td class="px-6 py-5 text-right">
                    <button class="p-2 text-gray-300 hover:text-brand transition"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h.01M12 12h.01M19 12h.01M6 12a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0z"/></svg></button>
                </td>
            </tr>
        </tbody>
    </table>
    
    <div class="p-6 bg-surface/10 border-t border-gray-50 flex items-center justify-between">
        <button class="text-sm font-bold text-brand-muted hover:text-brand flex items-center gap-2 transition"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg> Previous</button>
        <div class="flex items-center gap-3">
            <button class="w-10 h-10 rounded-lg bg-brand text-accent font-black text-sm">1</button>
            <button class="w-10 h-10 rounded-lg hover:bg-surface font-bold text-sm">2</button>
            <span class="text-gray-300">...</span>
        </div>
        <button class="text-sm font-bold text-brand-muted hover:text-brand flex items-center gap-2 transition">Next <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></button>
    </div>
</div>

@endsection
