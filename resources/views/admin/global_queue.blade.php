@extends('admin.layout')
@section('title', 'Global Queue')
@section('content')

<div class="mb-8 flex items-center justify-between">
    <div>
        <h2 class="text-2xl font-black text-brand tracking-tight">Global Queue</h2>
        <p class="text-brand-muted font-medium mt-1">Real-time oversight of all platform transactions, orders, and fulfillment pipelines.</p>
    </div>
    <div class="flex gap-4">
        <button class="px-6 py-3 bg-surface text-brand font-bold rounded-lg border border-gray-100 hover:bg-gray-100 transition flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
            Advanced Filters
        </button>
        <button class="px-6 py-3 bg-brand text-white font-bold rounded-lg hover:bg-brand-light transition flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
            Manual Dispatch
        </button>
    </div>
</div>

<!-- Stats Bar -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <div class="bg-white p-6 rounded-lg border border-gray-100 shadow-sm relative overflow-hidden group">
        <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-accent/5 rounded-full group-hover:scale-150 transition-transform duration-500"></div>
        <p class="text-[10px] font-black text-brand-muted uppercase tracking-widest relative z-10">Active Pipeline</p>
        <p class="text-3xl font-black text-brand tracking-tight mt-2 relative z-10">4,291</p>
    </div>
    <div class="bg-white p-6 rounded-lg border border-gray-100 shadow-sm relative overflow-hidden group">
        <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-blue-500/5 rounded-full group-hover:scale-150 transition-transform duration-500"></div>
        <p class="text-[10px] font-black text-brand-muted uppercase tracking-widest relative z-10">Awaiting Node</p>
        <p class="text-3xl font-black text-blue-600 tracking-tight mt-2 relative z-10">184</p>
    </div>
    <div class="bg-white p-6 rounded-lg border border-gray-100 shadow-sm relative overflow-hidden group">
        <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-green-500/5 rounded-full group-hover:scale-150 transition-transform duration-500"></div>
        <p class="text-[10px] font-black text-brand-muted uppercase tracking-widest relative z-10">In Progress</p>
        <p class="text-3xl font-black text-green-600 tracking-tight mt-2 relative z-10">4,012</p>
    </div>
    <div class="bg-white p-6 rounded-lg border border-gray-100 shadow-sm relative overflow-hidden group">
        <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-red-500/5 rounded-full group-hover:scale-150 transition-transform duration-500"></div>
        <p class="text-[10px] font-black text-brand-muted uppercase tracking-widest relative z-10">Anomalies/Delayed</p>
        <p class="text-3xl font-black text-red-500 tracking-tight mt-2 relative z-10">95</p>
    </div>
</div>

