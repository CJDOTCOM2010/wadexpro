@extends('admin.layout')
@section('title', 'Ticket Inbox')
@section('content')

<div class="mb-8 flex items-center justify-between">
    <div>
        <h2 class="text-2xl font-black text-brand tracking-tight">Support Tickets</h2>
        <p class="text-brand-muted font-medium mt-1">Resolve customer and driver issues, disputes, and inquiries.</p>
    </div>
</div>

<div class="bg-white rounded-lg border border-gray-100 shadow-sm overflow-hidden flex h-[700px]">
    
    <!-- Sidebar / Filters -->
    <div class="w-64 border-r border-gray-50 flex flex-col bg-surface/30">
        <div class="p-4 border-b border-gray-50">
            <button class="w-full py-2.5 bg-brand text-white font-bold rounded hover:bg-brand-light transition text-sm">Compose New</button>
        </div>
        <div class="flex-1 overflow-y-auto py-4">
            <div class="px-4 mb-2 text-[10px] font-black text-brand-muted uppercase tracking-widest">Queues</div>
            <a href="#" class="flex items-center justify-between px-4 py-2 bg-white border-l-2 border-brand text-brand">
                <span class="text-sm font-bold">Unassigned</span>
                <span class="bg-brand text-white text-[10px] font-black px-2 py-0.5 rounded-full">12</span>
            </a>
            <a href="#" class="flex items-center justify-between px-4 py-2 text-brand/70 hover:bg-white transition">
                <span class="text-sm font-bold">My Tickets</span>
                <span class="bg-surface border border-gray-200 text-brand-muted text-[10px] font-black px-2 py-0.5 rounded-full">4</span>
            </a>
            <a href="#" class="flex items-center justify-between px-4 py-2 text-brand/70 hover:bg-white transition">
                <span class="text-sm font-bold">All Open</span>
            </a>
            
            <div class="px-4 mt-8 mb-2 text-[10px] font-black text-brand-muted uppercase tracking-widest">Categories</div>
            <a href="#" class="flex items-center gap-2 px-4 py-2 text-brand/70 hover:bg-white transition text-sm font-medium">
                <span class="w-2 h-2 rounded-full bg-red-500"></span> Billing Disputes
            </a>
            <a href="#" class="flex items-center gap-2 px-4 py-2 text-brand/70 hover:bg-white transition text-sm font-medium">
                <span class="w-2 h-2 rounded-full bg-blue-500"></span> Driver Behavior
            </a>
            <a href="#" class="flex items-center gap-2 px-4 py-2 text-brand/70 hover:bg-white transition text-sm font-medium">
                <span class="w-2 h-2 rounded-full bg-purple-500"></span> App Issues
            </a>
        </div>
    </div>

    <!-- Ticket List -->
    <div class="flex-1 flex flex-col">
        <!-- Toolbar -->
        <div class="p-4 border-b border-gray-50 flex items-center justify-between bg-white">
            <div class="relative w-64">
                <input type="text" placeholder="Search tickets..." class="w-full bg-surface border border-gray-100 rounded pl-9 pr-3 py-1.5 text-sm font-medium outline-none focus:ring-1 focus:ring-brand/20 transition-all">
                <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
            <div class="flex items-center gap-2">
                <button class="p-1.5 text-gray-400 hover:text-brand transition"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg></button>
            </div>
        </div>

        <!-- List -->
        <div class="flex-1 overflow-y-auto bg-surface/10 divide-y divide-gray-50">
            
            <!-- Ticket Row -->
            <a href="{{ route('orchestrator.support.ticket.show', 1) }}" class="block p-4 bg-white hover:bg-surface/50 transition border-l-4 border-red-500">
                <div class="flex items-start justify-between mb-1">
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold text-brand">Customer: Sarah Jenkins</span>
                        <span class="px-2 py-0.5 rounded text-[9px] font-black uppercase tracking-widest bg-red-100 text-red-700">Urgent</span>
                    </div>
                    <span class="text-xs text-brand-muted">10m ago</span>
                </div>
                <h4 class="text-sm font-bold text-brand mb-1">Driver demanded extra cash and refused to drop me</h4>
                <p class="text-xs text-brand-muted truncate">The driver (Kwame M.) stopped halfway and said the app fare is too small. He locked the doors until I...</p>
                <div class="mt-3 flex items-center gap-4 text-[10px] font-bold uppercase tracking-widest text-brand-muted">
                    <span>#TKT-8921</span>
                    <span class="flex items-center gap-1"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/></svg> Driver Behavior</span>
                </div>
            </a>

            <!-- Ticket Row -->
            <a href="#" class="block p-4 bg-white hover:bg-surface/50 transition border-l-4 border-amber-500 opacity-80">
                <div class="flex items-start justify-between mb-1">
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold text-brand">Driver: Osei Appiah</span>
                        <span class="px-2 py-0.5 rounded text-[9px] font-black uppercase tracking-widest bg-amber-100 text-amber-700">High</span>
                    </div>
                    <span class="text-xs text-brand-muted">2h ago</span>
                </div>
                <h4 class="text-sm font-bold text-brand mb-1">Wallet payout failed to MoMo</h4>
                <p class="text-xs text-brand-muted truncate">I tried to withdraw my earnings from yesterday but the transaction failed and the money left my wallet...</p>
                <div class="mt-3 flex items-center gap-4 text-[10px] font-bold uppercase tracking-widest text-brand-muted">
                    <span>#TKT-8919</span>
                    <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-amber-500"></span> Billing Disputes</span>
                </div>
            </a>

        </div>
    </div>
</div>

@endsection
