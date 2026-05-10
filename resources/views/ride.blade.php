<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
    <title>WADEXPRO | Ride</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: { DEFAULT: '#000000', light: '#1A1A1A', muted: '#6B6B6B' },
                        accent: { DEFAULT: '#F8B803', hover: '#FFD60A' },
                        surface: { DEFAULT: '#F3F4F6', dark: '#E5E7EB' },
                    },
                    fontFamily: { outfit: ['Outfit', 'sans-serif'] },
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Outfit', sans-serif; -webkit-font-smoothing: antialiased; background-color: #ffffff; }
        [x-cloak] { display: none !important; }
        #map { width: 100%; height: 100%; background-color: #f3f4f6; }
    </style>
</head>
<body class="h-full bg-white text-brand overflow-hidden" x-data="rideApp()">

    <x-global-header theme="light" layout="app" />

    <main class="h-[calc(100vh-72px)] mt-[72px] flex flex-col lg:flex-row p-4 lg:p-6 gap-6">
        
        <!-- SIDEBAR: Ride Booking -->
        <aside class="w-full lg:w-[420px] bg-white rounded-2xl shadow-[0_4px_24px_rgba(0,0,0,0.06)] border border-gray-100 flex flex-col overflow-hidden shrink-0">
            <div class="p-6 lg:p-8 flex-1 overflow-y-auto">
                <h1 class="text-[28px] font-bold tracking-tight mb-8">Go anywhere</h1>

                <div class="space-y-4">
                    <!-- Pickup -->
                    <div class="relative group">
                        <div class="absolute left-4 top-1/2 -translate-y-1/2 text-brand">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="8" fill="none" stroke="currentColor" stroke-width="5"/><circle cx="12" cy="12" r="3" fill="currentColor"/></svg>
                        </div>
                        <input type="text" id="pickup-input" placeholder="Pickup location" 
                               class="w-full bg-[#EEEEEE] hover:bg-[#E2E2E2] focus:bg-white border-2 border-transparent focus:border-brand transition-all rounded-lg py-3.5 pl-12 pr-4 text-[15px] font-medium outline-none placeholder-gray-500">
                    </div>

                    <!-- Dropoff -->
                    <div class="relative group">
                        <div class="absolute left-4 top-1/2 -translate-y-1/2 text-brand">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><rect x="4" y="4" width="16" height="16" fill="currentColor"/></svg>
                        </div>
                        <input type="text" id="dropoff-input" placeholder="Where to?" 
                               class="w-full bg-[#EEEEEE] hover:bg-[#E2E2E2] focus:bg-white border-2 border-transparent focus:border-brand transition-all rounded-lg py-3.5 pl-12 pr-4 text-[15px] font-medium outline-none placeholder-gray-500">
                    </div>

                    <!-- Quick Options -->
                    <div class="pt-4 flex flex-col gap-2">
                        <button class="flex items-center gap-4 p-3 hover:bg-surface rounded-xl transition-colors text-left group">
                            <div class="w-10 h-10 bg-surface group-hover:bg-white rounded-full flex items-center justify-center text-brand transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                            </div>
                            <div>
                                <p class="text-sm font-bold">Home</p>
                                <p class="text-xs text-brand-muted">Set home address</p>
                            </div>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Action Area -->
            <div class="p-6 lg:p-8 pt-0">
                <button class="w-full bg-brand text-white py-4 rounded-xl text-[17px] font-bold hover:bg-brand/90 transition-all active:scale-[0.98] shadow-lg disabled:opacity-50"
                        :disabled="!routeSelected">
                    See prices
                </button>
            </div>
        </aside>

        <!-- MAIN VIEW: Map -->
        <article class="flex-1 bg-gray-100 rounded-3xl overflow-hidden relative shadow-[0_4px_24px_rgba(0,0,0,0.04)]">
            <div id="map"></div>
            @if(empty($google_maps_api_key))
                <div class="absolute inset-0 bg-gray-100 flex flex-col items-center justify-center p-8 text-center z-50">
                    <h3 class="text-xl font-bold mb-2 text-brand/40 tracking-tight">MAP PREPARATION</h3>
                    <p class="text-brand-muted max-w-sm text-sm">Dynamic route visualization active. Waiting for system settings activation.</p>
                </div>
            @endif
        </article>
    </main>

    <script>
        function rideApp() {
            return {
                routeSelected: false,
                distance: '',
                duration: '',
            }
        }

        let map, directionsService, directionsRenderer;
        function initMap() {
            const mapElement = document.getElementById('map');
            if (!mapElement || typeof google === 'undefined') return;

            map = new google.maps.Map(mapElement, {
                zoom: 13, center: { lat: 5.6037, lng: -0.1870 }, disableDefaultUI: true,
                styles: [{ "featureType": "landscape", "stylers": [{ "color": "#f5f5f5" }] }, { "featureType": "road", "stylers": [{ "color": "#ffffff" }] }, { "featureType": "water", "stylers": [{ "color": "#c9d2d4" }] }]
            });

            directionsService = new google.maps.DirectionsService();
            directionsRenderer = new google.maps.DirectionsRenderer({ map: map, suppressMarkers: true, polylineOptions: { strokeColor: '#000', strokeWeight: 4 } });

            const options = { types: ['address'], componentRestrictions: { country: 'gh' } };
            const pickup = new google.maps.places.Autocomplete(document.getElementById('pickup-input'), options);
            const dropoff = new google.maps.places.Autocomplete(document.getElementById('dropoff-input'), options);

            const calculate = () => {
                const p = document.getElementById('pickup-input').value;
                const d = document.getElementById('dropoff-input').value;
                if (!p || !d) return;
                directionsService.route({ origin: p, destination: d, travelMode: 'DRIVING' }, (res, stat) => {
                    if (stat === 'OK') {
                        directionsRenderer.setDirections(res);
                        document.querySelector('[x-data]').__x.$data.routeSelected = true;
                    }
                });
            };

            pickup.addListener('place_changed', calculate);
            dropoff.addListener('place_changed', calculate);
        }
    </script>
    @if(!empty($google_maps_api_key))
        <script async src="https://maps.googleapis.com/maps/api/js?key={{ $google_maps_api_key }}&libraries=places&callback=initMap"></script>
    @endif
</body>
</html>
