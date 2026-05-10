@props(['theme' => 'dark', 'layout' => 'site'])

@php
    $bgClass = $theme === 'dark' ? 'bg-brand text-white' : 'bg-white text-brand border-b border-gray-100';
    $logoBg = $theme === 'dark' ? 'bg-accent text-brand' : 'bg-brand text-accent';
    $navLinkClass = $theme === 'dark' ? 'text-white/80 hover:text-white hover:bg-white/10' : 'text-brand/70 hover:text-brand hover:bg-surface';
    $authBtnClass = $theme === 'dark' ? 'bg-white text-brand hover:bg-surface' : 'bg-brand text-white hover:bg-brand-light';
    $loginLinkClass = $theme === 'dark' ? 'text-white hover:bg-white/10' : 'text-brand hover:bg-surface';
@endphp

<!-- NAVIGATION BAR -->
<nav class="fixed top-0 left-0 right-0 z-[100] {{ $bgClass }} transition-colors duration-300">
    <div class="max-w-[1440px] mx-auto px-4 lg:px-10">
        <div class="flex items-center h-[72px] gap-8">

            <!-- Logo -->
            <a href="{{ route('home', ['country' => request()->route('country', 'gh'), 'lang' => request()->route('lang', 'en')]) }}" class="flex items-center gap-2.5 shrink-0">
                <div class="w-9 h-9 {{ $logoBg }} rounded-lg flex items-center justify-center font-black text-lg">W</div>
                <span class="text-[22px] font-bold tracking-tight">WADEX<span class="text-accent">PRO</span></span>
            </a>

            <!-- Dynamic Content based on Layout -->
            @if($layout === 'app')
                <!-- App Tabs (Centered) -->
                <div class="flex-1 flex justify-center">
                    <div class="flex items-center bg-gray-50 p-1 rounded-full">
                        <a href="{{ route('ride', ['country' => request()->route('country', 'gh'), 'lang' => request()->route('lang', 'en')]) }}" 
                           class="flex items-center gap-2 px-6 py-2 rounded-full text-sm font-bold transition-all {{ request()->routeIs('ride') ? 'bg-white text-brand shadow-sm' : 'text-brand/50 hover:text-brand' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/></svg>
                            Ride
                        </a>
                        <a href="{{ route('courier', ['country' => request()->route('country', 'gh'), 'lang' => request()->route('lang', 'en')]) }}" 
                           class="flex items-center gap-2 px-6 py-2 rounded-full text-sm font-bold transition-all {{ request()->routeIs('courier') ? 'bg-white text-brand shadow-sm' : 'text-brand/50 hover:text-brand' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                            Courier
                        </a>
                    </div>
                </div>

                <!-- Right: App Actions (Activity, Profile) -->
                <div class="flex items-center gap-4">
                    <button class="flex items-center gap-2 px-4 py-2 hover:bg-surface rounded-full transition-all group">
                        <div class="w-8 h-8 rounded-full bg-surface flex items-center justify-center text-brand group-hover:bg-white transition-colors">
                            <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        </div>
                        <span class="text-sm font-bold">Activity</span>
                    </button>
                    
                    <div class="relative" x-data="{ open: false }" @click.away="open = false">
                        <button @click="open = !open" class="flex items-center gap-1 p-1 hover:bg-surface rounded-full transition-all">
                            <div class="w-9 h-9 bg-surface border border-gray-100 rounded-full flex items-center justify-center overflow-hidden">
                                <svg class="w-6 h-6 text-brand-muted" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm0 14.2c-2.5 0-4.71-1.28-6-3.22.03-1.99 4-3.08 6-3.08s5.97 1.09 6 3.08c-1.29 1.94-3.5 3.22-6 3.22z"/></svg>
                            </div>
                            <svg class="w-4 h-4 text-brand-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                    </div>
                </div>
            @else
                <!-- Dynamic Mega Menu (Standard Layout) -->
                <div class="flex-1">
                    @include('components.mega-menu', ['slug' => 'main-nav', 'theme' => $theme, 'mode' => 'desktop'])
                </div>

                <!-- Right: Auth Actions -->
                <div class="flex items-center gap-3">
                    <!-- Language/Help -->
                    <button class="hidden lg:flex items-center justify-center w-10 h-10 rounded-full hover:bg-current/10 transition-colors" title="Language">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/></svg>
                    </button>

                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" class="px-5 py-2.5 {{ $authBtnClass }} text-sm font-bold rounded-lg transition-colors whitespace-nowrap">Dashboard</a>
                        @else
                            <a href="{{ route('login', ['country' => request()->route('country', 'gh'), 'lang' => request()->route('lang', 'en')]) }}" class="hidden sm:block px-5 py-2.5 text-sm font-semibold rounded-lg transition-colors whitespace-nowrap {{ $loginLinkClass }}">Log in</a>
                            @if (Route::has('register'))
                                <a href="{{ route('register', ['country' => request()->route('country', 'gh'), 'lang' => request()->route('lang', 'en')]) }}" class="px-5 py-2.5 {{ $authBtnClass }} text-sm font-bold rounded-lg transition-colors whitespace-nowrap">Sign up</a>
                            @endif
                        @endauth
                    @endif

                    <!-- Mobile Hamburger -->
                    <button onclick="toggleGlobalMobileMenu()" class="lg:hidden flex items-center justify-center w-10 h-10 rounded-full hover:bg-current/10 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                </div>
            @endif
        </div>
    </div>
