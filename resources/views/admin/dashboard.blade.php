@extends('admin.layout')
@section('title', 'Orchestrator Command Center')

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
@endpush

@section('content')
<div class="space-y-8">
    <!-- Header Section -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <span class="px-3 py-1 bg-green-100 text-green-700 text-xs font-bold rounded-full flex items-center gap-1">
                    <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span> LIVE
                </span>
                <span class="text-xs text-brand-muted font-medium">{{ now()->format('l, F j, Y • g:i A') }}</span>
            </div>
            <h1 class="text-3xl font-black text-brand tracking-tight">Command Center</h1>
            <p class="text-brand-muted font-medium mt-1">Welcome back, {{ $admin->name ?? 'Administrator' }} • {{ ucfirst($admin->level ?? 'Admin') }}</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('orchestrator.drivers') }}" class="px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm font-bold text-brand hover:border-accent hover:bg-accent/5 transition-all flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Manage Drivers
            </a>
            <a href="{{ route('orchestrator.orders') }}" class="px-4 py-2.5 bg-brand text-white rounded-xl text-sm font-bold hover:bg-brand-light transition-all flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                View Orders
            </a>
        </div>
    </div>

    <!-- System Health Bar -->
    <div class="bg-white rounded-2xl border border-gray-100 p-4 flex items-center justify-between shadow-sm">
        <div class="flex items-center gap-6">
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 bg-green-500 rounded-full"></span>
                <span class="text-sm font-bold text-brand">API</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 bg-green-500 rounded-full"></span>
                <span class="text-sm font-bold text-brand">Database</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 bg-green-500 rounded-full"></span>
                <span class="text-sm font-bold text-brand">Cache</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 bg-green-500 rounded-full"></span>
                <span class="text-sm font-bold text-brand">Queue</span>
            </div>
        </div>
        <div class="flex items-center gap-4 text-sm">
            <span class="text-brand-muted">Active Connections: <strong class="text-brand">{{ $systemHealth['active_connections'] ?? 0 }}</strong></span>
            <span class="text-brand-muted">Server Load: <strong class="text-brand">{{ $systemHealth['server_load'] ?? '0%' }}</strong></span>
        </div>
    </div>

    <!-- Main Metrics Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Drivers Card -->
        <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm hover:shadow-lg transition-all">
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

        <!-- Customers Card -->
        <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm hover:shadow-lg transition-all">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-brand/5 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </div>
                <span class="px-2 py-1 bg-blue-100 text-blue-700 text-xs font-bold rounded-full">+{{ $customerStats['new_today'] ?? 0 }} Today</span>
            </div>
            <p class="text-xs font-bold text-brand-muted uppercase tracking-wider mb-1">Customer Base</p>
            <div class="flex items-baseline gap-2">
                <span class="text-3xl font-black text-brand">{{ $customerStats['total'] ?? 0 }}</span>
                <span class="text-sm text-brand-muted font-medium">Total Users</span>
            </div>
            <div class="mt-4 pt-4 border-t border-gray-50 flex items-center justify-between text-sm">
                <div class="text-center">
                    <p class="text-xs text-brand-muted">Active (30d)</p>
                    <p class="text-lg font-black text-brand">{{ $customerStats['active_30d'] ?? 0 }}</p>
                </div>
                <div class="text-center">
                    <p class="text-xs text-brand-muted">Verified</p>
                    <p class="text-lg font-black text-green-600">{{ $customerStats['verified'] ?? 0 }}</p>
                </div>
                <div class="text-center">
                    <p class="text-xs text-brand-muted">New Today</p>
                    <p class="text-lg font-black text-accent">{{ $customerStats['new_today'] ?? 0 }}</p>
                </div>
            </div>
        </div>

        <!-- Rides Card -->
        <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm hover:shadow-lg transition-all">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                </div>
                <span class="px-2 py-1 bg-amber-100 text-amber-700 text-xs font-bold rounded-full">{{ $rideStats['active'] ?? 0 }} Active</span>
            </div>
            <p class="text-xs font-bold text-brand-muted uppercase tracking-wider mb-1">Ride Requests</p>
            <div class="flex items-baseline gap-2">
                <span class="text-3xl font-black text-brand">{{ $rideStats['today'] ?? 0 }}</span>
                <span class="text-sm text-brand-muted font-medium">Today</span>
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
                    <p class="text-xs text-brand-muted">Yesterday</p>
                    <p class="text-lg font-black text-brand">{{ $rideStats['yesterday'] ?? 0 }}</p>
                </div>
            </div>
        </div>

        <!-- Revenue Card -->
        <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm hover:shadow-lg transition-all">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                @php
                    $growth = $revenueStats['growth'] ?? 0;
                    $isPositive = $growth >= 0;
                @endphp
                <span class="px-2 py-1 {{ $isPositive ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }} text-xs font-bold rounded-full">
                    {{ $isPositive ? '+' : '' }}{{ number_format($growth, 1) }}%
                </span>
            </div>
            <p class="text-xs font-bold text-brand-muted uppercase tracking-wider mb-1">Today's Revenue</p>
            <div class="flex items-baseline gap-2">
                <span class="text-3xl font-black text-brand">GHS {{ number_format($revenueStats['today'] ?? 0, 2) }}</span>
            </div>
            <div class="mt-4 pt-4 border-t border-gray-50 flex items-center justify-between text-sm">
                <div class="text-center">
                    <p class="text-xs text-brand-muted">Yesterday</p>
                    <p class="text-lg font-black text-brand">GHS {{ number_format($revenueStats['yesterday'] ?? 0, 0) }}</p>
                </div>
                <div class="text-center">
                    <p class="text-xs text-brand-muted">This Month</p>
                    <p class="text-lg font-black text-green-600">GHS {{ number_format($revenueStats['this_month'] ?? 0, 0) }}</p>
                </div>
                <div class="text-center">
                    <p class="text-xs text-brand-muted">Growth</p>
                    <p class="text-lg font-black {{ $isPositive ? 'text-green-600' : 'text-red-500' }}">{{ $isPositive ? '+' : '' }}{{ number_format($growth, 1) }}%</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Secondary Row: Charts & Tables -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Weekly Trend Chart -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-100 p-6 shadow-sm">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-black text-brand">Weekly Performance</h3>
                <div class="flex items-center gap-2">
                    <span class="flex items-center gap-1 text-xs font-bold text-brand-muted">
                        <span class="w-3 h-3 bg-accent rounded"></span> Rides
                    </span>
                    <span class="flex items-center gap-1 text-xs font-bold text-brand-muted">
                        <span class="w-3 h-3 bg-green-500 rounded"></span> Revenue
                    </span>
                </div>
            </div>
            <div id="weeklyChart" class="h-64"></div>
        </div>

        <!-- Top Drivers -->
        <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-black text-brand">Top Performers</h3>
                <a href="{{ route('orchestrator.driver.management') }}" class="text-xs text-accent font-bold hover:underline">View All</a>
            </div>
            <div class="space-y-4">
                @forelse($topDrivers as $index => $driver)
                    <div class="flex items-center gap-3 p-3 bg-surface rounded-xl">
                        <div class="w-8 h-8 rounded-full {{ $index === 0 ? 'bg-amber-100 text-amber-600' : ($index === 1 ? 'bg-gray-100 text-gray-600' : ($index === 2 ? 'bg-orange-100 text-orange-600' : 'bg-gray-50 text-gray-400')) }} flex items-center justify-center text-xs font-black">
                            {{ $index + 1 }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-bold text-brand truncate">{{ $driver->driver_name }}</p>
                            <p class="text-xs text-brand-muted">{{ $driver->rides }} rides • GHS {{ number_format($driver->earnings, 0) }}</p>
                        </div>
                        <div class="text-right">
                            <div class="flex items-center gap-1 text-amber-500">
                                <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                <span class="text-sm font-bold">{{ number_format($driver->rating, 1) }}</span>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-brand-muted text-center py-4">No data available yet</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Third Row: Recent Activity & Regions -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Rides -->
        <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-black text-brand">Recent Rides</h3>
                <a href="{{ route('orchestrator.orders') }}" class="text-xs text-accent font-bold hover:underline">View All</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-100">
                            <th class="text-left text-xs font-bold text-brand-muted uppercase tracking-wider py-2">ID</th>
                            <th class="text-left text-xs font-bold text-brand-muted uppercase tracking-wider py-2">Customer</th>
                            <th class="text-left text-xs font-bold text-brand-muted uppercase tracking-wider py-2">Route</th>
                            <th class="text-left text-xs font-bold text-brand-muted uppercase tracking-wider py-2">Status</th>
                            <th class="text-left text-xs font-bold text-brand-muted uppercase tracking-wider py-2">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentRides->take(5) as $ride)
                            <tr class="border-b border-gray-50 hover:bg-surface/50">
                                <td class="py-3 text-xs font-mono text-brand-muted">{{ substr($ride->id, 0, 8) }}</td>
                                <td class="py-3 text-sm font-medium text-brand">{{ $ride->customer->name ?? 'N/A' }}</td>
                                <td class="py-3 text-xs text-brand-muted max-w-[150px] truncate">{{ $ride->pickup_address ?? 'N/A' }}</td>
                                <td class="py-3">
                                    <span class="px-2 py-1 text-[10px] font-bold rounded-full 
                                        {{ $ride->status === 'completed' ? 'bg-green-100 text-green-700' : 
                                           ($ride->status === 'cancelled' ? 'bg-red-100 text-red-700' : 
                                           ($ride->status === 'in_progress' ? 'bg-blue-100 text-blue-700' : 'bg-amber-100 text-amber-700')) }}">
                                        {{ ucfirst($ride->status ?? 'pending') }}
                                    </span>
                                </td>
                                <td class="py-3 text-sm font-bold text-brand">GHS {{ number_format($ride->final_price ?? 0, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-4 text-center text-brand-muted text-sm">No recent rides</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Regional Stats -->
        <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-black text-brand">Regional Distribution</h3>
            </div>
            <div class="space-y-4">
                @forelse($regionStats as $region)
                    @php $total = array_sum(array_column($regionStats, 'rides')); $percent = $total > 0 ? ($region['rides'] / $total) * 100 : 0; @endphp
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-bold text-brand">{{ $region['region'] }}</span>
                            <span class="text-xs text-brand-muted">{{ $region['rides'] }} rides • GHS {{ number_format($region['revenue'], 0) }}</span>
                        </div>
                        <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                            <div class="h-full bg-gradient-to-r from-accent to-amber-500 rounded-full" style="width: {{ $percent }}%"></div>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-brand-muted text-center py-4">No regional data available</p>
                @endforelse
            </div>
        </div>
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

    <!-- Quick Actions Panel -->
    <div class="bg-gradient-to-r from-brand to-brand-light rounded-2xl p-6 text-white">
        <h3 class="text-lg font-black mb-4">Quick Actions</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
            <a href="{{ route('orchestrator.driver.management') }}" class="bg-white/10 hover:bg-white/20 rounded-xl p-4 text-center transition-all">
                <svg class="w-6 h-6 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                <span class="text-xs font-bold">Add Driver</span>
            </a>
            <a href="{{ route('orchestrator.users') }}" class="bg-white/10 hover:bg-white/20 rounded-xl p-4 text-center transition-all">
                <svg class="w-6 h-6 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                <span class="text-xs font-bold">Add User</span>
            </a>
            <a href="{{ route('orchestrator.marketing.promos') }}" class="bg-white/10 hover:bg-white/20 rounded-xl p-4 text-center transition-all">
                <svg class="w-6 h-6 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                <span class="text-xs font-bold">Create Promo</span>
            </a>
            <a href="{{ route('orchestrator.settings') }}" class="bg-white/10 hover:bg-white/20 rounded-xl p-4 text-center transition-all">
                <svg class="w-6 h-6 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/></svg>
                <span class="text-xs font-bold">Settings</span>
            </a>
            <a href="{{ route('orchestrator.analytics') }}" class="bg-white/10 hover:bg-white/20 rounded-xl p-4 text-center transition-all">
                <svg class="w-6 h-6 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                <span class="text-xs font-bold">Analytics</span>
            </a>
            <a href="{{ route('orchestrator.infrastructure') }}" class="bg-white/10 hover:bg-white/20 rounded-xl p-4 text-center transition-all">
                <svg class="w-6 h-6 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"/></svg>
                <span class="text-xs font-bold">System</span>
            </a>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Weekly Chart
    const weeklyData = @json($weeklyTrend);
    
    if (weeklyData && weeklyData.length > 0) {
        const ridesData = weeklyData.map(d => d.rides);
        const revenueData = weeklyData.map(d => d.revenue / 100); // Scale down for visualization
        const labels = weeklyData.map(d => d.date);

        const options = {
            series: [
                { name: 'Rides', data: ridesData },
                { name: 'Revenue', data: revenueData }
            ],
            chart: {
                type: 'area',
                height: 250,
                toolbar: { show: false },
                fontFamily: 'Outfit, sans-serif'
            },
            colors: ['#F8B803', '#10B981'],
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.4,
                    opacityTo: 0.05,
                    stops: [0, 100]
                }
            },
            stroke: { curve: 'smooth', width: 2 },
            xaxis: {
                categories: labels,
                labels: { style: { colors: '#6B6B6B', fontSize: '11px' } },
                axisBorder: { show: false },
                axisTicks: { show: false }
            },
            yaxis: {
                labels: { style: { colors: '#6B6B6B', fontSize: '11px' } }
            },
            grid: {
                borderColor: '#F3F4F6',
                strokeDashArray: 4
            },
            legend: { show: false }
        };

        const chart = new ApexCharts(document.querySelector('#weeklyChart'), options);
        chart.render();
    }

    // Tactical Density Map - Initialize when Leaflet is ready
    function initMap() {
        const mapData = @json($mapData);
        const mapContainer = document.getElementById('tacticalMap');
        
        if (!mapContainer) return;
        
        // Wait for Leaflet to be available
        if (typeof L === 'undefined') {
            setTimeout(initMap, 100);
            return;
        }
        
        // Initialize map - center on Ghana (Accra)
        const map = L.map('tacticalMap').setView([5.6037, -0.1870], 11);
        
        L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
            attribution: '&copy; OpenStreetMap &copy; CARTO',
            maxZoom: 19
        }).addTo(map);

        // Add markers only if data exists
        if (mapData.drivers && mapData.drivers.length > 0) {
            mapData.drivers.forEach(driver => {
                if (driver.lat && driver.lng) {
                    const icon = driver.status === 'available' 
                        ? L.divIcon({ className: 'driver-marker', html: '<div class="w-4 h-4 bg-blue-500 rounded-full border-2 border-white shadow-lg"></div>', iconSize: [16, 16] })
                        : L.divIcon({ className: 'driver-marker', html: '<div class="w-4 h-4 bg-orange-500 rounded-full border-2 border-white shadow-lg"></div>', iconSize: [16, 16] });
                    
                    L.marker([driver.lat, driver.lng], { icon })
                        .bindPopup(`<div class="text-xs"><strong>Driver</strong><br>Status: ${driver.status}</div>`)
                        .addTo(map);
                }
            });
        }

        if (mapData.rides && mapData.rides.length > 0) {
            mapData.rides.forEach(ride => {
                if (ride.lat && ride.lng) {
                    const icon = L.divIcon({ 
                        className: 'ride-marker', 
                        html: '<div class="w-3 h-3 bg-green-500 rounded-full border border-white shadow-lg animate-pulse"></div>', 
                        iconSize: [12, 12] 
                    });
                    
                    L.marker([ride.lat, ride.lng], { icon })
                        .bindPopup(`<div class="text-xs"><strong>Ride Request</strong><br>${ride.status || 'pending'}<br><span class="text-gray-500">${ride.address ? ride.address.substring(0, 30) + '...' : 'Location'}</span></div>`)
                        .addTo(map);
                }
            });
        }

        // Show message if no data
        if ((!mapData.drivers || mapData.drivers.length === 0) && (!mapData.rides || mapData.rides.length === 0)) {
            mapContainer.innerHTML = `
                <div class="flex flex-col items-center justify-center h-full text-brand-muted">
                    <svg class="w-16 h-16 mb-4 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                    <p class="text-sm font-bold">No active drivers or rides to display</p>
                    <p class="text-xs mt-1">Map will update when drivers come online</p>
                </div>
            `;
        }
    }

    // Initialize map after a short delay to ensure Leaflet is loaded
    setTimeout(initMap, 500);
});
</script>

<style>
.driver-marker, .ride-marker {
    background: transparent !important;
    border: none !important;
}
</style>
@endsection