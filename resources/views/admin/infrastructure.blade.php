@extends('admin.layout')
@section('title', 'Dispatcher Infrastructure & System Core')
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

<div class="mb-12 flex items-center justify-between">
    <div>
        <h2 class="text-3xl font-black text-brand tracking-tight">Infrastructure Hub</h2>
        <p class="text-brand-muted font-medium mt-1">Direct oversight of platform modules, core architecture, and system health.</p>
    </div>
    <div class="flex items-center gap-4">
        <div class="px-6 py-3 bg-white text-brand border border-gray-100 font-bold rounded-xl flex items-center gap-3 shadow-sm">
            <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse shadow-[0_0_8px_rgba(34,197,94,0.5)]"></div>
            <span class="text-xs uppercase tracking-widest">Global Node Pulse: 99.8%</span>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
    
    <!-- Left: Module Matrix -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-xl overflow-hidden flex flex-col">
        <div class="p-8 border-b border-gray-50 flex items-center justify-between bg-surface/30">
            <div>
                <h3 class="text-xl font-black text-brand tracking-tight">Module Matrix</h3>
                <p class="text-xs text-brand-muted font-bold uppercase mt-1">Enable/Disable Platform Features</p>
            </div>
            <span class="px-3 py-1 bg-brand text-white text-[10px] font-black rounded-lg uppercase">Hot Toggles Active</span>
        </div>

        <div class="p-8 space-y-6 flex-1 overflow-y-auto">
            <!-- Logistic Module -->
            <div class="p-6 bg-surface rounded-2xl flex items-center justify-between group hover:border-accent transition-all border border-transparent hover:shadow-md">
                <div class="flex items-center gap-5">
                    <div class="w-12 h-12 bg-brand text-accent rounded-2xl flex items-center justify-center shadow-lg">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/></svg>
                    </div>
                    <div>
                        <p class="text-sm font-black text-brand">Logistics Management</p>
                        <p class="text-xs text-brand-muted font-medium mt-0.5">Fleet, Orders, and Dispatch logic.</p>
                    </div>
                </div>
                <button class="w-14 h-8 bg-brand rounded-full relative transition-colors">
                    <div class="absolute top-1 right-1 w-6 h-6 bg-accent rounded-full"></div>
                </button>
            </div>

            <!-- Financial Module -->
            <div class="p-6 bg-surface rounded-lg flex items-center justify-between">
                <div class="flex items-center gap-5">
                    <div class="w-12 h-12 bg-brand text-accent rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <p class="text-sm font-black text-brand">Treasury & Accounting</p>
                        <p class="text-xs text-brand-muted font-medium mt-0.5">Invoices, Payouts, and Ledgers.</p>
                    </div>
                </div>
                <button class="w-14 h-8 bg-brand rounded-full relative">
                    <div class="absolute top-1 right-1 w-6 h-6 bg-accent rounded-full"></div>
                </button>
            </div>

            <!-- SOS Module -->
            <div class="p-6 bg-surface rounded-lg flex items-center justify-between italic">
                <div class="flex items-center gap-5">
                    <div class="w-12 h-12 bg-red-50 text-red-600 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                    <div>
                        <p class="text-sm font-black text-red-600">Emergency Systems</p>
                        <p class="text-xs text-brand-muted font-medium mt-0.5">SOS Signals and First Response.</p>
                    </div>
                </div>
                <button class="w-14 h-8 bg-brand rounded-full relative">
                    <div class="absolute top-1 right-1 w-6 h-6 bg-accent rounded-full"></div>
                </button>
            </div>
        </div>

        <div class="p-8 border-t border-gray-50 bg-surface/10">
            <p class="text-[10px] font-black text-brand-muted uppercase tracking-[0.2em] text-center italic">Advanced Module Hardening Controlled via System Node PRO-X</p>
        </div>
    </div>

    <!-- Right: Health & Cache Center -->
    <div class="space-y-10">
        
        <!-- System Health Telemetry -->
        <div class="bg-brand rounded-lg p-10 text-white relative overflow-hidden group">
            <div class="absolute top-0 right-0 p-10 opacity-10 group-hover:rotate-12 transition-transform duration-700">
                <svg class="w-48 h-48" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2"/></svg>
            </div>
            <div class="relative z-10">
                <h3 class="text-xl font-black mb-8 border-b border-white/10 pb-4">Core Health Monitor</h3>
                <div class="grid grid-cols-2 gap-8 mb-10">
                    <div>
                        <p class="text-[10px] font-black text-white/40 uppercase tracking-widest mb-1">Database Cluster</p>
                        <div class="flex items-center gap-2">
                            <span class="text-lg font-black text-accent uppercase">Healthy</span>
                            <span class="text-[10px] font-bold text-white/30">(2ms LAT)</span>
                        </div>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-white/40 uppercase tracking-widest mb-1">In-Memory Cache</p>
                        <div class="flex items-center gap-2">
                            <span class="text-lg font-black text-accent uppercase">Active</span>
                            <span class="text-[10px] font-bold text-white/30">PULSE: OK</span>
                        </div>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-white/40 uppercase tracking-widest mb-1">Storage Array</p>
                        <div class="flex items-center gap-2">
                            <span class="text-lg font-black text-white uppercase">14.2 GB</span>
                            <span class="text-[10px] font-bold text-white/30">(Used)</span>
                        </div>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-white/40 uppercase tracking-widest mb-1">Server Load</p>
                        <div class="flex items-center gap-2">
                            <span class="text-lg font-black text-white uppercase">12.4%</span>
                            <span class="text-[10px] font-bold text-white/30">CPU PULSE</span>
                        </div>
                    </div>
                </div>
                <button class="w-full py-4 bg-white/10 hover:bg-white text-white hover:text-brand font-black text-[10px] uppercase rounded-lg transition tracking-widest">Generate Infrastructure Report</button>
            </div>
        </div>

        <!-- Tactical Cache Control center -->
        <div class="bg-white rounded-lg border border-gray-100 shadow-xl p-10">
            <h3 class="text-xl font-black text-brand tracking-tight mb-8">System Cache Commands</h3>
            
            <form action="{{ route('orchestrator.infrastructure.command') }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @csrf
                <button type="submit" name="command" value="optimize" class="p-6 bg-surface rounded-lg border border-gray-50 flex flex-col gap-2 hover:border-accent transition group text-left">
                    <span class="text-[9px] font-black text-brand-muted uppercase tracking-widest">Artisan optimize</span>
                    <span class="text-sm font-black text-brand">Aggressive Cache Clear</span>
                    <p class="text-[10px] text-brand-muted mt-2">Flushes app, routes & views.</p>
                </button>
                <button type="submit" name="command" value="config" class="p-6 bg-surface rounded-lg border border-gray-50 flex flex-col gap-2 hover:border-accent transition group text-left">
                    <span class="text-[9px] font-black text-brand-muted uppercase tracking-widest">Config:cache</span>
                    <span class="text-sm font-black text-brand">Rebuild Config Map</span>
                    <p class="text-[10px] text-brand-muted mt-2">Atomically refreshes .env.</p>
                </button>
                <button type="submit" name="command" value="route" class="p-6 bg-surface rounded-lg border border-gray-50 flex flex-col gap-2 hover:border-accent transition group text-left">
                    <span class="text-[9px] font-black text-brand-muted uppercase tracking-widest">Route:clear</span>
                    <span class="text-sm font-black text-brand">Flush Pathing Cache</span>
                    <p class="text-[10px] text-brand-muted mt-2">Zero-downtime path reset.</p>
                </button>
                <button type="submit" name="command" value="nuclear" class="p-6 bg-red-50 rounded-lg border border-red-100 flex flex-col gap-2 hover:bg-red-100 transition group items-center justify-center">
                    <span class="text-[10px] font-black text-red-600 uppercase tracking-widest italic">Nuclear Flash</span>
                    <span class="text-sm font-black text-red-600">Wipe All Node Sessions</span>
                </button>
            </form>
        </div>

    </div>
</div>

@endsection
