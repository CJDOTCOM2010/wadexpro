@extends('admin.layout')
@section('title', 'Analytics & Reports')
@section('content')

<div class="mb-8 flex items-center justify-between">
    <div>
        <h2 class="text-2xl font-black text-brand tracking-tight">Business Intelligence</h2>
        <p class="text-brand-muted font-medium mt-1">Real-time metrics, revenue analysis, and growth reports.</p>
    </div>
    <div class="flex gap-4 items-center">
        <form action="{{ route('orchestrator.analytics') }}" method="GET" class="flex items-center gap-4">
            <select name="period" onchange="this.form.submit()" class="bg-white border border-gray-100 rounded-lg px-4 py-2 text-sm font-bold text-brand outline-none focus:ring-2 focus:ring-brand/20 cursor-pointer shadow-sm">
                <option value="7days" {{ $period === '7days' ? 'selected' : '' }}>Last 7 Days</option>
                <option value="30days" {{ $period === '30days' ? 'selected' : '' }}>Last 30 Days</option>
                <option value="quarter" {{ $period === 'quarter' ? 'selected' : '' }}>This Quarter</option>
                <option value="year" {{ $period === 'year' ? 'selected' : '' }}>Year to Date</option>
            </select>
            <button type="button" class="px-6 py-2.5 bg-brand text-white font-bold rounded-lg hover:bg-brand-light transition flex items-center gap-2 shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                Export CSV
            </button>
        </form>
    </div>
</div>

