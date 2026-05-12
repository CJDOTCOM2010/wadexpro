@extends('admin.layout')
@section('title', 'Global Operations & Fleet Dynamics')
@section('content')

<div class="h-[calc(100vh-12rem)] flex gap-8">
    
    <!-- Left: Core Intelligence Map -->
    <div class="flex-1 bg-brand rounded-lg shadow-2xl relative overflow-hidden group border-4 border-white">
        
        <!-- Google Map Container -->
        <div id="operations-map" class="absolute inset-0 z-0 bg-[#0A0A1A]"></div>
        
        <!-- Fallback if no API key -->
        @if(empty($google_maps_api_key))
        <div class="absolute inset-0 bg-[#0A0A1A] opacity-90 z-0">
            <div class="absolute inset-0 opacity-20" style="background-image: radial-gradient(#F8B803 1px, transparent 1px); background-size: 40px 40px;"></div>
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[80%] h-[80%] border border-accent/20 rounded-full animate-ping opacity-20" style="animation-duration: 4s;"></div>
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[60%] h-[60%] border border-accent/10 rounded-full"></div>
            <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                <span class="text-white/20 font-black tracking-widest uppercase">MAP API OFFLINE</span>
            </div>
        </div>
        @endif

        <!-- Floating UI Overlays -->
        <div class="absolute top-8 left-8 flex flex-col gap-4 z-10 pointer-events-none">
            <div class="bg-brand/80 backdrop-blur-xl p-6 rounded-lg border border-white/10 shadow-2xl pointer-events-auto">
                <p class="text-[10px] font-black text-accent uppercase tracking-widest mb-1">Active Assets</p>
                <div class="flex items-center gap-4">
                    <h4 class="text-3xl font-black text-white">{{ number_format($liveNodes) }}</h4>
                    <span class="px-2 py-1 bg-green-500/20 text-green-400 text-[10px] font-black rounded-lg">LIVE</span>
                </div>
            </div>
            <a href="{{ route('orchestrator.dispatcher') }}" class="bg-accent p-6 rounded-lg border border-white/20 shadow-2xl flex items-center gap-4 group/btn hover:scale-105 transition-transform active:scale-95 pointer-events-auto">
                <div class="w-10 h-10 bg-brand text-white rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </div>
                <div>
                    <span class="text-[10px] font-black text-brand uppercase tracking-widest block mb-0.5">Tactical Hub</span>
                    <span class="text-brand font-bold text-sm">Launch Dispatcher</span>
                </div>
            </a>
        </div>

        <!-- Coordinate HUD -->
        <div class="absolute bottom-8 right-8 z-10 pointer-events-none">
            <div class="bg-brand/40 backdrop-blur-md px-6 py-4 rounded-lg border border-white/10 text-white/40 font-mono text-[10px] uppercase tracking-tighter">
                LAT: 5.6037° N | LONG: 0.1870° W | ALT: 12.0m
            </div>
        </div>

        <!-- Center Hub Overlay -->
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 text-center group z-10 pointer-events-none">
            <div class="relative">
                <div class="absolute inset-0 bg-accent rounded-full blur-2xl opacity-40 group-hover:opacity-80 transition-opacity"></div>
                <div class="relative w-16 h-16 bg-brand border-4 border-accent rounded-full flex items-center justify-center text-accent shadow-2xl scale-125 group-hover:scale-110 transition-transform">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Right: Event Telemetry Ticker -->
    <div class="w-[450px] bg-white rounded-lg border border-gray-100 flex flex-col overflow-hidden shadow-xl shrink-0">
        <div class="p-6 border-b border-gray-50 flex items-center justify-between">
            <h3 class="text-lg font-black text-brand tracking-tight">Node Events</h3>
            <span class="w-2 h-2 bg-red-500 rounded-full animate-pulse shadow-[0_0_8px_rgba(239,68,68,0.5)]"></span>
        </div>
        
        <div class="flex-1 overflow-y-auto p-6 space-y-6">
            
            @foreach($sosAlerts as $alert)
            <!-- SOS Alert -->
            <div class="relative pl-5 border-l-2 border-red-500 bg-red-50/50 p-3 rounded-r">
                <div class="absolute -left-[5px] top-4 w-2 h-2 bg-red-500 rounded-full animate-ping"></div>
                <div class="absolute -left-[5px] top-4 w-2 h-2 bg-red-500 rounded-full"></div>
                <p class="text-xs font-black text-red-600 uppercase tracking-wider mb-1">SOS SIGNAL DETECTED</p>
                <p class="text-sm font-medium text-red-800 leading-relaxed">Alert triggered on Ride <a href="#" class="font-bold underline">{{ $alert->ride->reference ?? 'Unknown' }}</a>. Immediate verification required.</p>
                <p class="text-[10px] text-red-400 font-bold uppercase mt-2">{{ $alert->created_at->format('H:i:s UTC') }}</p>
            </div>
            @endforeach

            @foreach($highValueOrders as $order)
            <!-- High Value Transaction -->
            <div class="relative pl-5 border-l-2 border-accent">
                <div class="absolute -left-[5px] top-1 w-2 h-2 bg-accent rounded-full"></div>
                <p class="text-xs font-black text-accent uppercase tracking-wider mb-1">High Value Transaction</p>
                <p class="text-sm font-medium text-brand-muted leading-relaxed">Order <a href="#" class="text-brand font-bold underline decoration-accent decoration-2">{{ $order->reference }}</a> value exceeding ₵500 detected (₵{{ number_format($order->total_amount) }}).</p>
                <p class="text-[10px] text-gray-400 font-bold uppercase mt-2">{{ $order->created_at->format('H:i:s UTC') }}</p>
            </div>
            @endforeach

            @foreach($recentDeployments as $driver)
            <!-- Driver Deployment -->
            <div class="relative pl-5 border-l-2 border-gray-200">
                <div class="absolute -left-[5px] top-1 w-2 h-2 bg-gray-300 rounded-full"></div>
                <p class="text-xs font-black text-brand uppercase tracking-wider mb-1">Node Online</p>
                <p class="text-sm font-medium text-brand-muted leading-relaxed">Node <span class="text-brand font-bold">{{ substr($driver->id, 0, 8) }}</span> ({{ $driver->user->name ?? 'Unknown' }}) established uplink.</p>
                <p class="text-[10px] text-gray-400 font-bold uppercase mt-2">{{ $driver->last_location_at ? $driver->last_location_at->format('H:i:s UTC') : 'Just now' }}</p>
            </div>
            @endforeach

            @if($sosAlerts->isEmpty() && $highValueOrders->isEmpty() && $recentDeployments->isEmpty())
                <p class="text-center text-gray-400 text-sm mt-10">No recent network events.</p>
            @endif

        </div>

        <div class="p-6 bg-surface/50 border-t border-gray-50 text-center">
            <span class="text-[10px] font-black text-brand-muted uppercase tracking-[0.2em] italic">Telemetry Active</span>
        </div>
    </div>
