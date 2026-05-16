@extends('admin.layout')
@section('title', 'Geolocation & Mapping Configuration')
@php
function getSetting($key, $default = null) {
    global $settings;
    return $settings[$key]->value ?? $default;
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

<div class="p-8 lg:p-12 max-w-[1400px] mx-auto">
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-10 gap-6">
        <div>
            <div class="flex items-center gap-2 text-[10px] font-black text-accent uppercase tracking-[0.2em] mb-2">
                <a href="{{ route('orchestrator.settings') }}" class="hover:text-brand transition-colors">Settings Hub</a>
                <span class="text-gray-300">/</span>
                <span>Geolocation</span>
            </div>
            <h2 class="text-3xl font-black text-brand tracking-tight">Geospatial Configuration</h2>
            <p class="text-sm text-brand-muted font-medium mt-1">Configure mapping providers, geocoding, and real-time tracking settings.</p>
        </div>
        <div class="flex items-center gap-3">
            <span class="text-xs font-bold text-green-600 bg-green-50 px-3 py-1.5 rounded-full flex items-center gap-1.5">
                <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                System Active
            </span>
        </div>
    </div>

    <form action="{{ route('orchestrator.settings.update') }}" method="POST" class="space-y-8">
        @csrf

        <div class="grid grid-cols-1 xl:grid-cols-2 gap-8">
            <!-- Google Maps Platform -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-lg overflow-hidden">
                <div class="p-6 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-[#4285F4]/10 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-[#4285F4]" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">Google Maps Platform</h3>
                            <p class="text-xs text-gray-500">Primary mapping and geocoding engine</p>
                        </div>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer group">
                        <input type="checkbox" name="settings[google_maps_enabled]" value="1" {{ isChecked('google_maps_enabled') ? 'checked' : '' }} class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-[#4285F4]/20 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#4285F4]"></div>
                    </label>
                </div>
                
                <div class="p-6 space-y-5">
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">API Key</label>
                        <div class="relative">
                            <input type="password" name="settings[google_maps_key]" value="{{ getSetting('google_maps_key') ? '********' : '' }}" placeholder="Enter Google Maps API key" class="w-full bg-gray-50 border border-gray-200 rounded-xl py-3 px-4 text-sm font-medium text-gray-700 focus:border-[#4285F4] focus:ring-2 focus:ring-[#4285F4]/10 outline-none transition-all">
                            <div class="absolute right-3 top-1/2 -translate-y-1/2">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                            </div>
                        </div>
                        <p class="text-[11px] text-gray-400 mt-1.5">Required for: Directions, Places Autocomplete, Geocoding, Static Maps</p>
                    </div>
                    
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Enabled Features</label>
                        <div class="grid grid-cols-2 gap-3">
                            <label class="flex items-center gap-2.5 cursor-pointer group">
                                <input type="checkbox" name="settings[google_maps_directions][]" value="directions" {{ inArraySetting('google_maps_directions', 'directions') ? 'checked' : '' }} class="w-4 h-4 text-[#4285F4] rounded border-gray-300 focus:ring-[#4285F4]">
                                <span class="text-sm text-gray-600 group-hover:text-gray-900">Directions</span>
                            </label>
                            <label class="flex items-center gap-2.5 cursor-pointer group">
                                <input type="checkbox" name="settings[google_maps_places][]" value="places" {{ inArraySetting('google_maps_places', 'places') ? 'checked' : '' }} class="w-4 h-4 text-[#4285F4] rounded border-gray-300 focus:ring-[#4285F4]">
                                <span class="text-sm text-gray-600 group-hover:text-gray-900">Places Autocomplete</span>
                            </label>
                            <label class="flex items-center gap-2.5 cursor-pointer group">
                                <input type="checkbox" name="settings[google_maps_geocoding][]" value="geocoding" {{ inArraySetting('google_maps_geocoding', 'geocoding') ? 'checked' : '' }} class="w-4 h-4 text-[#4285F4] rounded border-gray-300 focus:ring-[#4285F4]">
                                <span class="text-sm text-gray-600 group-hover:text-gray-900">Geocoding</span>
                            </label>
                            <label class="flex items-center gap-2.5 cursor-pointer group">
                                <input type="checkbox" name="settings[google_maps_static][]" value="static" {{ inArraySetting('google_maps_static', 'static') ? 'checked' : '' }} class="w-4 h-4 text-[#4285F4] rounded border-gray-300 focus:ring-[#4285F4]">
                                <span class="text-sm text-gray-600 group-hover:text-gray-900">Static Maps</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mapbox -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-lg overflow-hidden">
                <div class="p-6 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-[#000000]/5 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-gray-900" viewBox="0 0 24 24" fill="currentColor"><path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm0 22c-5.523 0-10-4.477-10-10S6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z"/><path d="M12 5c-3.866 0-7 3.134-7 7s3.134 7 7 7 7-3.134 7-7-3.134-7-7-7z"/></svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">Mapbox</h3>
                            <p class="text-xs text-gray-500">High-performance vector maps</p>
                        </div>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer group">
                        <input type="checkbox" name="settings[mapbox_enabled]" value="1" {{ isChecked('mapbox_enabled') ? 'checked' : '' }} class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-gray-900/20 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-gray-900"></div>
                    </label>
                </div>
                
                <div class="p-6 space-y-5">
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Public Access Token</label>
                        <div class="relative">
                            <input type="password" name="settings[mapbox_key]" value="{{ getSetting('mapbox_key') ? '********' : '' }}" placeholder="Enter Mapbox access token" class="w-full bg-gray-50 border border-gray-200 rounded-xl py-3 px-4 text-sm font-medium text-gray-700 focus:border-gray-900 focus:ring-2 focus:ring-gray-900/10 outline-none transition-all">
                            <div class="absolute right-3 top-1/2 -translate-y-1/2">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Default Location Settings -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-lg overflow-hidden">
            <div class="p-6 border-b border-gray-100 bg-gray-50">
                <h3 class="text-lg font-bold text-gray-900">Default Location Settings</h3>
                <p class="text-xs text-gray-500 mt-1">System fallback location when user location is not available</p>
            </div>
            
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Default Latitude</label>
                        <input type="text" name="settings[default_latitude]" value="{{ getSetting('default_latitude', '5.6037') }}" placeholder="e.g. 5.6037" class="w-full bg-gray-50 border border-gray-200 rounded-xl py-3 px-4 text-sm font-medium text-gray-700 focus:border-brand focus:ring-2 focus:ring-brand/10 outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Default Longitude</label>
                        <input type="text" name="settings[default_longitude]" value="{{ getSetting('default_longitude', '-0.1870') }}" placeholder="e.g. -0.1870" class="w-full bg-gray-50 border border-gray-200 rounded-xl py-3 px-4 text-sm font-medium text-gray-700 focus:border-brand focus:ring-2 focus:ring-brand/10 outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Default Zoom Level</label>
                        @php 
                            $currentZoom = getSetting('default_zoom_level', '14'); 
                            $zoomLabels = [
                                '8' => '8 - Continent View',
                                '10' => '10 - Region View',
                                '12' => '12 - City View',
                                '14' => '14 - Neighborhood',
                                '16' => '16 - Street View',
                                '18' => '18 - Building View'
                            ];
                            $zoomLabel = $zoomLabels[$currentZoom] ?? '14 - Neighborhood';
                        @endphp
                        <div x-data="{ open: false, value: '{{ $currentZoom }}', label: '{{ $zoomLabel }}' }" class="relative">
                            <input type="hidden" name="settings[default_zoom_level]" :value="value">
                            <button type="button" @click="open = !open" @click.outside="open = false" class="w-full bg-gray-50 border border-gray-200 rounded-xl py-3 px-4 text-sm font-medium text-gray-700 hover:bg-white focus:border-brand focus:ring-2 focus:ring-brand/10 outline-none transition-all flex items-center justify-between">
                                <span x-text="label"></span>
                                <svg class="w-4 h-4 text-gray-400 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <div x-show="open" x-transition.opacity.duration.200ms style="display: none;" class="absolute z-20 w-full mt-2 bg-white border border-gray-100 rounded-xl shadow-xl py-1.5 max-h-60 overflow-y-auto">
                                @foreach($zoomLabels as $val => $lbl)
                                <button type="button" @click="value = '{{ $val }}'; label = '{{ $lbl }}'; open = false" class="w-full text-left px-4 py-2.5 text-sm transition-colors flex items-center justify-between group" :class="value === '{{ $val }}' ? 'bg-brand/5 text-brand font-bold' : 'text-gray-700 hover:bg-gray-50 hover:text-brand'">
                                    <span>{{ $lbl }}</span>
                                    <svg x-show="value === '{{ $val }}'" class="w-4 h-4 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                </button>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Default Map Style</label>
                        @php 
                            $currentStyle = getSetting('default_map_style', 'streets'); 
                            $styleLabels = [
                                'streets' => 'Streets',
                                'satellite' => 'Satellite',
                                'light' => 'Light',
                                'dark' => 'Dark',
                                'navigation' => 'Navigation'
                            ];
                            $styleLabel = $styleLabels[$currentStyle] ?? 'Streets';
                        @endphp
                        <div x-data="{ open: false, value: '{{ $currentStyle }}', label: '{{ $styleLabel }}' }" class="relative">
                            <input type="hidden" name="settings[default_map_style]" :value="value">
                            <button type="button" @click="open = !open" @click.outside="open = false" class="w-full bg-gray-50 border border-gray-200 rounded-xl py-3 px-4 text-sm font-medium text-gray-700 hover:bg-white focus:border-brand focus:ring-2 focus:ring-brand/10 outline-none transition-all flex items-center justify-between">
                                <span x-text="label"></span>
                                <svg class="w-4 h-4 text-gray-400 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <div x-show="open" x-transition.opacity.duration.200ms style="display: none;" class="absolute z-20 w-full mt-2 bg-white border border-gray-100 rounded-xl shadow-xl py-1.5 max-h-60 overflow-y-auto">
                                @foreach($styleLabels as $val => $lbl)
                                <button type="button" @click="value = '{{ $val }}'; label = '{{ $lbl }}'; open = false" class="w-full text-left px-4 py-2.5 text-sm transition-colors flex items-center justify-between group" :class="value === '{{ $val }}' ? 'bg-brand/5 text-brand font-bold' : 'text-gray-700 hover:bg-gray-50 hover:text-brand'">
                                    <span>{{ $lbl }}</span>
                                    <svg x-show="value === '{{ $val }}'" class="w-4 h-4 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                </button>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Default Distance Unit</label>
                        @php 
                            $currentUnit = getSetting('distance_unit', 'km'); 
                            $unitLabels = [
                                'km' => 'Kilometers (km)',
                                'miles' => 'Miles (mi)'
                            ];
                            $unitLabel = $unitLabels[$currentUnit] ?? 'Kilometers (km)';
                        @endphp
                        <div x-data="{ open: false, value: '{{ $currentUnit }}', label: '{{ $unitLabel }}' }" class="relative">
                            <input type="hidden" name="settings[distance_unit]" :value="value">
                            <button type="button" @click="open = !open" @click.outside="open = false" class="w-full bg-gray-50 border border-gray-200 rounded-xl py-3 px-4 text-sm font-medium text-gray-700 hover:bg-white focus:border-brand focus:ring-2 focus:ring-brand/10 outline-none transition-all flex items-center justify-between">
                                <span x-text="label"></span>
                                <svg class="w-4 h-4 text-gray-400 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <div x-show="open" x-transition.opacity.duration.200ms style="display: none;" class="absolute z-20 w-full mt-2 bg-white border border-gray-100 rounded-xl shadow-xl py-1.5">
                                @foreach($unitLabels as $val => $lbl)
                                <button type="button" @click="value = '{{ $val }}'; label = '{{ $lbl }}'; open = false" class="w-full text-left px-4 py-2.5 text-sm transition-colors flex items-center justify-between group" :class="value === '{{ $val }}' ? 'bg-brand/5 text-brand font-bold' : 'text-gray-700 hover:bg-gray-50 hover:text-brand'">
                                    <span>{{ $lbl }}</span>
                                    <svg x-show="value === '{{ $val }}'" class="w-4 h-4 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                </button>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Geocoding Settings -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-lg overflow-hidden">
            <div class="p-6 border-b border-gray-100 bg-gray-50">
                <h3 class="text-lg font-bold text-gray-900">Geocoding Settings</h3>
                <p class="text-xs text-gray-500 mt-1">Configure address lookup and location search preferences</p>
            </div>
            
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Default Country</label>
                        @php 
                            $currentCountry = getSetting('geocoding_country', 'GH'); 
                            $countryLabels = [
                                'GH' => 'Ghana',
                                'NG' => 'Nigeria',
                                'KE' => 'Kenya',
                                'ZA' => 'South Africa',
                                'US' => 'United States',
                                'GB' => 'United Kingdom',
                                'worldwide' => 'Worldwide'
                            ];
                            $countryLabel = $countryLabels[$currentCountry] ?? 'Ghana';
                        @endphp
                        <div x-data="{ open: false, value: '{{ $currentCountry }}', label: '{{ $countryLabel }}' }" class="relative">
                            <input type="hidden" name="settings[geocoding_country]" :value="value">
                            <button type="button" @click="open = !open" @click.outside="open = false" class="w-full bg-gray-50 border border-gray-200 rounded-xl py-3 px-4 text-sm font-medium text-gray-700 hover:bg-white focus:border-brand focus:ring-2 focus:ring-brand/10 outline-none transition-all flex items-center justify-between">
                                <span x-text="label"></span>
                                <svg class="w-4 h-4 text-gray-400 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <div x-show="open" x-transition.opacity.duration.200ms style="display: none;" class="absolute z-20 w-full mt-2 bg-white border border-gray-100 rounded-xl shadow-xl py-1.5 max-h-60 overflow-y-auto">
                                @foreach($countryLabels as $val => $lbl)
                                <button type="button" @click="value = '{{ $val }}'; label = '{{ $lbl }}'; open = false" class="w-full text-left px-4 py-2.5 text-sm transition-colors flex items-center justify-between group" :class="value === '{{ $val }}' ? 'bg-brand/5 text-brand font-bold' : 'text-gray-700 hover:bg-gray-50 hover:text-brand'">
                                    <span>{{ $lbl }}</span>
                                    <svg x-show="value === '{{ $val }}'" class="w-4 h-4 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                </button>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Geocoding Language</label>
                        @php 
                            $currentLang = getSetting('geocoding_language', 'en'); 
                            $langLabels = [
                                'en' => 'English',
                                'fr' => 'French',
                                'es' => 'Spanish',
                                'de' => 'German',
                                'ar' => 'Arabic'
                            ];
                            $langLabel = $langLabels[$currentLang] ?? 'English';
                        @endphp
                        <div x-data="{ open: false, value: '{{ $currentLang }}', label: '{{ $langLabel }}' }" class="relative">
                            <input type="hidden" name="settings[geocoding_language]" :value="value">
                            <button type="button" @click="open = !open" @click.outside="open = false" class="w-full bg-gray-50 border border-gray-200 rounded-xl py-3 px-4 text-sm font-medium text-gray-700 hover:bg-white focus:border-brand focus:ring-2 focus:ring-brand/10 outline-none transition-all flex items-center justify-between">
                                <span x-text="label"></span>
                                <svg class="w-4 h-4 text-gray-400 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <div x-show="open" x-transition.opacity.duration.200ms style="display: none;" class="absolute z-20 w-full mt-2 bg-white border border-gray-100 rounded-xl shadow-xl py-1.5 max-h-60 overflow-y-auto">
                                @foreach($langLabels as $val => $lbl)
                                <button type="button" @click="value = '{{ $val }}'; label = '{{ $lbl }}'; open = false" class="w-full text-left px-4 py-2.5 text-sm transition-colors flex items-center justify-between group" :class="value === '{{ $val }}' ? 'bg-brand/5 text-brand font-bold' : 'text-gray-700 hover:bg-gray-50 hover:text-brand'">
                                    <span>{{ $lbl }}</span>
                                    <svg x-show="value === '{{ $val }}'" class="w-4 h-4 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                </button>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Real-time Tracking -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-lg overflow-hidden">
            <div class="p-6 border-b border-gray-100 bg-gray-50 flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Real-time Tracking</h3>
                    <p class="text-xs text-gray-500 mt-1">GPS tracking and driver location settings</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer group">
                    <input type="checkbox" name="settings[geofencing_enabled]" value="1" {{ isChecked('geofencing_enabled') ? 'checked' : '' }} class="sr-only peer">
                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-brand/20 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-brand"></div>
                </label>
            </div>
            
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">GPS Update Interval</label>
                        <div class="relative">
                            <input type="number" name="settings[gps_update_interval]" value="{{ getSetting('gps_update_interval', '5') }}" min="1" max="60" class="w-full bg-gray-50 border border-gray-200 rounded-xl py-3 px-4 pr-12 text-sm font-medium text-gray-700 focus:border-brand focus:ring-2 focus:ring-brand/10 outline-none transition-all">
                            <span class="absolute right-4 top-1/2 -translate-y-1/2 text-xs text-gray-400">seconds</span>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Location History Retention</label>
                        <div class="relative">
                            <input type="number" name="settings[location_history_days]" value="{{ getSetting('location_history_days', '30') }}" min="1" max="365" class="w-full bg-gray-50 border border-gray-200 rounded-xl py-3 px-4 pr-12 text-sm font-medium text-gray-700 focus:border-brand focus:ring-2 focus:ring-brand/10 outline-none transition-all">
                            <span class="absolute right-4 top-1/2 -translate-y-1/2 text-xs text-gray-400">days</span>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Default Search Radius</label>
                        <div class="relative">
                            <input type="number" name="settings[default_search_radius]" value="{{ getSetting('default_search_radius', '5') }}" min="1" max="50" class="w-full bg-gray-50 border border-gray-200 rounded-xl py-3 px-4 pr-12 text-sm font-medium text-gray-700 focus:border-brand focus:ring-2 focus:ring-brand/10 outline-none transition-all">
                            <span class="absolute right-4 top-1/2 -translate-y-1/2 text-xs text-gray-400">km</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Travel Mode Default -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-lg overflow-hidden">
            <div class="p-6 border-b border-gray-100 bg-gray-50">
                <h3 class="text-lg font-bold text-gray-900">Route Calculation</h3>
                <p class="text-xs text-gray-500 mt-1">Default travel mode for ETA and distance calculations</p>
            </div>
            
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <label class="flex items-center gap-3 p-4 border border-gray-200 rounded-xl cursor-pointer hover:border-brand hover:bg-brand/5 transition-all {{ getSetting('default_travel_mode', 'driving') == 'driving' ? 'border-brand bg-brand/5' : '' }}">
                        <input type="radio" name="settings[default_travel_mode]" value="driving" {{ getSetting('default_travel_mode', 'driving') == 'driving' ? 'checked' : '' }} class="w-5 h-5 text-brand">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-brand/10 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 17h8M8 17a2 2 0 11-4 0 2 2 0 014 0zM16 17a2 2 0 104 0 2 2 0 00-4 0zM4 16V7a2 2 0 012-2h12a2 2 0 012 2v9"/><circle cx="4" cy="18" r="2"/></svg>
                            </div>
                            <div>
                                <span class="text-sm font-bold text-gray-900">Driving</span>
                                <p class="text-[11px] text-gray-500">Car, taxi, rideshare</p>
                            </div>
                        </div>
                    </label>
                    <label class="flex items-center gap-3 p-4 border border-gray-200 rounded-xl cursor-pointer hover:border-brand hover:bg-brand/5 transition-all {{ getSetting('default_travel_mode') == 'walking' ? 'border-brand bg-brand/5' : '' }}">
                        <input type="radio" name="settings[default_travel_mode]" value="walking" {{ getSetting('default_travel_mode') == 'walking' ? 'checked' : '' }} class="w-5 h-5 text-brand">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-brand/10 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                            </div>
                            <div>
                                <span class="text-sm font-bold text-gray-900">Walking</span>
                                <p class="text-[11px] text-gray-500">Pedestrian routes</p>
                            </div>
                        </div>
                    </label>
                    <label class="flex items-center gap-3 p-4 border border-gray-200 rounded-xl cursor-pointer hover:border-brand hover:bg-brand/5 transition-all {{ getSetting('default_travel_mode') == 'bicycling' ? 'border-brand bg-brand/5' : '' }}">
                        <input type="radio" name="settings[default_travel_mode]" value="bicycling" {{ getSetting('default_travel_mode') == 'bicycling' ? 'checked' : '' }} class="w-5 h-5 text-brand">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-brand/10 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4V1M8 4H5a2 2 0 00-2 2v9a2 2 0 002 2h3M16 4h3a2 2 0 012 2v9a2 2 0 01-2 2h-1M9 20l3-9 3 9"/></svg>
                            </div>
                            <div>
                                <span class="text-sm font-bold text-gray-900">Bicycling</span>
                                <p class="text-[11px] text-gray-500">Bike routes</p>
                            </div>
                        </div>
                    </label>
                </div>
            </div>
        </div>

        <div class="flex justify-end pt-4">
            <button type="submit" class="bg-brand text-white px-8 py-4 rounded-xl text-sm font-bold shadow-lg hover:shadow-brand/25 hover:-translate-y-0.5 transition-all flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Save Configuration
            </button>
        </div>
    </form>
</div>

@endsection