@extends('admin.layout')
@section('title', 'Vehicle Types')
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

<div x-data="vehicleTypeManager()" class="max-w-6xl mx-auto">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-black text-brand tracking-tight">Vehicle Types & Pricing</h2>
            <p class="text-sm text-brand-muted font-medium mt-0.5">Configure ride categories, capacities, and base fares.</p>
        </div>
        <button @click="openAdd()" class="px-5 py-2.5 bg-brand text-white rounded-lg text-xs font-bold hover:bg-brand-light transition-colors flex items-center gap-2 shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Add Vehicle Type
        </button>
    </div>

    {{-- Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($vehicleTypes as $type)
        <div class="bg-white border border-gray-100 rounded-xl overflow-hidden {{ !$type->is_active ? 'opacity-60' : '' }}">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-10 h-10 rounded-lg bg-surface flex items-center justify-center text-brand shrink-0">
                        @if(str_contains(strtolower($type->name), 'bike') || str_contains(strtolower($type->name), 'moto'))
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                        @elseif(str_contains(strtolower($type->name), 'premium') || str_contains(strtolower($type->name), 'suv'))
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                        @else
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                        @endif
                    </div>
                    <div class="min-w-0">
                        <h3 class="text-sm font-bold text-brand truncate">{{ $type->name }}</h3>
                        <p class="text-[10px] text-brand-muted">{{ $type->capacity }} seats</p>
                    </div>
                </div>
                <form action="{{ route('orchestrator.vehicle.types.toggle', $type->id) }}" method="POST" class="shrink-0">
                    @csrf @method('PATCH')
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" onchange="this.form.submit()" class="sr-only peer" {{ $type->is_active ? 'checked' : '' }}>
                        <div class="w-9 h-5 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[0.5px] after:left-[0.5px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-green-500"></div>
                    </label>
                </form>
            </div>

            <div class="p-5 grid grid-cols-2 gap-4">
                <div>
                    <p class="text-[9px] font-bold text-brand-muted uppercase tracking-wider">Base Fare</p>
                    <p class="text-base font-bold text-brand mt-0.5">₵ {{ number_format($type->base_fare, 2) }}</p>
                </div>
                <div>
                    <p class="text-[9px] font-bold text-brand-muted uppercase tracking-wider">Per Km</p>
                    <p class="text-base font-bold text-brand mt-0.5">₵ {{ number_format($type->per_km_rate, 2) }}</p>
                </div>
                <div>
                    <p class="text-[9px] font-bold text-brand-muted uppercase tracking-wider">Per Min</p>
                    <p class="text-base font-bold text-brand mt-0.5">₵ {{ number_format($type->per_minute_rate, 2) }}</p>
                </div>
                <div>
                    <p class="text-[9px] font-bold text-brand-muted uppercase tracking-wider">Min Fare</p>
                    <p class="text-base font-bold text-brand mt-0.5">₵ {{ number_format($type->min_fare, 2) }}</p>
                </div>
            </div>

            @if(!$type->is_active)
            <div class="px-5 pb-2">
                <span class="inline-flex items-center gap-1.5 text-[10px] font-bold text-amber-600 bg-amber-50 px-2 py-1 rounded">Disabled system-wide</span>
            </div>
            @endif

            <div class="px-5 py-3 border-t border-gray-100 flex items-center justify-between bg-surface/20">
                <span class="text-[10px] text-brand-muted">{{ number_format($type->vehicles_count ?? 0) }} active vehicles</span>
                <div class="flex items-center gap-1.5">
                    <button @click="openEdit({{ json_encode($type) }})" class="px-3 py-1.5 bg-white border border-gray-200 text-brand rounded-lg text-[10px] font-bold hover:bg-surface transition-colors">Edit</button>
                    <button @click="confirmDelete('{{ $type->id }}', '{{ addslashes($type->name) }}')" class="px-3 py-1.5 bg-white border border-gray-200 text-red-500 rounded-lg text-[10px] font-bold hover:bg-red-50 transition-colors">Delete</button>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full flex flex-col items-center justify-center py-16 text-brand-muted bg-white border border-gray-100 rounded-xl">
            <svg class="w-14 h-14 mb-3 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
            <p class="text-sm font-bold">No vehicle types configured</p>
            <p class="text-xs mt-1">Add one to get started.</p>
        </div>
        @endforelse
    </div>

    {{-- Add Modal --}}
    <div x-show="showAdd" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-brand/50 backdrop-blur-sm" @click="showAdd = false"></div>
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg relative z-10 max-h-[90vh] overflow-y-auto" @click.outside="showAdd = false">
            <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between sticky top-0 bg-white">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 bg-accent/10 rounded-lg flex items-center justify-center text-accent">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-brand">New Vehicle Type</h3>
                        <p class="text-xs text-brand-muted">Define pricing and capacity for a new fleet tier.</p>
                    </div>
                </div>
                <button @click="showAdd = false" class="w-7 h-7 bg-surface rounded-lg flex items-center justify-center text-brand-muted hover:text-brand transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form action="{{ route('orchestrator.vehicle.types.store') }}" method="POST" class="p-6 space-y-4">
                @csrf
                <div>
                    <label class="text-[10px] font-bold text-brand-muted uppercase tracking-wider mb-1.5 block">Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" required placeholder="e.g. Wadex Economy" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium outline-none focus:ring-2 focus:ring-accent/20 transition-shadow">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-[10px] font-bold text-brand-muted uppercase tracking-wider mb-1.5 block">Base Fare (₵)</label>
                        <input type="number" step="0.01" name="base_fare" required placeholder="10.00" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium outline-none focus:ring-2 focus:ring-accent/20 transition-shadow">
                    </div>
                    <div>
                        <label class="text-[10px] font-bold text-brand-muted uppercase tracking-wider mb-1.5 block">Min Fare (₵)</label>
                        <input type="number" step="0.01" name="min_fare" required placeholder="15.00" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium outline-none focus:ring-2 focus:ring-accent/20 transition-shadow">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-[10px] font-bold text-brand-muted uppercase tracking-wider mb-1.5 block">Per Km Rate (₵)</label>
                        <input type="number" step="0.01" name="per_km_rate" required placeholder="2.50" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium outline-none focus:ring-2 focus:ring-accent/20 transition-shadow">
                    </div>
                    <div>
                        <label class="text-[10px] font-bold text-brand-muted uppercase tracking-wider mb-1.5 block">Per Minute Rate (₵)</label>
                        <input type="number" step="0.01" name="per_minute_rate" required placeholder="0.50" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium outline-none focus:ring-2 focus:ring-accent/20 transition-shadow">
                    </div>
                </div>
                <div>
                    <label class="text-[10px] font-bold text-brand-muted uppercase tracking-wider mb-1.5 block">Passenger Capacity</label>
                    <input type="number" name="capacity" value="4" required class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium outline-none focus:ring-2 focus:ring-accent/20 transition-shadow">
                </div>
                <div class="flex justify-end gap-2 pt-2 border-t border-gray-100">
                    <button type="button" @click="showAdd = false" class="px-4 py-2 text-xs font-bold text-brand-muted hover:text-brand transition-colors">Cancel</button>
                    <button type="submit" class="px-5 py-2 bg-brand text-white rounded-lg text-xs font-bold hover:bg-brand-light transition-colors">Create</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Edit Modal --}}
    <div x-show="showEdit" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-brand/50 backdrop-blur-sm" @click="showEdit = false"></div>
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg relative z-10 max-h-[90vh] overflow-y-auto" @click.outside="showEdit = false">
            <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between sticky top-0 bg-white">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 bg-blue-50 rounded-lg flex items-center justify-center text-blue-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-brand">Edit Vehicle Type</h3>
                        <p class="text-xs text-brand-muted" x-text="'Editing: ' + editForm.name"></p>
                    </div>
                </div>
                <button @click="showEdit = false" class="w-7 h-7 bg-surface rounded-lg flex items-center justify-center text-brand-muted hover:text-brand transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form :action="`/orchestrator/driver-management/vehicle-types/${editForm.id}`" method="POST" class="p-6 space-y-4">
                @csrf @method('PUT')
                <div>
                    <label class="text-[10px] font-bold text-brand-muted uppercase tracking-wider mb-1.5 block">Name</label>
                    <input type="text" name="name" x-model="editForm.name" required class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium outline-none focus:ring-2 focus:ring-accent/20 transition-shadow">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-[10px] font-bold text-brand-muted uppercase tracking-wider mb-1.5 block">Base Fare (₵)</label>
                        <input type="number" step="0.01" name="base_fare" x-model="editForm.base_fare" required class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium outline-none focus:ring-2 focus:ring-accent/20 transition-shadow">
                    </div>
                    <div>
                        <label class="text-[10px] font-bold text-brand-muted uppercase tracking-wider mb-1.5 block">Min Fare (₵)</label>
                        <input type="number" step="0.01" name="min_fare" x-model="editForm.min_fare" required class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium outline-none focus:ring-2 focus:ring-accent/20 transition-shadow">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-[10px] font-bold text-brand-muted uppercase tracking-wider mb-1.5 block">Per Km Rate (₵)</label>
                        <input type="number" step="0.01" name="per_km_rate" x-model="editForm.per_km_rate" required class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium outline-none focus:ring-2 focus:ring-accent/20 transition-shadow">
                    </div>
                    <div>
                        <label class="text-[10px] font-bold text-brand-muted uppercase tracking-wider mb-1.5 block">Per Minute Rate (₵)</label>
                        <input type="number" step="0.01" name="per_minute_rate" x-model="editForm.per_minute_rate" required class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium outline-none focus:ring-2 focus:ring-accent/20 transition-shadow">
                    </div>
                </div>
                <div>
                    <label class="text-[10px] font-bold text-brand-muted uppercase tracking-wider mb-1.5 block">Passenger Capacity</label>
                    <input type="number" name="capacity" x-model="editForm.capacity" required class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium outline-none focus:ring-2 focus:ring-accent/20 transition-shadow">
                </div>
                <div class="flex justify-end gap-2 pt-2 border-t border-gray-100">
                    <button type="button" @click="showEdit = false" class="px-4 py-2 text-xs font-bold text-brand-muted hover:text-brand transition-colors">Cancel</button>
                    <button type="submit" class="px-5 py-2 bg-brand text-white rounded-lg text-xs font-bold hover:bg-brand-light transition-colors">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Multi-step Delete Confirmation --}}
    <div x-show="deleteStep > 0" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-brand/60 backdrop-blur-sm" @click="closeDelete()"></div>
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md relative z-10 overflow-hidden" @click.outside="closeDelete()">
            <template x-if="deleteStep === 1">
                <div class="p-6">
                    <div class="w-14 h-14 bg-red-50 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-7 h-7 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                    <h3 class="text-lg font-bold text-brand text-center mb-2">Delete Vehicle Type?</h3>
                    <p class="text-sm text-brand-muted text-center mb-6">You are about to permanently delete <strong class="text-brand" x-text="deleteLabel"></strong>. This cannot be undone.</p>
                    <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 mb-6">
                        <p class="text-xs font-bold text-amber-800">Vehicles assigned to this type may be affected.</p>
                    </div>
                    <div class="flex gap-2">
                        <button type="button" @click="closeDelete()" class="flex-1 px-4 py-2.5 bg-surface text-brand-muted rounded-lg text-xs font-bold hover:bg-gray-100">Cancel</button>
                        <button type="button" @click="deleteStep = 2" class="flex-1 px-4 py-2.5 bg-red-600 text-white rounded-lg text-xs font-bold hover:bg-red-700">Continue</button>
                    </div>
                </div>
            </template>
            <template x-if="deleteStep === 2">
                <div class="p-6">
                    <div class="w-14 h-14 bg-red-50 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-7 h-7 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h3 class="text-lg font-bold text-brand text-center mb-2">Final Confirmation</h3>
                    <p class="text-sm text-brand-muted text-center mb-6">Type <strong class="text-red-600 font-mono bg-red-50 px-2 py-0.5 rounded">DELETE</strong> to confirm.</p>
                    <input type="text" x-model="deleteConfirm" @input="deleteConfirm = deleteConfirm.toUpperCase()" placeholder="Type DELETE to confirm" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-bold text-center outline-none focus:ring-2 focus:ring-red-300 transition-shadow mb-6 uppercase tracking-widest">
                    <div class="flex gap-2">
                        <button type="button" @click="deleteStep = 1" class="flex-1 px-4 py-2.5 bg-surface text-brand-muted rounded-lg text-xs font-bold hover:bg-gray-100">Back</button>
                        <button type="button" @click="executeDelete()" :disabled="deleteConfirm !== 'DELETE'" class="flex-1 px-4 py-2.5 rounded-lg text-xs font-bold" :class="deleteConfirm === 'DELETE' ? 'bg-red-600 text-white hover:bg-red-700' : 'bg-gray-100 text-gray-400 cursor-not-allowed'">Confirm Delete</button>
                    </div>
                </div>
            </template>
        </div>
    </div>
