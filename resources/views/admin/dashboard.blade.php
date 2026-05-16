@extends('admin.layout')
@section('title', 'Super Admin Dashboard')

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
@endpush

@section('content')
<div class="space-y-8">
    <!-- Header Section -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div>
            <div class="flex items-center gap-3 mb-2 flex-wrap">
                <span class="px-3 py-1 bg-green-100 text-green-700 text-xs font-bold rounded-full flex items-center gap-1">
                    <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span> LIVE
                </span>
                <span class="text-xs text-brand-muted font-medium">{{ now()->format('l, F j, Y • g:i A') }}</span>
                <span class="text-xs text-brand-muted hidden sm:inline">•</span>
                <span class="text-xs font-bold text-accent hidden sm:inline">
                    Drivers: {{ $driverStats['total'] ?? 0 }} | Rides Today: {{ $rideStats['today'] ?? 0 }} | Map: {{ $mapData['drivers']->count() ?? 0 }} drivers
                </span>
            </div>
            <h1 class="text-3xl font-black text-brand tracking-tight">Super Admin Dashboard</h1>
            <p class="text-brand-muted font-medium mt-1">Welcome back, {{ $admin->name ?? 'Administrator' }} • {{ ucfirst($admin->level ?? 'Super Admin') }}</p>
        </div>
        <div class="flex items-center gap-3 flex-wrap">
            <a href="{{ route('orchestrator.drivers') }}" class="px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm font-bold text-brand hover:border-accent hover:bg-accent/5 transition-all flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Drivers
            </a>
            <a href="{{ route('orchestrator.orders') }}" class="px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm font-bold text-brand hover:border-accent hover:bg-accent/5 transition-all flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                Rides
            </a>
            <a href="{{ route('orchestrator.analytics') }}" class="px-4 py-2.5 bg-brand text-white rounded-xl text-sm font-bold hover:bg-brand-light transition-all flex items-center gap-2 shadow-lg shadow-brand/20">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                Analytics
            </a>
        </div>
    </div>

    <!-- Alerts Section -->
    @if(!empty($alerts))
    <div class="space-y-3">
        @foreach($alerts as $alert)
        <div class="p-4 rounded-2xl border flex items-center gap-4 @if($alert['type'] === 'danger') bg-red-50 border-red-200 @elseif($alert['type'] === 'warning') bg-amber-50 border-amber-200 @else bg-blue-50 border-blue-200 @endif">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center @if($alert['type'] === 'danger') bg-red-100 text-red-600 @elseif($alert['type'] === 'warning') bg-amber-100 text-amber-600 @else bg-blue-100 text-blue-600 @endif">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $alert['icon'] }}"/></svg>
            </div>
            <div class="flex-1">
                <p class="text-sm font-bold text-brand">{{ $alert['title'] }}</p>
                <p class="text-xs text-brand-muted">{{ $alert['message'] }}</p>
            </div>
            <button class="text-brand-muted hover:text-brand">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        @endforeach
    </div>
    @endif

    <!-- System Health Bar -->
    <div class="bg-white rounded-2xl border border-gray-100 p-4 flex items-center justify-between shadow-sm">
        <div class="flex items-center gap-4 sm:gap-6 flex-wrap">
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 {{ $systemHealth['api_status'] === 'healthy' ? 'bg-green-500' : 'bg-red-500' }} rounded-full"></span>
                <span class="text-sm font-bold text-brand">API</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 {{ $systemHealth['database_status'] === 'healthy' ? 'bg-green-500' : 'bg-red-500' }} rounded-full"></span>
                <span class="text-sm font-bold text-brand">Database</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 {{ $systemHealth['cache_status'] === 'healthy' ? 'bg-green-500' : 'bg-red-500' }} rounded-full"></span>
                <span class="text-sm font-bold text-brand">Cache</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 {{ $systemHealth['queue_status'] === 'healthy' ? 'bg-green-500' : 'bg-red-500' }} rounded-full"></span>
                <span class="text-sm font-bold text-brand">Queue</span>
            </div>
        </div>
        <div class="flex items-center gap-3 sm:gap-4 text-xs sm:text-sm flex-wrap">
            <span class="text-brand-muted">Connections: <strong class="text-brand">{{ $systemHealth['active_connections'] ?? 0 }}</strong></span>
            <span class="text-brand-muted hidden sm:inline">|</span>
            <span class="text-brand-muted">Load: <strong class="text-brand">{{ $systemHealth['server_load'] ?? '0%' }}</strong></span>
            <span class="text-brand-muted hidden sm:inline">|</span>
            <span class="text-brand-muted">RAM: <strong class="text-brand">{{ $systemHealth['memory_usage'] ?? '0%' }}</strong></span>
            <span class="text-brand-muted hidden sm:inline">|</span>
            <span class="text-brand-muted">Uptime: <strong class="text-brand">{{ $systemHealth['uptime'] ?? 'N/A' }}</strong></span>
        </div>
    </div>

    <!-- Main Metrics Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Revenue Card -->
        <div class="bg-gradient-to-br from-brand to-brand-light rounded-2xl p-6 text-white shadow-lg hover:shadow-xl transition-all hover:-translate-y-1">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <span class="px-2 py-1 bg-white/20 text-xs font-bold rounded-full {{ ($revenueStats['growth'] ?? 0) >= 0 ? 'text-green-300' : 'text-red-300' }}">
                    {{ ($revenueStats['growth'] ?? 0) >= 0 ? '+' : '' }}{{ number_format($revenueStats['growth'] ?? 0, 1) }}%
                </span>
            </div>
            <p class="text-xs font-bold text-white/60 uppercase tracking-wider mb-1">Today's Revenue</p>
            <div class="flex items-baseline gap-2">
                <span class="text-3xl font-black">₵{{ number_format($revenueStats['today'] ?? 0) }}</span>
            </div>
            <div class="mt-4 pt-4 border-t border-white/20 flex items-center justify-between text-xs">
                <span class="text-white/60">This Month: <strong class="text-white">₵{{ number_format($revenueStats['this_month'] ?? 0) }}</strong></span>
                <span class="text-white/60">Avg/Ride: <strong class="text-white">₵{{ number_format($revenueStats['avg_per_ride'] ?? 0, 2) }}</strong></span>
            </div>
        </div>

        <!-- Drivers Card -->
        <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm hover:shadow-lg transition-all hover:-translate-y-1">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-accent/10 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/></svg>
                </div>
                <span class="px-2 py-1 bg-green-100 text-green-700 text-xs font-bold rounded-full">{{ $driverStats['online'] ?? 0 }} Online</span>
            </div>
            <p class="text-xs font-bold text-brand-muted uppercase tracking-wider mb-1">Fleet Status</p>
            <div class="flex items-baseline gap-2">
                <span class="text-3xl font-black text-brand">{{ $driverStats['total'] ?? 0 }}</span>
                <span class="text-sm text-brand-muted font-medium">Total Drivers</span>
            </div>
            <div class="mt-4 pt-4 border-t border-gray-50 flex items-center justify-between text-sm">
                <div class="text-center">
                    <p class="text-xs text-brand-muted">Available</p>
                    <p class="text-lg font-black text-green-600">{{ $driverStats['available'] ?? 0 }}</p>
                </div>
                <div class="text-center">
                    <p class="text-xs text-brand-muted">Busy</p>
                    <p class="text-lg font-black text-amber-600">{{ $driverStats['busy'] ?? 0 }}</p>
                </div>
                <div class="text-center">
                    <p class="text-xs text-brand-muted">Pending</p>
                    <p class="text-lg font-black text-brand">{{ $driverStats['pending_verification'] ?? 0 }}</p>
                </div>
            </div>
        </div>

        <!-- Rides Card -->
        <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm hover:shadow-lg transition-all hover:-translate-y-1">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-purple-50 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                </div>
                <span class="px-2 py-1 bg-purple-100 text-purple-700 text-xs font-bold rounded-full">{{ $rideStats['active'] ?? 0 }} Active</span>
            </div>
            <p class="text-xs font-bold text-brand-muted uppercase tracking-wider mb-1">Rides Today</p>
            <div class="flex items-baseline gap-2">
                <span class="text-3xl font-black text-brand">{{ $rideStats['today'] ?? 0 }}</span>
                <span class="text-sm text-brand-muted font-medium">Total Rides</span>
            </div>
            <div class="mt-4 pt-4 border-t border-gray-50 flex items-center justify-between text-sm">
                <div class="text-center">
                    <p class="text-xs text-brand-muted">Completed</p>
                    <p class="text-lg font-black text-green-600">{{ $rideStats['completed_today'] ?? 0 }}</p>
                </div>
                <div class="text-center">
                    <p class="text-xs text-brand-muted">Cancelled</p>
                    <p class="text-lg font-black text-red-500">{{ $rideStats['cancelled_today'] ?? 0 }}</p>
                </div>
                <div class="text-center">
                    <p class="text-xs text-brand-muted">Rate</p>
                    <p class="text-lg font-black text-brand">{{ $rideStats['completion_rate'] ?? 0 }}%</p>
                </div>
            </div>
        </div>

        <!-- Customers Card -->
        <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm hover:shadow-lg transition-all hover:-translate-y-1">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </div>
                <span class="px-2 py-1 bg-blue-100 text-blue-700 text-xs font-bold rounded-full">+{{ $customerStats['new_today'] ?? 0 }} Today</span>
            </div>
            <p class="text-xs font-bold text-brand-muted uppercase tracking-wider mb-1">Customer Base</p>
            <div class="flex items-baseline gap-2">
                <span class="text-3xl font-black text-brand">{{ number_format($customerStats['total'] ?? 0) }}</span>
                <span class="text-sm text-brand-muted font-medium">Total</span>
            </div>
            <div class="mt-4 pt-4 border-t border-gray-50 flex items-center justify-between text-sm">
                <div class="text-center">
                    <p class="text-xs text-brand-muted">Active 30d</p>
                    <p class="text-lg font-black text-green-600">{{ $customerStats['active_30d'] ?? 0 }}</p>
                </div>
                <div class="text-center">
                    <p class="text-xs text-brand-muted">Verified</p>
                    <p class="text-lg font-black text-brand">{{ $customerStats['verified'] ?? 0 }}</p>
                </div>
                <div class="text-center">
                    <p class="text-xs text-brand-muted">New Month</p>
                    <p class="text-lg font-black text-brand">{{ $customerStats['new_this_month'] ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Secondary Metrics Row -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Year Revenue -->
        <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm hover:shadow-md transition-all">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 bg-green-50 rounded-xl flex items-center justify-center text-green-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                </div>
                <p class="text-xs font-bold text-brand-muted uppercase">Year Revenue</p>
            </div>
            <p class="text-2xl font-black text-brand">₵{{ number_format($revenueStats['this_year'] ?? 0) }}</p>
            <p class="text-xs text-brand-muted mt-1">Year {{ now()->year }}</p>
        </div>

        <!-- Pending Actions -->
        <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm hover:shadow-md transition-all">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 bg-amber-50 rounded-xl flex items-center justify-center text-amber-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <p class="text-xs font-bold text-brand-muted uppercase">Pending Actions</p>
            </div>
            <p class="text-2xl font-black text-brand">{{ array_sum($pendingActions) }}</p>
            <div class="flex gap-2 mt-2 flex-wrap">
                <span class="text-[10px] bg-amber-50 text-amber-700 px-2 py-0.5 rounded">Drivers: {{ $pendingActions['pending_drivers'] ?? 0 }}</span>
                <span class="text-[10px] bg-blue-50 text-blue-700 px-2 py-0.5 rounded">Docs: {{ $pendingActions['pending_documents'] ?? 0 }}</span>
            </div>
        </div>

        <!-- Staff Stats -->
        <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm hover:shadow-md transition-all">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 bg-purple-50 rounded-xl flex items-center justify-center text-purple-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </div>
                <p class="text-xs font-bold text-brand-muted uppercase">Admin Staff</p>
            </div>
            <p class="text-2xl font-black text-brand">{{ $staffStats['total_admins'] ?? 0 }}</p>
            <p class="text-xs text-brand-muted mt-1">{{ $staffStats['active_today'] ?? 0 }} active today</p>
        </div>

        <!-- Vehicle Types -->
        <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm hover:shadow-md transition-all">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 bg-brand/10 rounded-xl flex items-center justify-center text-brand">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                </div>
                <p class="text-xs font-bold text-brand-muted uppercase">Vehicle Types</p>
            </div>
            <div class="grid grid-cols-2 gap-2 mt-2">
                <div class="bg-surface rounded-lg p-2 text-center">
                    <p class="text-lg font-black text-brand">{{ $vehicleStats['economy'] ?? 0 }}</p>
                    <p class="text-[10px] text-brand-muted">Economy</p>
                </div>
                <div class="bg-surface rounded-lg p-2 text-center">
                    <p class="text-lg font-black text-brand">{{ $vehicleStats['premium'] ?? 0 }}</p>
                    <p class="text-[10px] text-brand-muted">Premium</p>
                </div>
                <div class="bg-surface rounded-lg p-2 text-center">
                    <p class="text-lg font-black text-brand">{{ $vehicleStats['bike'] ?? 0 }}</p>
                    <p class="text-[10px] text-brand-muted">Bike</p>
                </div>
                <div class="bg-surface rounded-lg p-2 text-center">
                    <p class="text-lg font-black text-brand">{{ $vehicleStats['van'] ?? 0 }}</p>
                    <p class="text-[10px] text-brand-muted">Van</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Weekly Trend Chart -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-100 p-6 shadow-sm">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-black text-brand">Weekly Performance</h3>
                    <p class="text-xs text-brand-muted">Rides and revenue trend (7 days)</p>
                </div>
                <div class="flex items-center gap-4 text-xs">
                    <span class="flex items-center gap-2"><span class="w-3 h-3 bg-brand rounded-full"></span> Rides</span>
                    <span class="flex items-center gap-2"><span class="w-3 h-3 bg-accent rounded-full"></span> Revenue</span>
                </div>
            </div>
            <div id="weeklyChart" class="h-64"></div>
        </div>

        <!-- Regional Stats -->
        <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-black text-brand">Regional Distribution</h3>
                    <p class="text-xs text-brand-muted">Rides by city</p>
                </div>
            </div>
            <div class="space-y-4">
                @foreach($regionStats as $region)
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 bg-surface rounded-xl flex items-center justify-center text-brand font-bold text-xs">
                        {{ substr($region['region'], 0, 2) }}
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-sm font-bold text-brand">{{ $region['region'] }}</span>
                            <span class="text-xs font-bold text-brand-muted">{{ $region['rides'] }} rides</span>
                        </div>
                        <div class="h-2 bg-surface rounded-full overflow-hidden">
                            <div class="h-full bg-brand rounded-full" style="width: {{ ($region['rides'] / 500) * 100 }}%"></div>
                        </div>
                    </div>
                    <span class="text-xs font-bold text-green-600">₵{{ number_format($region['revenue']) }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Bottom Row -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Top Drivers -->
        <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-black text-brand">Top Drivers Today</h3>
                    <p class="text-xs text-brand-muted">Best performers by earnings</p>
                </div>
                <a href="{{ route('orchestrator.driver.management') }}" class="text-xs font-bold text-accent hover:underline">View All</a>
            </div>
            <div class="space-y-4">
                @forelse($topDrivers as $index => $driver)
                <div class="flex items-center gap-4 p-3 bg-surface rounded-xl">
                    <span class="w-8 h-8 rounded-xl flex items-center justify-center text-xs font-black {{ $index === 0 ? 'bg-accent text-white' : ($index === 1 ? 'bg-amber-400 text-white' : 'bg-gray-200 text-brand') }}">{{ $index + 1 }}</span>
                    <div class="flex-1">
                        <p class="text-sm font-bold text-brand">{{ $driver->driver_name }}</p>
                        <div class="flex items-center gap-2 text-xs text-brand-muted">
                            <span>{{ $driver->rides }} rides</span>
                            <span>•</span>
                            <span class="text-amber-500">★ {{ number_format($driver->rating, 1) }}</span>
                        </div>
                    </div>
                    <span class="text-sm font-black text-green-600">₵{{ number_format($driver->earnings) }}</span>
                </div>
                @empty
                <p class="text-sm text-brand-muted text-center py-4">No rides today</p>
                @endforelse
            </div>
        </div>

        <!-- Recent Rides -->
        <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-black text-brand">Recent Rides</h3>
                    <p class="text-xs text-brand-muted">Latest ride activity</p>
                </div>
                <a href="{{ route('orchestrator.orders') }}" class="text-xs font-bold text-accent hover:underline">View All</a>
            </div>
            <div class="space-y-3">
                @forelse($recentRides as $ride)
                <div class="flex items-center gap-4 p-3 rounded-xl hover:bg-surface transition-colors">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center {{ $ride->status === 'completed' ? 'bg-green-50 text-green-600' : ($ride->status === 'cancelled' ? 'bg-red-50 text-red-600' : 'bg-amber-50 text-amber-600') }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold text-brand truncate">{{ $ride->pickup_address ?? 'Unknown' }}</p>
                        <p class="text-xs text-brand-muted">{{ $ride->created_at->diffForHumans() }}</p>
                    </div>
                    <span class="text-xs font-bold {{ $ride->status === 'completed' ? 'text-green-600' : ($ride->status === 'cancelled' ? 'text-red-600' : 'text-amber-600') }}">
                        {{ ucfirst($ride->status) }}
                    </span>
                </div>
                @empty
                <p class="text-sm text-brand-muted text-center py-4">No recent rides</p>
                @endforelse
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-black text-brand">System Activity</h3>
                    <p class="text-xs text-brand-muted">Latest events log</p>
                </div>
                <button class="text-xs font-bold text-brand-muted hover:text-brand">Mark all read</button>
            </div>
            <div class="space-y-4">
                @forelse($recentActivity as $activity)
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-xl flex items-center justify-center shrink-0 @if($activity['type'] === 'ride') bg-blue-50 text-blue-600 @elseif($activity['type'] === 'driver') bg-green-50 text-green-600 @elseif($activity['type'] === 'payment') bg-amber-50 text-amber-600 @elseif($activity['type'] === 'alert') bg-red-50 text-red-600 @else bg-gray-50 text-gray-600 @endif">
                        @if($activity['type'] === 'ride')
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                        @elseif($activity['type'] === 'driver')
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        @elseif($activity['type'] === 'payment')
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        @else
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-bold text-brand truncate">{{ $activity['message'] }}</p>
                        <p class="text-[10px] text-brand-muted">{{ $activity['time'] }}</p>
                    </div>
                </div>
                @empty
                <p class="text-sm text-brand-muted text-center py-4">No recent activity</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Monthly Trend Chart -->
    <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="text-lg font-black text-brand">Monthly Revenue Trend</h3>
                <p class="text-xs text-brand-muted">Last 12 months performance</p>
            </div>
            <div class="flex items-center gap-4 text-xs">
                <span class="flex items-center gap-2"><span class="w-3 h-3 bg-brand rounded-full"></span> Revenue</span>
                <span class="flex items-center gap-2"><span class="w-3 h-3 bg-accent rounded-full"></span> Rides</span>
            </div>
        </div>
        <div id="monthlyChart" class="h-64"></div>
    </div>

    <!-- Tactical Density Map -->
    <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-accent/10 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                </div>
                <div>
                    <h3 class="text-lg font-black text-brand">Tactical Density Map</h3>
                    <p class="text-xs text-brand-muted">Real-time driver & ride positions</p>
                </div>
            </div>
            <div class="flex items-center gap-4">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 bg-blue-500 rounded-full"></span>
                    <span class="text-xs font-bold text-brand-muted">Available Driver</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 bg-orange-500 rounded-full"></span>
                    <span class="text-xs font-bold text-brand-muted">Busy Driver</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 bg-green-500 rounded-full animate-pulse"></span>
                    <span class="text-xs font-bold text-brand-muted">Pending Ride</span>
                </div>
                <a href="{{ route('orchestrator.operations_map') }}" class="text-xs text-accent font-bold hover:underline">Full Screen</a>
            </div>
        </div>
        <div id="tacticalMap" class="h-80 rounded-xl overflow-hidden bg-surface border border-gray-100"></div>
    </div>

    <!-- Quick Actions Footer -->
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
        <a href="{{ route('orchestrator.driver.documents') }}" class="bg-white rounded-2xl border border-gray-100 p-4 text-center hover:border-accent hover:shadow-md transition-all group">
            <div class="w-12 h-12 bg-amber-50 rounded-xl flex items-center justify-center mx-auto mb-3 group-hover:bg-amber-500 group-hover:text-white transition-all">
                <svg class="w-6 h-6 text-amber-600 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <p class="text-xs font-bold text-brand">Driver KYC</p>
            <p class="text-[10px] text-brand-muted mt-1">{{ $pendingActions['pending_drivers'] ?? 0 }} pending</p>
        </a>
        <a href="{{ route('orchestrator.support.tickets') }}" class="bg-white rounded-2xl border border-gray-100 p-4 text-center hover:border-accent hover:shadow-md transition-all group">
            <div class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center mx-auto mb-3 group-hover:bg-blue-500 group-hover:text-white transition-all">
                <svg class="w-6 h-6 text-blue-600 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            </div>
            <p class="text-xs font-bold text-brand">Support</p>
            <p class="text-[10px] text-brand-muted mt-1">View tickets</p>
        </a>
        <a href="{{ route('orchestrator.financials') }}" class="bg-white rounded-2xl border border-gray-100 p-4 text-center hover:border-accent hover:shadow-md transition-all group">
            <div class="w-12 h-12 bg-green-50 rounded-xl flex items-center justify-center mx-auto mb-3 group-hover:bg-green-500 group-hover:text-white transition-all">
                <svg class="w-6 h-6 text-green-600 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <p class="text-xs font-bold text-brand">Payouts</p>
            <p class="text-[10px] text-brand-muted mt-1">Manage</p>
        </a>
        <a href="{{ route('orchestrator.users') }}" class="bg-white rounded-2xl border border-gray-100 p-4 text-center hover:border-accent hover:shadow-md transition-all group">
            <div class="w-12 h-12 bg-purple-50 rounded-xl flex items-center justify-center mx-auto mb-3 group-hover:bg-purple-500 group-hover:text-white transition-all">
                <svg class="w-6 h-6 text-purple-600 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            </div>
            <p class="text-xs font-bold text-brand">Users</p>
            <p class="text-[10px] text-brand-muted mt-1">{{ $customerStats['total'] ?? 0 }} total</p>
        </a>
        <a href="{{ route('orchestrator.settings.assets') }}" class="bg-white rounded-2xl border border-gray-100 p-4 text-center hover:border-accent hover:shadow-md transition-all group">
            <div class="w-12 h-12 bg-brand/10 rounded-xl flex items-center justify-center mx-auto mb-3 group-hover:bg-brand group-hover:text-white transition-all">
                <svg class="w-6 h-6 text-brand group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </div>
            <p class="text-xs font-bold text-brand">Assets</p>
            <p class="text-[10px] text-brand-muted mt-1">Gallery</p>
        </a>
        <a href="{{ route('orchestrator.settings') }}" class="bg-white rounded-2xl border border-gray-100 p-4 text-center hover:border-accent hover:shadow-md transition-all group">
            <div class="w-12 h-12 bg-gray-50 rounded-xl flex items-center justify-center mx-auto mb-3 group-hover:bg-gray-500 group-hover:text-white transition-all">
                <svg class="w-6 h-6 text-gray-600 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
            <p class="text-xs font-bold text-brand">Settings</p>
            <p class="text-[10px] text-brand-muted mt-1">Configure</p>
        </a>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Weekly Chart
    const weeklyData = @json($weeklyTrend);
    const weeklyOptions = {
        series: [{
            name: 'Rides',
            data: weeklyData.map(d => d.rides)
        }, {
            name: 'Revenue',
            data: weeklyData.map(d => Math.round(d.revenue / 100))
        }],
        chart: {
            type: 'area',
            height: 250,
            toolbar: { show: false },
            fontFamily: 'inherit'
        },
        colors: ['#0A0A1A', '#F8B803'],
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.4,
                opacityTo: 0.1,
                stops: [0, 100]
            }
        },
        stroke: { curve: 'smooth', width: 2 },
        xaxis: {
            categories: weeklyData.map(d => d.date),
            labels: { style: { colors: '#6B7280', fontSize: '11px' } },
            axisBorder: { show: false },
            axisTicks: { show: false }
        },
        yaxis: {
            labels: { style: { colors: '#6B7280', fontSize: '11px' } }
        },
        grid: { borderColor: '#F3F4F6' },
        legend: { show: false }
    };
    new ApexCharts(document.querySelector('#weeklyChart'), weeklyOptions).render();

    // Monthly Chart
    const monthlyData = @json($monthlyTrend);
    const monthlyOptions = {
        series: [{
            name: 'Revenue',
            data: monthlyData.map(d => Math.round(d.revenue))
        }, {
            name: 'Rides',
            data: monthlyData.map(d => d.rides * 10)
        }],
        chart: {
            type: 'bar',
            height: 250,
            toolbar: { show: false },
            fontFamily: 'inherit'
        },
        colors: ['#0A0A1A', '#F8B803'],
        plotOptions: {
            bar: { borderRadius: 4, columnWidth: '50%' }
        },
        stroke: { show: true, width: 0 },
        xaxis: {
            categories: monthlyData.map(d => d.month),
            labels: { style: { colors: '#6B7280', fontSize: '11px' } },
            axisBorder: { show: false },
            axisTicks: { show: false }
        },
        yaxis: {
            labels: { style: { colors: '#6B7280', fontSize: '11px' } }
        },
        grid: { borderColor: '#F3F4F6' },
        legend: { show: false },
        dataLabels: { enabled: false }
    };
    new ApexCharts(document.querySelector('#monthlyChart'), monthlyOptions).render();

    // Tactical Density Map
    const mapData = @json($mapData);

    if (typeof L !== 'undefined' && (mapData.drivers.length > 0 || mapData.rides.length > 0)) {
        const map = L.map('tacticalMap').setView([5.6037, -0.1870], 11);

        L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
            attribution: '&copy; OpenStreetMap &copy; CARTO',
            maxZoom: 19
        }).addTo(map);

        mapData.drivers.forEach(driver => {
            if (driver.lat && driver.lng) {
                const color = driver.status === 'available' ? '#3B82F6' : '#F97316';
                const icon = L.divIcon({
                    className: 'driver-marker',
                    html: `<div style="width:14px;height:14px;background:${color};border-radius:50%;border:2px solid white;box-shadow:0 2px 6px rgba(0,0,0,0.3)"></div>`,
                    iconSize: [14, 14],
                    iconAnchor: [7, 7]
                });
                L.marker([driver.lat, driver.lng], { icon })
                    .bindPopup(`<div style="font-size:11px;font-weight:600">Driver<br><span style="color:${color};text-transform:capitalize">${driver.status}</span></div>`)
                    .addTo(map);
            }
        });

        mapData.rides.forEach(ride => {
            if (ride.lat && ride.lng) {
                const icon = L.divIcon({
                    className: 'ride-marker',
                    html: '<div style="width:12px;height:12px;background:#22C55E;border-radius:50%;border:2px solid white;box-shadow:0 0 12px rgba(34,197,94,0.6);animation:pulse 2s infinite"></div>',
                    iconSize: [12, 12],
                    iconAnchor: [6, 6]
                });
                L.marker([ride.lat, ride.lng], { icon })
                    .bindPopup(`<div style="font-size:11px;font-weight:600">Ride Request<br><span style="text-transform:capitalize">${ride.status}</span></div>`)
                    .addTo(map);
            }
        });
    } else {
        const el = document.getElementById('tacticalMap');
        if (el) {
            el.innerHTML = `
                <div class="flex flex-col items-center justify-center h-full text-brand-muted">
                    <svg class="w-16 h-16 mb-4 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                    <p class="text-sm font-bold">No active drivers or rides to display</p>
                    <p class="text-xs mt-1">Map will update when drivers come online</p>
                </div>
            `;
        }
    }
});
</script>

<style>
[x-cloak] { display: none !important; }
.driver-marker, .ride-marker { background: transparent !important; border: none !important; }
@keyframes pulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.5; } }
</style>
@endsection