@extends('admin.layout')
@section('title', 'Geolocation & Mapping Nodes')
@section('content')

<div class="p-8 lg:p-12 max-w-[1200px] mx-auto">
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-12 gap-6">
        <div>
            <div class="flex items-center gap-2 text-[10px] font-black text-accent uppercase tracking-[0.2em] mb-2">
                <a href="{{ route('orchestrator.settings') }}" class="hover:text-brand transition-colors">Settings Hub</a>
                <span class="text-gray-300">/</span>
                <span>Geolocation</span>
            </div>
            <h2 class="text-3xl font-black text-brand tracking-tight">Geospatial Intelligence</h2>
            <p class="text-sm text-brand-muted font-medium mt-1">Configure Google Maps, Mapbox, and real-time geocoding APIs.</p>
        </div>
    </div>

    <form action="{{ route('orchestrator.settings.update') }}" method="POST" class="space-y-10">
        @csrf
        
        <!-- Google Maps Section -->
        <div class="bg-white rounded-3xl border border-gray-100 shadow-2xl overflow-hidden">
            <div class="p-8 border-b border-gray-50 bg-surface/30 flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-black text-brand">Google Maps Platform</h3>
                    <p class="text-xs text-brand-muted font-bold">Primary engine for navigation, autocomplete, and static maps.</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="settings[google_maps_enabled]" value="1" {{ \App\Modules\Admin\Models\SystemSetting::get('google_maps_enabled') == 'true' ? 'checked' : '' }} class="sr-only peer">
                    <div class="w-14 h-7 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-brand"></div>
                </label>
            </div>
            
            <div class="p-8 space-y-8">
                <div>
                    <label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-3">Google Maps API Key (Server & Client)</label>
                    <input type="password" name="settings[google_maps_key]" value="{{ \App\Modules\Admin\Models\SystemSetting::get('google_maps_key') ? '********' : '' }}" class="w-full bg-surface border-2 border-transparent focus:border-brand rounded-2xl py-4 px-6 text-sm font-bold outline-none transition-all">
                    <p class="text-[10px] text-brand-muted font-bold mt-2">Required for: Distance Matrix, Geocoding, Places Autocomplete.</p>
                </div>
            </div>
        </div>

        <!-- Mapbox Section -->
        <div class="bg-white rounded-3xl border border-gray-100 shadow-xl overflow-hidden opacity-80 hover:opacity-100 transition-opacity">
            <div class="p-8 border-b border-gray-50 bg-surface/30 flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-black text-brand">Mapbox Vector Maps</h3>
                    <p class="text-xs text-brand-muted font-bold">High-performance vector rendering and custom terrain layers.</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="settings[mapbox_enabled]" value="1" {{ \App\Modules\Admin\Models\SystemSetting::get('mapbox_enabled') == 'true' ? 'checked' : '' }} class="sr-only peer">
                    <div class="w-14 h-7 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-brand"></div>
                </label>
            </div>
            
            <div class="p-8 space-y-8">
                <div>
                    <label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-3">Mapbox Public Access Token</label>
                    <input type="password" name="settings[mapbox_key]" value="{{ \App\Modules\Admin\Models\SystemSetting::get('mapbox_key') ? '********' : '' }}" class="w-full bg-surface border-2 border-transparent focus:border-brand rounded-2xl py-4 px-6 text-sm font-bold outline-none transition-all">
                </div>
            </div>
        </div>

        <div class="flex justify-end pt-4">
            <button type="submit" class="bg-brand text-white px-12 py-5 rounded-2xl text-xs font-black shadow-xl hover:shadow-brand/20 hover:-translate-y-1 transition-all uppercase tracking-widest">
                Synchronize Mapping Nodes
            </button>
        </div>
    </form>
</div>

@endsection
