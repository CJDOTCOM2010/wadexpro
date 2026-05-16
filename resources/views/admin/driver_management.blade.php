@extends('admin.layout')
@section('title', 'Driver Registry')
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

<div x-data="driverManager()" class="max-w-6xl mx-auto">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-black text-brand tracking-tight">Driver Registry</h2>
            <p class="text-sm text-brand-muted font-medium mt-0.5">Manage active drivers, track performance, and handle suspensions.</p>
        </div>
        <a href="{{ route('orchestrator.driver.documents') }}" class="px-5 py-2.5 bg-brand text-white rounded-lg text-xs font-bold hover:bg-brand-light transition-colors flex items-center gap-2 shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Pending KYC
            @if(($stats['pending'] ?? 0) > 0)
            <span class="bg-red-500 text-white text-[9px] font-bold px-1.5 py-0.5 rounded-full">{{ $stats['pending'] }}</span>
            @endif
        </a>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 mb-6">
        <div class="bg-white border border-gray-100 rounded-xl p-4">
            <p class="text-[10px] font-bold text-brand-muted uppercase tracking-wider">Total Drivers</p>
            <p class="text-2xl font-black text-brand mt-1">{{ number_format($stats['total'] ?? 0) }}</p>
        </div>
        <div class="bg-white border border-gray-100 rounded-xl p-4">
            <p class="text-[10px] font-bold text-brand-muted uppercase tracking-wider">Active</p>
            <p class="text-2xl font-black text-green-600 mt-1">{{ number_format($stats['active'] ?? 0) }}</p>
        </div>
        <div class="bg-white border border-gray-100 rounded-xl p-4">
            <p class="text-[10px] font-bold text-brand-muted uppercase tracking-wider">Pending Review</p>
            <p class="text-2xl font-black text-amber-600 mt-1">{{ number_format($stats['pending'] ?? 0) }}</p>
        </div>
        <div class="bg-white border border-gray-100 rounded-xl p-4">
            <p class="text-[10px] font-bold text-brand-muted uppercase tracking-wider">Suspended</p>
            <p class="text-2xl font-black text-red-500 mt-1">{{ number_format($stats['suspended'] ?? 0) }}</p>
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-white border border-gray-100 rounded-xl overflow-hidden">
        {{-- Toolbar --}}
        <div class="px-5 py-4 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-surface/20">
            <form action="{{ route('orchestrator.driver.management') }}" method="GET" class="flex items-center gap-3 flex-1">
                <div class="relative flex-1 max-w-xs">
                    <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name, email, phone..." class="w-full bg-white border border-gray-200 rounded-lg pl-9 pr-3 py-2 text-sm outline-none focus:ring-2 focus:ring-accent/20 transition-shadow">
                </div>
                <select name="status" onchange="this.form.submit()" class="bg-white border border-gray-200 rounded-lg px-3 py-2 text-xs font-bold outline-none focus:ring-2 focus:ring-accent/20">
                    <option value="">All Statuses</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="pending_verification" {{ request('status') === 'pending_verification' ? 'selected' : '' }}>Pending Review</option>
                    <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Suspended</option>
                </select>
                @if(request('search'))<input type="hidden" name="search" value="{{ request('search') }}">@endif
                @if(request('search') || request('status'))
                <a href="{{ route('orchestrator.driver.management') }}" class="text-xs font-bold text-brand-muted hover:text-brand shrink-0">Clear</a>
                @endif
            </form>
        </div>

        {{-- List --}}
        <div class="divide-y divide-gray-50">
            @forelse($drivers as $driver)
            <div class="px-5 py-4 hover:bg-surface/20 transition-colors">
                <div class="flex items-start gap-4">
                    {{-- Avatar --}}
                    <div class="w-10 h-10 rounded-lg flex items-center justify-center shrink-0 font-bold text-sm {{ $driver->status === 'active' ? 'bg-green-50 text-green-700' : ($driver->status === 'suspended' ? 'bg-red-50 text-red-600' : 'bg-amber-50 text-amber-600') }}">
                        {{ substr($driver->user->name ?? 'D', 0, 2) }}
                    </div>
                    <div class="flex-1 min-w-0 grid grid-cols-1 lg:grid-cols-4 gap-2 lg:gap-4">
                        {{-- Driver info --}}
                        <div>
                            <p class="text-sm font-bold text-brand">{{ $driver->user->name ?? 'Unknown' }}</p>
                            <p class="text-[11px] text-brand-muted font-mono">{{ $driver->user->phone ?? 'N/A' }}</p>
                        </div>
                        {{-- Vehicle --}}
                        <div>
                            @if($driver->activeVehicle)
                            <p class="text-xs font-bold text-brand">{{ $driver->activeVehicle->make }} {{ $driver->activeVehicle->model }}</p>
                            <p class="text-[10px] text-brand-muted font-mono">{{ $driver->activeVehicle->plate_number ?? $driver->activeVehicle->license_plate }} · {{ $driver->activeVehicle->color }}</p>
                            @else
                            <p class="text-xs text-brand-muted italic">No active vehicle</p>
                            @endif
                        </div>
                        {{-- Performance --}}
                        <div>
                            <div class="flex items-center gap-1.5 mb-0.5">
                                <svg class="w-3 h-3 text-accent" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                <span class="text-sm font-bold text-brand">{{ number_format($driver->rating, 1) }}</span>
                                <span class="text-[10px] text-brand-muted">· {{ number_format($driver->total_deliveries) }} rides</span>
                            </div>
                        </div>
                        {{-- Status + Actions --}}
                        <div class="flex items-center justify-between lg:justify-end gap-3">
                            <span class="px-2 py-0.5 text-[9px] font-bold rounded
                                @if($driver->status === 'active') bg-green-50 text-green-700
                                @elseif($driver->status === 'suspended') bg-red-50 text-red-600
                                @else bg-amber-50 text-amber-600 @endif">
                                {{ ucfirst(str_replace('_', ' ', $driver->status)) }}
                            </span>
                            <div class="flex items-center gap-1">
                                @if($driver->status === 'active')
                                <button @click="confirmSuspend('{{ $driver->id }}', '{{ addslashes($driver->user->name ?? '') }}')" class="px-2.5 py-1.5 text-[10px] font-bold text-red-500 hover:bg-red-50 rounded-lg transition-colors">Suspend</button>
                                @elseif($driver->status === 'suspended')
                                <form action="{{ route('orchestrator.driver.activate', $driver->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="px-2.5 py-1.5 text-[10px] font-bold text-green-600 hover:bg-green-50 rounded-lg transition-colors">Activate</button>
                                </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="flex flex-col items-center justify-center py-16 text-brand-muted">
                <svg class="w-14 h-14 mb-3 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                <p class="text-sm font-bold">No drivers found</p>
                <p class="text-xs mt-1">Try adjusting your search or filters.</p>
            </div>
            @endforelse
        </div>

        @if($drivers->hasPages())
        <div class="px-5 py-3 border-t border-gray-100 bg-surface/20">{{ $drivers->links() }}</div>
        @endif
    </div>

    {{-- Suspend Modal --}}
    <div x-show="showSuspend" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-brand/50 backdrop-blur-sm" @click="showSuspend = false"></div>
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md relative z-10" @click.outside="showSuspend = false">
            <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 bg-red-50 rounded-lg flex items-center justify-center text-red-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-brand">Suspend Driver</h3>
                        <p class="text-xs text-brand-muted">This will prevent <strong x-text="suspendLabel"></strong> from accepting rides.</p>
                    </div>
                </div>
                <button @click="showSuspend = false" class="w-7 h-7 bg-surface rounded-lg flex items-center justify-center text-brand-muted hover:text-brand transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form method="POST" :action="suspendUrl" class="p-6 space-y-4">
                @csrf
                <div>
                    <label class="text-[10px] font-bold text-brand-muted uppercase tracking-wider mb-1.5 block">Reason for Suspension</label>
                    <textarea name="reason" rows="3" required placeholder="Explain why this driver is being suspended..." class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium outline-none focus:ring-2 focus:ring-red-300 transition-shadow resize-none"></textarea>
                </div>
                <div class="flex justify-end gap-2 pt-2 border-t border-gray-100">
                    <button type="button" @click="showSuspend = false" class="px-4 py-2 text-xs font-bold text-brand-muted hover:text-brand transition-colors">Cancel</button>
                    <button type="submit" class="px-5 py-2 bg-red-600 text-white rounded-lg text-xs font-bold hover:bg-red-700 transition-colors">Suspend Driver</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function driverManager() {
    return {
        showSuspend: false,
        suspendId: '',
        suspendLabel: '',
        suspendUrl: '',
        confirmSuspend(id, name) {
            this.suspendId = id;
            this.suspendLabel = name;
            this.suspendUrl = '{{ url(env('ORCHESTRATOR_PATH', 'orchestrator').'/driver-management') }}/' + id + '/suspend';
            this.showSuspend = true;
        }
    };
}
</script>
<style>[x-cloak] { display: none !important; }</style>
@endsection