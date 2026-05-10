@extends('admin.layout')
@section('title', 'Platform Treasury & Financial Ledgers')
@section('content')

<div class="mb-12 flex items-center justify-between">
    <div>
        <h2 class="text-3xl font-black text-brand tracking-tight">Financial Infrastructure</h2>
        <p class="text-brand-muted font-medium mt-1">Audit global liquidity, reconcile invoices, and monitor revenue streams.</p>
    </div>
    <div class="flex items-center gap-4">
        <button class="px-6 py-3 bg-white text-brand border border-gray-100 font-bold rounded-lg hover:bg-surface transition flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
            Export Ledger
        </button>
        <button class="px-6 py-3 bg-brand text-white font-bold rounded-lg hover:bg-brand-light transition shadow-xl shadow-brand/10">
            Provision Manual Payout
        </button>
    </div>
</div>

<!-- Main Treasury Stats -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-12">
    <div class="bg-brand rounded-lg p-8 text-white relative overflow-hidden">
        <div class="absolute top-0 right-0 p-6 opacity-10">
            <svg class="w-24 h-24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <p class="text-xs font-black text-white/40 uppercase tracking-widest mb-4">Gross Revenue (L30D)</p>
        <p class="text-4xl font-black mt-2 tracking-tighter">$1.24M</p>
        <div class="mt-4 flex items-center gap-2">
            <span class="text-[10px] font-black py-1 px-2 bg-green-500 rounded-lg">+24.8%</span>
            <span class="text-[10px] font-bold text-white/30 lowercase italic">vs previous moon</span>
        </div>
    </div>
    
    <div class="bg-white border border-gray-100 rounded-lg p-8">
        <p class="text-xs font-black text-brand-muted uppercase tracking-widest mb-4">Pending Payouts</p>
        <p class="text-4xl font-black mt-2 tracking-tighter text-brand">$42,850</p>
        <div class="mt-4 flex items-center gap-2">
            <span class="text-[10px] font-black py-1 px-2 bg-accent/20 text-brand rounded-lg">12 ENTITIES</span>
        </div>
    </div>

    <div class="bg-white border border-gray-100 rounded-lg p-8">
        <p class="text-xs font-black text-brand-muted uppercase tracking-widest mb-4">Platform Profit</p>
        <p class="text-4xl font-black mt-2 tracking-tighter text-brand">$184,300</p>
        <div class="mt-4 flex items-center gap-2">
            <span class="text-[10px] font-black py-1 px-2 bg-blue-50 text-blue-600 rounded-lg">14.8% NET</span>
        </div>
    </div>

    <div class="bg-white border border-gray-100 rounded-lg p-8">
        <p class="text-xs font-black text-brand-muted uppercase tracking-widest mb-4">Suspicious Flow</p>
        <p class="text-4xl font-black mt-2 tracking-tighter text-red-600">$1,450</p>
        <div class="mt-4 flex items-center gap-2">
            <span class="text-[10px] font-black py-1 px-2 bg-red-50 text-red-600 rounded-lg">2 ALERTS</span>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Active Invoices / Ledger -->
    <div class="lg:col-span-2 bg-white rounded-lg border border-gray-100 shadow-sm overflow-hidden flex flex-col">
        <div class="p-8 border-b border-gray-50 flex items-center justify-between bg-surface/30">
            <h3 class="text-xl font-black text-brand tracking-tight">Recent Financial Ingress</h3>
            <div class="flex items-center gap-3">
                <button class="text-xs font-bold text-brand-muted hover:text-brand transition">Daily</button>
                <div class="w-[1px] h-3 bg-gray-200"></div>
                <button class="text-xs font-black text-accent">Monthly</button>
            </div>
        </div>
        
        <div class="flex-1 overflow-y-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-surface/10 border-b border-gray-50">
                        <th class="px-8 py-4 text-[10px] font-black text-brand-muted uppercase tracking-widest">Description</th>
                        <th class="px-8 py-4 text-[10px] font-black text-brand-muted uppercase tracking-widest">Node ID</th>
                        <th class="px-8 py-4 text-[10px] font-black text-brand-muted uppercase tracking-widest">Amount</th>
                        <th class="px-8 py-4 text-[10px] font-black text-brand-muted uppercase tracking-widest">State</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <tr class="hover:bg-surface/30 transition-colors">
                        <td class="px-8 py-6">
                            <p class="text-sm font-bold text-brand">Order Settlement #4928</p>
                            <p class="text-[10px] text-gray-300 font-bold uppercase mt-1">April 18, 14:12</p>
                        </td>
                        <td class="px-8 py-6 font-mono text-xs text-brand-muted">TXN-492-X</td>
                        <td class="px-8 py-6 font-black text-brand">+$142.50</td>
                        <td class="px-8 py-6">
                            <span class="px-2 py-1 bg-green-50 text-green-600 text-[10px] font-black rounded-lg uppercase">Settled</span>
                        </td>
                    </tr>
                    <tr class="hover:bg-surface/30 transition-colors">
                        <td class="px-8 py-6">
                            <p class="text-sm font-bold text-brand">Voucher Redemption</p>
                            <p class="text-[10px] text-gray-300 font-bold uppercase mt-1">April 18, 13:45</p>
                        </td>
                        <td class="px-8 py-6 font-mono text-xs text-brand-muted">TXN-102-Y</td>
                        <td class="px-8 py-6 font-black text-brand">-$12.00</td>
                        <td class="px-8 py-6">
                            <span class="px-2 py-1 bg-yellow-50 text-yellow-600 text-[10px] font-black rounded-lg uppercase">Processing</span>
                        </td>
                    </tr>
                    <tr class="hover:bg-surface/30 transition-colors">
                        <td class="px-8 py-6">
                            <p class="text-sm font-bold text-brand">Enterprise Subscription</p>
                            <p class="text-[10px] text-gray-300 font-bold uppercase mt-1">April 18, 09:12</p>
                        </td>
                        <td class="px-8 py-6 font-mono text-xs text-brand-muted">SUB-9922-A</td>
                        <td class="px-8 py-6 font-black text-brand">+$2,499.00</td>
                        <td class="px-8 py-6">
                            <span class="px-2 py-1 bg-green-50 text-green-600 text-[10px] font-black rounded-lg uppercase">Settled</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <div class="p-8 border-t border-gray-50 flex justify-center">
            <button class="text-sm font-bold text-brand-muted hover:text-brand transition">Load Extensive Audit Log</button>
        </div>
    </div>

    <!-- Payout Queue Sidebar -->
    <div class="bg-surface rounded-lg p-8 flex flex-col">
        <div class="mb-10">
            <h3 class="text-xl font-black text-brand tracking-tight">Egress Queue</h3>
            <p class="text-sm text-brand-muted font-medium mt-1">Authorized payouts awaiting network release.</p>
        </div>
        
        <div class="flex-1 space-y-6">
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100 group hover:shadow-xl transition-all">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-[11px] font-black text-brand-muted uppercase tracking-widest">Ref: #PAY-0182</span>
                    <button class="text-xs font-black text-accent hover:underline">Approve</button>
                </div>
                <p class="text-lg font-black text-brand">$12,400.00</p>
                <div class="flex items-center gap-3 mt-4">
                    <div class="w-8 h-8 rounded-lg bg-surface flex items-center justify-center font-bold text-[10px]">JK</div>
                    <p class="text-xs font-bold text-brand-muted truncate">John Kwesi Logistics</p>
                </div>
            </div>

            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100 group hover:shadow-xl transition-all">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-[11px] font-black text-brand-muted uppercase tracking-widest">Ref: #PAY-0183</span>
                    <button class="text-xs font-black text-accent hover:underline">Approve</button>
                </div>
                <p class="text-lg font-black text-brand">$2,140.00</p>
                <div class="flex items-center gap-3 mt-4">
                    <div class="w-8 h-8 rounded-lg bg-surface flex items-center justify-center font-bold text-[10px]">AD</div>
                    <p class="text-xs font-bold text-brand-muted truncate">Apex Delivery Services</p>
                </div>
            </div>
        </div>

        <div class="mt-10">
            <div class="p-6 bg-brand rounded-lg text-white">
                <p class="text-[10px] font-black text-white/40 uppercase mb-2">Security Hash</p>
                <p class="font-mono text-[10px] break-all opacity-80 uppercase leading-relaxed font-bold">SHA256: 9E12B...A3B02C...98B</p>
            </div>
        </div>
    </div>
</div>

@endsection