</nav>

<!-- Global Mobile Menu Overlay -->
<div class="fixed inset-0 z-[110] bg-black/50 opacity-0 pointer-events-none transition-opacity duration-300 lg:hidden" id="globalMobileOverlay" onclick="toggleGlobalMobileMenu()"></div>

<!-- Global Mobile Menu Panel -->
<div class="fixed top-0 left-0 bottom-0 z-[120] w-[85vw] max-w-[340px] bg-white text-brand transform -translate-x-full transition-all duration-300 ease-in-out shadow-2xl lg:hidden flex flex-col" id="globalMobileMenu">
    <div class="p-6 border-b border-gray-100 flex items-center justify-between">
        <span class="text-xl font-bold">WADEXPRO</span>
        <button onclick="toggleGlobalMobileMenu()" class="w-10 h-10 flex items-center justify-center rounded-full hover:bg-surface">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>
    
    <div class="flex-1 overflow-y-auto p-4 no-scrollbar">
        {{-- Mobile Mega Menu items (Database Driven) --}}
        @include('components.mega-menu', ['slug' => 'main-nav', 'theme' => 'light', 'mode' => 'mobile'])
    </div>

    <div class="absolute bottom-0 left-0 right-0 p-6 border-t border-gray-100 space-y-3 bg-white">
        @auth
            <a href="{{ url('/dashboard') }}" class="block w-full text-center px-5 py-3 bg-brand text-white text-sm font-bold rounded-full">Dashboard</a>
        @else
            @if (Route::has('login'))
                <a href="{{ route('login', ['country' => request()->route('country', 'gh'), 'lang' => request()->route('lang', 'en')]) }}" class="block w-full text-center px-5 py-3 bg-brand text-white text-sm font-bold rounded-full">Log in</a>
            @endif
            @if (Route::has('register'))
                <a href="{{ route('register', ['country' => request()->route('country', 'gh'), 'lang' => request()->route('lang', 'en')]) }}" class="block w-full text-center px-5 py-3 bg-surface text-brand text-sm font-bold rounded-full">Sign up</a>
            @endif
        @endauth
    </div>
</div>

<script>
    function toggleGlobalMobileMenu() {
        const menu = document.getElementById('globalMobileMenu');
        const overlay = document.getElementById('globalMobileOverlay');
        const isOpening = menu.classList.contains('-translate-x-full') || menu.style.transform === 'translateX(-100%)';
        
        if (isOpening) {
            menu.classList.remove('-translate-x-full');
            menu.style.transform = 'translateX(0)';
            overlay.classList.remove('opacity-0', 'pointer-events-none');
            overlay.classList.add('opacity-100', 'pointer-events-auto');
            document.body.classList.add('overflow-hidden');
        } else {
            menu.style.transform = 'translateX(-100%)';
            overlay.classList.add('opacity-0', 'pointer-events-none');
            overlay.classList.remove('opacity-100', 'pointer-events-auto');
            document.body.classList.remove('overflow-hidden');
        }
    }
</script>

<style>
    .no-scrollbar::-webkit-scrollbar { display: none; }
    .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>
