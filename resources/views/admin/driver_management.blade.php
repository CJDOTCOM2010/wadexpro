@extends('admin.layout')
@section('title', 'Driver Registry')
@section('content')

<!-- Error Alert -->
@if(session('error'))
<div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl flex items-center gap-3">
    <svg class="w-5 h-5 text-red-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
    <p class="text-sm font-medium text-red-700">{{ session('error') }}</p>
</div>
@endif

<div class="mb-8 flex items-center justify-between">
    <div>
        <h2 class="text-2xl font-black text-brand tracking-tight">Driver Registry</h2>
        <p class="text-brand-muted font-medium mt-1">Manage active drivers, track performance, and handle suspensions.</p>
    </div>
    <div class="flex gap-4">
        <a href="{{ route('orchestrator.driver.documents') }}" class="px-6 py-3 bg-brand text-white font-bold rounded-xl hover:bg-brand-light transition shadow-lg shadow-brand/20 flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Review Pending KYC
            @if(($stats['pending'] ?? 0) > 0)
                <span class="bg-red-500 text-white text-[10px] font-black px-2 py-0.5 rounded-full ml-1">{{ $stats['pending'] }}</span>
            @endif
        </a>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
    <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-4 hover:shadow-md transition-all group">
        <div class="w-14 h-14 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0 group-hover:bg-amber-500 group-hover:text-white transition-all">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div>
            <p class="text-[10px] font-black text-brand-muted uppercase tracking-widest">Pending Review</p>
            <p class="text-2xl font-black text-brand mt-0.5">{{ number_format($stats['pending'] ?? 0) }}</p>
        </div>
    </div>
    <div class="bg-white p-5 rounded-2xl border border-red-100 shadow-sm flex items-center gap-4 hover:shadow-md transition-all group">
        <div class="w-14 h-14 rounded-2xl bg-red-50 text-red-600 flex items-center justify-center shrink-0 group-hover:bg-red-500 group-hover:text-white transition-all">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
        </div>
        <div>
            <p class="text-[10px] font-black text-brand-muted uppercase tracking-widest">Suspended</p>
            <p class="text-2xl font-black text-brand mt-0.5">{{ number_format($stats['suspended'] ?? 0) }}</p>
        </div>
    </div>
</div>

<div class="bg-white rounded-lg border border-gray-100 shadow-sm overflow-hidden">
    <div class="p-6 border-b border-gray-50 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <form action="{{ route('orchestrator.driver.management') }}" method="GET" class="relative w-full md:w-96 flex">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search driver name, email or phone..." class="w-full bg-surface border border-gray-100 rounded-l-lg pl-10 pr-4 py-2 text-sm font-medium outline-none focus:ring-2 focus:ring-brand/20 transition-all">
            <svg class="w-4 h-4 text-gray-400 absolute left-4 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <button type="submit" class="bg-brand text-white px-4 rounded-r-lg font-bold text-sm">Search</button>
        </form>
        <div class="flex gap-2">
            <form action="{{ route('orchestrator.driver.management') }}" method="GET">
                <select name="status" onchange="this.form.submit()" class="bg-surface border border-gray-100 rounded-lg px-4 py-2 text-sm font-bold text-brand outline-none focus:ring-2 focus:ring-brand/20 cursor-pointer">
                    <option value="">All Statuses</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active Only</option>
                    <option value="pending_verification" {{ request('status') === 'pending_verification' ? 'selected' : '' }}>Pending Review</option>
                    <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Suspended</option>
                </select>
                @if(request('search')) <input type="hidden" name="search" value="{{ request('search') }}"> @endif
            </form>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-surface/30 border-b border-gray-50">
                    <th class="px-6 py-4 text-[10px] font-black text-brand-muted uppercase tracking-widest whitespace-nowrap">Driver</th>
                    <th class="px-6 py-4 text-[10px] font-black text-brand-muted uppercase tracking-widest whitespace-nowrap">Vehicle Info</th>
                    <th class="px-6 py-4 text-[10px] font-black text-brand-muted uppercase tracking-widest whitespace-nowrap">Performance</th>
                    <th class="px-6 py-4 text-[10px] font-black text-brand-muted uppercase tracking-widest whitespace-nowrap">Status</th>
                    <th class="px-6 py-4 text-[10px] font-black text-brand-muted uppercase tracking-widest text-right whitespace-nowrap">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($drivers as $driver)
                <tr class="hover:bg-surface/20 transition-colors group">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-4">
                            @if($driver->driver_photo_url)
                                <img src="{{ asset('storage/'.$driver->driver_photo_url) }}" class="w-10 h-10 rounded-full border border-gray-200 shrink-0 object-cover">
                            @else
                                <div class="w-10 h-10 rounded-full bg-brand/10 text-brand font-bold flex items-center justify-center shrink-0 border border-brand/20">
                                    {{ substr($driver->user->name ?? 'D', 0, 2) }}
                                </div>
                            @endif
                            <div>
                                <p class="text-sm font-bold text-brand">{{ $driver->user->name ?? 'Unknown' }}</p>
                                <p class="text-xs text-brand-muted font-mono mt-0.5">{{ $driver->user->phone ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        @if($driver->activeVehicle)
                            <p class="text-sm font-bold text-brand">{{ $driver->activeVehicle->make }} {{ $driver->activeVehicle->model }}</p>
                            <p class="text-xs text-brand-muted font-mono mt-0.5">{{ $driver->activeVehicle->license_plate }} • {{ $driver->activeVehicle->color }}</p>
                        @else
                            <span class="text-xs text-gray-400 italic">No active vehicle</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2 mb-1">
                            <svg class="w-3 h-3 text-accent" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            <span class="text-sm font-bold text-brand">{{ number_format($driver->rating, 1) }}</span>
                        </div>
                        <p class="text-[10px] text-brand-muted uppercase tracking-widest">{{ number_format($driver->total_deliveries) }} Rides</p>
                    </td>
                    <td class="px-6 py-4">
                        @if($driver->status === 'active')
                            <span class="px-2 py-1 bg-green-100 text-green-700 text-[9px] font-black uppercase tracking-widest rounded shadow-sm">Active</span>
                        @elseif($driver->status === 'suspended')
                            <span class="px-2 py-1 bg-red-100 text-red-700 text-[9px] font-black uppercase tracking-widest rounded shadow-sm">Suspended</span>
                        @else
                            <span class="px-2 py-1 bg-amber-100 text-amber-700 text-[9px] font-black uppercase tracking-widest rounded shadow-sm">Pending</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-3">
                            <button class="text-brand font-bold text-xs hover:text-brand-light transition">View Full Profile</button>
                            @if($driver->status === 'active')
                                <form action="{{ route('orchestrator.driver.suspend', $driver->id) }}" method="POST" class="inline">
                                    @csrf
                                    <input type="hidden" name="reason" value="Manual suspension by admin">
                                    <button type="submit" class="text-red-500 font-bold text-xs hover:text-red-700 transition">Suspend</button>
                                </form>
                            @elseif($driver->status === 'suspended')
                                <form action="{{ route('orchestrator.driver.activate', $driver->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-green-500 font-bold text-xs hover:text-green-700 transition">Activate</button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                        <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        <p class="font-medium">No drivers found.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="p-4 border-t border-gray-50">
        {{ $drivers->links() }}
    </div>
</div>

@endsection
