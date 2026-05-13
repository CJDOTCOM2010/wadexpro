@extends('admin.layout')
@section('title', 'Vehicle Types')
@section('content')

<div x-data="{ 
    showAddModal: false, 
    showEditModal: false,
    currentType: {
        id: '',
        name: '',
        base_fare: 0,
        per_km_rate: 0,
        per_minute_rate: 0,
        min_fare: 0,
        capacity: 4,
        description: ''
    },
    editType(type) {
        this.currentType = {
            id: type.id,
            name: type.name,
            base_fare: type.base_fare,
            per_km_rate: type.per_km_rate,
            per_minute_rate: type.per_minute_rate,
            min_fare: type.min_fare,
            capacity: type.capacity,
            description: type.description || ''
        };
        this.showEditModal = true;
    }
}">
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-black text-brand tracking-tight">Vehicle Types & Pricing</h2>
            <p class="text-brand-muted font-medium mt-1">Configure ride categories, capacities, and base fares.</p>
        </div>
        <div class="flex gap-4">
            <button @click="showAddModal = true" class="px-6 py-3 bg-brand text-white font-bold rounded-lg hover:bg-brand-light transition flex items-center gap-2 shadow-xl shadow-brand/20">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Add Vehicle Type
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

        @forelse($vehicleTypes as $type)
        <!-- Vehicle Type Card -->
        <div class="bg-white rounded-lg border border-gray-100 shadow-sm overflow-hidden flex flex-col {{ !$type->is_active ? 'opacity-75' : '' }} group transition hover:shadow-md">
            <div class="p-6 border-b border-gray-50 flex items-start justify-between bg-surface/30">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-lg bg-white border border-gray-100 shadow-sm flex items-center justify-center text-brand">
                        @if(str_contains(strtolower($type->name), 'delivery') || str_contains(strtolower($type->name), 'bike') || str_contains(strtolower($type->name), 'moto'))
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                        @elseif(str_contains(strtolower($type->name), 'premium') || str_contains(strtolower($type->name), 'suv') || str_contains(strtolower($type->name), 'xl'))
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
            <div class="p-4 border-t border-gray-50 flex items-center justify-between bg-surface/10">
                <span class="text-xs text-brand-muted font-medium">{{ number_format($type->vehicles_count ?? 0) }} active vehicles</span>
                <div class="flex gap-3">
                    <button @click="editType({{ json_encode($type) }})" class="px-3 py-1 bg-white border border-gray-100 text-brand text-xs font-black rounded uppercase hover:bg-surface transition shadow-sm">Edit</button>
                    <form action="{{ route('orchestrator.vehicle.types.destroy', $type->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this vehicle type?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-3 py-1 bg-white border border-gray-100 text-red-500 text-xs font-black rounded uppercase hover:bg-red-50 transition shadow-sm">Delete</button>
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

    <!-- Add Modal -->
    <div x-show="showAddModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-4">
        <div @click="showAddModal = false" class="absolute inset-0 bg-brand/60 backdrop-blur-sm"></div>
        <div class="relative bg-white w-full max-w-lg rounded-2xl shadow-2xl overflow-hidden animate-fade-in-down" @click.stop>
            <div class="p-8 border-b border-gray-100 flex items-center justify-between bg-surface/30">
                <h3 class="text-2xl font-black text-brand tracking-tight">Add Vehicle Type</h3>
                <button @click="showAddModal = false" class="p-2 hover:bg-white rounded-full transition"><svg class="w-6 h-6 text-brand-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
            </div>
            <form action="{{ route('orchestrator.vehicle.types.store') }}" method="POST" class="p-8 space-y-5">
                @csrf
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-brand-muted uppercase tracking-widest">Name (e.g. Economy)</label>
                    <input type="text" name="name" required class="w-full bg-surface border border-gray-100 rounded-lg px-4 py-3 text-sm font-bold focus:ring-2 focus:ring-accent outline-none">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-brand-muted uppercase tracking-widest">Base Fare (₵)</label>
                        <input type="number" step="0.01" name="base_fare" required class="w-full bg-surface border border-gray-100 rounded-lg px-4 py-3 text-sm font-bold focus:ring-2 focus:ring-accent outline-none">
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-brand-muted uppercase tracking-widest">Min Fare (₵)</label>
                        <input type="number" step="0.01" name="min_fare" required class="w-full bg-surface border border-gray-100 rounded-lg px-4 py-3 text-sm font-bold focus:ring-2 focus:ring-accent outline-none">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-brand-muted uppercase tracking-widest">Per Km Rate (₵)</label>
                        <input type="number" step="0.01" name="per_km_rate" required class="w-full bg-surface border border-gray-100 rounded-lg px-4 py-3 text-sm font-bold focus:ring-2 focus:ring-accent outline-none">
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-brand-muted uppercase tracking-widest">Per Minute Rate (₵)</label>
                        <input type="number" step="0.01" name="per_minute_rate" required class="w-full bg-surface border border-gray-100 rounded-lg px-4 py-3 text-sm font-bold focus:ring-2 focus:ring-accent outline-none">
                    </div>
                </div>
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-brand-muted uppercase tracking-widest">Passenger Capacity</label>
                    <input type="number" name="capacity" value="4" required class="w-full bg-surface border border-gray-100 rounded-lg px-4 py-3 text-sm font-bold focus:ring-2 focus:ring-accent outline-none">
                </div>
                <div class="pt-4 flex gap-4">
                    <button type="button" @click="showAddModal = false" class="flex-1 py-4 text-sm font-bold text-brand-muted hover:text-brand transition">Cancel</button>
                    <button type="submit" class="flex-1 py-4 bg-brand text-white font-black rounded-xl hover:bg-brand-light transition shadow-lg shadow-brand/20">Save Vehicle Type</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Modal -->
    <div x-show="showEditModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-4">
        <div @click="showEditModal = false" class="absolute inset-0 bg-brand/60 backdrop-blur-sm"></div>
        <div class="relative bg-white w-full max-w-lg rounded-2xl shadow-2xl overflow-hidden animate-fade-in-down" @click.stop>
            <div class="p-8 border-b border-gray-100 flex items-center justify-between bg-surface/30">
                <h3 class="text-2xl font-black text-brand tracking-tight">Edit Vehicle Type</h3>
                <button @click="showEditModal = false" class="p-2 hover:bg-white rounded-full transition"><svg class="w-6 h-6 text-brand-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
            </div>
            <form :action="`/orchestrator/driver-management/vehicle-types/${currentType.id}`" method="POST" class="p-8 space-y-5">
                @csrf
                @method('PUT')
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-brand-muted uppercase tracking-widest">Name</label>
                    <input type="text" name="name" x-model="currentType.name" required class="w-full bg-surface border border-gray-100 rounded-lg px-4 py-3 text-sm font-bold focus:ring-2 focus:ring-accent outline-none">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-brand-muted uppercase tracking-widest">Base Fare (₵)</label>
                        <input type="number" step="0.01" name="base_fare" x-model="currentType.base_fare" required class="w-full bg-surface border border-gray-100 rounded-lg px-4 py-3 text-sm font-bold focus:ring-2 focus:ring-accent outline-none">
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-brand-muted uppercase tracking-widest">Min Fare (₵)</label>
                        <input type="number" step="0.01" name="min_fare" x-model="currentType.min_fare" required class="w-full bg-surface border border-gray-100 rounded-lg px-4 py-3 text-sm font-bold focus:ring-2 focus:ring-accent outline-none">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-brand-muted uppercase tracking-widest">Per Km Rate (₵)</label>
                        <input type="number" step="0.01" name="per_km_rate" x-model="currentType.per_km_rate" required class="w-full bg-surface border border-gray-100 rounded-lg px-4 py-3 text-sm font-bold focus:ring-2 focus:ring-accent outline-none">
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-brand-muted uppercase tracking-widest">Per Minute Rate (₵)</label>
                        <input type="number" step="0.01" name="per_minute_rate" x-model="currentType.per_minute_rate" required class="w-full bg-surface border border-gray-100 rounded-lg px-4 py-3 text-sm font-bold focus:ring-2 focus:ring-accent outline-none">
                    </div>
                </div>
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-brand-muted uppercase tracking-widest">Passenger Capacity</label>
                    <input type="number" name="capacity" x-model="currentType.capacity" required class="w-full bg-surface border border-gray-100 rounded-lg px-4 py-3 text-sm font-bold focus:ring-2 focus:ring-accent outline-none">
                </div>
                <div class="pt-4 flex gap-4">
                    <button type="button" @click="showEditModal = false" class="flex-1 py-4 text-sm font-bold text-brand-muted hover:text-brand transition">Cancel</button>
                    <button type="submit" class="flex-1 py-4 bg-brand text-white font-black rounded-xl hover:bg-brand-light transition shadow-lg shadow-brand/20">Update Vehicle Type</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
