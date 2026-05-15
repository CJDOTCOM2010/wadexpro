@extends('admin.layout')
@section('title', 'Platform Treasury & Financial Ledgers')
@section('content')

<!-- Error Alert -->
@if(session('error'))
<div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl flex items-center gap-3">
    <svg class="w-5 h-5 text-red-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
    <p class="text-sm font-medium text-red-700">{{ session('error') }}</p>
</div>
@endif

<div class="mb-12 flex items-center justify-between">
    <div>
        <h2 class="text-3xl font-black text-brand tracking-tight">Financial Infrastructure</h2>
        <p class="text-brand-muted font-medium mt-1">Audit global liquidity, reconcile invoices, and monitor revenue streams.</p>
    </div>
    <div class="flex items-center gap-4">
        <button class="px-6 py-3 bg-white text-brand border border-gray-100 font-bold rounded-xl hover:bg-surface transition flex items-center gap-2 shadow-lg">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
            Export Ledger
        </button>
    </div>
</div>

<!-- Main Treasury Stats -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-12">
    <div class="bg-brand rounded-2xl p-8 text-white relative overflow-hidden shadow-lg hover:shadow-xl transition-all">
        <div class="absolute top-0 right-0 p-6 opacity-10">
            <svg class="w-24 h-24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <p class="text-xs font-black text-white/40 uppercase tracking-widest mb-4">Gross Revenue (L30D)</p>
        <p class="text-4xl font-black mt-2 tracking-tighter">₵{{ number_format($stats['gross_revenue'] ?? 0, 2) }}</p>
        <div class="mt-4 flex items-center gap-2">
            <span class="text-[10px] font-black py-1 px-2 {{ ($stats['revenue_change'] ?? 0) >= 0 ? 'bg-green-500' : 'bg-red-500' }} rounded-lg">
                {{ ($stats['revenue_change'] ?? 0) >= 0 ? '+' : '' }}{{ $stats['revenue_change'] ?? 0 }}%
            </span>
            <span class="text-[10px] font-bold text-white/30 lowercase italic">vs previous moon</span>
        </div>
    </div>
    
    <div class="bg-white border border-gray-100 rounded-2xl p-8 shadow-sm hover:shadow-md transition-all">
        <p class="text-xs font-black text-brand-muted uppercase tracking-widest mb-4">Pending Payouts</p>
        <p class="text-4xl font-black mt-2 tracking-tighter text-brand">₵{{ number_format($stats['pending_payouts_sum'] ?? 0, 2) }}</p>
        <div class="mt-4 flex items-center gap-2">
            <span class="text-[10px] font-black py-1 px-2 bg-accent/20 text-brand rounded-lg">{{ $stats['pending_payouts_count'] ?? 0 }} ENTITIES</span>
        </div>
    </div>

    <div class="bg-white border border-gray-100 rounded-2xl p-8 shadow-sm hover:shadow-md transition-all">
        <p class="text-xs font-black text-brand-muted uppercase tracking-widest mb-4">Platform Profit</p>
        <p class="text-4xl font-black mt-2 tracking-tighter text-brand">₵{{ number_format($stats['platform_profit'] ?? 0, 2) }}</p>
        <div class="mt-4 flex items-center gap-2">
            <span class="text-[10px] font-black py-1 px-2 bg-blue-50 text-blue-600 rounded-lg">{{ $stats['profit_margin'] ?? 0 }}% NET</span>
        </div>
    </div>

    <div class="bg-white border border-gray-100 rounded-2xl p-8 shadow-sm relative overflow-hidden group hover:shadow-md transition-all">
        <div class="absolute inset-0 bg-red-500/5 pointer-events-none group-hover:bg-red-500/10 transition"></div>
        <div class="relative z-10">
            <p class="text-xs font-black text-brand-muted uppercase tracking-widest mb-4">Suspicious Flow</p>
            <p class="text-4xl font-black mt-2 tracking-tighter text-red-600">₵{{ number_format($stats['suspicious_flow_sum'], 2) }}</p>
            <div class="mt-4 flex items-center gap-2">
                <span class="text-[10px] font-black py-1 px-2 bg-red-50 text-red-600 rounded-lg">{{ $stats['suspicious_flow_count'] }} ALERTS</span>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Active Invoices / Ledger -->
    <div class="lg:col-span-2 bg-white rounded-lg border border-gray-100 shadow-xl overflow-hidden flex flex-col h-[700px]">
        <div class="p-6 border-b border-gray-50 flex items-center justify-between bg-surface/30">
            <h3 class="text-xl font-black text-brand tracking-tight">Recent Financial Ingress</h3>
            <div class="flex items-center gap-3">
                <button class="text-xs font-bold text-brand hover:text-brand-light transition">All Time</button>
            </div>
        </div>
        
        <div class="flex-1 overflow-y-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-surface/10 border-b border-gray-50">
                        <th class="px-6 py-4 text-[10px] font-black text-brand-muted uppercase tracking-widest">Description / User</th>
                        <th class="px-6 py-4 text-[10px] font-black text-brand-muted uppercase tracking-widest">Node ID</th>
                        <th class="px-6 py-4 text-[10px] font-black text-brand-muted uppercase tracking-widest">Amount</th>
                        <th class="px-6 py-4 text-[10px] font-black text-brand-muted uppercase tracking-widest text-right">State</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($recentTransactions as $txn)
                    <tr class="hover:bg-surface/50 transition-colors">
                        <td class="px-6 py-4">
                            <p class="text-sm font-bold text-brand truncate max-w-[200px]">{{ $txn->user?->name ?? 'Unknown User' }}</p>
                            <p class="text-[10px] text-gray-400 font-bold uppercase mt-1">{{ $txn->created_at?->format('M d, H:i') ?? 'Unknown Date' }} • {{ ucfirst($txn->gateway) }}</p>
                        </td>
                        <td class="px-6 py-4 font-mono text-xs text-brand-muted">{{ substr($txn->reference, 0, 12) }}...</td>
                        <td class="px-6 py-4 font-black text-brand">₵{{ number_format($txn->amount, 2) }}</td>
                        <td class="px-6 py-4 text-right">
                            @if($txn->status === 'completed')
                                <span class="px-2 py-1 bg-green-50 text-green-600 text-[10px] font-black rounded-lg uppercase shadow-sm">Settled</span>
                            @elseif($txn->status === 'failed')
                                <span class="px-2 py-1 bg-red-50 text-red-600 text-[10px] font-black rounded-lg uppercase shadow-sm">Failed</span>
                            @else
                                <span class="px-2 py-1 bg-amber-50 text-amber-600 text-[10px] font-black rounded-lg uppercase shadow-sm">{{ $txn->status }}</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                            No recent transactions found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="p-4 border-t border-gray-50 flex justify-center bg-surface/30">
            {{ $recentTransactions->appends(request()->except('transactions_page'))->links() }}
        </div>
    </div>

    <!-- Payout Queue Sidebar -->
    <div class="bg-surface rounded-lg p-6 border border-gray-100 shadow-xl flex flex-col h-[700px]">
        <div class="mb-6">
            <h3 class="text-xl font-black text-brand tracking-tight">Egress Queue</h3>
            <p class="text-sm text-brand-muted font-medium mt-1">Authorized payouts awaiting network release.</p>
        </div>
        
        <div class="flex-1 overflow-y-auto space-y-4 pr-2 custom-scrollbar">
            @forelse($pendingPayouts as $payout)
            <div class="bg-white p-5 rounded-lg shadow-sm border border-gray-100 group hover:shadow-md transition-all">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-[10px] font-black text-brand-muted uppercase tracking-widest font-mono">Ref: {{ substr($payout->reference, 0, 10) }}</span>
                    <form action="{{ route('orchestrator.financials.payout.approve', $payout->id) }}" method="POST" onsubmit="return confirm('Authorize this payout? Funds will be marked as disbursed.');">
                        @csrf @method('PATCH')
                        <button type="submit" class="text-[10px] font-black text-white bg-accent px-3 py-1 rounded uppercase hover:bg-accent-light transition shadow-sm">Approve</button>
                    </form>
                </div>
                <p class="text-xl font-black text-brand">₵{{ number_format($payout->amount, 2) }}</p>
                <div class="flex items-center gap-3 mt-4 pt-3 border-t border-gray-50">
                    <div class="w-8 h-8 rounded-full bg-surface border border-gray-200 flex items-center justify-center font-bold text-[10px] text-brand">
                        {{ strtoupper(substr($payout->user?->name ?? 'U', 0, 2)) }}
                    </div>
                    <div>
                        <p class="text-xs font-bold text-brand truncate max-w-[150px]">{{ $payout->user?->name ?? 'Unknown Driver' }}</p>
                        <p class="text-[10px] text-brand-muted">{{ $payout->created_at?->diffForHumans() ?? 'Unknown Date' }}</p>
                    </div>
                </div>
            </div>
            @empty
            <div class="flex flex-col items-center justify-center h-48 text-gray-400">
                <svg class="w-12 h-12 mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <p class="text-sm font-bold">No pending payouts.</p>
            </div>
            @endforelse
        </div>
        
        @if($pendingPayouts->hasPages())
        <div class="mt-4 pt-4 border-t border-gray-200">
            {{ $pendingPayouts->appends(request()->except('payouts_page'))->links() }}
        </div>
        @endif
    </div>
</div>

@endsection
