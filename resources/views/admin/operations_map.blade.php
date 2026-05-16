@extends('admin.layout')
@section('title', 'Operations Map')
@section('content')

@if(session('error'))
<div class="mb-4 p-3.5 bg-red-50 border border-red-200 rounded-lg flex items-center gap-2.5">
    <svg class="w-4 h-4 text-red-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    <p class="text-sm font-medium text-red-700">{{ session('error') }}</p>
</div>
@endif

<div class="h-[calc(100vh-10rem)] flex flex-col lg:flex-row gap-4">

    {{-- Left: Map --}}
    <div class="flex-1 bg-brand rounded-xl relative overflow-hidden min-h-[400px]">
        <div id="operations-map" class="absolute inset-0 z-0 bg-[#0A0A1A]"></div>

        @if(empty($google_maps_api_key))
        <div class="absolute inset-0 bg-[#0A0A1A] z-0">
            <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(#F8B803 1px, transparent 1px); background-size: 40px 40px;"></div>
            <div class="absolute inset-0 flex items-center justify-center">
                <div class="text-center">
                    <svg class="w-16 h-16 mx-auto mb-4 text-white/10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                    <p class="text-white/30 text-sm font-bold">Map API Key Not Configured</p>
                    <p class="text-white/20 text-xs mt-1">Add your Google Maps API key in Settings &gt; Geolocation</p>
                </div>
            </div>
        </div>
        @endif

        {{-- Top-left overlay --}}
        <div class="absolute top-4 left-4 z-10 flex flex-col gap-2">
            <div class="bg-brand/80 backdrop-blur-md px-4 py-3 rounded-lg border border-white/10 shadow-lg">
                <p class="text-[9px] font-bold text-accent uppercase tracking-widest mb-0.5">Active Assets</p>
                <div class="flex items-center gap-2.5">
                    <span class="text-2xl font-black text-white">{{ number_format($liveNodes ?? 0) }}</span>
                    <span class="px-2 py-0.5 bg-green-500/20 text-green-400 text-[9px] font-bold rounded">LIVE</span>
                </div>
            </div>
            <a href="{{ route('orchestrator.dispatcher') }}" class="bg-accent hover:bg-accent-hover transition-colors px-4 py-3 rounded-lg shadow-lg flex items-center gap-3">
                <div class="w-8 h-8 bg-brand text-white rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </div>
                <div>
                    <span class="text-[9px] font-bold text-brand uppercase tracking-wider block">Tactical Hub</span>
                    <span class="text-sm font-bold text-brand">Launch Dispatcher</span>
                </div>
            </a>
        </div>

        {{-- Bottom-right HUD --}}
        <div class="absolute bottom-4 right-4 z-10">
            <div class="bg-brand/60 backdrop-blur-md px-4 py-2.5 rounded-lg border border-white/10">
                <span class="text-white/40 font-mono text-[9px] uppercase tracking-tighter">LAT: 5.6037° N | LNG: -0.1870° W</span>
            </div>
        </div>
    </div>

    {{-- Right: Event Telemetry --}}
    <div class="lg:w-80 bg-white border border-gray-100 rounded-xl flex flex-col overflow-hidden shrink-0 max-h-[600px] lg:max-h-none">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between bg-surface/20">
            <div>
                <h3 class="text-sm font-bold text-brand">Node Events</h3>
                <p class="text-[10px] text-brand-muted">Real-time telemetry feed</p>
            </div>
            <span class="flex items-center gap-1.5 text-[9px] font-bold text-red-500">
                <span class="w-1.5 h-1.5 bg-red-500 rounded-full animate-pulse"></span> LIVE
            </span>
        </div>

        <div class="flex-1 overflow-y-auto p-4 space-y-3">
            @forelse($sosAlerts as $alert)
            <div class="pl-4 border-l-2 border-red-500 bg-red-50/50 p-3 rounded-r-lg">
                <div class="flex items-center gap-2 mb-1">
                    <span class="w-1.5 h-1.5 bg-red-500 rounded-full animate-ping"></span>
                    <span class="text-[10px] font-bold text-red-600 uppercase tracking-wider">SOS Signal</span>
                </div>
                <p class="text-xs text-red-800 leading-relaxed">Alert on ride <strong>{{ $alert->ride->reference ?? 'Unknown' }}</strong>. Immediate verification required.</p>
                <p class="text-[9px] text-red-400 font-bold mt-1.5">{{ $alert->created_at->format('H:i:s') }} UTC</p>
            </div>
            @endforeach

            @forelse($highValueOrders as $order)
            <div class="pl-4 border-l-2 border-accent p-3">
                <p class="text-[10px] font-bold text-accent uppercase tracking-wider mb-1">High Value Transaction</p>
                <p class="text-xs text-brand-muted leading-relaxed">Order <strong class="text-brand">{{ $order->reference }}</strong> valued at ₵{{ number_format($order->total_amount) }}.</p>
                <p class="text-[9px] text-gray-400 font-bold mt-1.5">{{ $order->created_at->format('H:i:s') }} UTC</p>
            </div>
            @endforeach

            @forelse($recentDeployments as $driver)
            <div class="pl-4 border-l-2 border-gray-200 p-3">
                <p class="text-[10px] font-bold text-brand uppercase tracking-wider mb-1">Node Online</p>
                <p class="text-xs text-brand-muted leading-relaxed">Driver <strong class="text-brand font-mono">{{ substr($driver->id, 0, 8) }}</strong> ({{ $driver->user->name ?? 'Unknown' }}) came online.</p>
                <p class="text-[9px] text-gray-400 font-bold mt-1.5">{{ $driver->last_location_at ? $driver->last_location_at->format('H:i:s') : 'Just now' }} UTC</p>
            </div>
            @endforeach

            @if($sosAlerts->isEmpty() && $highValueOrders->isEmpty() && $recentDeployments->isEmpty())
            <div class="flex flex-col items-center justify-center py-12 text-brand-muted">
                <svg class="w-12 h-12 mb-3 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <p class="text-sm font-bold">No recent events</p>
            </div>
            @endif
        </div>

        <div class="px-5 py-3 bg-surface/30 border-t border-gray-100 text-center">
            <span class="text-[9px] font-bold text-brand-muted uppercase tracking-wider">Telemetry Active</span>
        </div>
    </div>
</div>

@if(!empty($google_maps_api_key))
<script async src="https://maps.googleapis.com/maps/api/js?key={{ $google_maps_api_key }}&callback=initOpsMap"></script>
<script>
    function initOpsMap() {
        const el = document.getElementById('operations-map');
        if (!el || typeof google === 'undefined') return;

        const map = new google.maps.Map(el, {
            zoom: 12,
            center: { lat: 5.6037, lng: -0.1870 },
            disableDefaultUI: true,
            styles: [
                { elementType: "geometry", stylers: [{ color: "#0A0A1A" }] },
                { elementType: "labels.text.stroke", stylers: [{ color: "#242f3e" }] },
                { elementType: "labels.text.fill", stylers: [{ color: "#746855" }] },
                { featureType: "administrative.locality", elementType: "labels.text.fill", stylers: [{ color: "#d59563" }] },
                { featureType: "poi", elementType: "labels.text.fill", stylers: [{ color: "#d59563" }] },
                { featureType: "poi.park", elementType: "geometry", stylers: [{ color: "#263c3f" }] },
                { featureType: "poi.park", elementType: "labels.text.fill", stylers: [{ color: "#6b9a76" }] },
                { featureType: "road", elementType: "geometry", stylers: [{ color: "#38414e" }] },
                { featureType: "road", elementType: "geometry.stroke", stylers: [{ color: "#212a37" }] },
                { featureType: "road", elementType: "labels.text.fill", stylers: [{ color: "#9ca5b3" }] },
                { featureType: "road.highway", elementType: "geometry", stylers: [{ color: "#746855" }] },
                { featureType: "road.highway", elementType: "geometry.stroke", stylers: [{ color: "#1f2835" }] },
                { featureType: "road.highway", elementType: "labels.text.fill", stylers: [{ color: "#f3d19c" }] },
                { featureType: "transit", elementType: "geometry", stylers: [{ color: "#2f3948" }] },
                { featureType: "water", elementType: "geometry", stylers: [{ color: "#17263c" }] },
                { featureType: "water", elementType: "labels.text.fill", stylers: [{ color: "#515c6d" }] },
            ]
        });

        window.operationsMapInstance = map;
        const deployments = @json($recentDeployments);
        deployments.forEach(d => {
            new google.maps.Marker({
                position: { lat: 5.6037 + (Math.random() - 0.5) * 0.1, lng: -0.1870 + (Math.random() - 0.5) * 0.1 },
                map: map,
                icon: {
                    path: google.maps.SymbolPath.CIRCLE,
                    scale: 6,
                    fillColor: '#F8B803',
                    fillOpacity: 1,
                    strokeColor: '#0A0A1A',
                    strokeWeight: 2,
                },
                title: d.id
            });
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        if (window.Echo) {
            window.Echo.channel('operations-map')
                .listen('.driver.location.updated', (e) => {
                    if (!window.operationsMapInstance) return;
                    if (!window.driverMarkers) window.driverMarkers = {};
                    if (window.driverMarkers[e.driverId]) {
                        window.driverMarkers[e.driverId].setPosition({ lat: e.latitude, lng: e.longitude });
                    } else {
                        window.driverMarkers[e.driverId] = new google.maps.Marker({
                            position: { lat: e.latitude, lng: e.longitude },
                            map: window.operationsMapInstance,
                            icon: { path: google.maps.SymbolPath.CIRCLE, scale: 6, fillColor: e.isBusy ? '#F8B803' : '#22C55E', fillOpacity: 1, strokeColor: '#0A0A1A', strokeWeight: 2 },
                            title: e.driverId
                        });
                    }
                });
        }
    });
</script>
@endif

<style>
#operations-map { background: #0A0A1A; }
</style>
@endsection