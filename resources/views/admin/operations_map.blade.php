@extends('admin.layout')
@section('title', 'Global Operations & Fleet Dynamics')
@section('content')

<div class="h-[calc(100vh-12rem)] flex gap-8">
    
    <!-- Left: Core Intelligence Map -->
    <div class="flex-1 bg-brand rounded-lg shadow-2xl relative overflow-hidden group border-4 border-white">
        <!-- Map Placeholder Styling (Gradient based) -->
        <div class="absolute inset-0 bg-[#0A0A1A] opacity-90">
            <div class="absolute inset-0 opacity-20" style="background-image: radial-gradient(#F8B803 1px, transparent 1px); background-size: 40px 40px;"></div>
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[80%] h-[80%] border border-accent/20 rounded-full animate-ping opacity-20" style="animation-duration: 4s;"></div>
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[60%] h-[60%] border border-accent/10 rounded-full"></div>
        </div>

        <!-- Floating UI Overlays -->
        <div class="absolute top-8 left-8 flex flex-col gap-4 z-10">
            <div class="bg-brand/80 backdrop-blur-xl p-6 rounded-lg border border-white/10 shadow-2xl">
                <p class="text-[10px] font-black text-accent uppercase tracking-widest mb-1">Active Assets</p>
                <div class="flex items-center gap-4">
                    <h4 class="text-3xl font-black text-white">142</h4>
                    <span class="px-2 py-1 bg-green-500/20 text-green-400 text-[10px] font-black rounded-lg">LIVE</span>
                </div>
            </div>
            <a href="{{ route('orchestrator.dispatcher') }}" class="bg-accent p-6 rounded-lg border border-white/20 shadow-2xl flex items-center gap-4 group/btn hover:scale-105 transition-transform active:scale-95">
                <div class="w-10 h-10 bg-brand text-white rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </div>
                <div>
                    <span class="text-[10px] font-black text-brand uppercase tracking-widest block mb-0.5">Tactical Hub</span>
                    <span class="text-brand font-bold text-sm">Launch Dispatcher</span>
                </div>
            </a>
            <div class="bg-brand/80 backdrop-blur-xl p-6 rounded-lg border border-white/10 shadow-2xl">
                <p class="text-[10px] font-black text-white/40 uppercase tracking-widest mb-1">Cluster Density</p>
                <h4 class="text-3xl font-black text-white">Normal</h4>
            </div>
        </div>

        <!-- Coordinate HUD -->
        <div class="absolute bottom-8 right-8 z-10">
            <div class="bg-brand/40 backdrop-blur-md px-6 py-4 rounded-lg border border-white/10 text-white/40 font-mono text-[10px] uppercase tracking-tighter">
                LAT: 40.7128° N | LONG: 74.0060° W | ALT: 12.0m
            </div>
        </div>

        <!-- Center Hub Node (Mock) -->
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 text-center group">
            <div class="relative">
                <div class="absolute inset-0 bg-accent rounded-full blur-2xl opacity-40 group-hover:opacity-80 transition-opacity"></div>
                <div class="relative w-16 h-16 bg-brand border-4 border-accent rounded-full flex items-center justify-center text-accent shadow-2xl scale-125 group-hover:scale-110 transition-transform">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                </div>
            </div>
            <p class="text-white font-black text-sm mt-8 tracking-widest uppercase">Global Orchestrator Hub</p>
            <p class="text-white/40 text-[10px] font-bold uppercase mt-1 tracking-widest">W-PRO-EU-01</p>
        </div>
    </div>

    <!-- Right: Event Telemetry Ticker -->
    <div class="w-96 bg-white rounded-lg border border-gray-100 flex flex-col overflow-hidden shadow-xl">
        <div class="p-8 border-b border-gray-50 flex items-center justify-between">
            <h3 class="text-xl font-black text-brand tracking-tight">Node Events</h3>
            <span class="w-2.5 h-2.5 bg-red-500 rounded-full animate-pulse"></span>
        </div>
        
        <div class="flex-1 overflow-y-auto p-6 space-y-8">
            <!-- Event 1 -->
            <div class="relative pl-6 border-l-2 border-gray-100 italic">
                <div class="absolute -left-[5px] top-0 w-2 h-2 bg-brand rounded-full"></div>
                <p class="text-xs font-black text-brand uppercase tracking-wider mb-2">Driver Deployment</p>
                <p class="text-sm font-medium text-brand-muted leading-relaxed">Node <span class="text-brand font-bold underline decoration-accent decoration-2">DR-9281</span> initialized in Accra Central Sector.</p>
                <p class="text-[10px] text-gray-300 font-bold uppercase mt-3">02:14:12 UTC</p>
            </div>

            <!-- Event 2 -->
            <div class="relative pl-6 border-l-2 border-accent italic">
                <div class="absolute -left-[5px] top-0 w-2 h-2 bg-accent rounded-full"></div>
                <p class="text-xs font-black text-accent uppercase tracking-wider mb-2">High Value Transaction</p>
                <p class="text-sm font-medium text-brand-muted leading-relaxed">Order <span class="text-brand font-bold underline decoration-accent decoration-2">#ORD-K7</span> value exceeding $2k threshold detected.</p>
                <p class="text-[10px] text-gray-300 font-bold uppercase mt-3">02:12:04 UTC</p>
            </div>

            <!-- Event 3 -->
            <div class="relative pl-6 border-l-2 border-gray-100 italic">
                <div class="absolute -left-[5px] top-0 w-2 h-2 bg-brand rounded-full"></div>
                <p class="text-xs font-black text-brand uppercase tracking-wider mb-2">Fleet Optimization</p>
                <p class="text-sm font-medium text-brand-muted leading-relaxed">System triggered dynamic routing update for Sector B-9.</p>
                <p class="text-[10px] text-gray-300 font-bold uppercase mt-3">02:08:45 UTC</p>
            </div>

            <!-- Event 4 (SOS Alert) -->
            <div class="relative pl-6 border-l-2 border-red-500 italic">
                <div class="absolute -left-[5px] top-0 w-2 h-2 bg-red-500 rounded-full"></div>
                <p class="text-xs font-black text-red-500 uppercase tracking-wider mb-2">SOS SIGNAL DETECTED</p>
                <p class="text-sm font-medium text-brand-muted leading-relaxed">Manual override requested by <span class="text-brand font-bold">Node AC-02</span>. Verification required.</p>
                <p class="text-[10px] text-gray-300 font-bold uppercase mt-3">01:54:30 UTC</p>
            </div>
        </div>

        <div class="p-8 bg-surface/50 border-t border-gray-50">
            <button class="w-full py-4 bg-brand text-white font-black text-xs uppercase rounded-lg hover:bg-brand-light transition tracking-widest">Expand Ticker Hub</button>
        </div>
    </div>
</div>

@endsection
