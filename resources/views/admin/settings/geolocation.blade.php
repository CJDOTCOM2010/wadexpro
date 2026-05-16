@extends('admin.layout')
@section('title', 'Geolocation & Mapping Configuration')
@php
function gs($key, $default = null) {
    global $settings;
    return $settings[$key]->value ?? $default;
}
function gsChecked($key) {
    return gs($key, 'false') === 'true';
}
function gsInArray($key, $needle) {
    return in_array($needle, json_decode(gs($key, '[]'), true) ?? []);
}
@endphp
@section('content')

<div class="p-8 lg:p-12 max-w-5xl mx-auto" x-data="{
    gmOpen: false,
    gmLbl: '{{ gs('default_zoom_level', '14') }}',
    gmVal: '{{ gs('default_zoom_level', '14') }}',
    msOpen: false,
    msLbl: '{{ ['streets'=>'Streets','satellite'=>'Satellite','light'=>'Light','dark'=>'Dark','navigation'=>'Navigation'][gs('default_map_style', 'streets')] }}',
    msVal: '{{ gs('default_map_style', 'streets') }}',
    duOpen: false,
    duLbl: '{{ gs('distance_unit', 'km') == 'miles' ? 'Miles (mi)' : 'Kilometers (km)' }}',
    duVal: '{{ gs('distance_unit', 'km') }}',
    gcOpen: false,
    gcLbl: '{{ ['GH'=>'Ghana','NG'=>'Nigeria','KE'=>'Kenya','ZA'=>'South Africa','US'=>'United States','GB'=>'United Kingdom','worldwide'=>'Worldwide'][gs('geocoding_country', 'GH')] }}',
    gcVal: '{{ gs('geocoding_country', 'GH') }}',
    glOpen: false,
    glLbl: '{{ ['en'=>'English','fr'=>'French','es'=>'Spanish','de'=>'German','ar'=>'Arabic'][gs('geocoding_language', 'en')] }}',
    glVal: '{{ gs('geocoding_language', 'en') }}',
    closeAll() { this.gmOpen=false; this.msOpen=false; this.duOpen=false; this.gcOpen=false; this.glOpen=false; },
    select(dropdown, val, lbl) { this[dropdown+'Val']=val; this[dropdown+'Lbl']=lbl; this[dropdown+'Open']=false; this.closeAll(); }
}">
    <form method="POST" action="{{ route('orchestrator.settings.update') }}">
        @csrf

        <div class="flex items-center justify-between mb-12">
            <div>
                <div class="flex items-center gap-2 text-[10px] font-black text-accent uppercase tracking-[0.2em] mb-2">
                    <a href="{{ route('orchestrator.settings') }}" class="hover:text-brand transition-colors">Settings Hub</a>
                    <span class="text-gray-300">/</span>
                    <span>Geolocation</span>
                </div>
                <h2 class="text-3xl font-black text-brand tracking-tight">Geospatial Configuration</h2>
                <p class="text-brand-muted font-medium mt-1">Configure mapping providers, geocoding, and real-time tracking settings.</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('orchestrator.settings') }}" class="bg-surface text-brand-muted hover:bg-gray-100 px-6 py-3 rounded-lg text-xs font-bold transition-all flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Back
                </a>
                <button type="submit" class="bg-brand text-white hover:bg-brand-light px-8 py-3 rounded-lg text-xs font-bold transition-all flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Save Changes
                </button>
            </div>
        </div>

        @if(session('success'))
        <div class="mb-8 p-4 bg-green-50 border border-green-200 rounded-xl flex items-center gap-3">
            <svg class="w-5 h-5 text-green-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <p class="text-sm font-medium text-green-700">{{ session('success') }}</p>
        </div>
        @endif

        <div class="space-y-8">

            {{-- Maps Providers --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm">
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-[#4285F4]/10 rounded-xl flex items-center justify-center">
                                <svg class="w-5 h-5 text-[#4285F4]" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-gray-900">Google Maps</h3>
                                <p class="text-[11px] text-gray-500">Primary mapping engine</p>
                            </div>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="settings[google_maps_enabled]" value="1" {{ gsChecked('google_maps_enabled') ? 'checked' : '' }} class="sr-only peer">
                            <div class="w-10 h-5 bg-gray-200 peer-focus:ring-2 peer-focus:ring-brand/20 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[3px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-brand"></div>
                        </label>
                    </div>
                    <div class="space-y-4">
                        <div>
                            <label class="text-[10px] font-black text-brand-muted uppercase tracking-widest mb-2 block">API Key</label>
                            <input type="password" name="settings[google_maps_key]" value="{{ gs('google_maps_key') ? '********' : '' }}" placeholder="Enter your Google Maps API key" class="w-full bg-surface border border-gray-100 rounded-xl px-4 py-3 text-sm font-medium text-gray-700 focus:ring-2 focus:ring-brand/20 focus:border-brand outline-none transition-all">
                        </div>
                        <div>
                            <label class="text-[10px] font-black text-brand-muted uppercase tracking-widest mb-3 block">Enabled Features</label>
                            <div class="grid grid-cols-2 gap-2.5">
                                @foreach(['directions'=>'Directions','places'=>'Places Autocomplete','geocoding'=>'Geocoding','static'=>'Static Maps'] as $gk=>$gv)
                                <label class="flex items-center gap-2.5 cursor-pointer p-2.5 rounded-lg hover:bg-surface transition-colors">
                                    <input type="checkbox" name="settings[google_maps_{{$gk}}][]" value="{{ $gk }}" {{ gsInArray('google_maps_'.$gk, $gk) ? 'checked' : '' }} class="w-4 h-4 rounded border-gray-300 text-brand focus:ring-brand/30">
                                    <span class="text-sm text-gray-600">{{ $gv }}</span>
                                </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm">
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-gray-900/5 rounded-xl flex items-center justify-center">
                                <svg class="w-5 h-5 text-gray-900" viewBox="0 0 24 24" fill="currentColor"><path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm0 22c-5.523 0-10-4.477-10-10S6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z"/><path d="M12 5c-3.866 0-7 3.134-7 7s3.134 7 7 7 7-3.134 7-7-3.134-7-7-7z"/></svg>
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-gray-900">Mapbox</h3>
                                <p class="text-[11px] text-gray-500">Vector map tiles</p>
                            </div>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="settings[mapbox_enabled]" value="1" {{ gsChecked('mapbox_enabled') ? 'checked' : '' }} class="sr-only peer">
                            <div class="w-10 h-5 bg-gray-200 peer-focus:ring-2 peer-focus:ring-brand/20 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[3px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-brand"></div>
                        </label>
                    </div>
                    <div class="space-y-3">
                        <label class="text-[10px] font-black text-brand-muted uppercase tracking-widest mb-2 block">Public Access Token</label>
                        <input type="password" name="settings[mapbox_key]" value="{{ gs('mapbox_key') ? '********' : '' }}" placeholder="Enter your Mapbox token" class="w-full bg-surface border border-gray-100 rounded-xl px-4 py-3 text-sm font-medium text-gray-700 focus:ring-2 focus:ring-brand/20 focus:border-brand outline-none transition-all">
                    </div>
                </div>
            </div>

            {{-- Default Location --}}
            <div class="bg-white rounded-2xl border border-gray-100 p-8 shadow-sm">
                <div class="flex items-center gap-4 mb-8">
                    <div class="w-12 h-12 bg-accent/10 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-black text-brand">Default Location</h3>
                        <p class="text-xs text-brand-muted font-medium">System fallback coordinates and map defaults</p>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="text-[10px] font-black text-brand-muted uppercase tracking-widest mb-2 block">Latitude</label>
                        <input type="text" name="settings[default_latitude]" value="{{ gs('default_latitude', '5.6037') }}" placeholder="5.6037" class="w-full bg-surface border border-gray-100 rounded-xl px-4 py-3 text-sm font-medium text-gray-700 focus:ring-2 focus:ring-brand/20 focus:border-brand outline-none transition-all">
                    </div>
                    <div>
                        <label class="text-[10px] font-black text-brand-muted uppercase tracking-widest mb-2 block">Longitude</label>
                        <input type="text" name="settings[default_longitude]" value="{{ gs('default_longitude', '-0.1870') }}" placeholder="-0.1870" class="w-full bg-surface border border-gray-100 rounded-xl px-4 py-3 text-sm font-medium text-gray-700 focus:ring-2 focus:ring-brand/20 focus:border-brand outline-none transition-all">
                    </div>
                    <div>
                        <label class="text-[10px] font-black text-brand-muted uppercase tracking-widest mb-2 block">Zoom Level</label>
                        <input type="hidden" name="settings[default_zoom_level]" :value="gmVal">
                        <div class="relative">
                            <button type="button" @click="closeAll(); gmOpen=!gmOpen" class="w-full bg-surface border border-gray-100 rounded-xl px-4 py-3 text-sm font-medium text-gray-700 hover:bg-white focus:ring-2 focus:ring-brand/20 focus:border-brand outline-none transition-all flex items-center justify-between">
                                <span x-text="gmLbl" class="text-left"></span>
                                <svg class="w-4 h-4 text-gray-400 transition-transform" :class="gmOpen && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <div x-show="gmOpen" @click.outside="gmOpen=false" x-cloak class="absolute z-20 w-full mt-1.5 bg-white border border-gray-100 rounded-xl shadow-xl py-1">
                                @foreach(['8'=>'8 - Continent View','10'=>'10 - Region View','12'=>'12 - City View','14'=>'14 - Neighborhood','16'=>'16 - Street View','18'=>'18 - Building View'] as $val=>$lbl)
                                <button type="button" @click="select('gm', '{{ $val }}', '{{ $lbl }}')" class="w-full text-left px-4 py-2.5 text-sm flex items-center justify-between transition-colors" :class="gmVal === '{{ $val }}' ? 'text-brand font-bold bg-brand/5' : 'text-gray-700 hover:bg-gray-50'">
                                    <span>{{ $lbl }}</span>
                                    <svg x-show="gmVal === '{{ $val }}'" class="w-4 h-4 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                </button>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                    <div>
                        <label class="text-[10px] font-black text-brand-muted uppercase tracking-widest mb-2 block">Map Style</label>
                        <input type="hidden" name="settings[default_map_style]" :value="msVal">
                        <div class="relative">
                            <button type="button" @click="closeAll(); msOpen=!msOpen" class="w-full bg-surface border border-gray-100 rounded-xl px-4 py-3 text-sm font-medium text-gray-700 hover:bg-white focus:ring-2 focus:ring-brand/20 focus:border-brand outline-none transition-all flex items-center justify-between">
                                <span x-text="msLbl" class="text-left"></span>
                                <svg class="w-4 h-4 text-gray-400 transition-transform" :class="msOpen && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <div x-show="msOpen" @click.outside="msOpen=false" x-cloak class="absolute z-20 w-full mt-1.5 bg-white border border-gray-100 rounded-xl shadow-xl py-1">
                                @foreach(['streets'=>'Streets','satellite'=>'Satellite','light'=>'Light','dark'=>'Dark','navigation'=>'Navigation'] as $val=>$lbl)
                                <button type="button" @click="select('ms', '{{ $val }}', '{{ $lbl }}')" class="w-full text-left px-4 py-2.5 text-sm flex items-center justify-between transition-colors" :class="msVal === '{{ $val }}' ? 'text-brand font-bold bg-brand/5' : 'text-gray-700 hover:bg-gray-50'">
                                    <span>{{ $lbl }}</span>
                                    <svg x-show="msVal === '{{ $val }}'" class="w-4 h-4 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                </button>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div>
                        <label class="text-[10px] font-black text-brand-muted uppercase tracking-widest mb-2 block">Distance Unit</label>
                        <input type="hidden" name="settings[distance_unit]" :value="duVal">
                        <div class="relative">
                            <button type="button" @click="closeAll(); duOpen=!duOpen" class="w-full bg-surface border border-gray-100 rounded-xl px-4 py-3 text-sm font-medium text-gray-700 hover:bg-white focus:ring-2 focus:ring-brand/20 focus:border-brand outline-none transition-all flex items-center justify-between">
                                <span x-text="duLbl" class="text-left"></span>
                                <svg class="w-4 h-4 text-gray-400 transition-transform" :class="duOpen && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <div x-show="duOpen" @click.outside="duOpen=false" x-cloak class="absolute z-20 w-full mt-1.5 bg-white border border-gray-100 rounded-xl shadow-xl py-1">
                                @foreach(['km'=>'Kilometers (km)','miles'=>'Miles (mi)'] as $val=>$lbl)
                                <button type="button" @click="select('du', '{{ $val }}', '{{ $lbl }}')" class="w-full text-left px-4 py-2.5 text-sm flex items-center justify-between transition-colors" :class="duVal === '{{ $val }}' ? 'text-brand font-bold bg-brand/5' : 'text-gray-700 hover:bg-gray-50'">
                                    <span>{{ $lbl }}</span>
                                    <svg x-show="duVal === '{{ $val }}'" class="w-4 h-4 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                </button>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Geocoding --}}
            <div class="bg-white rounded-2xl border border-gray-100 p-8 shadow-sm">
                <div class="flex items-center gap-4 mb-8">
                    <div class="w-12 h-12 bg-accent/10 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-black text-brand">Geocoding</h3>
                        <p class="text-xs text-brand-muted font-medium">Address lookup and location search preferences</p>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="text-[10px] font-black text-brand-muted uppercase tracking-widest mb-2 block">Default Country</label>
                        <input type="hidden" name="settings[geocoding_country]" :value="gcVal">
                        <div class="relative">
                            <button type="button" @click="closeAll(); gcOpen=!gcOpen" class="w-full bg-surface border border-gray-100 rounded-xl px-4 py-3 text-sm font-medium text-gray-700 hover:bg-white focus:ring-2 focus:ring-brand/20 focus:border-brand outline-none transition-all flex items-center justify-between">
                                <span x-text="gcLbl" class="text-left"></span>
                                <svg class="w-4 h-4 text-gray-400 transition-transform" :class="gcOpen && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <div x-show="gcOpen" @click.outside="gcOpen=false" x-cloak class="absolute z-20 w-full mt-1.5 bg-white border border-gray-100 rounded-xl shadow-xl py-1 max-h-52 overflow-y-auto">
                                @foreach(['GH'=>'Ghana','NG'=>'Nigeria','KE'=>'Kenya','ZA'=>'South Africa','US'=>'United States','GB'=>'United Kingdom','worldwide'=>'Worldwide'] as $val=>$lbl)
                                <button type="button" @click="select('gc', '{{ $val }}', '{{ $lbl }}')" class="w-full text-left px-4 py-2.5 text-sm flex items-center justify-between transition-colors" :class="gcVal === '{{ $val }}' ? 'text-brand font-bold bg-brand/5' : 'text-gray-700 hover:bg-gray-50'">
                                    <span>{{ $lbl }}</span>
                                    <svg x-show="gcVal === '{{ $val }}'" class="w-4 h-4 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                </button>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div>
                        <label class="text-[10px] font-black text-brand-muted uppercase tracking-widest mb-2 block">Language</label>
                        <input type="hidden" name="settings[geocoding_language]" :value="glVal">
                        <div class="relative">
                            <button type="button" @click="closeAll(); glOpen=!glOpen" class="w-full bg-surface border border-gray-100 rounded-xl px-4 py-3 text-sm font-medium text-gray-700 hover:bg-white focus:ring-2 focus:ring-brand/20 focus:border-brand outline-none transition-all flex items-center justify-between">
                                <span x-text="glLbl" class="text-left"></span>
                                <svg class="w-4 h-4 text-gray-400 transition-transform" :class="glOpen && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <div x-show="glOpen" @click.outside="glOpen=false" x-cloak class="absolute z-20 w-full mt-1.5 bg-white border border-gray-100 rounded-xl shadow-xl py-1">
                                @foreach(['en'=>'English','fr'=>'French','es'=>'Spanish','de'=>'German','ar'=>'Arabic'] as $val=>$lbl)
                                <button type="button" @click="select('gl', '{{ $val }}', '{{ $lbl }}')" class="w-full text-left px-4 py-2.5 text-sm flex items-center justify-between transition-colors" :class="glVal === '{{ $val }}' ? 'text-brand font-bold bg-brand/5' : 'text-gray-700 hover:bg-gray-50'">
                                    <span>{{ $lbl }}</span>
                                    <svg x-show="glVal === '{{ $val }}'" class="w-4 h-4 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                </button>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Real-time Tracking --}}
            <div class="bg-white rounded-2xl border border-gray-100 p-8 shadow-sm">
                <div class="flex items-center justify-between mb-8">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-accent/10 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-black text-brand">Real-time Tracking</h3>
                            <p class="text-xs text-brand-muted font-medium">GPS intervals, location retention, and radius limits</p>
                        </div>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="settings[geofencing_enabled]" value="1" {{ gsChecked('geofencing_enabled') ? 'checked' : '' }} class="sr-only peer">
                        <div class="w-10 h-5 bg-gray-200 peer-focus:ring-2 peer-focus:ring-brand/20 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[3px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-brand"></div>
                    </label>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="text-[10px] font-black text-brand-muted uppercase tracking-widest mb-2 block">GPS Update Interval</label>
                        <div class="relative">
                            <input type="number" name="settings[gps_update_interval]" value="{{ gs('gps_update_interval', '5') }}" min="1" max="60" class="w-full bg-surface border border-gray-100 rounded-xl px-4 py-3 pr-14 text-sm font-medium text-gray-700 focus:ring-2 focus:ring-brand/20 focus:border-brand outline-none transition-all">
                            <span class="absolute right-4 top-1/2 -translate-y-1/2 text-[10px] font-bold text-brand-muted">seconds</span>
                        </div>
                    </div>
                    <div>
                        <label class="text-[10px] font-black text-brand-muted uppercase tracking-widest mb-2 block">Location History</label>
                        <div class="relative">
                            <input type="number" name="settings[location_history_days]" value="{{ gs('location_history_days', '30') }}" min="1" max="365" class="w-full bg-surface border border-gray-100 rounded-xl px-4 py-3 pr-14 text-sm font-medium text-gray-700 focus:ring-2 focus:ring-brand/20 focus:border-brand outline-none transition-all">
                            <span class="absolute right-4 top-1/2 -translate-y-1/2 text-[10px] font-bold text-brand-muted">days</span>
                        </div>
                    </div>
                    <div>
                        <label class="text-[10px] font-black text-brand-muted uppercase tracking-widest mb-2 block">Search Radius</label>
                        <div class="relative">
                            <input type="number" name="settings[default_search_radius]" value="{{ gs('default_search_radius', '5') }}" min="1" max="50" class="w-full bg-surface border border-gray-100 rounded-xl px-4 py-3 pr-14 text-sm font-medium text-gray-700 focus:ring-2 focus:ring-brand/20 focus:border-brand outline-none transition-all">
                            <span class="absolute right-4 top-1/2 -translate-y-1/2 text-[10px] font-bold text-brand-muted">km</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Route Calculation --}}
            <div class="bg-white rounded-2xl border border-gray-100 p-8 shadow-sm">
                <div class="flex items-center gap-4 mb-8">
                    <div class="w-12 h-12 bg-accent/10 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-black text-brand">Route Calculation</h3>
                        <p class="text-xs text-brand-muted font-medium">Default travel mode for ETA and distance</p>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @php $travelMode = gs('default_travel_mode', 'driving'); @endphp
                    @foreach(['driving'=>['Car, taxi, rideshare','<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 17h8M8 17a2 2 0 11-4 0 2 2 0 014 0zM16 17a2 2 0 104 0 2 2 0 00-4 0zM4 16V7a2 2 0 012-2h12a2 2 0 012 2v9"/><circle cx="4" cy="18" r="2"/>'],'walking'=>['Pedestrian routes','<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>'],'bicycling'=>['Bike routes','<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4V1M8 4H5a2 2 0 00-2 2v9a2 2 0 002 2h3M16 4h3a2 2 0 012 2v9a2 2 0 01-2 2h-1M9 20l3-9 3 9"/>']] as $val=>$info)
                    <label class="relative cursor-pointer">
                        <input type="radio" name="settings[default_travel_mode]" value="{{ $val }}" {{ $travelMode === $val ? 'checked' : '' }} class="peer sr-only">
                        <div class="p-5 rounded-xl border-2 border-gray-100 peer-checked:border-brand peer-checked:bg-brand/5 hover:border-brand/30 transition-all flex items-center gap-4">
                            <div class="w-10 h-10 bg-brand/10 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $info[1] !!}</svg>
                            </div>
                            <div>
                                <span class="text-sm font-bold text-gray-900 capitalize">{{ $val }}</span>
                                <p class="text-[11px] text-gray-500">{{ $info[0] }}</p>
                            </div>
                        </div>
                        <div class="absolute -top-2 -right-2 w-5 h-5 bg-brand text-white rounded-full items-center justify-center hidden peer-checked:flex">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                        </div>
                    </label>
                    @endforeach
                </div>
            </div>

        </div>
    </form>
</div>

@endsection