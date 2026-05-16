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
            <div class="flex items-center gap-3 mb-2">
                <span class="px-3 py-1 bg-green-100 text-green-700 text-xs font-bold rounded-full flex items-center gap-1">
                    <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span> LIVE
                </span>
                <span class="text-xs text-brand-muted font-medium">{{ now()->format('l, F j, Y • g:i A') }}</span>
            </div>
            <h1 class="text-3xl font-black text-brand tracking-tight">Super Admin Dashboard</h1>
            <p class="text-brand-muted font-medium mt-1">Welcome back, {{ $admin->name ?? 'Administrator' }}</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('orchestrator.drivers') }}" class="px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm font-bold text-brand hover:border-accent hover:bg-accent/5 transition-all flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Drivers
            </a>
            <a href="{{ route('orchestrator.orders') }}" class="px-4 py-2.5 bg-brand text-white rounded-xl text-sm font-bold hover:bg-brand-light transition-all flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                Rides
            </a>
        </div>
    </div>

    <!-- System Health Bar -->
    <div class="bg-white rounded-2xl border border-gray-100 p-4 flex items-center justify-between shadow-sm">
        <div class="flex items-center gap-6 flex-wrap">
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
            <span class="text-brand-muted">Connections: <strong class="text-brand">{{ $systemHealth['active_connections'] ?? 0 }}</strong></span>
            <span class="text-brand-muted">Server Load: <strong class="text-brand">{{ $systemHealth['server_load'] ?? '0%' }}</strong></span>
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
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Weekly Trend Chart -->
        <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-black text-brand">Weekly Performance</h3>
                    <p class="text-xs text-brand-muted">Last 7 days</p>
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
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Top Drivers -->
        <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-black text-brand">Top Drivers Today</h3>
                    <p class="text-xs text-brand-muted">Best performers</p>
                </div>
            </div>
            <div class="space-y-4">
                @forelse($topDrivers as $index => $driver)
                <div class="flex items-center gap-4 p-3 bg-surface rounded-xl">
                    <span class="w-8 h-8 rounded-xl flex items-center justify-center text-xs font-black {{ $index === 0 ? 'bg-accent text-white' : ($index === 1 ? 'bg-amber-400 text-white' : 'bg-gray-200 text-brand') }}">{{ $index + 1 }}</span>
                    <div class="flex-1">
                        <p class="text-sm font-bold text-brand">{{ $driver->driver_name ?? 'Unknown' }}</p>
                        <div class="flex items-center gap-2 text-xs text-brand-muted">
                            <span>{{ $driver->rides ?? 0 }} rides</span>
                            <span>•</span>
                            <span class="text-amber-500">★ {{ number_format($driver->rating ?? 0, 1) }}</span>
                        </div>
                    </div>
                    <span class="text-sm font-black text-green-600">₵{{ number_format($driver->earnings ?? 0) }}</span>
                </div>
                @empty
                <p class="text-sm text-brand-muted text-center py-4">No rides completed today</p>
                @endforelse
            </div>
        </div>

        <!-- Recent Rides -->
        <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-black text-brand">Recent Rides</h3>
                    <p class="text-xs text-brand-muted">Latest activity</p>
                </div>
            </div>
            <div class="space-y-3">
                @forelse($recentRides as $ride)
                <div class="flex items-center gap-4 p-3 rounded-xl hover:bg-surface transition-colors">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center {{ $ride->status === 'completed' ? 'bg-green-50 text-green-600' : ($ride->status === 'cancelled' ? 'bg-red-50 text-red-600' : 'bg-amber-50 text-amber-600') }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold text-brand truncate">{{ $ride->pickup_address ?? 'Unknown Location' }}</p>
                        <p class="text-xs text-brand-muted">{{ !empty($ride->created_at) ? \Carbon\Carbon::parse($ride->created_at)->diffForHumans() : 'Unknown time' }}</p>
                    </div>
                    <span class="text-xs font-bold {{ $ride->status === 'completed' ? 'text-green-600' : ($ride->status === 'cancelled' ? 'text-red-600' : 'text-amber-600') }}">
                        {{ ucfirst($ride->status ?? 'pending') }}
                    </span>
                </div>
                @empty
                <p class="text-sm text-brand-muted text-center py-4">No recent rides</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Pending Actions -->
    <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="text-lg font-black text-brand">Pending Actions</h3>
                <p class="text-xs text-brand-muted">Items requiring attention</p>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <a href="{{ route('orchestrator.driver.documents') }}" class="p-4 bg-amber-50 rounded-xl hover:bg-amber-100 transition-colors">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-amber-100 rounded-xl flex items-center justify-center text-amber-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <div>
                        <p class="text-lg font-black text-brand">{{ $pendingActions['pending_drivers'] ?? 0 }}</p>
                        <p class="text-xs text-brand-muted">Driver Verifications</p>
                    </div>
                </div>
            </a>
            <a href="{{ route('orchestrator.support') }}" class="p-4 bg-blue-50 rounded-xl hover:bg-blue-100 transition-colors">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center text-blue-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    </div>
                    <div>
                        <p class="text-lg font-black text-brand">0</p>
                        <p class="text-xs text-brand-muted">Support Tickets</p>
                    </div>
                </div>
            </a>
            <a href="{{ route('orchestrator.financials') }}" class="p-4 bg-green-50 rounded-xl hover:bg-green-100 transition-colors">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center text-green-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <p class="text-lg font-black text-brand">0</p>
                        <p class="text-xs text-brand-muted">Pending Payouts</p>
                    </div>
                </div>
            </a>
            <a href="{{ route('orchestrator.users') }}" class="p-4 bg-purple-50 rounded-xl hover:bg-purple-100 transition-colors">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-purple-100 rounded-xl flex items-center justify-center text-purple-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    </div>
                    <div>
                        <p class="text-lg font-black text-brand">{{ $customerStats['total'] ?? 0 }}</p>
                        <p class="text-xs text-brand-muted">Total Customers</p>
                    </div>
                </div>
            </a>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Weekly Chart
    const weeklyData = @json($weeklyTrend ?? []);
    const weeklyOptions = {
        series: [{
            name: 'Rides',
            data: weeklyData.map(d => d.rides)
        }],
        chart: {
            type: 'area',
            height: 250,
            toolbar: { show: false },
            fontFamily: 'inherit'
        },
        colors: ['#0A0A1A'],
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
            labels: { style: { colors: '#6B7280', fontSize: '11px' } }
        },
        yaxis: {
            labels: { style: { colors: '#6B7280', fontSize: '11px' } }
        },
        grid: { borderColor: '#F3F4F6' },
        legend: { show: false }
    };
    new ApexCharts(document.querySelector('#weeklyChart'), weeklyOptions).render();
});
</script>

<style>
[x-cloak] { display: none !important; }
</style>
@endsection