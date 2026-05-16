@extends('admin.layout')
@section('title', 'Financials')
@section('content')

@if(session('error'))
<div class="mb-4 p-3.5 bg-red-50 border border-red-200 rounded-lg flex items-center gap-2.5">
    <svg class="w-4 h-4 text-red-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    <p class="text-sm font-medium text-red-700">{{ session('error') }}</p>
</div>
@endif
@if(session('success'))
<div class="mb-4 p-3.5 bg-green-50 border border-green-200 rounded-lg flex items-center gap-2.5">
    <svg class="w-4 h-4 text-green-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    <p class="text-sm font-medium text-green-700">{{ session('success') }}</p>
</div>
@endif

<div class="max-w-6xl mx-auto">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-black text-brand tracking-tight">Financial Infrastructure</h2>
            <p class="text-sm text-brand-muted font-medium mt-0.5">Audit global liquidity, reconcile invoices, and monitor revenue streams.</p>
        </div>
        <button class="px-5 py-2.5 bg-white border border-gray-200 text-brand rounded-lg text-xs font-bold hover:bg-surface transition-colors flex items-center gap-2 shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
            Export Ledger
        </button>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-brand rounded-xl p-5 text-white">
            <p class="text-[10px] font-bold text-white/40 uppercase tracking-wider">Gross Revenue (L30D)</p>
            <p class="text-2xl font-black mt-2 tracking-tight">₵{{ number_format($stats['gross_revenue'] ?? 0, 2) }}</p>
            <div class="flex items-center gap-2 mt-3">
                <span class="px-2 py-0.5 text-[10px] font-bold rounded {{ ($stats['revenue_change'] ?? 0) >= 0 ? 'bg-green-500 text-white' : 'bg-red-500 text-white' }}">
                    {{ ($stats['revenue_change'] ?? 0) >= 0 ? '+' : '' }}{{ $stats['revenue_change'] ?? 0 }}%
                </span>
                <span class="text-[9px] text-white/30">vs prev 30d</span>
            </div>
        </div>
        <div class="bg-white border border-gray-100 rounded-xl p-5">
            <p class="text-[10px] font-bold text-brand-muted uppercase tracking-wider">Pending Payouts</p>
            <p class="text-2xl font-black text-brand mt-2">₵{{ number_format($stats['pending_payouts_sum'] ?? 0, 2) }}</p>
            <div class="flex items-center gap-2 mt-3">
                <span class="px-2 py-0.5 bg-accent/10 text-brand text-[10px] font-bold rounded">{{ $stats['pending_payouts_count'] ?? 0 }} entities</span>
            </div>
        </div>
        <div class="bg-white border border-gray-100 rounded-xl p-5">
            <p class="text-[10px] font-bold text-brand-muted uppercase tracking-wider">Platform Profit</p>
            <p class="text-2xl font-black text-brand mt-2">₵{{ number_format($stats['platform_profit'] ?? 0, 2) }}</p>
            <div class="flex items-center gap-2 mt-3">
                <span class="px-2 py-0.5 bg-blue-50 text-blue-600 text-[10px] font-bold rounded">{{ $stats['profit_margin'] ?? 0 }}% margin</span>
            </div>
        </div>
        <div class="bg-white border border-gray-100 rounded-xl p-5">
            <p class="text-[10px] font-bold text-brand-muted uppercase tracking-wider">Suspicious Flow</p>
            <p class="text-2xl font-black text-red-600 mt-2">₵{{ number_format($stats['suspicious_flow_sum'] ?? 0, 2) }}</p>
            <div class="flex items-center gap-2 mt-3">
                <span class="px-2 py-0.5 bg-red-50 text-red-600 text-[10px] font-bold rounded">{{ $stats['suspicious_flow_count'] ?? 0 }} alerts</span>
            </div>
        </div>
    </div>

    {{-- Main content --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Recent Transactions --}}
        <div class="lg:col-span-2 bg-white border border-gray-100 rounded-xl overflow-hidden flex flex-col">
            <div class="px-5 py-4 border-b border-gray-100 bg-surface/20">
                <h3 class="text-sm font-bold text-brand">Recent Financial Ingress</h3>
            </div>
            <div class="divide-y divide-gray-50 flex-1 overflow-y-auto" style="max-height: 500px;">
                @forelse($recentTransactions as $txn)
                <div class="px-5 py-4 hover:bg-surface/20 transition-colors">
                    <div class="flex items-center justify-between gap-4">
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-bold text-brand truncate">{{ $txn->user?->name ?? 'Unknown User' }}</p>
                            <div class="flex items-center gap-2 text-[10px] text-brand-muted mt-0.5">
                                <span>{{ $txn->created_at?->format('M d, H:i') ?? '—' }}</span>
                                <span>·</span>
                                <span class="font-mono">{{ substr($txn->reference, 0, 12) }}...</span>
                                <span>·</span>
                                <span class="uppercase">{{ ucfirst($txn->gateway) }}</span>
                            </div>
                        </div>
                        <div class="text-right shrink-0">
                            <p class="text-sm font-black text-brand">₵{{ number_format($txn->amount, 2) }}</p>
                            <span class="inline-block mt-0.5 px-2 py-0.5 text-[9px] font-bold rounded
                                @if($txn->status === 'completed') bg-green-50 text-green-700
                                @elseif($txn->status === 'failed') bg-red-50 text-red-700
                                @else bg-amber-50 text-amber-700 @endif">
                                {{ $txn->status === 'completed' ? 'Settled' : ucfirst($txn->status) }}
                            </span>
                        </div>
                    </div>
                </div>
                @empty
                <div class="flex flex-col items-center justify-center py-16 text-brand-muted">
                    <svg class="w-12 h-12 mb-3 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <p class="text-sm font-bold">No transactions found</p>
                </div>
                @endforelse
            </div>
            @if($recentTransactions->hasPages())
            <div class="px-5 py-3 border-t border-gray-100 bg-surface/20">{{ $recentTransactions->appends(request()->except('transactions_page'))->links() }}</div>
            @endif
        </div>

        {{-- Pending Payouts --}}
        <div class="bg-white border border-gray-100 rounded-xl overflow-hidden flex flex-col">
            <div class="px-5 py-4 border-b border-gray-100 bg-surface/20">
                <h3 class="text-sm font-bold text-brand">Egress Queue</h3>
                <p class="text-[10px] text-brand-muted">Authorized payouts awaiting release</p>
            </div>
            <div class="divide-y divide-gray-50 flex-1 overflow-y-auto" style="max-height: 500px;">
                @forelse($pendingPayouts as $payout)
                <div class="px-5 py-4 hover:bg-surface/20 transition-colors">
                    <div class="flex items-start justify-between gap-3 mb-2">
                        <div>
                            <p class="text-xs font-bold text-brand-muted font-mono">Ref: {{ substr($payout->reference, 0, 10) }}</p>
                            <p class="text-lg font-black text-brand mt-1">₵{{ number_format($payout->amount, 2) }}</p>
                        </div>
                        <form action="{{ route('orchestrator.financials.payout.approve', $payout->id) }}" method="POST">
                            @csrf @method('PATCH')
                            <button type="submit" onclick="return confirm('Authorize this payout of ₵{{ number_format($payout->amount, 2) }}?')" class="px-3 py-1.5 bg-accent text-brand rounded-lg text-[10px] font-bold hover:bg-accent-hover transition-colors">Approve</button>
                        </form>
                    </div>
                    <div class="flex items-center gap-2.5">
                        <div class="w-7 h-7 rounded-lg bg-surface flex items-center justify-center text-xs font-bold text-brand shrink-0">
                            {{ strtoupper(substr($payout->user?->name ?? 'U', 0, 2)) }}
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs font-bold text-brand truncate">{{ $payout->user?->name ?? 'Unknown' }}</p>
                            <p class="text-[10px] text-brand-muted">{{ $payout->created_at?->diffForHumans() ?? '—' }}</p>
                        </div>
                    </div>
                </div>
                @empty
                <div class="flex flex-col items-center justify-center py-16 text-brand-muted">
                    <svg class="w-12 h-12 mb-3 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <p class="text-sm font-bold">No pending payouts</p>
                </div>
                @endforelse
            </div>
            @if($pendingPayouts->hasPages())
            <div class="px-5 py-3 border-t border-gray-100 bg-surface/20">{{ $pendingPayouts->appends(request()->except('payouts_page'))->links() }}</div>
            @endif
        </div>
    </div>
</div>
@endsection