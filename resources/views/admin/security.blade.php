@extends('admin.layout')
@section('title', 'Security Protocols & Clearance Control')
@section('content')

<div class="mb-12 flex items-center justify-between">
    <div>
        <h2 class="text-3xl font-black text-brand tracking-tight">Security Protocols</h2>
        <p class="text-brand-muted font-medium mt-1">Management of global system roles, permission matrices, and technical access audits.</p>
    </div>
    <div class="flex items-center gap-4">
        <button class="px-6 py-3 bg-brand text-white font-bold rounded-lg hover:bg-brand-light transition flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
            Create New Role
        </button>
    </div>
</div>

<!-- Key Security Metrics -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
    <div class="bg-white p-6 rounded-lg border border-gray-100 shadow-sm flex items-center gap-5">
        <div class="w-12 h-12 bg-brand/5 rounded-lg flex items-center justify-center text-brand">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
        </div>
        <div>
            <p class="text-[10px] font-black text-brand-muted uppercase tracking-widest">Active Roles</p>
            <p class="text-2xl font-black text-brand">6 Classes</p>
        </div>
    </div>
    <div class="bg-white p-6 rounded-lg border border-gray-100 shadow-sm flex items-center gap-5">
        <div class="w-12 h-12 bg-accent/10 rounded-lg flex items-center justify-center text-brand">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
        </div>
        <div>
            <p class="text-[10px] font-black text-brand-muted uppercase tracking-widest">Total Atomic Permissions</p>
            <p class="text-2xl font-black text-brand">84 Nodes</p>
        </div>
    </div>
    <div class="bg-white p-6 rounded-lg border border-gray-100 shadow-sm flex items-center gap-5">
        <div class="w-12 h-12 bg-red-50 rounded-lg flex items-center justify-center text-red-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        </div>
        <div>
            <p class="text-[10px] font-black text-brand-muted uppercase tracking-widest">Compromise Risk</p>
            <p class="text-2xl font-black text-green-500 uppercase">Negligible</p>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
    
    <!-- Permission Matrix UI -->
    <div class="bg-white rounded-lg border border-gray-100 shadow-xl overflow-hidden flex flex-col">
        <div class="p-8 border-b border-gray-50 flex items-center justify-between">
            <div>
                <h3 class="text-xl font-black text-brand tracking-tight">Permission Matrix</h3>
                <p class="text-xs text-brand-muted font-bold uppercase mt-1">Cross-Functional Access Control</p>
            </div>
            <button class="px-5 py-2.5 bg-brand text-white text-[10px] font-black rounded-lg uppercase hover:bg-brand-light transition">Update Strategy</button>
        </div>

        <div class="p-4 overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="text-[10px] font-black text-brand-muted uppercase tracking-widest">
                        <th class="p-4 border-b border-gray-50">Operational Module</th>
                        <th class="p-4 border-b border-gray-50 text-center">Super Admin</th>
                        <th class="p-4 border-b border-gray-50 text-center">Admin</th>
                        <th class="p-4 border-b border-gray-50 text-center">Driver</th>
                        <th class="p-4 border-b border-gray-50 text-center">Support</th>
                    </tr>
                </thead>
                <tbody class="text-sm font-bold text-brand">
                    <tr class="border-b border-gray-50/50 hover:bg-surface/50 transition">
                        <td class="p-4">Logistics: Dispatch Control</td>
                        <td class="p-4 text-center"><div class="w-4 h-4 bg-accent rounded-sm mx-auto flex items-center justify-center"><svg class="w-3 h-3 text-brand" fill="currentColor" viewBox="0 0 20 20"><path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/></svg></div></td>
                        <td class="p-4 text-center"><div class="w-4 h-4 bg-accent rounded-sm mx-auto flex items-center justify-center"><svg class="w-3 h-3 text-brand" fill="currentColor" viewBox="0 0 20 20"><path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/></svg></div></td>
                        <td class="p-4 text-center"><div class="w-4 h-4 border-2 border-gray-200 rounded-sm mx-auto"></div></td>
                        <td class="p-4 text-center"><div class="w-4 h-4 bg-accent/30 rounded-sm mx-auto flex items-center justify-center opacity-50"><svg class="w-3 h-3 text-brand" fill="currentColor" viewBox="0 0 20 20"><path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/></svg></div></td>
                    </tr>
                    <tr class="border-b border-gray-50/50 hover:bg-surface/50 transition">
                        <td class="p-4">Finance: Payout Authorization</td>
                        <td class="p-4 text-center"><div class="w-4 h-4 bg-accent rounded-sm mx-auto flex items-center justify-center"><svg class="w-3 h-3 text-brand" fill="currentColor" viewBox="0 0 20 20"><path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/></svg></div></td>
                        <td class="p-4 text-center"><div class="w-4 h-4 border-2 border-gray-200 rounded-sm mx-auto"></div></td>
                        <td class="p-4 text-center"><div class="w-4 h-4 border-2 border-gray-200 rounded-sm mx-auto"></div></td>
                        <td class="p-4 text-center"><div class="w-4 h-4 border-2 border-gray-200 rounded-sm mx-auto"></div></td>
                    </tr>
                    <tr class="border-b border-gray-50/50 hover:bg-surface/50 transition">
                        <td class="p-4">System: Infrastructure Access</td>
                        <td class="p-4 text-center"><div class="w-4 h-4 bg-accent rounded-sm mx-auto flex items-center justify-center"><svg class="w-3 h-3 text-brand" fill="currentColor" viewBox="0 0 20 20"><path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/></svg></div></td>
                        <td class="p-4 text-center"><div class="w-4 h-4 border-2 border-gray-200 rounded-sm mx-auto"></div></td>
                        <td class="p-4 text-center"><div class="w-4 h-4 border-2 border-gray-200 rounded-sm mx-auto"></div></td>
                        <td class="p-4 text-center"><div class="w-4 h-4 border-2 border-gray-200 rounded-sm mx-auto"></div></td>
                    </tr>
                    <tr class="border-b border-gray-50/50 hover:bg-surface/50 transition">
                        <td class="p-4">Customer: KYC Verification</td>
                        <td class="p-4 text-center"><div class="w-4 h-4 bg-accent rounded-sm mx-auto flex items-center justify-center"><svg class="w-3 h-3 text-brand" fill="currentColor" viewBox="0 0 20 20"><path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/></svg></div></td>
                        <td class="p-4 text-center"><div class="w-4 h-4 bg-accent rounded-sm mx-auto flex items-center justify-center"><svg class="w-3 h-3 text-brand" fill="currentColor" viewBox="0 0 20 20"><path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/></svg></div></td>
                        <td class="p-4 text-center"><div class="w-4 h-4 border-2 border-gray-200 rounded-sm mx-auto"></div></td>
                        <td class="p-4 text-center"><div class="w-4 h-4 bg-accent rounded-sm mx-auto flex items-center justify-center"><svg class="w-3 h-3 text-brand" fill="currentColor" viewBox="0 0 20 20"><path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/></svg></div></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="p-8 border-t border-gray-50 bg-surface/30">
            <p class="text-[10px] font-black text-brand-muted uppercase tracking-[0.2em] text-center italic leading-relaxed">Matrix integrity verified by Platform Engine Secure Handshake v4.1</p>
        </div>
    </div>

    <!-- Access Audit Trail -->
    <div class="bg-brand rounded-lg p-8 text-white relative overflow-hidden flex flex-col">
        <div class="absolute top-0 right-0 p-8 opacity-10">
            <svg class="w-48 h-48" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
        </div>
        
        <div class="relative z-10 flex flex-col h-full">
            <div class="flex items-center justify-between mb-10 pb-4 border-b border-white/10">
                <div>
                    <h3 class="text-xl font-black">Technical Access Audit</h3>
                    <p class="text-xs text-white/40 font-bold uppercase mt-1">Live Entry/Exit Telemetry</p>
                </div>
                <button class="text-xs font-black text-accent uppercase hover:underline">Full Log Report</button>
            </div>

            <div class="space-y-6 flex-1">
                <div class="flex items-start gap-4 p-4 bg-white/5 rounded-lg border border-white/5 transition hover:bg-white/10">
                    <div class="w-10 h-10 bg-accent rounded-lg flex items-center justify-center text-brand shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <p class="text-sm font-black">Successful Orchestrator Entry</p>
                            <span class="px-1.5 py-0.5 bg-green-500/20 text-green-400 text-[8px] font-black rounded uppercase">Super Admin</span>
                        </div>
                        <p class="text-xs text-white/50 mt-1">Auth via secure token. Gateway: Accra North Hub Area.</p>
                        <p class="text-[9px] text-white/30 font-bold uppercase mt-2">Just Now • IP: 197.255.122.1</p>
                    </div>
                </div>

                <div class="flex items-start gap-4 p-4 bg-white/5 rounded-lg border border-white/5 transition hover:bg-white/10">
                    <div class="w-10 h-10 bg-surface rounded-lg flex items-center justify-center text-brand shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <p class="text-sm font-black text-accent">Permission State Altered</p>
                        </div>
                        <p class="text-xs text-white/50 mt-1">Role 'Logistics Support' granted node 'dispatch.override'.</p>
                        <p class="text-[9px] text-white/30 font-bold uppercase mt-2">12 Minutes Ago • IP: 197.255.122.1</p>
                    </div>
                </div>

                <div class="flex items-start gap-4 p-4 bg-red-500/10 rounded-lg border border-red-500/20 transition group">
                    <div class="w-10 h-10 bg-red-500 rounded-lg flex items-center justify-center text-white shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <p class="text-sm font-black text-red-400">Brute Force Deflection</p>
                        </div>
                        <p class="text-xs text-red-300/60 mt-1">Multiple failed attempts detected. Origin: Lagos, Nigeria Hub (IP: 102.89.22.4).</p>
                        <p class="text-[9px] text-red-500/40 font-bold uppercase mt-2">1 Hour Ago • AUTO-LOCKED</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

@endsection
