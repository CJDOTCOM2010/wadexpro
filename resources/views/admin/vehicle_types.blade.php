@extends('admin.layout')
@section('title', 'Vehicle Types')
@section('content')

<div class="mb-8 flex items-center justify-between">
    <div>
        <h2 class="text-2xl font-black text-brand tracking-tight">Vehicle Types & Pricing</h2>
        <p class="text-brand-muted font-medium mt-1">Configure ride categories, capacities, and base fares.</p>
    </div>
    <div class="flex gap-4">
        <button class="px-6 py-3 bg-brand text-white font-bold rounded-lg hover:bg-brand-light transition flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Add Vehicle Type
        </button>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

    <!-- Vehicle Type Card -->
    <div class="bg-white rounded-lg border border-gray-100 shadow-sm overflow-hidden flex flex-col">
        <div class="p-6 border-b border-gray-50 flex items-start justify-between bg-surface/30">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-lg bg-white border border-gray-100 shadow-sm flex items-center justify-center text-brand">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                </div>
                <div>
                    <h3 class="text-lg font-black text-brand">Economy</h3>
                    <p class="text-[10px] font-black text-brand-muted uppercase tracking-widest mt-0.5">Sedan • 4 Seats</p>
                </div>
            </div>
            <label class="relative inline-flex items-center cursor-pointer">
                <input type="checkbox" value="" class="sr-only peer" checked>
                <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-green-500"></div>
            </label>
        </div>
        <div class="p-6 grid grid-cols-2 gap-4 flex-1">
            <div>
                <p class="text-[10px] font-black text-brand-muted uppercase tracking-widest">Base Fare</p>
                <p class="text-lg font-bold text-brand mt-1">₵ 15.00</p>
            </div>
            <div>
                <p class="text-[10px] font-black text-brand-muted uppercase tracking-widest">Per Km</p>
                <p class="text-lg font-bold text-brand mt-1">₵ 2.50</p>
            </div>
            <div>
                <p class="text-[10px] font-black text-brand-muted uppercase tracking-widest">Per Min</p>
                <p class="text-lg font-bold text-brand mt-1">₵ 0.50</p>
            </div>
            <div>
                <p class="text-[10px] font-black text-brand-muted uppercase tracking-widest">Min Fare</p>
                <p class="text-lg font-bold text-brand mt-1">₵ 20.00</p>
            </div>
        </div>
        <div class="p-4 border-t border-gray-50 flex items-center justify-between">
            <span class="text-xs text-brand-muted font-medium">850 active drivers</span>
            <button class="text-accent text-sm font-bold hover:text-accent-light transition">Edit Config</button>
        </div>
    </div>

    <!-- Vehicle Type Card -->
    <div class="bg-white rounded-lg border border-gray-100 shadow-sm overflow-hidden flex flex-col">
        <div class="p-6 border-b border-gray-50 flex items-start justify-between bg-surface/30">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-lg bg-white border border-gray-100 shadow-sm flex items-center justify-center text-brand">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                </div>
                <div>
                    <h3 class="text-lg font-black text-brand">Premium</h3>
                    <p class="text-[10px] font-black text-brand-muted uppercase tracking-widest mt-0.5">SUV • 6 Seats</p>
                </div>
            </div>
            <label class="relative inline-flex items-center cursor-pointer">
                <input type="checkbox" value="" class="sr-only peer" checked>
                <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-green-500"></div>
            </label>
        </div>
        <div class="p-6 grid grid-cols-2 gap-4 flex-1">
            <div>
                <p class="text-[10px] font-black text-brand-muted uppercase tracking-widest">Base Fare</p>
                <p class="text-lg font-bold text-brand mt-1">₵ 25.00</p>
            </div>
            <div>
                <p class="text-[10px] font-black text-brand-muted uppercase tracking-widest">Per Km</p>
                <p class="text-lg font-bold text-brand mt-1">₵ 4.00</p>
            </div>
            <div>
                <p class="text-[10px] font-black text-brand-muted uppercase tracking-widest">Per Min</p>
                <p class="text-lg font-bold text-brand mt-1">₵ 0.80</p>
            </div>
            <div>
                <p class="text-[10px] font-black text-brand-muted uppercase tracking-widest">Min Fare</p>
                <p class="text-lg font-bold text-brand mt-1">₵ 35.00</p>
            </div>
        </div>
        <div class="p-4 border-t border-gray-50 flex items-center justify-between">
            <span class="text-xs text-brand-muted font-medium">124 active drivers</span>
            <button class="text-accent text-sm font-bold hover:text-accent-light transition">Edit Config</button>
        </div>
    </div>

    <!-- Vehicle Type Card -->
    <div class="bg-white rounded-lg border border-gray-100 shadow-sm overflow-hidden flex flex-col">
        <div class="p-6 border-b border-gray-50 flex items-start justify-between bg-surface/30 opacity-75">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-lg bg-white border border-gray-100 shadow-sm flex items-center justify-center text-brand">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                </div>
                <div>
                    <h3 class="text-lg font-black text-brand">Delivery</h3>
                    <p class="text-[10px] font-black text-brand-muted uppercase tracking-widest mt-0.5">Motorcycle • 15kg</p>
                </div>
            </div>
            <label class="relative inline-flex items-center cursor-pointer">
                <input type="checkbox" value="" class="sr-only peer">
                <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-green-500"></div>
            </label>
        </div>
        <div class="p-6 grid grid-cols-2 gap-4 flex-1">
            <div>
                <p class="text-[10px] font-black text-brand-muted uppercase tracking-widest">Base Fare</p>
                <p class="text-lg font-bold text-gray-400 mt-1">₵ 10.00</p>
            </div>
            <div>
                <p class="text-[10px] font-black text-brand-muted uppercase tracking-widest">Per Km</p>
                <p class="text-lg font-bold text-gray-400 mt-1">₵ 1.50</p>
            </div>
            <div class="col-span-2">
                <p class="text-xs text-amber-600 font-medium flex items-center gap-1 bg-amber-50 p-2 rounded border border-amber-100">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    Currently disabled system-wide.
                </p>
            </div>
        </div>
        <div class="p-4 border-t border-gray-50 flex items-center justify-between">
            <span class="text-xs text-brand-muted font-medium">0 active drivers</span>
            <button class="text-accent text-sm font-bold hover:text-accent-light transition">Edit Config</button>
        </div>
    </div>

</div>

@endsection