<!-- Queue List -->
<div class="bg-white rounded-lg border border-gray-100 shadow-sm overflow-hidden">
    <div class="p-6 border-b border-gray-50 flex items-center justify-between bg-surface/30">
        <div class="flex items-center gap-6">
            <h3 class="text-sm font-black text-brand uppercase tracking-widest">Live Transaction Stream</h3>
            <div class="flex gap-2">
                <span class="w-2.5 h-2.5 bg-green-500 rounded-full animate-pulse shadow-[0_0_8px_rgba(34,197,94,0.4)] mt-0.5"></span>
                <span class="text-[10px] font-bold text-green-600 uppercase tracking-widest">Live</span>
            </div>
        </div>
        <div class="flex items-center gap-4">
            <div class="relative">
                <input type="text" placeholder="Search Order ID, Client..." class="bg-white border border-gray-100 rounded-lg pl-10 pr-4 py-2 text-sm font-medium outline-none focus:ring-2 focus:ring-accent/20 w-72">
                <svg class="w-4 h-4 text-gray-400 absolute left-4 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
        </div>
    </div>
    
    <table class="w-full text-left">
        <thead>
            <tr class="bg-surface/10 border-b border-gray-50">
                <th class="px-6 py-4 text-[11px] font-black text-brand-muted uppercase tracking-widest">Order ID</th>
                <th class="px-6 py-4 text-[11px] font-black text-brand-muted uppercase tracking-widest">Type</th>
                <th class="px-6 py-4 text-[11px] font-black text-brand-muted uppercase tracking-widest">Routing Nodes (From → To)</th>
                <th class="px-6 py-4 text-[11px] font-black text-brand-muted uppercase tracking-widest">Financials</th>
                <th class="px-6 py-4 text-[11px] font-black text-brand-muted uppercase tracking-widest">State</th>
                <th class="px-6 py-4 text-[11px] font-black text-brand-muted uppercase tracking-widest text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            <!-- Order 1 -->
            <tr class="hover:bg-surface/30 transition-colors group cursor-pointer">
                <td class="px-6 py-5">
                    <p class="text-sm font-black text-brand">#ORD-A91X</p>
                    <p class="text-[10px] text-brand-muted mt-0.5 font-mono uppercase">2 mins ago</p>
                </td>
                <td class="px-6 py-5">
                    <span class="px-2 py-1 bg-surface border border-gray-100 text-brand text-[10px] font-black rounded uppercase tracking-wider">Ride (Standard)</span>
                </td>
                <td class="px-6 py-5">
                    <div class="flex flex-col gap-1">
                        <div class="flex items-center gap-2">
                            <div class="w-1.5 h-1.5 rounded-full bg-accent"></div>
                            <p class="text-xs font-bold text-brand w-32 truncate">Kotoka Airport T3</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-1.5 h-1.5 rounded-full border border-brand"></div>
                            <p class="text-xs font-medium text-brand-muted w-32 truncate">Kempinski Hotel</p>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-5">
                    <p class="text-sm font-black text-green-600">GH₵ 124.50</p>
                    <p class="text-[10px] text-brand-muted mt-0.5 uppercase tracking-widest">Card (Paid)</p>
                </td>
                <td class="px-6 py-5">
                    <div class="flex items-center gap-2 text-green-500">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        <span class="text-[10px] font-black uppercase tracking-tighter">In Transit</span>
                    </div>
                </td>
                <td class="px-6 py-5 text-right">
                    <button class="p-2 text-gray-300 hover:text-brand transition"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></button>
                </td>
            </tr>
            <!-- Order 2 -->
            <tr class="hover:bg-surface/30 transition-colors group cursor-pointer bg-red-50/30">
                <td class="px-6 py-5">
                    <p class="text-sm font-black text-brand">#ORD-B221</p>
                    <p class="text-[10px] text-brand-muted mt-0.5 font-mono uppercase">14 mins ago</p>
                </td>
                <td class="px-6 py-5">
                    <span class="px-2 py-1 bg-surface border border-gray-100 text-brand text-[10px] font-black rounded uppercase tracking-wider">Heavy Cargo</span>
                </td>
                <td class="px-6 py-5">
                    <div class="flex flex-col gap-1">
                        <div class="flex items-center gap-2">
                            <div class="w-1.5 h-1.5 rounded-full bg-accent"></div>
                            <p class="text-xs font-bold text-brand w-32 truncate">Tema Port Zone 4</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-1.5 h-1.5 rounded-full border border-brand"></div>
                            <p class="text-xs font-medium text-brand-muted w-32 truncate">Osu Central Mall</p>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-5">
                    <p class="text-sm font-black text-brand">GH₵ 850.00</p>
                    <p class="text-[10px] text-brand-muted mt-0.5 uppercase tracking-widest">Cash (Pending)</p>
                </td>
                <td class="px-6 py-5">
                    <div class="flex items-center gap-2 text-red-500">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span class="text-[10px] font-black uppercase tracking-tighter animate-pulse">Node Unresponsive</span>
                    </div>
                </td>
                <td class="px-6 py-5 text-right">
                    <button class="px-3 py-1 bg-red-100 text-red-600 text-[10px] font-black uppercase rounded hover:bg-red-200 transition tracking-widest">Intervene</button>
                </td>
            </tr>
            <!-- Order 3 -->
            <tr class="hover:bg-surface/30 transition-colors group cursor-pointer">
                <td class="px-6 py-5">
                    <p class="text-sm font-black text-brand">#ORD-C774</p>
                    <p class="text-[10px] text-brand-muted mt-0.5 font-mono uppercase">Just now</p>
                </td>
                <td class="px-6 py-5">
                    <span class="px-2 py-1 bg-surface border border-gray-100 text-brand text-[10px] font-black rounded uppercase tracking-wider">Courier</span>
                </td>
                <td class="px-6 py-5">
                    <div class="flex flex-col gap-1">
                        <div class="flex items-center gap-2">
                            <div class="w-1.5 h-1.5 rounded-full bg-accent"></div>
                            <p class="text-xs font-bold text-brand w-32 truncate">East Legon Ave</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-1.5 h-1.5 rounded-full border border-brand"></div>
                            <p class="text-xs font-medium text-brand-muted w-32 truncate">Cantonments City</p>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-5">
                    <p class="text-sm font-black text-green-600">GH₵ 45.00</p>
                    <p class="text-[10px] text-brand-muted mt-0.5 uppercase tracking-widest">Wallet (Paid)</p>
                </td>
                <td class="px-6 py-5">
                    <div class="flex items-center gap-2 text-blue-500">
                        <div class="flex gap-0.5">
                            <div class="w-1 h-1 bg-blue-500 rounded-full animate-bounce"></div>
                            <div class="w-1 h-1 bg-blue-500 rounded-full animate-bounce" style="animation-delay: 100ms"></div>
                            <div class="w-1 h-1 bg-blue-500 rounded-full animate-bounce" style="animation-delay: 200ms"></div>
                        </div>
                        <span class="text-[10px] font-black uppercase tracking-tighter">Locating Node</span>
                    </div>
                </td>
                <td class="px-6 py-5 text-right">
                    <button class="p-2 text-gray-300 hover:text-brand transition"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></button>
                </td>
            </tr>
        </tbody>
    </table>
    
    <div class="p-4 bg-surface/10 border-t border-gray-50 flex items-center justify-center">
        <button class="text-[10px] font-black text-brand-muted uppercase tracking-widest hover:text-brand transition flex items-center gap-2">
            Load More Transactions
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
        </button>
    </div>
</div>

@endsection
