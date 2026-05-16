@extends('admin.layout')
@section('title', 'Global Queue')
@section('content')

@if(session('error'))
<div class="mb-4 p-3.5 bg-red-50 border border-red-200 rounded-lg flex items-center gap-2.5">
    <svg class="w-4 h-4 text-red-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    <p class="text-sm font-medium text-red-700">{{ session('error') }}</p>
</div>
@endif

<div class="max-w-6xl mx-auto">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-black text-brand tracking-tight">Global Queue</h2>
            <p class="text-sm text-brand-muted font-medium mt-0.5">Real-time oversight of all platform transactions, orders, and fulfillment pipelines.</p>
        </div>
        <a href="{{ route('orchestrator.dispatcher') }}" class="px-5 py-2.5 bg-brand text-white rounded-lg text-xs font-bold hover:bg-brand-light transition-colors flex items-center gap-2 shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Manual Dispatch
        </a>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white border border-gray-100 rounded-xl p-4">
            <p class="text-[10px] font-bold text-brand-muted uppercase tracking-wider">Active Pipeline</p>
            <p class="text-2xl font-black text-brand mt-1">{{ number_format($stats['active_pipeline']) }}</p>
        </div>
        <div class="bg-white border border-gray-100 rounded-xl p-4">
            <p class="text-[10px] font-bold text-brand-muted uppercase tracking-wider">Awaiting Node</p>
            <p class="text-2xl font-black text-blue-600 mt-1">{{ number_format($stats['awaiting_node']) }}</p>
        </div>
        <div class="bg-white border border-gray-100 rounded-xl p-4">
            <p class="text-[10px] font-bold text-brand-muted uppercase tracking-wider">In Progress</p>
            <p class="text-2xl font-black text-green-600 mt-1">{{ number_format($stats['in_progress']) }}</p>
        </div>
        <div class="bg-white border border-gray-100 rounded-xl p-4">
            <p class="text-[10px] font-bold text-brand-muted uppercase tracking-wider">Anomalies</p>
            <p class="text-2xl font-black text-red-500 mt-1">{{ number_format($stats['anomalies']) }}</p>
        </div>
    </div>

    {{-- Queue --}}
    <div class="bg-white border border-gray-100 rounded-xl overflow-hidden">
        {{-- Toolbar --}}
        <div class="px-5 py-4 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-surface/20">
            <div class="flex items-center gap-2">
                <h3 class="text-sm font-bold text-brand">Transaction Stream</h3>
                <span class="flex items-center gap-1.5 text-[9px] font-bold text-green-600">
                    <span class="w-1.5 h-1.5 bg-green-500 rounded-full animate-pulse"></span> Live
                </span>
            </div>
            <form action="{{ route('orchestrator.orders') }}" method="GET" class="relative">
                <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search Order ID, Client..." class="bg-white border border-gray-200 rounded-lg pl-9 pr-3 py-2 text-sm outline-none focus:ring-2 focus:ring-accent/20 transition-shadow w-64">
            </form>
        </div>

        {{-- List --}}
        <div class="divide-y divide-gray-50">
            @forelse($orders as $order)
            <div class="px-5 py-4 hover:bg-surface/20 transition-colors {{ $order->status === 'cancelled' ? 'bg-red-50/20' : '' }}">
                <div class="flex items-start gap-4">
                    {{-- Status icon --}}
                    <div class="w-9 h-9 rounded-lg flex items-center justify-center shrink-0 mt-0.5
                        @if($order->status === 'completed') bg-green-50 text-green-600
                        @elseif($order->status === 'cancelled') bg-red-50 text-red-500
                        @elseif($order->status === 'pending') bg-blue-50 text-blue-600
                        @else bg-amber-50 text-amber-600 @endif">
                        @if($order->status === 'completed')
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        @elseif($order->status === 'cancelled')
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        @else
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        @endif
                    </div>

                    <div class="flex-1 min-w-0 grid grid-cols-1 lg:grid-cols-5 gap-2 lg:gap-4">
                        {{-- Order info --}}
                        <div class="lg:col-span-1">
                            <p class="text-sm font-bold text-brand">{{ $order->reference }}</p>
                            <p class="text-[10px] text-brand-muted font-mono">{{ $order->created_at->diffForHumans() }}</p>
                            <span class="inline-block mt-1 px-1.5 py-0.5 bg-surface border border-gray-100 text-[9px] font-bold text-brand-muted rounded">{{ $order->priority ?? 'standard' }}</span>
                        </div>

                        {{-- Route --}}
                        <div class="lg:col-span-2">
                            <div class="flex items-start gap-2 text-xs">
                                <div class="flex flex-col items-center gap-0.5 mt-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-accent"></span>
                                    <span class="w-px h-3 bg-gray-200"></span>
                                    <span class="w-1.5 h-1.5 rounded-full border border-brand"></span>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-xs font-bold text-brand truncate">{{ $order->pickup_address }}</p>
                                    <p class="text-[10px] text-brand-muted truncate">{{ optional($order->stops->last())->dropoff_address ?? 'TBD' }}</p>
                                </div>
                            </div>
                            <p class="text-[10px] text-brand-muted mt-1">Client: <strong class="text-brand">{{ $order->customer->name ?? 'Guest' }}</strong></p>
                        </div>

                        {{-- Financials --}}
                        <div class="lg:col-span-1">
                            <p class="text-sm font-black {{ $order->status === 'cancelled' ? 'text-brand-muted' : 'text-green-600' }}">₵{{ number_format($order->total_amount, 2) }}</p>
                            <p class="text-[10px] text-brand-muted">{{ $order->payment_method ?? 'Cash' }} · {{ $order->payment_status ?? 'Pending' }}</p>
                        </div>

                        {{-- Driver --}}
                        <div class="lg:col-span-1">
                            @if($order->driver)
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 rounded-lg bg-brand/10 text-brand flex items-center justify-center text-[10px] font-bold shrink-0">
                                    {{ strtoupper(substr($order->driver->user->name ?? 'D', 0, 2)) }}
                                </div>
                                <div class="min-w-0">
                                    <p class="text-xs font-bold text-brand truncate">{{ $order->driver->user->name ?? 'Unknown' }}</p>
                                    <p class="text-[9px] text-brand-muted font-mono">{{ $order->driver->user->phone ?? '—' }}</p>
                                </div>
                            </div>
                            @else
                            <span class="inline-flex px-2 py-1 bg-amber-50 text-amber-600 text-[10px] font-bold rounded">Unassigned</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="flex flex-col items-center justify-center py-16 text-brand-muted">
                <svg class="w-14 h-14 mb-3 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                <p class="text-sm font-bold">No orders found</p>
                <p class="text-xs mt-1">Try adjusting your search.</p>
            </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if($orders->hasPages())
        <div class="px-5 py-3 border-t border-gray-100 bg-surface/20">
            {{ $orders->appends(request()->except('page'))->links() }}
        </div>
        @endif
    </div>
</div>
@endsection