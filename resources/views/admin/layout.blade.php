<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ \App\Modules\Admin\Models\SystemSetting::get('dashboard_app_name', 'WADEXPRO') }} {{ \App\Modules\Admin\Models\SystemSetting::get('dashboard_tagline', 'Orchestrator') }}</title>
    
    @php $favicon = \App\Modules\Admin\Models\SystemSetting::get('dashboard_favicon_url'); @endphp
    @if($favicon)
        <link rel="icon" type="image/x-icon" href="{{ $favicon }}">
    @endif
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Socket.IO Client -->
    <script src="https://cdn.socket.io/4.7.5/socket.io.min.js"></script>
    <script>
        window.WADEX_SOCKET_URL = '{{ \App\Modules\Admin\Models\SystemSetting::get("flutter_rtc_url", "https://wadexpro-4rexnj1k.on-forge.com:3000") }}';
    </script>

    <!-- Leaflet.js Assets -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: { DEFAULT: '#0A0A0A', light: '#1A1A1A', muted: '#6B6B6B' },
                        accent: { DEFAULT: '#F8B803', hover: '#FFD60A' },
                        surface: { DEFAULT: '#F6F6F6', dark: '#EEEEEE' },
                    },
                    fontFamily: { outfit: ['Outfit', 'sans-serif'] }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Outfit', sans-serif; }
        [x-cloak] { display: none !important; }
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,0.2); }
        @keyframes fade-in-down {
            0% { opacity: 0; transform: translateY(-20px); }
            100% { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body class="bg-surface text-brand min-h-screen flex selection:bg-accent/30 overflow-hidden" x-data="{ sidebarOpen: true, userMenuOpen: false }">
    
    <!-- Sidebar -->
    <aside class="bg-brand text-white flex flex-col shrink-0 h-screen border-r border-white/10 transition-all duration-500 ease-in-out relative z-50 overflow-hidden"
           :class="sidebarOpen ? 'w-72' : 'w-0 border-none'">
        
        <!-- Sticky Logo Area -->
        <div class="h-20 flex items-center px-6 border-b border-white/10 shrink-0 bg-brand sticky top-0 z-20">
            <div class="flex items-center gap-3">
                @php $dashLogo = \App\Modules\Admin\Models\SystemSetting::get('dashboard_logo_url'); @endphp
                @if($dashLogo)
                    <div class="w-10 h-10 bg-brand rounded-lg flex items-center justify-center shrink-0 overflow-hidden">
                        <img src="{{ $dashLogo }}" class="w-full h-full object-contain">
                    </div>
                @else
                    <div class="w-10 h-10 bg-accent rounded-lg flex items-center justify-center shrink-0">
                        <span class="text-brand font-black text-xl">{{ substr(\App\Modules\Admin\Models\SystemSetting::get('dashboard_app_name', 'W'), 0, 1) }}</span>
                    </div>
                @endif
                
                <div class="transition-all duration-300" :class="sidebarOpen ? 'opacity-100 translate-x-0' : 'opacity-0 -translate-x-4 pointer-events-none'">
                    <span class="text-xl font-bold tracking-tight">
                        @php 
                            $appName = \App\Modules\Admin\Models\SystemSetting::get('dashboard_app_name', 'WADEXPRO');
                            $firstWord = strtok($appName, ' ');
                            $rest = substr($appName, strlen($firstWord));
                        @endphp
                        {{ $firstWord }}<span class="text-accent">{{ $rest }}</span>
                    </span>
                    <p class="text-[10px] font-bold text-white/40 uppercase tracking-[0.2em] -mt-1">{{ \App\Modules\Admin\Models\SystemSetting::get('dashboard_tagline', 'Orchestrator') }}</p>
                </div>
            </div>
        </div>

        <!-- Scrollable Navigation Middle -->
        <nav class="flex-1 py-8 px-5 space-y-8 overflow-y-auto overflow-x-hidden transition-all duration-500" :class="sidebarOpen ? 'opacity-100' : 'opacity-0 translate-x-10'">
            
            <!-- Section: Core -->
            <div class="space-y-1">
                <p class="px-4 text-[10px] font-black text-white/30 uppercase tracking-[0.2em] mb-4">Platform Core</p>
                <a href="{{ route('orchestrator.dashboard') }}" class="flex items-center gap-4 px-4 py-3.5 rounded-lg hover:bg-white/5 transition group {{ request()->routeIs('orchestrator.dashboard') ? 'bg-white/10 text-accent shadow-lg shadow-black/20' : 'text-white/60 hover:text-white' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                    <span class="text-sm font-semibold whitespace-nowrap">Overview Dashboard</span>
                </a>
            </div>

            <!-- Section: Support Operations (High Priority) -->
            <div class="space-y-1">
                <p class="px-4 text-[10px] font-black text-accent uppercase tracking-[0.2em] mb-4">Support Operations</p>
                <a href="{{ route('orchestrator.livechat') }}" class="flex items-center gap-4 px-4 py-3.5 rounded-lg bg-white/5 border border-white/10 hover:bg-white/10 transition group {{ request()->routeIs('orchestrator.livechat*') ? 'bg-white/10 text-accent shadow-lg shadow-black/20 border-accent/50' : 'text-white/80 hover:text-white' }}">
                    <svg class="w-5 h-5 shrink-0 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"/></svg>
                    <span class="text-sm font-black whitespace-nowrap">LIVE CHAT SUPPORT</span>
                    <span class="ml-auto flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-2 w-2 rounded-full bg-accent opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-accent"></span>
                    </span>
                </a>
                <a href="{{ route('orchestrator.support.tickets') }}" class="flex items-center gap-4 px-4 py-3.5 rounded-lg hover:bg-white/5 transition group {{ request()->routeIs('orchestrator.support.*') ? 'bg-white/10 text-accent shadow-lg shadow-black/20' : 'text-white/60 hover:text-white' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    <span class="text-sm font-semibold whitespace-nowrap">Ticket Inbox</span>
                </a>
                <a href="{{ route('orchestrator.users') }}" class="flex items-center gap-4 px-4 py-3.5 rounded-lg hover:bg-white/5 transition group {{ request()->routeIs('orchestrator.users') ? 'bg-white/10 text-accent shadow-lg shadow-black/20' : 'text-white/60 hover:text-white' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    <span class="text-sm font-semibold whitespace-nowrap">User Matrix</span>
                </a>
                <a href="{{ route('orchestrator.security') }}" class="flex items-center gap-4 px-4 py-3.5 rounded-lg hover:bg-white/5 transition group {{ request()->routeIs('orchestrator.security') ? 'bg-white/10 text-accent shadow-lg shadow-black/20' : 'text-white/60 hover:text-white' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    <span class="text-sm font-semibold whitespace-nowrap">Security Protocols</span>
                </a>
                <a href="{{ route('orchestrator.operations_map') }}" class="flex items-center gap-4 px-4 py-3.5 rounded-lg hover:bg-white/5 transition group {{ request()->routeIs('orchestrator.operations_map') ? 'bg-white/10 text-accent shadow-lg shadow-black/20' : 'text-white/60 hover:text-white' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <span class="text-sm font-semibold whitespace-nowrap">Operations Map</span>
                </a>
            </div>

            <!-- Section: Logistics Engine -->
            <div class="space-y-1">
                <p class="px-4 text-[10px] font-black text-white/30 uppercase tracking-[0.2em] mb-4">Logistics Engine</p>
                <a href="{{ route('orchestrator.drivers') }}" class="flex items-center gap-4 px-4 py-3.5 rounded-lg hover:bg-white/5 transition group {{ request()->routeIs('orchestrator.drivers') ? 'bg-white/10 text-accent shadow-lg shadow-black/20' : 'text-white/60 hover:text-white' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/></svg>
                    <span class="text-sm font-semibold whitespace-nowrap">Asset Registry</span>
                </a>
                <a href="{{ route('orchestrator.orders') }}" class="flex items-center gap-4 px-4 py-3.5 rounded-lg hover:bg-white/5 transition group {{ request()->routeIs('orchestrator.orders') ? 'bg-white/10 text-accent shadow-lg shadow-black/20' : 'text-white/60 hover:text-white' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    <span class="text-sm font-semibold whitespace-nowrap">Global Queue</span>
                </a>
                <a href="{{ route('orchestrator.financials') }}" class="flex items-center gap-4 px-4 py-3.5 rounded-lg hover:bg-white/5 transition group {{ request()->routeIs('orchestrator.financials') ? 'bg-white/10 text-accent shadow-lg shadow-black/20' : 'text-white/60 hover:text-white' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span class="text-sm font-semibold whitespace-nowrap">Financials</span>
                </a>
            </div>

            <!-- Section: Driver Management -->
            <div class="space-y-1">
                <p class="px-4 text-[10px] font-black text-white/30 uppercase tracking-[0.2em] mb-4">Driver Management</p>
                <a href="{{ route('orchestrator.driver.management') }}" class="flex items-center gap-4 px-4 py-3.5 rounded-lg hover:bg-white/5 transition group {{ request()->routeIs('orchestrator.driver.*') ? 'bg-white/10 text-accent shadow-lg shadow-black/20' : 'text-white/60 hover:text-white' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    <span class="text-sm font-semibold whitespace-nowrap">Driver Registry</span>
                </a>
                <a href="{{ route('orchestrator.driver.documents') }}" class="flex items-center gap-4 px-4 py-3.5 rounded-lg hover:bg-white/5 transition group {{ request()->routeIs('orchestrator.driver.documents') ? 'bg-white/10 text-accent shadow-lg shadow-black/20' : 'text-white/60 hover:text-white' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span class="text-sm font-semibold whitespace-nowrap">Document Approvals</span>
                </a>
                <a href="{{ route('orchestrator.vehicle.types') }}" class="flex items-center gap-4 px-4 py-3.5 rounded-lg hover:bg-white/5 transition group {{ request()->routeIs('orchestrator.vehicle.*') ? 'bg-white/10 text-accent shadow-lg shadow-black/20' : 'text-white/60 hover:text-white' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/></svg>
                    <span class="text-sm font-semibold whitespace-nowrap">Vehicle Types</span>
                </a>
            </div>

            <!-- Section: Customer Support -->
            <div class="space-y-1">
                <p class="px-4 text-[10px] font-black text-white/30 uppercase tracking-[0.2em] mb-4">Customer Support</p>
                <a href="{{ route('orchestrator.support.tickets') }}" class="flex items-center gap-4 px-4 py-3.5 rounded-lg hover:bg-white/5 transition group {{ request()->routeIs('orchestrator.support.ticket*') ? 'bg-white/10 text-accent shadow-lg shadow-black/20' : 'text-white/60 hover:text-white' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
                    <span class="text-sm font-semibold whitespace-nowrap">Support Tickets</span>
                </a>
                <a href="{{ route('orchestrator.livechat') }}" class="flex items-center gap-4 px-4 py-3.5 rounded-lg hover:bg-white/5 transition group {{ request()->routeIs('orchestrator.livechat*') ? 'bg-white/10 text-accent shadow-lg shadow-black/20' : 'text-white/60 hover:text-white' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                    <span class="text-sm font-semibold whitespace-nowrap">Live Chat</span>
                </a>
            </div>

            <!-- Section: Analytics -->
            <div class="space-y-1">
                <p class="px-4 text-[10px] font-black text-white/30 uppercase tracking-[0.2em] mb-4">Analytics & Reports</p>
                <a href="{{ route('orchestrator.analytics') }}" class="flex items-center gap-4 px-4 py-3.5 rounded-lg hover:bg-white/5 transition group {{ request()->routeIs('orchestrator.analytics*') ? 'bg-white/10 text-accent shadow-lg shadow-black/20' : 'text-white/60 hover:text-white' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    <span class="text-sm font-semibold whitespace-nowrap">Business Intelligence</span>
                </a>
            </div>

            <!-- Section: Marketing -->
            <div class="space-y-1">
                <p class="px-4 text-[10px] font-black text-white/30 uppercase tracking-[0.2em] mb-4">Marketing</p>
                <a href="{{ route('orchestrator.marketing.promos') }}" class="flex items-center gap-4 px-4 py-3.5 rounded-lg hover:bg-white/5 transition group {{ request()->routeIs('orchestrator.marketing.*') ? 'bg-white/10 text-accent shadow-lg shadow-black/20' : 'text-white/60 hover:text-white' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                    <span class="text-sm font-semibold whitespace-nowrap">Promos & Coupons</span>
                </a>
                <a href="{{ route('orchestrator.marketing.banners') }}" class="flex items-center gap-4 px-4 py-3.5 rounded-lg hover:bg-white/5 transition group text-white/60 hover:text-white">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <span class="text-sm font-semibold whitespace-nowrap">Banner Manager</span>
                </a>
            </div>

            <!-- Section: CMS & Content -->
            <div class="space-y-1">
                <p class="px-4 text-[10px] font-black text-white/30 uppercase tracking-[0.2em] mb-4">Content & CMS</p>
                <a href="{{ route('orchestrator.menus') }}" class="flex items-center gap-4 px-4 py-3.5 rounded-lg hover:bg-white/5 transition group {{ request()->routeIs('orchestrator.menus') ? 'bg-white/10 text-accent shadow-lg shadow-black/20' : 'text-white/60 hover:text-white' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    <span class="text-sm font-semibold whitespace-nowrap">Mega Menu Manager</span>
                </a>
                <a href="{{ route('orchestrator.cms.blog') }}" class="flex items-center gap-4 px-4 py-3.5 rounded-lg hover:bg-white/5 transition group {{ request()->routeIs('orchestrator.cms.*') ? 'bg-white/10 text-accent shadow-lg shadow-black/20' : 'text-white/60 hover:text-white' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                    <span class="text-sm font-semibold whitespace-nowrap">Blog Manager</span>
                </a>
                <a href="{{ route('orchestrator.cms.faq') }}" class="flex items-center gap-4 px-4 py-3.5 rounded-lg hover:bg-white/5 transition text-white/60 hover:text-white">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span class="text-sm font-semibold whitespace-nowrap">FAQ Manager</span>
                </a>
                <a href="{{ route('orchestrator.templates.index') }}" class="flex items-center gap-4 px-4 py-3.5 rounded-lg hover:bg-white/5 transition group {{ request()->routeIs('orchestrator.templates.*') ? 'bg-white/10 text-accent shadow-lg shadow-black/20' : 'text-white/60 hover:text-white' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                    <span class="text-sm font-semibold whitespace-nowrap">Notification Templates</span>
                </a>
            </div>

            <!-- Section: HR -->
            <div class="space-y-1">
                <p class="px-4 text-[10px] font-black text-white/30 uppercase tracking-[0.2em] mb-4">HR & Staff</p>
                <a href="{{ route('orchestrator.hr') }}" class="flex items-center gap-4 px-4 py-3.5 rounded-lg hover:bg-white/5 transition group {{ request()->routeIs('orchestrator.hr*') ? 'bg-white/10 text-accent shadow-lg shadow-black/20' : 'text-white/60 hover:text-white' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    <span class="text-sm font-semibold whitespace-nowrap">Employee Registry</span>
                </a>
            </div>

            <!-- Section: System -->
            <div class="space-y-1 border-t border-white/5 pt-6">
                <a href="{{ route('orchestrator.infrastructure') }}" class="flex items-center gap-4 px-4 py-3.5 rounded-lg hover:bg-white/5 transition group {{ request()->routeIs('orchestrator.infrastructure') ? 'bg-white/10 text-accent shadow-lg shadow-black/20' : 'text-white/60 hover:text-white' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    <span class="text-sm font-semibold whitespace-nowrap">Infrastructure Hub</span>
                </a>
                <a href="{{ route('orchestrator.error_monitoring') }}" class="flex items-center gap-4 px-4 py-3.5 rounded-lg hover:bg-white/5 transition group {{ request()->routeIs('orchestrator.error_monitoring') ? 'bg-white/10 text-red-400 shadow-lg shadow-black/20' : 'text-white/60 hover:text-red-400' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <span class="text-sm font-semibold whitespace-nowrap">Error Monitoring</span>
                </a>
                <a href="{{ route('orchestrator.modules') }}" class="flex items-center gap-4 px-4 py-3.5 rounded-lg hover:bg-white/5 transition group {{ request()->routeIs('orchestrator.modules') ? 'bg-white/10 text-accent shadow-lg shadow-black/20' : 'text-white/60 hover:text-white' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 4a2 2 0 114 0v1a2 2 0 01-2 2H3a2 2 0 01-2-2V4a2 2 0 114 0v1a2 2 0 012 2h4a2 2 0 012-2V4z"/></svg>
                    <span class="text-sm font-semibold whitespace-nowrap">Module Hardening</span>
                </a>
                <a href="{{ route('orchestrator.roles.index') }}" class="flex items-center gap-4 px-4 py-3.5 rounded-lg hover:bg-white/5 transition group {{ request()->routeIs('orchestrator.roles.*') ? 'bg-white/10 text-accent shadow-lg shadow-black/20' : 'text-white/60 hover:text-white' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    <span class="text-sm font-semibold whitespace-nowrap">Roles & Permissions</span>
                </a>
                <a href="{{ route('orchestrator.settings') }}" class="flex items-center gap-4 px-4 py-3.5 rounded-lg hover:bg-white/5 transition group {{ request()->routeIs('orchestrator.settings*') ? 'bg-white/10 text-accent shadow-lg shadow-black/20' : 'text-white/60 hover:text-white' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <span class="text-sm font-semibold whitespace-nowrap">Platform Settings</span>
                </a>
            </div>
        </nav>

        <!-- Sticky Footer Area -->
        <div class="p-6 border-t border-white/10 bg-brand shrink-0 sticky bottom-0 z-20 transition-all duration-300" :class="sidebarOpen ? 'opacity-100' : 'opacity-0 scale-90 pointer-events-none'">
            <div class="px-4 py-3 bg-white/5 rounded-lg border border-white/10">
                <p class="text-[10px] font-black text-white/40 uppercase tracking-widest mb-1">Session Node</p>
                <div class="flex items-center gap-2">
                    <div class="w-1.5 h-1.5 bg-accent rounded-full animate-pulse"></div>
                    <span class="text-xs font-bold text-white/80">W-PRO-EU-01</span>
                </div>
            </div>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 flex flex-col h-screen overflow-hidden bg-white relative">
        
        <!-- Flash Notifications -->
        @if(session('success'))
            <div x-data="{ show: true }" x-show="show" class="absolute top-4 right-4 z-50 bg-green-50 border-l-4 border-green-500 p-4 rounded shadow-lg flex items-start gap-3 w-96 animate-[fade-in-down_0.3s_ease-out]">
                <svg class="w-5 h-5 text-green-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <div class="flex-1">
                    <p class="text-sm font-bold text-green-800">Success</p>
                    <p class="text-xs text-green-700 mt-1">{{ session('success') }}</p>
                </div>
                <button @click="show = false" class="text-green-600 hover:text-green-800"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
            </div>
            <script>setTimeout(() => { document.querySelector('[x-data="{ show: true }"]')?.remove(); }, 5000);</script>
        @endif

        @if(session('error'))
            <div x-data="{ show: true }" x-show="show" class="absolute top-4 right-4 z-50 bg-red-50 border-l-4 border-red-500 p-4 rounded shadow-lg flex items-start gap-3 w-96 animate-[fade-in-down_0.3s_ease-out]">
                <svg class="w-5 h-5 text-red-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <div class="flex-1">
                    <p class="text-sm font-bold text-red-800">Error</p>
                    <p class="text-xs text-red-700 mt-1">{{ session('error') }}</p>
                </div>
                <button @click="show = false" class="text-red-600 hover:text-red-800"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
            </div>
            <script>setTimeout(() => { document.querySelector('[x-data="{ show: true }"]')?.remove(); }, 8000);</script>
        @endif

        @if($errors->any())
            <div x-data="{ show: true }" x-show="show" class="absolute top-4 right-4 z-50 bg-amber-50 border-l-4 border-amber-500 p-4 rounded shadow-lg flex items-start gap-3 w-96 animate-[fade-in-down_0.3s_ease-out]">
                <svg class="w-5 h-5 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <div class="flex-1">
                    <p class="text-sm font-bold text-amber-800">Validation Error</p>
                    <ul class="list-disc list-inside text-xs text-amber-700 mt-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                <button @click="show = false" class="text-amber-600 hover:text-amber-800"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
            </div>
        @endif
        
        <!-- Premium Topbar -->
        <header class="h-20 border-b border-gray-100 flex items-center justify-between px-8 bg-white/80 backdrop-blur-md shrink-0 sticky top-0 z-40">
            
            <!-- Left: Sidebar Toggle & Search Bar UI -->
            <div class="flex items-center gap-6 flex-1 max-w-2xl">
                <!-- Topbar Toggle Button -->
                <button @click="sidebarOpen = !sidebarOpen" 
                        class="p-2.5 bg-surface text-brand hover:bg-brand hover:text-white rounded-lg transition shadow-sm border border-gray-100 group">
                    <svg class="w-6 h-6 transform transition-transform duration-500" :class="!sidebarOpen && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h8m-8 6h16"/>
                    </svg>
                </button>

                <!-- Search Bar -->
                <div class="relative flex-1 group">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400 group-focus-within:text-accent transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <input type="text" placeholder="Global search orders, drivers or node IDs..." 
                           class="w-full bg-surface border-none rounded-lg py-3 pl-12 pr-4 text-sm font-medium outline-none focus:ring-2 focus:ring-accent/20 transition-all placeholder:text-gray-400">
                </div>
            </div>

            <!-- Right: Utilities & Profile -->
            <div class="flex items-center gap-6">
                
                <!-- System Status -->
                <div class="hidden xl:flex items-center gap-3 px-4 py-2 bg-surface rounded-lg border border-gray-100">
                    <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse shadow-[0_0_8px_rgba(34,197,94,0.5)]"></div>
                    <span class="text-[11px] font-bold text-gray-500 uppercase tracking-wider">Gateway Active</span>
                </div>

                <!-- Error Monitoring -->
                <a href="{{ route('orchestrator.error_monitoring') }}" class="relative p-2.5 bg-red-50 text-red-500 hover:bg-red-100 hover:text-red-700 rounded-lg border border-red-100 transition shadow-sm group">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <span class="absolute -top-1 -right-1 flex h-3 w-3">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-red-500 border-2 border-white"></span>
                    </span>
                    
                    <!-- Tooltip -->
                    <span class="absolute top-full left-1/2 -translate-x-1/2 mt-2 w-max px-2 py-1 bg-gray-900 text-white text-[10px] font-black rounded opacity-0 group-hover:opacity-100 transition pointer-events-none">System Errors</span>
                </a>

                <!-- Notifications -->
                <button class="relative p-2.5 bg-surface text-gray-500 hover:text-brand rounded-lg border border-gray-100 transition shadow-sm">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                    <span class="absolute top-2 right-2 w-2.5 h-2.5 bg-accent border-2 border-white rounded-full"></span>
                </button>

                <!-- Profile Dropdown -->
                <div class="relative" @click.away="userMenuOpen = false">
                    <button @click="userMenuOpen = !userMenuOpen" class="flex items-center gap-3 pl-1 pr-4 py-1.5 bg-brand text-white rounded-lg transition hover:opacity-90 shadow-lg shadow-black/10">
                        @php
                            $headerUser = auth('admin')->user();
                            $headerInitials = strtoupper(substr($headerUser?->first_name ?? $headerUser?->name ?? 'A', 0, 1) . substr($headerUser?->last_name ?? '', 0, 1)) ?: 'AD';
                        @endphp
                        <div class="w-9 h-9 rounded-lg bg-accent text-brand flex items-center justify-center font-black text-sm overflow-hidden">
                            @if($headerUser?->avatar_url)
                                <img src="{{ $headerUser->avatar_url }}" class="w-full h-full object-cover">
                            @else
                                {{ $headerInitials }}
                            @endif
                        </div>
                        <div class="text-left hidden sm:block">
                            <p class="text-[11px] font-black text-accent uppercase leading-none mb-0.5">Clearance: Level 5</p>
                            <p class="text-[13px] font-bold leading-none capitalize">{{ str_replace('_', ' ', $headerUser?->user_type ?? 'Administrator') }}</p>
                        </div>
                        <svg class="w-4 h-4 text-white/50 transition-transform" :class="userMenuOpen && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>

                    <!-- Dropdown Content -->
                    <div x-show="userMenuOpen" 
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="transform opacity-0 -translate-y-4 scale-95"
                         x-transition:enter-end="transform opacity-100 translate-y-0 scale-100"
                         class="absolute right-0 mt-4 w-64 bg-white rounded-lg shadow-2xl border border-gray-100 p-3 z-50 overflow-hidden" x-cloak>
                        
                        <div class="p-4 border-b border-gray-50 mb-2">
                            <p class="text-sm font-black text-brand">{{ auth('admin')->user()?->name ?? 'Orchestrator Account' }}</p>
                            <p class="text-xs text-brand-muted truncate">{{ auth('admin')->user()?->email ?? 'admin@wadexpro.com' }}</p>
                            <p class="text-[10px] text-brand-muted mt-0.5">{{ ucfirst(str_replace('_',' ', auth('admin')->user()?->user_type ?? 'admin')) }}</p>
                        </div>

                        <a href="{{ route('orchestrator.profile') }}" class="flex items-center gap-3 p-3 rounded-lg hover:bg-surface transition text-sm font-semibold text-gray-700">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            My Profile Settings
                        </a>
                        <a href="#" class="flex items-center gap-3 p-3 rounded-lg hover:bg-surface transition text-sm font-semibold text-gray-700">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                            Fleet Documentation
                        </a>

                        <div class="mt-2 pt-2 border-t border-gray-50">
                            <form method="POST" action="{{ route('orchestrator.logout') }}">
                                @csrf
                                <button type="submit" class="w-full flex items-center gap-3 p-3 rounded-lg bg-red-50 hover:bg-red-100 transition text-sm font-black text-red-600">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                    Sign Out Console
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Page Content Area -->
        <div class="flex-1 overflow-y-auto p-10 bg-white shadow-inner">
            <div class="max-w-[1600px] mx-auto">
                @yield('content')
            </div>
        </div>
    </main>

    <!-- Global SOS Interrupt Layer -->
    <div x-data="sosManager()" 
         x-init="init()"
         x-show="hasActiveSOS" 
         x-transition:enter="transition ease-out duration-500"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         class="fixed inset-0 z-[9999] flex items-center justify-center p-6 sm:p-10" 
         x-cloak>
        
        <!-- Backdrop with Pulsing Alarm Effect -->
        <div class="absolute inset-0 bg-red-950/90 backdrop-blur-xl animate-pulse"></div>
        
        <!-- SOS Dashboard -->
        <div class="relative w-full max-w-4xl bg-white rounded-lg shadow-[0_0_100px_rgba(220,38,38,0.5)] border border-red-500/20 overflow-hidden flex flex-col md:flex-row">
            
            <!-- Left: Alert Status & Info -->
            <div class="flex-1 p-8 md:p-12 relative overflow-hidden bg-gradient-to-br from-red-50 to-white">
                <div class="relative z-10">
                    <div class="inline-flex items-center gap-2 px-3 py-1 bg-red-600 text-white rounded-full mb-6">
                        <div class="w-2 h-2 bg-white rounded-full animate-ping"></div>
                        <span class="text-[10px] font-black uppercase tracking-widest">Active Emergency</span>
                    </div>

                    <h2 class="text-4xl md:text-5xl font-black text-brand mb-4 leading-tight">
                        SOS <span class="text-red-600">Triggered</span>
                    </h2>
                    
                    <div class="space-y-6 mt-8">
                        <div class="flex items-center gap-4">
                            <div class="w-14 h-14 bg-red-100 rounded-lg flex items-center justify-center text-red-600 shrink-0">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            </div>
                            <div>
                                <p class="text-xs font-black text-red-500 uppercase tracking-widest mb-1">User Identification</p>
                                <p class="text-2xl font-bold text-brand" x-text="currentSOS.user_name"></p>
                                <p class="text-brand-muted font-bold" x-text="currentSOS.user_phone"></p>
                            </div>
                        </div>

                        <div class="flex items-center gap-4">
                            <div class="w-14 h-14 bg-orange-100 rounded-lg flex items-center justify-center text-orange-600 shrink-0">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                            </div>
                            <div>
                                <p class="text-xs font-black text-orange-500 uppercase tracking-widest mb-1">Incident Geospatial</p>
                                <p class="text-lg font-bold text-brand" x-text="`${currentSOS.lat.toFixed(6)}, ${currentSOS.lng.toFixed(6)}`"></p>
                                <a :href="`https://www.google.com/maps?q=${currentSOS.lat},${currentSOS.lng}`" target="_blank" class="text-xs font-bold text-blue-600 hover:underline">View on Satellite Map</a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Background Graphic -->
                <div class="absolute -bottom-20 -left-20 w-80 h-80 bg-red-100/50 rounded-full blur-3xl"></div>
            </div>

            <!-- Right: Dispatch Actions -->
            <div class="w-full md:w-80 bg-brand p-8 md:p-12 flex flex-col justify-between border-l border-white/10">
                <div class="space-y-4">
                    <p class="text-[10px] font-black text-white/40 uppercase tracking-[0.2em] mb-4">Command Actions</p>
                    
                    <button @click="acknowledgeSOS(currentSOS.id)" 
                            :disabled="processing"
                            class="w-full py-4 bg-accent text-brand rounded-lg font-bold text-sm hover:bg-accent-hover transition-all flex items-center justify-center gap-3 disabled:opacity-50">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Acknowledge Case
                    </button>

                    <button @click="showResolutionModal = true" 
                            class="w-full py-4 bg-white/10 text-white rounded-lg font-bold text-sm hover:bg-white/20 transition-all flex items-center justify-center gap-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Resolve Incident
                    </button>
                    
                    <p class="text-[9px] text-white/30 text-center uppercase tracking-widest pt-4">Dispatch Reference: <span x-text="currentSOS.id"></span></p>
                </div>

                <div class="pt-10">
                    <div class="p-4 bg-red-500/10 rounded-lg border border-red-500/20 text-center">
                        <p class="text-red-400 text-[10px] font-black uppercase tracking-widest mb-1">Estimated Response</p>
                        <p class="text-white text-lg font-bold">Priority One</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Inner Resolution Dialog -->
        <div x-show="showResolutionModal" 
             class="fixed inset-0 z-[10000] flex items-center justify-center p-6" x-cloak>
             <div class="absolute inset-0 bg-brand/80 backdrop-blur-md"></div>
             <div class="relative bg-white rounded-lg p-8 w-full max-w-lg shadow-2xl">
                 <h3 class="text-xl font-black text-brand mb-4">Resolve Incident Case</h3>
                 <textarea x-model="resolutionNotes" 
                           placeholder="Describe the outcome of this emergency event..."
                           class="w-full h-32 bg-surface rounded-lg p-4 text-sm font-medium outline-none focus:ring-2 focus:ring-accent mb-6"></textarea>
                 
                 <div class="flex gap-4">
                     <button @click="showResolutionModal = false" class="flex-1 py-3 bg-gray-100 rounded-lg font-bold text-sm">Cancel</button>
                     <button @click="resolveSOS(currentSOS.id)" class="flex-1 py-3 bg-brand text-white rounded-lg font-bold text-sm">Close Case</button>
                 </div>
             </div>
        </div>
    </div>

    <script>
        function sosManager() {
            return {
                hasActiveSOS: false,
                currentSOS: null,
                processing: false,
                showResolutionModal: false,
                resolutionNotes: '',
                pollInterval: null,

                init() {
                    this.checkSOS();
                    this.pollInterval = setInterval(() => this.checkSOS(), 5000);
                },

                checkSOS() {
                    axios.get('/api/v1/logistics/admin/sos')
                        .then(res => {
                            const active = res.data.data.filter(s => s.status === 'triggered')[0];
                            if (active) {
                                this.currentSOS = active;
                                if (!this.hasActiveSOS) {
                                    this.hasActiveSOS = true;
                                    this.playAlarm();
                                }
                            } else {
                                this.hasActiveSOS = false;
                            }
                        })
                        .catch(err => console.error('SOS Scan Failed:', err));
                },

                playAlarm() {
                    // Audio feedback is recommended for production SOS triggers
                },

                acknowledgeSOS(id) {
                    this.processing = true;
                    axios.patch(`/api/v1/logistics/admin/sos/${id}`, { status: 'acknowledged' })
                        .then(() => {
                            this.checkSOS();
                        })
                        .finally(() => this.processing = false);
                },

                resolveSOS(id) {
                    axios.patch(`/api/v1/logistics/admin/sos/${id}`, { 
                        status: 'resolved',
                        notes: this.resolutionNotes 
                    })
                        .then(() => {
                            this.showResolutionModal = false;
                            this.resolutionNotes = '';
                            this.checkSOS();
                        });
                }
            }
        }
    </script>

    @stack('scripts')
</body>
</html>