<!-- Key Performance Indicators -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white p-6 rounded-lg border border-gray-100 shadow-sm relative overflow-hidden group">
        <div class="absolute right-0 top-0 w-16 h-full bg-brand/5 group-hover:bg-brand/10 transition flex items-center justify-center">
            <svg class="w-8 h-8 text-brand/20 group-hover:text-brand/40 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <p class="text-[10px] font-black text-brand-muted uppercase tracking-widest relative z-10">Total Revenue</p>
        <p class="text-3xl font-black text-brand tracking-tight mt-2 relative z-10">₵ {{ number_format($kpis['revenue']) }}</p>
        <p class="text-xs font-bold mt-2 flex items-center gap-1 relative z-10 {{ $kpis['revenue_change'] >= 0 ? 'text-green-500' : 'text-red-500' }}">
            @if($kpis['revenue_change'] >= 0)
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                +{{ $kpis['revenue_change'] }}% vs last period
            @else
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0v-8m0 8l-8-8-4 4-6-6"/></svg>
                {{ $kpis['revenue_change'] }}% vs last period
            @endif
        </p>
    </div>
    
    <div class="bg-white p-6 rounded-lg border border-gray-100 shadow-sm relative overflow-hidden group">
        <div class="absolute right-0 top-0 w-16 h-full bg-brand/5 group-hover:bg-brand/10 transition flex items-center justify-center">
            <svg class="w-8 h-8 text-brand/20 group-hover:text-brand/40 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
        </div>
        <p class="text-[10px] font-black text-brand-muted uppercase tracking-widest relative z-10">Total Rides</p>
        <p class="text-3xl font-black text-brand tracking-tight mt-2 relative z-10">{{ number_format($kpis['total_rides']) }}</p>
        <p class="text-xs font-bold mt-2 flex items-center gap-1 relative z-10 {{ $kpis['rides_change'] >= 0 ? 'text-green-500' : 'text-red-500' }}">
            @if($kpis['rides_change'] >= 0)
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                +{{ $kpis['rides_change'] }}% vs last period
            @else
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0v-8m0 8l-8-8-4 4-6-6"/></svg>
                {{ $kpis['rides_change'] }}% vs last period
            @endif
        </p>
    </div>

    <div class="bg-white p-6 rounded-lg border border-gray-100 shadow-sm relative overflow-hidden group">
        <div class="absolute right-0 top-0 w-16 h-full bg-brand/5 group-hover:bg-brand/10 transition flex items-center justify-center">
            <svg class="w-8 h-8 text-brand/20 group-hover:text-brand/40 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
        </div>
        <p class="text-[10px] font-black text-brand-muted uppercase tracking-widest relative z-10">New Customers</p>
        <p class="text-3xl font-black text-brand tracking-tight mt-2 relative z-10">{{ number_format($kpis['new_customers']) }}</p>
        <p class="text-xs text-brand-muted font-medium mt-2 relative z-10">In selected period</p>
    </div>

    <div class="bg-white p-6 rounded-lg border border-gray-100 shadow-sm relative overflow-hidden group">
        <div class="absolute right-0 top-0 w-16 h-full bg-brand/5 group-hover:bg-brand/10 transition flex items-center justify-center">
            <svg class="w-8 h-8 text-brand/20 group-hover:text-brand/40 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"/></svg>
        </div>
        <p class="text-[10px] font-black text-brand-muted uppercase tracking-widest relative z-10">Cancellation Rate</p>
        <p class="text-3xl font-black text-brand tracking-tight mt-2 relative z-10">{{ $kpis['cancel_rate'] }}%</p>
        <p class="text-xs text-brand-muted font-medium mt-2 relative z-10">Of total orders placed</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    
    <!-- Main Chart -->
    <div class="lg:col-span-2 bg-white rounded-lg border border-gray-100 shadow-sm p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-sm font-black text-brand uppercase tracking-widest">Daily Revenue (Last 7 Days)</h3>
        </div>
        <!-- Simulated Chart Container based on dynamic data -->
        <div class="h-72 flex items-end justify-between gap-2 px-2 pb-2 border-b border-l border-gray-100 relative pt-10">
            <!-- Grid lines -->
            <div class="absolute top-0 left-0 w-full border-t border-gray-50"></div>
            <div class="absolute top-1/3 left-0 w-full border-t border-gray-50"></div>
            <div class="absolute top-2/3 left-0 w-full border-t border-gray-50"></div>
            
            @php 
                $maxRev = $dailyRevenue->max('total') ?: 1; // prevent div by zero
            @endphp
            
            @for($i = 6; $i >= 0; $i--)
                @php
                    $dateObj = now()->subDays($i);
                    $dateStr = $dateObj->format('Y-m-d');
                    $dayTotal = $dailyRevenue->has($dateStr) ? $dailyRevenue[$dateStr]->total : 0;
                    $percent = ($dayTotal / $maxRev) * 100;
                    if($percent == 0 && $dayTotal == 0) $percent = 5; // minimum height for visibility
                @endphp
                <div class="w-full flex flex-col justify-end items-center gap-1 group relative">
                    <div class="w-full bg-brand/80 group-hover:bg-brand transition-colors rounded-t" style="height: {{ $percent }}%">
                        <div class="absolute -top-8 left-1/2 -translate-x-1/2 bg-gray-900 text-white text-[10px] font-bold py-1 px-2 rounded opacity-0 group-hover:opacity-100 transition whitespace-nowrap z-50 pointer-events-none">
                            ₵ {{ number_format($dayTotal) }}
                        </div>
                    </div>
                    <div class="text-[9px] text-brand-muted font-bold">{{ $dateObj->format('D') }}</div>
                </div>
            @endfor
        </div>
    </div>

    <!-- Right Sidebar Stats -->
    <div class="space-y-8">
        
        <!-- Top Drivers -->
        <div class="bg-white rounded-lg border border-gray-100 shadow-sm p-6">
            <h3 class="text-[10px] font-black text-brand-muted uppercase tracking-widest mb-6">Top Performing Drivers</h3>
            <div class="space-y-4">
                @forelse($topDrivers as $index => $driver)
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <span class="text-sm font-black text-gray-300">#{{ $index + 1 }}</span>
                        <div class="w-8 h-8 rounded-full bg-brand/10 text-brand font-bold flex items-center justify-center text-xs">
                            {{ substr($driver->user->name ?? 'D', 0, 2) }}
                        </div>
                        <div>
                            <p class="text-xs font-bold text-brand">{{ $driver->user->name ?? 'Unknown' }}</p>
                            <p class="text-[10px] text-brand-muted flex items-center gap-1">
                                <svg class="w-3 h-3 text-accent" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                {{ number_format($driver->rating, 1) }}
                            </p>
                        </div>
                    </div>
                    <span class="text-xs font-bold text-brand bg-surface px-2 py-1 rounded">{{ $driver->orders_count }} rides</span>
                </div>
                @empty
                <p class="text-xs text-gray-500 text-center">No driver data for this period.</p>
                @endforelse
            </div>
        </div>

    </div>
</div>

@endsection
