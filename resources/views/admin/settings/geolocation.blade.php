@extends('admin.layout')
@section('title', 'Geolocation & Mapping Configuration')
@php
function getSetting($key, $default = null) {
    global $settings;
    return $settings[$key]->value ?? $default;
}
function hasSetting($key) {
    global $settings;
    return isset($settings[$key]) && !empty($settings[$key]->value);
}
function isChecked($key, $value = 'true') {
    return getSetting($key, 'false') === $value;
}
function inArraySetting($key, $needle) {
    $val = getSetting($key, '[]');
    return in_array($needle, json_decode($val, true) ?? []);
}
@endphp
@section('content')

<style>
/* Polished Toggle Switch */
.geo-toggle { position: relative; width: 48px; height: 26px; }
.geo-toggle input { opacity: 0; width: 0; height: 0; }
.geo-toggle .slider { position: absolute; cursor: pointer; inset: 0; background: #d1d5db; border-radius: 999px; transition: 0.3s; }
.geo-toggle .slider::before { content: ''; position: absolute; height: 20px; width: 20px; left: 3px; bottom: 3px; background: white; border-radius: 50%; transition: 0.3s; box-shadow: 0 1px 3px rgba(0,0,0,0.15); }
.geo-toggle input:checked + .slider { background: #1c2c44; }
.geo-toggle input:checked + .slider::before { transform: translateX(22px); }
.geo-toggle input:focus + .slider { box-shadow: 0 0 0 3px rgba(28,44,68,0.15); }

/* Polished Select */
.geo-select {
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%236b7280' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 14px center;
    padding-right: 42px;
    transition: all 0.2s;
}
.geo-select:hover { border-color: #9ca3af; background-color: #fff; }
.geo-select:focus { border-color: #1c2c44; box-shadow: 0 0 0 3px rgba(28, 44, 68, 0.08); background-color: #fff; }

/* Card hover */
.settings-card { transition: all 0.2s ease; }
.settings-card:hover { border-color: rgba(28, 44, 68, 0.2); box-shadow: 0 4px 12px rgba(0,0,0,0.04); }
</style>

<div class="p-6 lg:p-8 max-w-6xl mx-auto">
    <!-- Page Header -->
    <div class="flex items-center justify-between mb-8">
        <div>
            <div class="flex items-center gap-2 text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2">
                <a href="{{ route('orchestrator.settings') }}" class="hover:text-brand">Settings Hub</a>
                <span>/</span>
                <span class="text-brand">Geolocation</span>
            </div>
            <h2 class="text-2xl font-bold text-gray-900">Geospatial Configuration</h2>
            <p class="text-sm text-gray-500 mt-1">Configure mapping providers and location services</p>
        </div>
    </div>

    <form action="{{ route('orchestrator.settings.update') }}" method="POST" class="space-y-6">
        @csrf

        <!-- Top Row: Map Providers -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Google Maps -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-lg settings-card">
                <div class="p-5 border-b border-gray-100 flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 bg-blue-50 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-semibold text-gray-900">Google Maps</h3>
                            <p class="text-xs text-gray-500">Primary mapping engine</p>
                        </div>
                    </div>
                    <label class="geo-toggle">
                        <input type="checkbox" name="settings[google_maps_enabled]" value="1" {{ isChecked('google_maps_enabled') ? 'checked' : '' }}>
                        <span class="slider"></span>
                    </label>
                </div>
                
                <div class="p-5 space-y-5">
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">API Key</label>
                        <div class="relative">
                            <input type="password" name="settings[google_maps_key]" value="{{ getSetting('google_maps_key') ? '••••••••••••' : '' }}" placeholder="Enter your API key" class="w-full bg-gray-50 border border-gray-200 rounded-xl py-3 px-4 text-sm text-gray-700 focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500/10 outline-none transition-all">
                            <div class="absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Enabled Features</label>
                        <div class="grid grid-cols-2 gap-3">
                            <label class="flex items-center gap-2.5 cursor-pointer">
                                <input type="checkbox" name="settings[google_maps_directions][]" value="directions" {{ inArraySetting('google_maps_directions', 'directions') ? 'checked' : '' }} class="w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500">
                                <span class="text-sm text-gray-600">Directions</span>
                            </label>
                            <label class="flex items-center gap-2.5 cursor-pointer">
                                <input type="checkbox" name="settings[google_maps_places][]" value="places" {{ inArraySetting('google_maps_places', 'places') ? 'checked' : '' }} class="w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500">
                                <span class="text-sm text-gray-600">Places</span>
                            </label>
                            <label class="flex items-center gap-2.5 cursor-pointer">
                                <input type="checkbox" name="settings[google_maps_geocoding][]" value="geocoding" {{ inArraySetting('google_maps_geocoding', 'geocoding') ? 'checked' : '' }} class="w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500">
                                <span class="text-sm text-gray-600">Geocoding</span>
                            </label>
                            <label class="flex items-center gap-2.5 cursor-pointer">
                                <input type="checkbox" name="settings[google_maps_static][]" value="static" {{ inArraySetting('google_maps_static', 'static') ? 'checked' : '' }} class="w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500">
                                <span class="text-sm text-gray-600">Static Maps</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mapbox -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-lg settings-card">
                <div class="p-5 border-b border-gray-100 flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-gray-900" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-semibold text-gray-900">Mapbox</h3>
                            <p class="text-xs text-gray-500">Vector maps provider</p>
                        </div>
                    </div>
                    <label class="geo-toggle">
                        <input type="checkbox" name="settings[mapbox_enabled]" value="1" {{ isChecked('mapbox_enabled') ? 'checked' : '' }}>
                        <span class="slider"></span>
                    </label>
                </div>
                
                <div class="p-5">
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Access Token</label>
                        <div class="relative">
                            <input type="password" name="settings[mapbox_key]" value="{{ getSetting('mapbox_key') ? '••••••••••••' : '' }}" placeholder="Enter your access token" class="w-full bg-gray-50 border border-gray-200 rounded-xl py-3 px-4 text-sm text-gray-700 focus:border-gray-900 focus:bg-white focus:ring-2 focus:ring-gray-900/10 outline-none transition-all">
                            <div class="absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Default Location -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-lg settings-card">
            <div class="px-5 py-4 border-b border-gray-100 bg-gray-50/50">
                <h3 class="text-base font-semibold text-gray-900">Default Location</h3>
                <p class="text-xs text-gray-500">System fallback coordinates</p>
            </div>
            
            <div class="p-5">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Latitude</label>
                        <input type="text" name="settings[default_latitude]" value="{{ getSetting('default_latitude', '5.6037') }}" class="w-full bg-gray-50 border border-gray-200 rounded-xl py-3 px-4 text-sm text-gray-700 focus:border-brand focus:bg-white transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Longitude</label>
                        <input type="text" name="settings[default_longitude]" value="{{ getSetting('default_longitude', '-0.1870') }}" class="w-full bg-gray-50 border border-gray-200 rounded-xl py-3 px-4 text-sm text-gray-700 focus:border-brand focus:bg-white transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Zoom Level</label>
                        @php $currentZoom = getSetting('default_zoom_level', '14'); @endphp
                        <select name="settings[default_zoom_level]" class="geo-select w-full bg-gray-50 border border-gray-200 rounded-xl py-3 px-4 text-sm text-gray-700 focus:border-brand focus:bg-white transition-all">
                            <option value="8" {{ $currentZoom == '8' ? 'selected' : '' }}>8 - Continent</option>
                            <option value="10" {{ $currentZoom == '10' ? 'selected' : '' }}>10 - Region</option>
                            <option value="12" {{ $currentZoom == '12' ? 'selected' : '' }}>12 - City</option>
                            <option value="14" {{ $currentZoom == '14' ? 'selected' : '' }}>14 - Neighborhood</option>
                            <option value="16" {{ $currentZoom == '16' ? 'selected' : '' }}>16 - Street</option>
                            <option value="18" {{ $currentZoom == '18' ? 'selected' : '' }}>18 - Building</option>
                        </select>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mt-5">
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Map Style</label>
                        @php $currentStyle = getSetting('default_map_style', 'streets'); @endphp
                        <select name="settings[default_map_style]" class="geo-select w-full bg-gray-50 border border-gray-200 rounded-xl py-3 px-4 text-sm text-gray-700 focus:border-brand focus:bg-white transition-all">
                            <option value="streets" {{ $currentStyle == 'streets' ? 'selected' : '' }}>Streets</option>
                            <option value="satellite" {{ $currentStyle == 'satellite' ? 'selected' : '' }}>Satellite</option>
                            <option value="light" {{ $currentStyle == 'light' ? 'selected' : '' }}>Light</option>
                            <option value="dark" {{ $currentStyle == 'dark' ? 'selected' : '' }}>Dark</option>
                            <option value="navigation" {{ $currentStyle == 'navigation' ? 'selected' : '' }}>Navigation</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Distance Unit</label>
                        @php $currentUnit = getSetting('distance_unit', 'km'); @endphp
                        <select name="settings[distance_unit]" class="geo-select w-full bg-gray-50 border border-gray-200 rounded-xl py-3 px-4 text-sm text-gray-700 focus:border-brand focus:bg-white transition-all">
                            <option value="km" {{ $currentUnit == 'km' ? 'selected' : '' }}>Kilometers (km)</option>
                            <option value="miles" {{ $currentUnit == 'miles' ? 'selected' : '' }}>Miles (mi)</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Geocoding -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-lg settings-card">
            <div class="px-5 py-4 border-b border-gray-100 bg-gray-50/50">
                <h3 class="text-base font-semibold text-gray-900">Geocoding</h3>
                <p class="text-xs text-gray-500">Address lookup preferences</p>
            </div>
            
            <div class="p-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Default Country</label>
                        @php $currentCountry = getSetting('geocoding_country', 'GH'); @endphp
                        <select name="settings[geocoding_country]" class="geo-select w-full bg-gray-50 border border-gray-200 rounded-xl py-3 px-4 text-sm text-gray-700 focus:border-brand focus:bg-white transition-all">
                            <option value="GH" {{ $currentCountry == 'GH' ? 'selected' : '' }}>Ghana</option>
                            <option value="NG" {{ $currentCountry == 'NG' ? 'selected' : '' }}>Nigeria</option>
                            <option value="KE" {{ $currentCountry == 'KE' ? 'selected' : '' }}>Kenya</option>
                            <option value="ZA" {{ $currentCountry == 'ZA' ? 'selected' : '' }}>South Africa</option>
                            <option value="US" {{ $currentCountry == 'US' ? 'selected' : '' }}>United States</option>
                            <option value="GB" {{ $currentCountry == 'GB' ? 'selected' : '' }}>United Kingdom</option>
                            <option value="worldwide" {{ $currentCountry == 'worldwide' ? 'selected' : '' }}>Worldwide</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Language</label>
                        @php $currentLang = getSetting('geocoding_language', 'en'); @endphp
                        <select name="settings[geocoding_language]" class="geo-select w-full bg-gray-50 border border-gray-200 rounded-xl py-3 px-4 text-sm text-gray-700 focus:border-brand focus:bg-white transition-all">
                            <option value="en" {{ $currentLang == 'en' ? 'selected' : '' }}>English</option>
                            <option value="fr" {{ $currentLang == 'fr' ? 'selected' : '' }}>French</option>
                            <option value="es" {{ $currentLang == 'es' ? 'selected' : '' }}>Spanish</option>
                            <option value="de" {{ $currentLang == 'de' ? 'selected' : '' }}>German</option>
                            <option value="ar" {{ $currentLang == 'ar' ? 'selected' : '' }}>Arabic</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Real-time Tracking -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-lg settings-card">
            <div class="px-5 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
                <div>
                    <h3 class="text-base font-semibold text-gray-900">Real-time Tracking</h3>
                    <p class="text-xs text-gray-500">GPS and location settings</p>
                </div>
                <label class="geo-toggle">
                    <input type="checkbox" name="settings[geofencing_enabled]" value="1" {{ isChecked('geofencing_enabled') ? 'checked' : '' }}>
                    <span class="slider"></span>
                </label>
            </div>
            
            <div class="p-5">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">GPS Update Interval</label>
                        <div class="relative">
                            <input type="number" name="settings[gps_update_interval]" value="{{ getSetting('gps_update_interval', '5') }}" min="1" max="60" class="w-full bg-gray-50 border border-gray-200 rounded-xl py-3 px-4 pr-10 text-sm text-gray-700 focus:border-brand focus:bg-white transition-all">
                            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-gray-400">sec</span>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Location History</label>
                        <div class="relative">
                            <input type="number" name="settings[location_history_days]" value="{{ getSetting('location_history_days', '30') }}" min="1" max="365" class="w-full bg-gray-50 border border-gray-200 rounded-xl py-3 px-4 pr-10 text-sm text-gray-700 focus:border-brand focus:bg-white transition-all">
                            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-gray-400">days</span>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Search Radius</label>
                        <div class="relative">
                            <input type="number" name="settings[default_search_radius]" value="{{ getSetting('default_search_radius', '5') }}" min="1" max="50" class="w-full bg-gray-50 border border-gray-200 rounded-xl py-3 px-4 pr-10 text-sm text-gray-700 focus:border-brand focus:bg-white transition-all">
                            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-gray-400">km</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Travel Mode -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-lg settings-card">
            <div class="px-5 py-4 border-b border-gray-100 bg-gray-50/50">
                <h3 class="text-base font-semibold text-gray-900">Route Calculation</h3>
                <p class="text-xs text-gray-500">Default travel mode for ETAs</p>
            </div>
            
            <div class="p-5">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @php $mode = getSetting('default_travel_mode', 'driving'); @endphp
                    <label class="flex items-center gap-3 p-4 border rounded-lg cursor-pointer transition-all {{ $mode == 'driving' ? 'border-brand bg-brand/5' : 'border-gray-200 hover:border-gray-300' }}">
                        <input type="radio" name="settings[default_travel_mode]" value="driving" {{ $mode == 'driving' ? 'checked' : '' }} class="w-4 h-4 text-brand">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 bg-brand/10 rounded-lg flex items-center justify-center">
                                <svg class="w-4 h-4 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 17h8M8 17a2 2 0 11-4 0 2 2 0 014 0zM16 17a2 2 0 104 0 2 2 0 00-4 0zM4 16V7a2 2 0 012-2h12a2 2 0 012 2v9"/></svg>
                            </div>
                            <div>
                                <span class="text-sm font-medium text-gray-900">Driving</span>
                            </div>
                        </div>
                    </label>
                    <label class="flex items-center gap-3 p-4 border rounded-lg cursor-pointer transition-all {{ $mode == 'walking' ? 'border-brand bg-brand/5' : 'border-gray-200 hover:border-gray-300' }}">
                        <input type="radio" name="settings[default_travel_mode]" value="walking" {{ $mode == 'walking' ? 'checked' : '' }} class="w-4 h-4 text-brand">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 bg-brand/10 rounded-lg flex items-center justify-center">
                                <svg class="w-4 h-4 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                            </div>
                            <div>
                                <span class="text-sm font-medium text-gray-900">Walking</span>
                            </div>
                        </div>
                    </label>
                    <label class="flex items-center gap-3 p-4 border rounded-lg cursor-pointer transition-all {{ $mode == 'bicycling' ? 'border-brand bg-brand/5' : 'border-gray-200 hover:border-gray-300' }}">
                        <input type="radio" name="settings[default_travel_mode]" value="bicycling" {{ $mode == 'bicycling' ? 'checked' : '' }} class="w-4 h-4 text-brand">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 bg-brand/10 rounded-lg flex items-center justify-center">
                                <svg class="w-4 h-4 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4V1M8 4H5a2 2 0 00-2 2v9a2 2 0 002 2h3M16 4h3a2 2 0 012 2v9a2 2 0 01-2 2h-1M9 20l3-9 3 9"/></svg>
                            </div>
                            <div>
                                <span class="text-sm font-medium text-gray-900">Bicycling</span>
                            </div>
                        </div>
                    </label>
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="flex justify-end pt-2">
            <button type="submit" class="bg-brand text-white px-8 py-3.5 rounded-xl text-sm font-bold shadow-lg hover:shadow-brand/25 hover:-translate-y-0.5 transition-all flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Save Configuration
            </button>
        </div>
    </form>
</div>

@endsection