</div>

@if(!empty($google_maps_api_key))
<script async src="https://maps.googleapis.com/maps/api/js?key={{ $google_maps_api_key }}&callback=initOpsMap"></script>
<script>
    function initOpsMap() {
        const mapElement = document.getElementById('operations-map');
        if (!mapElement || typeof google === 'undefined') return;

        const map = new google.maps.Map(mapElement, {
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
                { featureType: "transit.station", elementType: "labels.text.fill", stylers: [{ color: "#d59563" }] },
                { featureType: "water", elementType: "geometry", stylers: [{ color: "#17263c" }] },
                { featureType: "water", elementType: "labels.text.fill", stylers: [{ color: "#515c6d" }] },
                { featureType: "water", elementType: "labels.text.stroke", stylers: [{ color: "#17263c" }] }
            ]
        });

        window.operationsMapInstance = map;
        const deployments = @json($recentDeployments);
        deployments.forEach((d, index) => {
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
</script>

<script type="module">
    document.addEventListener('DOMContentLoaded', () => {
        if (window.Echo) {
            window.Echo.channel('operations-map')
                .listen('.driver.location.updated', (e) => {
                    console.log('Driver Location Update:', e);
                    // Add/update driver marker visually
                    if (window.operationsMapInstance) {
                        if (!window.driverMarkers) window.driverMarkers = {};
                        
                        if (window.driverMarkers[e.driverId]) {
                            window.driverMarkers[e.driverId].setPosition({ lat: e.latitude, lng: e.longitude });
                            window.driverMarkers[e.driverId].setIcon({
                                path: google.maps.SymbolPath.CIRCLE,
                                scale: 6,
                                fillColor: e.isBusy ? '#F8B803' : '#22C55E',
                                fillOpacity: 1,
                                strokeColor: '#0A0A1A',
                                strokeWeight: 2,
                            });
                        } else {
                            window.driverMarkers[e.driverId] = new google.maps.Marker({
                                position: { lat: e.latitude, lng: e.longitude },
                                map: window.operationsMapInstance,
                                icon: {
                                    path: google.maps.SymbolPath.CIRCLE,
                                    scale: 6,
                                    fillColor: e.isBusy ? '#F8B803' : '#22C55E',
                                    fillOpacity: 1,
                                    strokeColor: '#0A0A1A',
                                    strokeWeight: 2,
                                },
                                title: e.driverId
                            });
                        }
                    }
                });
        }
    });
</script>
@endif

@endsection
