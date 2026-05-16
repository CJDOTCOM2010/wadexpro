@extends('admin.layout')
@section('title', 'Analytics & Reports')
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
            <h2 class="text-2xl font-black text-brand tracking-tight">Business Intelligence</h2>
            <p class="text-sm text-brand-muted font-medium mt-0.5">Real-time metrics, revenue analysis, and growth reports.</p>
        </div>
        <form action="{{ route('orchestrator.analytics') }}" method="GET" class="flex items-center gap-2">
            <select name="period" onchange="this.form.submit()" class="bg-white border border-gray-200 rounded-lg px-3 py-2 text-xs font-bold text-brand outline-none focus:ring-2 focus:ring-accent/20">
                <option value="7days" {{ $period === '7days' ? 'selected' : '' }}>Last 7 Days</option>
                <option value="30days" {{ $period === '30days' ? 'selected' : '' }}>Last 30 Days</option>
                <option value="quarter" {{ $period === 'quarter' ? 'selected' : '' }}>This Quarter</option>
                <option value="year" {{ $period === 'year' ? 'selected' : '' }}>Year to Date</option>
            </select>
            <button type="button" class="px-4 py-2 bg-brand text-white rounded-lg text-xs font-bold hover:bg-brand-light transition-colors flex items-center gap-2">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                Export CSV
            </button>
        </form>
    </div>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white border border-gray-100 rounded-xl p-5">
            <p class="text-[10px] font-bold text-brand-muted uppercase tracking-wider">Total Revenue</p>
            <p class="text-2xl font-black text-brand mt-1">₵ {{ number_format($kpis['revenue'] ?? 0) }}</p>
            <p class="text-xs font-bold mt-2 {{ ($kpis['revenue_change'] ?? 0) >= 0 ? 'text-green-600' : 'text-red-500' }}">
                {{ ($kpis['revenue_change'] ?? 0) >= 0 ? '+' : '' }}{{ $kpis['revenue_change'] ?? 0 }}% vs last period
            </p>
        </div>
        <div class="bg-white border border-gray-100 rounded-xl p-5">
            <p class="text-[10px] font-bold text-brand-muted uppercase tracking-wider">Total Rides</p>
            <p class="text-2xl font-black text-brand mt-1">{{ number_format($kpis['total_rides'] ?? 0) }}</p>
            <p class="text-xs font-bold mt-2 {{ ($kpis['rides_change'] ?? 0) >= 0 ? 'text-green-600' : 'text-red-500' }}">
                {{ ($kpis['rides_change'] ?? 0) >= 0 ? '+' : '' }}{{ $kpis['rides_change'] ?? 0 }}% vs last period
            </p>
        </div>
        <div class="bg-white border border-gray-100 rounded-xl p-5">
            <p class="text-[10px] font-bold text-brand-muted uppercase tracking-wider">New Customers</p>
            <p class="text-2xl font-black text-brand mt-1">{{ number_format($kpis['new_customers'] ?? 0) }}</p>
            <p class="text-xs text-brand-muted font-medium mt-2">In selected period</p>
        </div>
        <div class="bg-white border border-gray-100 rounded-xl p-5">
            <p class="text-[10px] font-bold text-brand-muted uppercase tracking-wider">Cancellation Rate</p>
            <p class="text-2xl font-black text-brand mt-1">{{ $kpis['cancel_rate'] ?? 0 }}%</p>
            <p class="text-xs text-brand-muted font-medium mt-2">Of total orders placed</p>
        </div>
    </div>

    {{-- Main Content --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Daily Revenue Chart --}}
        <div class="lg:col-span-2 bg-white border border-gray-100 rounded-xl p-6">
            <h3 class="text-sm font-bold text-brand mb-6">Daily Revenue (Last 7 Days)</h3>
            @php $maxRev = $dailyRevenue->max('total') ?: 1; @endphp
            <div class="h-64 flex items-end justify-between gap-2 px-2 pb-2 border-b border-l border-gray-100 relative pt-8">
                <div class="absolute top-[33%] left-0 right-0 border-t border-gray-50"></div>
                <div class="absolute top-[66%] left-0 right-0 border-t border-gray-50"></div>
                @for($i = 6; $i >= 0; $i--)
                @php
                $dateObj = now()->subDays($i);
                $dayTotal = $dailyRevenue->has($dateObj->format('Y-m-d')) ? $dailyRevenue[$dateObj->format('Y-m-d')]->total : 0;
                $percent = $maxRev > 0 ? max(($dayTotal / $maxRev) * 100, 4) : 4;
                @endphp
                <div class="w-full flex flex-col justify-end items-center gap-1 relative">
                    <div class="w-full bg-brand/70 rounded-t transition-colors relative group" style="height: {{ $percent }}%">
                        <div class="absolute -top-8 left-1/2 -translate-x-1/2 bg-gray-900 text-white text-[9px] font-bold px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition whitespace-nowrap z-10 pointer-events-none">
                            ₵ {{ number_format($dayTotal) }}
                        </div>
                    </div>
                    <span class="text-[9px] font-bold text-brand-muted">{{ $dateObj->format('D') }}</span>
                </div>
                @endfor
            </div>
        </div>

        {{-- Top Drivers --}}
        <div class="bg-white border border-gray-100 rounded-xl p-6">
            <h3 class="text-sm font-bold text-brand mb-5">Top Performing Drivers</h3>
            <div class="space-y-4">
                @forelse($topDrivers as $index => $driver)
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3 min-w-0">
                        <span class="text-sm font-bold text-gray-300 w-5 shrink-0">#{{ $index + 1 }}</span>
                        <div class="w-8 h-8 rounded-lg bg-brand/10 text-brand font-bold flex items-center justify-center text-xs shrink-0">
                            {{ substr($driver->user->name ?? 'D', 0, 2) }}
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs font-bold text-brand truncate">{{ $driver->user->name ?? 'Unknown' }}</p>
                            <p class="text-[10px] text-brand-muted flex items-center gap-1">
                                <svg class="w-3 h-3 text-accent" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                {{ number_format($driver->rating, 1) }}
                            </p>
                        </div>
                    </div>
                    <span class="text-[10px] font-bold text-brand bg-surface px-2 py-1 rounded shrink-0 ml-2">{{ $driver->orders_count }} rides</span>
                </div>
                @empty
                <p class="text-xs text-brand-muted text-center py-4">No driver data for this period.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection