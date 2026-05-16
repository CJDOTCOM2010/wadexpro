@extends('admin.layout')
@section('title', 'Geolocation & Mapping Configuration')
@php
function gs($key, $default = null) {
    global $settings;
    if (!isset($settings[$key])) return $default;
    return $settings[$key]->value ?? $default;
}
function checked($key, $val = 'true') { return gs($key, 'false') === $val; }
function inArr($key, $needle) { return in_array($needle, json_decode(gs($key, '[]'), true) ?? []); }
function sel($key, $val, $default = null) { return (gs($key, $default) == $val) ? 'selected' : ''; }
@endphp
@section('content')

<div class="p-6 lg:p-10 max-w-7xl mx-auto">
    {{-- Breadcrumb --}}
    <div class="flex items-center gap-2 text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">
        <a href="{{ route('orchestrator.settings') }}" class="hover:text-brand transition-colors">Settings Hub</a>
        <span class="text-gray-300">/</span>
        <span class="text-brand">Geolocation</span>
    </div>
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-2xl font-black text-brand tracking-tight">Geolocation</h1>
            <p class="text-sm text-gray-500">Mapping providers, geocoding, and real-time tracking</p>
        </div>
        <span class="text-xs font-semibold text-emerald-700 bg-emerald-50 border border-emerald-200 px-3 py-1.5 rounded-lg flex items-center gap-1.5">
            <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></span>
            System Active
        </span>
    </div>

    <form action="{{ route('orchestrator.settings.update') }}" method="POST" class="space-y-6">
        @csrf

        {{-- ======================== PROVIDERS ======================== --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Google Maps --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-lg bg-blue-50 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5 text-blue-600" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-gray-900">Google Maps</h3>
                            <p class="text-[11px] text-gray-400">Primary mapping &amp; geocoding engine</p>
                        </div>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="settings[google_maps_enabled]" value="1" {{ checked('google_maps_enabled') ? 'checked' : '' }} class="sr-only peer">
                        <div class="w-10 h-5 bg-gray-200 peer-focus:ring-2 peer-focus:ring-blue-200 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-blue-600"></div>
                    </label>
                </div>
                <div class="px-5 py-4 space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 mb-1.5">API Key</label>
                        <div class="flex items-center border border-gray-200 rounded-lg overflow-hidden focus-within:ring-2 focus-within:ring-blue-100 focus-within:border-blue-300 transition-all">
                            <div class="flex items-center justify-center w-9 h-9 bg-gray-50 border-r border-gray-200 shrink-0">
                                <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                            </div>
                            <input type="password" name="settings[google_maps_key]" value="{{ gs('google_maps_key') ? '********' : '' }}" placeholder="Enter your API key" class="flex-1 min-w-0 px-3 py-2 text-sm text-gray-700 border-0 outline-none bg-transparent">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 mb-2">Enabled Features</label>
                        <div class="grid grid-cols-2 gap-2">
                            <label class="flex items-center gap-2 px-3 py-2 border border-gray-100 rounded-lg cursor-pointer hover:bg-blue-50 hover:border-blue-200 transition-all has-[:checked]:bg-blue-50 has-[:checked]:border-blue-300">
                                <input type="checkbox" name="settings[google_maps_directions][]" value="directions" {{ inArr('google_maps_directions', 'directions') ? 'checked' : '' }} class="w-3.5 h-3.5 text-blue-600 rounded border-gray-300 focus:ring-blue-200">
                                <span class="text-xs font-medium text-gray-600">Directions</span>
                            </label>
                            <label class="flex items-center gap-2 px-3 py-2 border border-gray-100 rounded-lg cursor-pointer hover:bg-blue-50 hover:border-blue-200 transition-all has-[:checked]:bg-blue-50 has-[:checked]:border-blue-300">
                                <input type="checkbox" name="settings[google_maps_places][]" value="places" {{ inArr('google_maps_places', 'places') ? 'checked' : '' }} class="w-3.5 h-3.5 text-blue-600 rounded border-gray-300 focus:ring-blue-200">
                                <span class="text-xs font-medium text-gray-600">Places</span>
                            </label>
                            <label class="flex items-center gap-2 px-3 py-2 border border-gray-100 rounded-lg cursor-pointer hover:bg-blue-50 hover:border-blue-200 transition-all has-[:checked]:bg-blue-50 has-[:checked]:border-blue-300">
                                <input type="checkbox" name="settings[google_maps_geocoding][]" value="geocoding" {{ inArr('google_maps_geocoding', 'geocoding') ? 'checked' : '' }} class="w-3.5 h-3.5 text-blue-600 rounded border-gray-300 focus:ring-blue-200">
                                <span class="text-xs font-medium text-gray-600">Geocoding</span>
                            </label>
                            <label class="flex items-center gap-2 px-3 py-2 border border-gray-100 rounded-lg cursor-pointer hover:bg-blue-50 hover:border-blue-200 transition-all has-[:checked]:bg-blue-50 has-[:checked]:border-blue-300">
                                <input type="checkbox" name="settings[google_maps_static][]" value="static" {{ inArr('google_maps_static', 'static') ? 'checked' : '' }} class="w-3.5 h-3.5 text-blue-600 rounded border-gray-300 focus:ring-blue-200">
                                <span class="text-xs font-medium text-gray-600">Static Maps</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Mapbox --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-lg bg-gray-50 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5 text-gray-700" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 15l-4-4 1.41-1.41L11 14.17l6.59-6.59L19 9l-8 8z"/></svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-gray-900">Mapbox</h3>
                            <p class="text-[11px] text-gray-400">Vector maps &amp; navigation</p>
                        </div>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="settings[mapbox_enabled]" value="1" {{ checked('mapbox_enabled') ? 'checked' : '' }} class="sr-only peer">
                        <div class="w-10 h-5 bg-gray-200 peer-focus:ring-2 peer-focus:ring-gray-200 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-gray-800"></div>
                    </label>
                </div>
                <div class="px-5 py-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 mb-1.5">Public Access Token</label>
                        <div class="flex items-center border border-gray-200 rounded-lg overflow-hidden focus-within:ring-2 focus-within:ring-gray-100 focus-within:border-gray-300 transition-all">
                            <div class="flex items-center justify-center w-9 h-9 bg-gray-50 border-r border-gray-200 shrink-0">
                                <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                            </div>
                            <input type="password" name="settings[mapbox_key]" value="{{ gs('mapbox_key') ? '********' : '' }}" placeholder="Enter your access token" class="flex-1 min-w-0 px-3 py-2 text-sm text-gray-700 border-0 outline-none bg-transparent">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ======================== DEFAULT LOCATION ======================== --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100">
                <h3 class="text-sm font-bold text-gray-900">Default Location</h3>
                <p class="text-[11px] text-gray-400 mt-0.5">Fallback coordinates when user location is unavailable</p>
            </div>
            <div class="px-5 py-4">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 mb-1.5">Latitude</label>
                        <input type="text" name="settings[default_latitude]" value="{{ gs('default_latitude', '5.6037') }}" placeholder="e.g. 5.6037" class="w-full px-3 py-2 text-sm text-gray-700 border border-gray-200 rounded-lg outline-none focus:border-brand focus:ring-2 focus:ring-brand/10 transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 mb-1.5">Longitude</label>
                        <input type="text" name="settings[default_longitude]" value="{{ gs('default_longitude', '-0.1870') }}" placeholder="e.g. -0.1870" class="w-full px-3 py-2 text-sm text-gray-700 border border-gray-200 rounded-lg outline-none focus:border-brand focus:ring-2 focus:ring-brand/10 transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 mb-1.5">Zoom Level</label>
                        <div class="relative">
                            <select name="settings[default_zoom_level]" class="w-full appearance-none px-3 py-2 pr-8 text-sm text-gray-700 border border-gray-200 rounded-lg outline-none bg-white focus:border-brand focus:ring-2 focus:ring-brand/10 transition-all cursor-pointer">
                                <option value="8" {{ sel('default_zoom_level', '8', '14') }}>8 – Continent</option>
                                <option value="10" {{ sel('default_zoom_level', '10', '14') }}>10 – Region</option>
                                <option value="12" {{ sel('default_zoom_level', '12', '14') }}>12 – City</option>
                                <option value="14" {{ sel('default_zoom_level', '14', '14') }}>14 – Neighborhood</option>
                                <option value="16" {{ sel('default_zoom_level', '16', '14') }}>16 – Street</option>
                                <option value="18" {{ sel('default_zoom_level', '18', '14') }}>18 – Building</option>
                            </select>
                            <svg class="absolute right-2.5 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </div>
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 mb-1.5">Map Style</label>
                        <div class="relative">
                            <select name="settings[default_map_style]" class="w-full appearance-none px-3 py-2 pr-8 text-sm text-gray-700 border border-gray-200 rounded-lg outline-none bg-white focus:border-brand focus:ring-2 focus:ring-brand/10 transition-all cursor-pointer">
                                <option value="streets" {{ sel('default_map_style', 'streets', 'streets') }}>Streets</option>
                                <option value="satellite" {{ sel('default_map_style', 'satellite', 'streets') }}>Satellite</option>
                                <option value="light" {{ sel('default_map_style', 'light', 'streets') }}>Light</option>
                                <option value="dark" {{ sel('default_map_style', 'dark', 'streets') }}>Dark</option>
                                <option value="navigation" {{ sel('default_map_style', 'navigation', 'streets') }}>Navigation</option>
                            </select>
                            <svg class="absolute right-2.5 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 mb-1.5">Distance Unit</label>
                        <div class="relative">
                            <select name="settings[distance_unit]" class="w-full appearance-none px-3 py-2 pr-8 text-sm text-gray-700 border border-gray-200 rounded-lg outline-none bg-white focus:border-brand focus:ring-2 focus:ring-brand/10 transition-all cursor-pointer">
                                <option value="km" {{ sel('distance_unit', 'km', 'km') }}>Kilometers (km)</option>
                                <option value="miles" {{ sel('distance_unit', 'miles', 'km') }}>Miles (mi)</option>
                            </select>
                            <svg class="absolute right-2.5 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ======================== GEOCODING ======================== --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100">
                <h3 class="text-sm font-bold text-gray-900">Geocoding</h3>
                <p class="text-[11px] text-gray-400 mt-0.5">Address lookup and location search preferences</p>
            </div>
            <div class="px-5 py-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 mb-1.5">Default Country</label>
                        <div class="relative">
                            <select name="settings[geocoding_country]" class="w-full appearance-none px-3 py-2 pr-8 text-sm text-gray-700 border border-gray-200 rounded-lg outline-none bg-white focus:border-brand focus:ring-2 focus:ring-brand/10 transition-all cursor-pointer">
                                <option value="GH" {{ sel('geocoding_country', 'GH', 'GH') }}>Ghana</option>
                                <option value="NG" {{ sel('geocoding_country', 'NG', 'GH') }}>Nigeria</option>
                                <option value="KE" {{ sel('geocoding_country', 'KE', 'GH') }}>Kenya</option>
                                <option value="ZA" {{ sel('geocoding_country', 'ZA', 'GH') }}>South Africa</option>
                                <option value="US" {{ sel('geocoding_country', 'US', 'GH') }}>United States</option>
                                <option value="GB" {{ sel('geocoding_country', 'GB', 'GH') }}>United Kingdom</option>
                                <option value="worldwide" {{ sel('geocoding_country', 'worldwide', 'GH') }}>Worldwide</option>
                            </select>
                            <svg class="absolute right-2.5 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 mb-1.5">Language</label>
                        <div class="relative">
                            <select name="settings[geocoding_language]" class="w-full appearance-none px-3 py-2 pr-8 text-sm text-gray-700 border border-gray-200 rounded-lg outline-none bg-white focus:border-brand focus:ring-2 focus:ring-brand/10 transition-all cursor-pointer">
                                <option value="en" {{ sel('geocoding_language', 'en', 'en') }}>English</option>
                                <option value="fr" {{ sel('geocoding_language', 'fr', 'en') }}>French</option>
                                <option value="es" {{ sel('geocoding_language', 'es', 'en') }}>Spanish</option>
                                <option value="de" {{ sel('geocoding_language', 'de', 'en') }}>German</option>
                                <option value="ar" {{ sel('geocoding_language', 'ar', 'en') }}>Arabic</option>
                            </select>
                            <svg class="absolute right-2.5 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ======================== REAL-TIME TRACKING ======================== --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-gray-900">Real-time Tracking</h3>
                    <p class="text-[11px] text-gray-400 mt-0.5">GPS updates, location history, and geofencing</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="settings[geofencing_enabled]" value="1" {{ checked('geofencing_enabled') ? 'checked' : '' }} class="sr-only peer">
                    <div class="w-10 h-5 bg-gray-200 peer-focus:ring-2 peer-focus:ring-brand/20 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-brand"></div>
                </label>
            </div>
            <div class="px-5 py-4">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 mb-1.5">GPS Update Interval</label>
                        <div class="flex items-center border border-gray-200 rounded-lg overflow-hidden focus-within:ring-2 focus-within:ring-brand/10 focus-within:border-brand transition-all">
                            <input type="number" name="settings[gps_update_interval]" value="{{ gs('gps_update_interval', '5') }}" min="1" max="60" class="flex-1 min-w-0 px-3 py-2 text-sm text-gray-700 border-0 outline-none">
                            <span class="px-3 py-2 text-xs font-medium text-gray-400 bg-gray-50 border-l border-gray-200">sec</span>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 mb-1.5">History Retention</label>
                        <div class="flex items-center border border-gray-200 rounded-lg overflow-hidden focus-within:ring-2 focus-within:ring-brand/10 focus-within:border-brand transition-all">
                            <input type="number" name="settings[location_history_days]" value="{{ gs('location_history_days', '30') }}" min="1" max="365" class="flex-1 min-w-0 px-3 py-2 text-sm text-gray-700 border-0 outline-none">
                            <span class="px-3 py-2 text-xs font-medium text-gray-400 bg-gray-50 border-l border-gray-200">days</span>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 mb-1.5">Search Radius</label>
                        <div class="flex items-center border border-gray-200 rounded-lg overflow-hidden focus-within:ring-2 focus-within:ring-brand/10 focus-within:border-brand transition-all">
                            <input type="number" name="settings[default_search_radius]" value="{{ gs('default_search_radius', '5') }}" min="1" max="50" class="flex-1 min-w-0 px-3 py-2 text-sm text-gray-700 border-0 outline-none">
                            <span class="px-3 py-2 text-xs font-medium text-gray-400 bg-gray-50 border-l border-gray-200">km</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ======================== ROUTE CALCULATION ======================== --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100">
                <h3 class="text-sm font-bold text-gray-900">Route Calculation</h3>
                <p class="text-[11px] text-gray-400 mt-0.5">Default travel mode for ETA and distance</p>
            </div>
            <div class="px-5 py-4">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <label class="flex items-center gap-3 px-4 py-3 border rounded-lg cursor-pointer transition-all {{ gs('default_travel_mode', 'driving') == 'driving' ? 'border-brand bg-brand/5' : 'border-gray-200 hover:border-gray-300 hover:bg-gray-50' }}">
                        <input type="radio" name="settings[default_travel_mode]" value="driving" {{ gs('default_travel_mode', 'driving') == 'driving' ? 'checked' : '' }} class="w-4 h-4 text-brand">
                        <div class="flex items-center gap-2.5">
                            <svg class="w-5 h-5 text-gray-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 17h8M8 17a2 2 0 11-4 0 2 2 0 014 0zM16 17a2 2 0 104 0 2 2 0 00-4 0zM4 16V7a2 2 0 012-2h12a2 2 0 012 2v9"/><circle cx="4" cy="18" r="2"/></svg>
                            <div>
                                <span class="text-sm font-semibold text-gray-800">Driving</span>
                                <p class="text-[10px] text-gray-400">Car, taxi</p>
                            </div>
                        </div>
                    </label>
                    <label class="flex items-center gap-3 px-4 py-3 border rounded-lg cursor-pointer transition-all {{ gs('default_travel_mode') == 'walking' ? 'border-brand bg-brand/5' : 'border-gray-200 hover:border-gray-300 hover:bg-gray-50' }}">
                        <input type="radio" name="settings[default_travel_mode]" value="walking" {{ gs('default_travel_mode') == 'walking' ? 'checked' : '' }} class="w-4 h-4 text-brand">
                        <div class="flex items-center gap-2.5">
                            <svg class="w-5 h-5 text-gray-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                            <div>
                                <span class="text-sm font-semibold text-gray-800">Walking</span>
                                <p class="text-[10px] text-gray-400">Pedestrian</p>
                            </div>
                        </div>
                    </label>
                    <label class="flex items-center gap-3 px-4 py-3 border rounded-lg cursor-pointer transition-all {{ gs('default_travel_mode') == 'bicycling' ? 'border-brand bg-brand/5' : 'border-gray-200 hover:border-gray-300 hover:bg-gray-50' }}">
                        <input type="radio" name="settings[default_travel_mode]" value="bicycling" {{ gs('default_travel_mode') == 'bicycling' ? 'checked' : '' }} class="w-4 h-4 text-brand">
                        <div class="flex items-center gap-2.5">
                            <svg class="w-5 h-5 text-gray-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4V1M8 4H5a2 2 0 00-2 2v9a2 2 0 002 2h3M16 4h3a2 2 0 012 2v9a2 2 0 01-2 2h-1M9 20l3-9 3 9"/></svg>
                            <div>
                                <span class="text-sm font-semibold text-gray-800">Bicycling</span>
                                <p class="text-[10px] text-gray-400">Bike routes</p>
                            </div>
                        </div>
                    </label>
                </div>
            </div>
        </div>

        {{-- ======================== SUBMIT ======================== --}}
        <div class="flex items-center justify-end gap-3 pt-2">
            <a href="{{ route('orchestrator.settings') }}" class="px-5 py-2.5 text-sm font-semibold text-gray-500 hover:text-gray-700 transition-colors">Cancel</a>
            <button type="submit" class="px-6 py-2.5 bg-brand text-white text-sm font-semibold rounded-lg hover:bg-brand-light shadow-sm transition-all flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Save Changes
            </button>
        </div>
    </form>
</div>

@endsection