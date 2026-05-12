@extends('admin.layout')
@section('title', 'Vehicle Types')
@section('content')

<div class="mb-8 flex items-center justify-between">
    <div>
        <h2 class="text-2xl font-black text-brand tracking-tight">Vehicle Types & Pricing</h2>
        <p class="text-brand-muted font-medium mt-1">Configure ride categories, capacities, and base fares.</p>
    </div>
    <div class="flex gap-4">
        <button onclick="document.getElementById('add-modal').classList.remove('hidden')" class="px-6 py-3 bg-brand text-white font-bold rounded-lg hover:bg-brand-light transition flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Add Vehicle Type
        </button>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

    @forelse($vehicleTypes as $type)
    <!-- Vehicle Type Card -->
    <div class="bg-white rounded-lg border border-gray-100 shadow-sm overflow-hidden flex flex-col {{ !$type->is_active ? 'opacity-75' : '' }}">
        <div class="p-6 border-b border-gray-50 flex items-start justify-between bg-surface/30">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-lg bg-white border border-gray-100 shadow-sm flex items-center justify-center text-brand">
                    @if(str_contains(strtolower($type->name), 'delivery') || str_contains(strtolower($type->name), 'bike'))
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                    @elseif(str_contains(strtolower($type->name), 'premium') || str_contains(strtolower($type->name), 'suv'))
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    @else
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                    @endif
                </div>
                <div>
                    <h3 class="text-lg font-black text-brand">{{ $type->name }}</h3>
                    <p class="text-[10px] font-black text-brand-muted uppercase tracking-widest mt-0.5">Capacity: {{ $type->capacity }} Seats</p>
                </div>
            </div>
            <form action="{{ route('orchestrator.vehicle.types.toggle', $type->id) }}" method="POST">
                @csrf
                @method('PATCH')
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" onchange="this.form.submit()" class="sr-only peer" {{ $type->is_active ? 'checked' : '' }}>
                    <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-green-500"></div>
                </label>
            </form>
        </div>
        <div class="p-6 grid grid-cols-2 gap-4 flex-1">
            <div>
                <p class="text-[10px] font-black text-brand-muted uppercase tracking-widest">Base Fare</p>
                <p class="text-lg font-bold {{ $type->is_active ? 'text-brand' : 'text-gray-400' }} mt-1">₵ {{ number_format($type->base_fare, 2) }}</p>
            </div>
            <div>
                <p class="text-[10px] font-black text-brand-muted uppercase tracking-widest">Per Km</p>
                <p class="text-lg font-bold {{ $type->is_active ? 'text-brand' : 'text-gray-400' }} mt-1">₵ {{ number_format($type->per_km_rate, 2) }}</p>
            </div>
            <div>
                <p class="text-[10px] font-black text-brand-muted uppercase tracking-widest">Per Min</p>
                <p class="text-lg font-bold {{ $type->is_active ? 'text-brand' : 'text-gray-400' }} mt-1">₵ {{ number_format($type->per_minute_rate, 2) }}</p>
            </div>
            <div>
                <p class="text-[10px] font-black text-brand-muted uppercase tracking-widest">Min Fare</p>
                <p class="text-lg font-bold {{ $type->is_active ? 'text-brand' : 'text-gray-400' }} mt-1">₵ {{ number_format($type->min_fare, 2) }}</p>
            </div>
            @if(!$type->is_active)
            <div class="col-span-2">
                <p class="text-xs text-amber-600 font-medium flex items-center gap-1 bg-amber-50 p-2 rounded border border-amber-100">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    Currently disabled system-wide.
                </p>
            </div>
            @endif
        </div>
        <div class="p-4 border-t border-gray-50 flex items-center justify-between">
            <span class="text-xs text-brand-muted font-medium">{{ number_format($type->vehicles_count) }} active vehicles</span>
            <div class="flex gap-3">
                <button class="text-accent text-sm font-bold hover:text-accent-light transition">Edit</button>
                <form action="{{ route('orchestrator.vehicle.types.destroy', $type->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this vehicle type?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-red-500 text-sm font-bold hover:text-red-700 transition">Delete</button>
                </form>
            </div>
        </div>
    </div>
    @empty
    <div class="col-span-full p-12 text-center bg-white rounded-lg border border-gray-100 shadow-sm">
        <p class="text-gray-500 font-medium">No vehicle types configured yet. Add one to get started.</p>
    </div>
    @endforelse

</div>

<!-- Add Modal (Hidden by default) -->
<div id="add-modal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-xl max-w-lg w-full p-6">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-lg font-black text-brand">Add Vehicle Type</h3>
            <button onclick="document.getElementById('add-modal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
        </div>
        <form action="{{ route('orchestrator.vehicle.types.store') }}" method="POST">
            @csrf
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div class="col-span-2">
                    <label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-1">Name (e.g. Economy)</label>
                    <input type="text" name="name" required class="w-full bg-surface border border-gray-200 rounded p-2 text-sm focus:ring-2 focus:ring-brand/20 outline-none">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-1">Base Fare (₵)</label>
                    <input type="number" step="0.01" name="base_fare" required class="w-full bg-surface border border-gray-200 rounded p-2 text-sm focus:ring-2 focus:ring-brand/20 outline-none">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-1">Min Fare (₵)</label>
                    <input type="number" step="0.01" name="min_fare" required class="w-full bg-surface border border-gray-200 rounded p-2 text-sm focus:ring-2 focus:ring-brand/20 outline-none">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-1">Per Km Rate (₵)</label>
                    <input type="number" step="0.01" name="per_km_rate" required class="w-full bg-surface border border-gray-200 rounded p-2 text-sm focus:ring-2 focus:ring-brand/20 outline-none">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-1">Per Minute Rate (₵)</label>
                    <input type="number" step="0.01" name="per_minute_rate" required class="w-full bg-surface border border-gray-200 rounded p-2 text-sm focus:ring-2 focus:ring-brand/20 outline-none">
                </div>
                <div class="col-span-2">
                    <label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-1">Passenger Capacity</label>
                    <input type="number" name="capacity" value="4" required class="w-full bg-surface border border-gray-200 rounded p-2 text-sm focus:ring-2 focus:ring-brand/20 outline-none">
                </div>
            </div>
            <div class="flex justify-end pt-4 border-t border-gray-100">
                <button type="submit" class="px-6 py-2.5 bg-brand text-white font-bold rounded shadow-sm hover:bg-brand-light transition">Save Vehicle Type</button>
            </div>
        </form>
    </div>
</div>

@endsection