</div>

<script>
function vehicleTypeManager() {
    return {
        showAdd: false,
        showEdit: false,
        editForm: { id: '', name: '', base_fare: 0, per_km_rate: 0, per_minute_rate: 0, min_fare: 0, capacity: 4 },
        deleteStep: 0,
        deleteId: '',
        deleteLabel: '',
        deleteConfirm: '',
        openAdd() { this.showAdd = true; },
        openEdit(type) {
            this.editForm = {
                id: type.id, name: type.name, base_fare: type.base_fare,
                per_km_rate: type.per_km_rate, per_minute_rate: type.per_minute_rate,
                min_fare: type.min_fare, capacity: type.capacity
            };
            this.showEdit = true;
        },
        confirmDelete(id, label) {
            this.deleteId = id; this.deleteLabel = label;
            this.deleteStep = 1; this.deleteConfirm = '';
        },
        closeDelete() { this.deleteStep = 0; this.deleteConfirm = ''; },
        executeDelete() {
            if (this.deleteConfirm !== 'DELETE') return;
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '/orchestrator/driver-management/vehicle-types/' + this.deleteId;
            const csrf = document.createElement('input');
            csrf.type = 'hidden'; csrf.name = '_token'; csrf.value = '{{ csrf_token() }}';
            form.appendChild(csrf);
            const method = document.createElement('input');
            method.type = 'hidden'; method.name = '_method'; method.value = 'DELETE';
            form.appendChild(method);
            document.body.appendChild(form);
            form.submit();
        }
    };
}
</script>
<style>[x-cloak] { display: none !important; }</style>
@endsection