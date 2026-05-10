<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>WADEXPRO | Request a Ride, Send a Package — Ghana's Mobility Platform</title>
    <meta name="description" content="Go anywhere in Ghana with WADEXPRO. Request rides, send packages, and earn as a driver. Fast, safe, and affordable.">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Alpine.js (for mega menu interactivity) -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>

    <!-- Styles -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: { DEFAULT: '#0A0A0A', light: '#1A1A1A', muted: '#6B6B6B' },
                        accent: { DEFAULT: '#F8B803', hover: '#FFD60A' },
                        surface: { DEFAULT: '#F6F6F6', dark: '#EEEEEE' },
                    },
                    fontFamily: {
                        outfit: ['Outfit', 'system-ui', 'sans-serif'],
                    }
                }
            }
        }
    </script>

    <style>
        * { -webkit-font-smoothing: antialiased; }
        body { font-family: 'Outfit', sans-serif; }

        /* Mobile Menu */
        .mobile-menu { transform: translateX(-100%); transition: transform 0.3s ease; }
        .mobile-menu.open { transform: translateX(0); }
        .mobile-overlay { opacity: 0; pointer-events: none; transition: opacity 0.3s ease; }
        .mobile-overlay.open { opacity: 1; pointer-events: auto; }

        /* Scroll Animations */
        .fade-up { opacity: 0; transform: translateY(30px); transition: all 0.7s cubic-bezier(0.16, 1, 0.3, 1); }
        .fade-up.visible { opacity: 1; transform: translateY(0); }

        /* Input Focus Ring */
        .location-input:focus-within { box-shadow: 0 0 0 2px #0A0A0A; }

        /* Service Card Hover */
        .service-card { transition: all 0.3s ease; }
        .service-card:hover { transform: translateY(-4px); box-shadow: 0 12px 40px rgba(0,0,0,0.08); }
    </style>
</head>

<body class="bg-white text-brand">

    <x-global-header theme="dark" />


    <!-- ============================================================ -->
    <!-- HERO SECTION (Uber-Style: Location Inputs + Service Tabs) -->
    <!-- ============================================================ -->
    <section class="pt-[72px]" id="ride">
        <div class="bg-brand text-white">
            <div class="max-w-[1440px] mx-auto px-6 lg:px-10">
                <div class="grid lg:grid-cols-2 gap-10 lg:gap-20 items-center py-16 lg:py-24">

                    <!-- Left: Headline & Inputs -->
                    <div class="space-y-8 max-w-xl">
                        <h1 class="text-[42px] md:text-[56px] lg:text-[64px] font-black leading-[1.05] tracking-tight">
                            Go anywhere with<br>WADEX<span class="text-accent">PRO</span>
                        </h1>
                        <p class="text-lg text-white/60 font-light leading-relaxed">Request a ride, hop in, and go. Or send a package across town in minutes.</p>

                        <!-- Location Inputs -->
                        <div class="bg-white rounded-[8px] p-1.5 space-y-1.5">
                            <div class="location-input flex items-center gap-3 bg-surface rounded-[8px] px-4 py-3.5">
                                <div class="w-2.5 h-2.5 rounded-full bg-brand shrink-0"></div>
                                <input type="text" placeholder="Pickup location" class="bg-transparent text-brand placeholder-brand/40 text-[15px] font-medium w-full outline-none">
                            </div>
                            <div class="location-input flex items-center gap-3 bg-surface rounded-[8px] px-4 py-3.5">
                                <div class="w-2.5 h-2.5 rounded-sm bg-brand shrink-0"></div>
                                <input type="text" placeholder="Dropoff location" class="bg-transparent text-brand placeholder-brand/40 text-[15px] font-medium w-full outline-none">
                            </div>
                            <a href="{{ route('ride', ['country' => request()->route('country', 'gh'), 'lang' => request()->route('lang', 'en')]) }}" class="block w-full text-center py-3.5 bg-brand text-white text-[15px] font-bold rounded-[8px] hover:bg-brand-light transition-colors">
                                See prices
                            </a>
                        </div>
                    </div>

                    <!-- Right: Service Selection Cards -->
                    <div class="hidden lg:grid grid-cols-3 gap-4">
                        <a href="{{ route('ride', ['country' => request()->route('country', 'gh'), 'lang' => request()->route('lang', 'en')]) }}" class="service-card block bg-white/10 backdrop-blur-sm rounded-[8px] p-6 cursor-pointer border-2 border-accent group">
                            <div class="w-14 h-14 bg-white/10 rounded-[8px] flex items-center justify-center mb-5 group-hover:bg-accent/20 transition-colors">
                                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                            </div>
                            <h3 class="font-bold text-lg mb-1">Ride</h3>
                            <p class="text-white/50 text-sm">Go anywhere</p>
                        </a>
                        <a href="{{ route('courier', ['country' => request()->route('country', 'gh'), 'lang' => request()->route('lang', 'en')]) }}" class="service-card block bg-white/5 rounded-[8px] p-6 cursor-pointer border-2 border-transparent hover:border-white/20 group">
                            <div class="w-14 h-14 bg-white/10 rounded-[8px] flex items-center justify-center mb-5 group-hover:bg-accent/20 transition-colors">
                                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                            </div>
                            <h3 class="font-bold text-lg mb-1">Deliver</h3>
                            <p class="text-white/50 text-sm">Send packages</p>
                        </a>
                        <a href="{{ route('ride', ['country' => request()->route('country', 'gh'), 'lang' => request()->route('lang', 'en')]) }}" class="service-card block bg-white/5 rounded-[8px] p-6 cursor-pointer border-2 border-transparent hover:border-white/20 group">
                            <div class="w-14 h-14 bg-white/10 rounded-[8px] flex items-center justify-center mb-5 group-hover:bg-accent/20 transition-colors">
                                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <h3 class="font-bold text-lg mb-1">Reserve</h3>
                            <p class="text-white/50 text-sm">Schedule ahead</p>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <!-- ============================================================ -->
    <!-- SUGGESTIONS / QUICK ACTIONS (Uber-Style) -->
    <!-- ============================================================ -->
    <!-- ============================================================ -->
    <!-- EXPLORE WADEXPRO (Elevated Suggestions Section) -->
    <!-- ============================================================ -->
    <section class="py-16 lg:py-24 bg-white fade-up">
        <div class="max-w-[1440px] mx-auto px-6 lg:px-10">
            <h2 class="text-[32px] md:text-[40px] font-black tracking-tight mb-10">Explore what you can do with WADEX<span class="text-accent">PRO</span></h2>
            
            <div class="grid md:grid-cols-2 lg:grid-cols-2 xl:grid-cols-4 gap-6">
                <!-- Suggestion 1: Ride -->
                <a href="{{ route('ride', ['country' => request()->route('country'), 'lang' => request()->route('lang')]) }}" class="group relative flex bg-surface rounded-[8px] p-6 hover:bg-surface-dark transition-all duration-300 overflow-hidden">
                    <div class="flex flex-col justify-between flex-1 pr-4 z-10">
                        <div>
                            <h3 class="text-xl font-bold mb-2">Ride</h3>
                            <p class="text-brand-muted text-[15px] leading-snug">Go anywhere with WADEXPRO. Request a ride, hop in, and go.</p>
                        </div>
                        <div class="mt-6">
                            <span class="inline-flex items-center justify-center px-5 py-2 bg-brand text-white text-sm font-bold rounded-full group-hover:bg-brand-light transition-colors">Details</span>
                        </div>
                    </div>
                    <div class="shrink-0 flex items-end">
                        <img src="https://mobile-content.uber.com/launch-experience/top_bar_rides_3d.png" 
                             alt="Ride" 
                             class="w-24 h-24 object-contain transform group-hover:scale-110 transition-transform duration-500">
                    </div>
                </a>

                <!-- Suggestion 2: Reserve -->
                <a href="{{ route('ride', ['country' => request()->route('country', 'gh'), 'lang' => request()->route('lang', 'en')]) }}" class="group relative flex bg-surface rounded-[8px] p-6 hover:bg-surface-dark transition-all duration-300 overflow-hidden">
                    <div class="flex flex-col justify-between flex-1 pr-4 z-10">
                        <div>
                            <h3 class="text-xl font-bold mb-2">Reserve</h3>
                            <p class="text-brand-muted text-[15px] leading-snug">Reserve your ride in advance so you can relax on the day of your trip.</p>
                        </div>
                        <div class="mt-6">
                            <span class="inline-flex items-center justify-center px-5 py-2 bg-brand text-white text-sm font-bold rounded-full group-hover:bg-brand-light transition-colors">Details</span>
                        </div>
                    </div>
                    <div class="shrink-0 flex items-end">
                        <img src="https://mobile-content.uber.com/uber_reserve/reserve_clock.png" 
                             alt="Reserve" 
                             class="w-24 h-24 object-contain transform group-hover:scale-110 transition-transform duration-500">
                    </div>
                </a>

                <!-- Suggestion 3: Courier -->
                <a href="{{ route('courier', ['country' => request()->route('country', 'gh'), 'lang' => request()->route('lang', 'en')]) }}" class="group relative flex bg-surface rounded-[8px] p-6 hover:bg-surface-dark transition-all duration-300 overflow-hidden">
                    <div class="flex flex-col justify-between flex-1 pr-4 z-10">
                        <div>
                            <h3 class="text-xl font-bold mb-2">Courier</h3>
                            <p class="text-brand-muted text-[15px] leading-snug">WADEXPRO makes same-day item delivery easier than ever.</p>
                        </div>
                        <div class="mt-6">
                            <span class="inline-flex items-center justify-center px-5 py-2 bg-brand text-white text-sm font-bold rounded-full group-hover:bg-brand-light transition-colors">Details</span>
                        </div>
                    </div>
                    <div class="shrink-0 flex items-end">
                        <img src="https://cn-geo1.uber.com/static/mobile-content/Courier.png" 
                             alt="Courier" 
                             class="w-24 h-24 object-contain transform group-hover:scale-110 transition-transform duration-500">
                    </div>
                </a>

                <!-- Suggestion 4: Moto (Okada) -->
                <a href="{{ route('moto', ['country' => request()->route('country', 'gh'), 'lang' => request()->route('lang', 'en')]) }}" class="group relative flex bg-surface rounded-[8px] p-6 hover:bg-surface-dark transition-all duration-300 overflow-hidden">
                    <div class="flex flex-col justify-between flex-1 pr-4 z-10">
                        <div>
                            <h3 class="text-xl font-bold mb-2">Moto (Okada)</h3>
                            <p class="text-brand-muted text-[15px] leading-snug">Get affordable motorbike rides in minutes at your doorstep.</p>
                        </div>
                        <div class="mt-6">
                            <span class="inline-flex items-center justify-center px-5 py-2 bg-brand text-white text-sm font-bold rounded-full group-hover:bg-brand-light transition-colors">Details</span>
                        </div>
                    </div>
                    <div class="shrink-0 flex items-end">
                        <img src="https://d1a3f4spazzrp4.cloudfront.net/car-types/haloProductImages/v1.1/Moto_v1.png" 
                             alt="Moto" 
                             class="w-24 h-24 object-contain transform group-hover:scale-110 transition-transform duration-500">
                    </div>
                </a>
            </div>
        </div>
    </section>

    <!-- ============================================================ -->
    <!-- COMPARISON SECTION (Why WADEXPRO is Better) -->
    <!-- ============================================================ -->
    <section class="py-16 lg:py-24 bg-white fade-up" id="why-wadexpro">
        <div class="max-w-[1120px] mx-auto px-6 lg:px-10">
            <h2 class="text-[34px] md:text-[40px] font-black tracking-tight leading-tight text-brand text-center mb-16">Why is WADEXPRO better than a taxi</h2>
            
            <div class="grid lg:grid-cols-3 gap-8">
                <!-- Card 1 -->
                <div class="bg-surface rounded-lg p-8 md:p-10 flex flex-col items-start space-y-6 shadow-sm hover:shadow-md transition-all group">
                    <div class="w-24 h-24 shrink-0">
                        <img src="https://avatars.mds.yandex.net/get-lpc/10704932/e6739b35-e804-433e-a4a2-e9ccb0ef3a78/orig" 
                             alt="Online Booking" 
                             class="w-full h-full object-contain transform group-hover:scale-110 transition-transform">
                    </div>
                    <div class="space-y-3">
                        <h3 class="text-xl font-bold text-brand">Book your ride online</h3>
                        <p class="text-brand-muted text-[15px] leading-relaxed">
                            No more calls to taxi services or street hailing in Ghana. Request your trip with a single tap in the app.
                        </p>
                    </div>
                </div>

                <!-- Card 2 -->
                <div class="bg-surface rounded-lg p-8 md:p-10 flex flex-col items-start space-y-6 shadow-sm hover:shadow-md transition-all group">
                    <div class="w-24 h-24 shrink-0">
                        <img src="https://avatars.mds.yandex.net/get-lpc/10116223/47ef94a0-d128-4336-8ef0-61dabfaf291a/orig" 
                             alt="Price Transparency" 
                             class="w-full h-full object-contain transform group-hover:scale-110 transition-transform">
                    </div>
                    <div class="space-y-3">
                        <h3 class="text-xl font-bold text-brand">Check the price before the ride</h3>
                        <p class="text-brand-muted text-[15px] leading-relaxed">
                            You don't need to negotiate — you already know the exact price while requesting a ride. No surprises.
                        </p>
                    </div>
                </div>

                <!-- Card 3 -->
                <div class="bg-surface rounded-lg p-8 md:p-10 flex flex-col items-start space-y-6 shadow-sm hover:shadow-md transition-all group">
                    <div class="w-24 h-24 shrink-0">
                        <img src="https://avatars.mds.yandex.net/get-lpc/10116223/ee539335-ea85-4b3e-bba3-0c774b369f30/orig" 
                             alt="Car Tracking" 
                             class="w-full h-full object-contain transform group-hover:scale-110 transition-transform">
                    </div>
                    <div class="space-y-3">
                        <h3 class="text-xl font-bold text-brand">Track your car</h3>
                        <p class="text-brand-muted text-[15px] leading-relaxed">
                            See the driver's location before arrival or track your journey progress in real time. Full visibility.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- ============================================================ -->
    <!-- HOW IT WORKS — 3 Step Visual Explainer -->
    <!-- ============================================================ -->
    <section class="py-16 lg:py-24 bg-surface fade-up">
        <div class="max-w-[1440px] mx-auto px-6 lg:px-10">
            <div class="max-w-2xl mb-16">
                <h2 class="text-[32px] md:text-[40px] font-black tracking-tight mb-4">How WADEXPRO works</h2>
                <p class="text-brand-muted text-lg">Getting from A to B has never been simpler.</p>
            </div>
            <div class="grid md:grid-cols-3 gap-8 lg:gap-16">
                <div class="space-y-5">
                    <div class="w-14 h-14 bg-brand rounded-[8px] flex items-center justify-center text-white font-black text-xl">1</div>
                    <h3 class="text-[22px] font-bold">Request a ride</h3>
                    <p class="text-brand-muted leading-relaxed">Open the app, enter your destination, and choose from our range of ride options at your fingertips.</p>
                </div>
                <div class="space-y-5">
                    <div class="w-14 h-14 bg-brand rounded-[8px] flex items-center justify-center text-white font-black text-xl">2</div>
                    <h3 class="text-[22px] font-bold">Get matched instantly</h3>
                    <p class="text-brand-muted leading-relaxed">Our intelligent engine connects you with a nearby driver in seconds. Track their arrival in real time.</p>
                </div>
                <div class="space-y-5">
                    <div class="w-14 h-14 bg-brand rounded-[8px] flex items-center justify-center text-white font-black text-xl">3</div>
                    <h3 class="text-[22px] font-bold">Arrive safely</h3>
                    <p class="text-brand-muted leading-relaxed">Pay seamlessly with cash or mobile money. Share your trip progress with loved ones for peace of mind.</p>
                </div>
            </div>
        </div>
    </section>


    <!-- ============================================================ -->
    <!-- DELIVERY / PACKAGE SECTION -->
    <!-- ============================================================ -->
    <section class="py-16 lg:py-24 bg-white fade-up" id="deliver">
        <div class="max-w-[1440px] mx-auto px-6 lg:px-10">
            <div class="grid lg:grid-cols-2 gap-12 lg:gap-20 items-center">
                <div class="order-2 lg:order-1">
                    <div class="bg-surface rounded-[8px] aspect-[4/3] flex items-center justify-center relative overflow-hidden">
                        <div class="text-center space-y-4 p-10">
                            <div class="w-20 h-20 bg-accent/20 rounded-[8px] mx-auto flex items-center justify-center">
                                <svg class="w-10 h-10 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                            </div>
                            <p class="text-3xl font-black text-brand">Multi-Stop<br>Express</p>
                            <p class="text-brand-muted text-sm">Deliver to 5 stops in one trip</p>
                        </div>
                    </div>
                </div>
                <div class="order-1 lg:order-2 space-y-6 max-w-lg">
                    <h2 class="text-[32px] md:text-[40px] font-black tracking-tight leading-tight">Send packages across town in minutes</h2>
                    <p class="text-brand-muted text-lg leading-relaxed">Whether it's documents, food, or merchandise — WADEXPRO Express gets it there fast. Multi-stop delivery means you can dispatch to an entire neighborhood in one run.</p>
                    <ul class="space-y-4">
                        <li class="flex items-start gap-3">
                            <svg class="w-6 h-6 text-accent mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                            <span class="font-medium">Real-time tracking from pickup to delivery</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <svg class="w-6 h-6 text-accent mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                            <span class="font-medium">Photo proof of delivery at every stop</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <svg class="w-6 h-6 text-accent mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                            <span class="font-medium">Affordable flat-rate pricing, no surge</span>
                        </li>
                    </ul>
                    <a href="{{ route('courier', ['country' => request()->route('country', 'gh'), 'lang' => request()->route('lang', 'en')]) }}" class="inline-block px-8 py-4 bg-brand text-white font-bold rounded-[8px] text-[15px] hover:bg-brand-light transition-colors">Send a package</a>
                </div>
            </div>
        </div>
    </section>


    <!-- ============================================================ -->
    <!-- RESERVE / SCHEDULE RIDE SECTION -->
    <!-- ============================================================ -->
    <section class="py-16 lg:py-24 bg-surface fade-up">
        <div class="max-w-[1440px] mx-auto px-6 lg:px-10">
            <div class="grid lg:grid-cols-2 gap-12 lg:gap-20 items-center">
                <div class="space-y-6 max-w-lg">
                    <h2 class="text-[32px] md:text-[40px] font-black tracking-tight leading-tight">Reserve your ride in advance</h2>
                    <p class="text-brand-muted text-lg leading-relaxed">Heading to the airport? Have an important meeting? Schedule your WADEXPRO ride up to 30 days ahead. Your driver will arrive on time, every time.</p>
                    <ul class="space-y-4">
                        <li class="flex items-start gap-3">
                            <svg class="w-6 h-6 text-accent mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                            <span class="font-medium">Schedule up to 30 days in advance</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <svg class="w-6 h-6 text-accent mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                            <span class="font-medium">Guaranteed pickup at your chosen time</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <svg class="w-6 h-6 text-accent mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                            <span class="font-medium">Extra wait time included at no extra cost</span>
                        </li>
                    </ul>
                    <a href="{{ route('ride', ['country' => request()->route('country', 'gh'), 'lang' => request()->route('lang', 'en')]) }}" class="inline-block px-8 py-4 bg-brand text-white font-bold rounded-[8px] text-[15px] hover:bg-brand-light transition-colors">Reserve now</a>
                </div>
                <div>
                    <div class="bg-white rounded-[8px] aspect-[4/3] flex items-center justify-center shadow-sm border border-gray-100">
                        <div class="text-center space-y-4 p-10">
                            <div class="w-20 h-20 bg-brand/5 rounded-[8px] mx-auto flex items-center justify-center">
                                <svg class="w-10 h-10 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>
                            <p class="text-3xl font-black text-brand">Plan<br>Ahead</p>
                            <p class="text-brand-muted text-sm">Schedule rides up to 30 days</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <!-- ============================================================ -->
    <!-- BECOME A PARTNER SECTION (High-Fidelity Showcase) -->
    <!-- ============================================================ -->
    <section class="py-16 lg:py-24 bg-white fade-up" id="drive">
        <div class="max-w-[1440px] mx-auto px-6 lg:px-10">
            <div class="grid lg:grid-cols-2 gap-12 lg:gap-20 items-center">
                <!-- Content (Left) -->
                <div class="order-2 lg:order-1 space-y-8 max-w-xl">
                    <div class="space-y-4">
                        <h2 class="text-[34px] md:text-[44px] font-black tracking-tight leading-tight text-brand">Become our partner</h2>
                        <div class="w-16 h-1.5 bg-accent rounded-full"></div>
                    </div>
                    
                    <p class="text-brand-muted text-lg leading-relaxed">
                        Team up with WADEXPRO and embark on an exciting journey with your transportation venture. Discover the endless possibilities, from attracting drivers to unlocking exclusive vehicle deals. 
                    </p>
                    <p class="text-brand-muted text-lg leading-relaxed font-light">
                        With our unwavering support, navigating the road to success has never been easier. Join us today and experience the WADEXPRO difference firsthand.
                    </p>

                    <div class="flex flex-col sm:flex-row gap-4 pt-4">
                        <a href="https://yango.com/en_gh/driver/partner/" target="_blank" class="inline-flex items-center justify-center px-10 py-4 bg-accent text-brand font-bold text-sm uppercase tracking-widest rounded-lg hover:bg-accent/90 transition-all shadow-lg hover:shadow-xl hover:-translate-y-0.5">
                            Learn more
                        </a>
                        <a href="{{ Route::has('register') ? route('register', ['country' => request()->route('country', 'gh'), 'lang' => request()->route('lang', 'en')]) : '#' }}" class="inline-flex items-center justify-center px-10 py-4 bg-brand text-white font-bold text-sm uppercase tracking-widest rounded-lg hover:bg-brand-light transition-all shadow-lg hover:shadow-xl hover:-translate-y-0.5">
                            Join Now
                        </a>
                    </div>
                </div>

                <!-- Image (Right) -->
                <div class="order-1 lg:order-2">
                    <div class="relative group">
                        <div class="absolute -inset-4 bg-accent/10 rounded-[12px] transform rotate-2 group-hover:rotate-0 transition-transform duration-500"></div>
                        <div class="relative bg-surface rounded-lg overflow-hidden shadow-2xl aspect-[4/3]">
                            <img src="https://avatars.mds.yandex.net/get-lpc/9736426/7e4a1f74-ee6a-49b6-8866-2107c848d2b2/orig" 
                                 alt="WADEXPRO Partnership" 
                                 class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-700">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <!-- ============================================================ -->
    <!-- BUSINESS SECTION -->
    <!-- ============================================================ -->
    <section class="py-16 lg:py-24 bg-surface fade-up" id="business">
        <div class="max-w-[1440px] mx-auto px-6 lg:px-10">
            <div class="grid lg:grid-cols-2 gap-12 lg:gap-20 items-center">
                <div class="space-y-6 max-w-lg">
                    <div class="inline-flex items-center gap-2 px-4 py-1.5 bg-accent/10 rounded-full text-xs font-bold text-accent uppercase tracking-wider">Enterprise</div>
                    <h2 class="text-[32px] md:text-[40px] font-black tracking-tight leading-tight">WADEXPRO for Business</h2>
                    <p class="text-brand-muted text-lg leading-relaxed">Transform your corporate transportation. Manage employee travel, client rides, and fleet logistics from a single dashboard. Detailed analytics and billing controls included.</p>
                    <ul class="space-y-4">
                        <li class="flex items-start gap-3">
                            <svg class="w-6 h-6 text-accent mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                            <span class="font-medium">Centralized billing & expense reports</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <svg class="w-6 h-6 text-accent mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                            <span class="font-medium">Employee ride management portal</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <svg class="w-6 h-6 text-accent mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                            <span class="font-medium">API integration for custom logistics workflows</span>
                        </li>
                    </ul>
                    <a href="#" class="inline-block px-8 py-4 bg-brand text-white font-bold rounded-[8px] text-[15px] hover:bg-brand-light transition-colors">Get started</a>
                </div>
                <div>
                    <div class="bg-white rounded-[8px] aspect-[4/3] flex items-center justify-center shadow-sm border border-gray-100">
                        <div class="text-center space-y-4 p-10">
                            <div class="w-20 h-20 bg-brand/5 rounded-[8px] mx-auto flex items-center justify-center">
                                <svg class="w-10 h-10 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            </div>
                            <p class="text-3xl font-black text-brand">Corporate<br>Dashboard</p>
                            <p class="text-brand-muted text-sm">Manage. Analyze. Scale.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <!-- ============================================================ -->
    <!-- SAFETY SECTION -->
    <!-- ============================================================ -->
    <section class="py-16 lg:py-24 bg-brand text-white fade-up" id="safety">
        <div class="max-w-[1440px] mx-auto px-6 lg:px-10">
            <div class="max-w-2xl mx-auto text-center mb-16">
                <h2 class="text-[32px] md:text-[40px] font-black tracking-tight mb-4">Your safety drives us</h2>
                <p class="text-white/50 text-lg">Every trip on WADEXPRO is backed by proactive safety technology and responsive support.</p>
            </div>
            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-white/5 rounded-[8px] p-7 border border-white/5 hover:bg-white/10 transition-colors">
                    <div class="w-12 h-12 bg-accent/20 rounded-[8px] flex items-center justify-center mb-5">
                        <svg class="w-6 h-6 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </div>
                    <h3 class="font-bold text-lg mb-2">Driver Verification</h3>
                    <p class="text-white/40 text-sm leading-relaxed">Every driver passes background and document checks before going online.</p>
                </div>
                <div class="bg-white/5 rounded-[8px] p-7 border border-white/5 hover:bg-white/10 transition-colors">
                    <div class="w-12 h-12 bg-red-500/20 rounded-[8px] flex items-center justify-center mb-5">
                        <svg class="w-6 h-6 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                    </div>
                    <h3 class="font-bold text-lg mb-2">SOS Emergency</h3>
                    <p class="text-white/40 text-sm leading-relaxed">One-tap emergency alert instantly notifies our response team and your contacts.</p>
                </div>
                <div class="bg-white/5 rounded-[8px] p-7 border border-white/5 hover:bg-white/10 transition-colors">
                    <div class="w-12 h-12 bg-blue-500/20 rounded-[8px] flex items-center justify-center mb-5">
                        <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    <h3 class="font-bold text-lg mb-2">Live Trip Sharing</h3>
                    <p class="text-white/40 text-sm leading-relaxed">Share your journey progress with family and friends in real time.</p>
                </div>
                <div class="bg-white/5 rounded-[8px] p-7 border border-white/5 hover:bg-white/10 transition-colors">
                    <div class="w-12 h-12 bg-green-500/20 rounded-[8px] flex items-center justify-center mb-5">
                        <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                    </div>
                    <h3 class="font-bold text-lg mb-2">24/7 Support</h3>
                    <p class="text-white/40 text-sm leading-relaxed">Round-the-clock in-app support for riders and drivers whenever you need it.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ============================================================ -->
    <!-- LIGHT CTA SECTION (Split Design: Order Online) -->
    <!-- ============================================================ -->
    <section class="py-16 lg:py-24 bg-surface fade-up">
        <div class="max-w-[1440px] mx-auto px-6 lg:px-10">
            <div class="bg-white rounded-lg p-8 md:p-12 shadow-sm border border-gray-100">
                <div class="grid lg:grid-cols-2 gap-12 items-center">
                    
                    <!-- Content (Left) -->
                    <div class="space-y-8">
                        <div class="space-y-4">
                            <h2 class="text-[34px] md:text-[44px] font-black tracking-tight leading-tight text-brand">Order a ride online</h2>
                            <p class="text-brand-muted text-lg leading-relaxed max-w-lg">
                                Download the WADEXPRO app on iOS and Android now to book your ride instantly. Experience the future of mobility in Ghana.
                            </p>
                        </div>

                        <!-- Scan & Download Block -->
                        <div class="flex flex-wrap items-center gap-10">
                            <!-- QR Code -->
                            <div class="flex items-center gap-4 bg-surface/50 p-4 rounded-lg border border-gray-100 shadow-inner">
                                <div class="w-24 h-24 bg-white p-2 rounded-lg shadow-sm">
                                    <img src="https://avatars.mds.yandex.net/get-lpc/9736426/3260b0a5-015f-44d1-8fee-25bd33dbfc86/orig" 
                                         alt="WADEXPRO QR" 
                                         class="w-full h-full object-contain">
                                </div>
                                <div>
                                    <p class="text-xs font-bold text-brand uppercase tracking-wider">Quick Download</p>
                                    <p class="text-[11px] text-brand-muted mt-0.5 leading-tight">Scan with your<br>phone camera</p>
                                </div>
                            </div>

                            <!-- CTA Button -->
                            <div class="flex flex-col gap-4">
                                <a href="#" class="inline-flex items-center justify-center px-10 py-4 bg-accent text-brand font-bold text-sm uppercase tracking-widest rounded-full hover:bg-accent/90 transition-all shadow-lg hover:-translate-y-0.5">
                                    Get the App
                                </a>
                                <div class="flex justify-center gap-4 text-brand-muted">
                                    <svg class="w-6 h-6 opacity-40 hover:opacity-100 transition-opacity cursor-pointer" fill="currentColor" viewBox="0 0 24 24"><path d="M18.71 19.5c-.83 1.24-1.71 2.45-3.05 2.47-1.34.03-1.77-.79-3.29-.79-1.53 0-2 .77-3.27.82-1.31.05-2.3-1.32-3.14-2.53C4.25 17 2.94 12.45 4.7 9.39c.87-1.52 2.43-2.48 4.12-2.51 1.28-.02 2.5.87 3.29.87.78 0 2.26-1.07 3.8-.91.65.03 2.47.26 3.64 1.98-.09.06-2.17 1.28-2.15 3.81.03 3.02 2.65 4.03 2.68 4.04-.03.07-.42 1.44-1.38 2.83M13 3.5c.73-.83 1.94-1.46 2.94-1.5.13 1.17-.34 2.35-1.04 3.19-.69.85-1.83 1.51-2.95 1.42-.15-1.15.41-2.35 1.05-3.11z"/></svg>
                                    <svg class="w-6 h-6 opacity-40 hover:opacity-100 transition-opacity cursor-pointer" fill="currentColor" viewBox="0 0 24 24"><path d="M3 20.5v-17c0-.59.34-1.11.84-1.35L13.69 12l-9.85 9.85c-.5-.24-.84-.76-.84-1.35zm13.81-5.38L6.05 21.34l8.49-8.49 2.27 2.27zm2.59-1.44l-2.56-1.42-2.47 2.47 2.47 2.47 2.56-1.42c.52-.29.84-.84.84-1.41 0-.57-.32-1.12-.84-1.41l.01-.28zm-15.97-10l10.76 6.22-2.27 2.27-8.49-8.49z"/></svg>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Side Image (Right) -->
                    <div class="relative min-h-[300px] h-full rounded-lg overflow-hidden shadow-2xl">
                        <img src="https://avatars.mds.yandex.net/get-lpc/9736426/d5dc1dd6-7ad8-4827-897f-25859093918c/orig" 
                             alt="WADEXPRO App Interface" 
                             class="absolute inset-0 w-full h-full object-cover transform hover:scale-105 transition-transform duration-700">
                    </div>

                </div>
            </div>
        </div>
    </section>


    <!-- ============================================================ -->
    <!-- APP PROMO SECTION (High-Fidelity Business Showcase) -->
    <!-- ============================================================ -->
    <section id="app-download-showcase" class="py-10 px-6 lg:px-10">
        <div class="max-w-[1440px] mx-auto">
            <div class="relative w-full aspect-[21/9] min-h-[500px] rounded-lg overflow-hidden bg-brand shadow-2xl">
                <!-- Background Image Showcase -->
                <div class="absolute inset-0 z-0">
                    <img src="https://avatars.mds.yandex.net/get-lpc/9736426/f8dc7a6c-a344-4c22-a1e4-28e341ab3ab2/orig" 
                         alt="WADEXPRO Experience" 
                         class="w-full h-full object-cover opacity-70">
                    <div class="absolute inset-0 bg-gradient-to-r from-brand via-brand/60 to-transparent"></div>
                </div>

                <!-- Content Card (Floated Left) -->
                <div class="relative z-10 h-full flex items-center p-8 md:p-16">
                    <div class="bg-brand border border-white/10 p-10 md:p-12 rounded-lg max-w-[480px] shadow-2xl backdrop-blur-md">
                        <div class="space-y-6">
                            <h2 class="text-3xl md:text-5xl font-black text-white leading-tight">Order a ride online</h2>
                            <p class="text-white/70 text-sm md:text-lg leading-relaxed font-light">
                                Download the WADEXPRO app on iOS and Android now to book your ride instantly and move with precision.
                            </p>

                            <!-- QR Code & Actions Row -->
                            <div class="flex items-center gap-8 pt-4">
                                <!-- QR CODE -->
                                <div class="shrink-0">
                                    <div class="bg-white p-3 rounded-lg shadow-lg">
                                        <img src="https://avatars.mds.yandex.net/get-lpc/9736426/a3716af0-39f1-4a2c-a654-587d204a7287/orig" 
                                             alt="Scan to Download" 
                                             class="w-24 h-24 md:w-28 md:h-28 object-contain">
                                    </div>
                                    <p class="text-[9px] text-center text-white/40 mt-2 font-bold uppercase tracking-widest leading-none">Scan to Book</p>
                                </div>

                                <!-- BUTTONS -->
                                <div class="flex flex-col gap-4">
                                    <a href="#" class="inline-flex items-center justify-center px-8 py-3.5 bg-accent text-brand font-bold text-sm uppercase tracking-widest rounded-full hover:bg-accent-hover transition-all shadow-xl hover:-translate-y-1">
                                        Get the App
                                    </a>
                                    <div class="flex gap-4">
                                        <a href="#" class="text-white/60 hover:text-white transition-colors" title="Apple Store">
                                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M18.71 19.5c-.83 1.24-1.71 2.45-3.05 2.47-1.34.03-1.77-.79-3.29-.79-1.53 0-2 .77-3.27.82-1.31.05-2.3-1.32-3.14-2.53C4.25 17 2.94 12.45 4.7 9.39c.87-1.52 2.43-2.48 4.12-2.51 1.28-.02 2.5.87 3.29.87.78 0 2.26-1.07 3.8-.91.65.03 2.47.26 3.64 1.98-.09.06-2.17 1.28-2.15 3.81.03 3.02 2.65 4.03 2.68 4.04-.03.07-.42 1.44-1.38 2.83M13 3.5c.73-.83 1.94-1.46 2.94-1.5.13 1.17-.34 2.35-1.04 3.19-.69.85-1.83 1.51-2.95 1.42-.15-1.15.41-2.35 1.05-3.11z"/></svg>
                                        </a>
                                        <a href="#" class="text-white/60 hover:text-white transition-colors" title="Google Play">
                                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M3 20.5v-17c0-.59.34-1.11.84-1.35L13.69 12l-9.85 9.85c-.5-.24-.84-.76-.84-1.35zm13.81-5.38L6.05 21.34l8.49-8.49 2.27 2.27zm2.59-1.44l-2.56-1.42-2.47 2.47 2.47 2.47 2.56-1.42c.52-.29.84-.84.84-1.41 0-.57-.32-1.12-.84-1.41l.01-.28zm-15.97-10l10.76 6.22-2.27 2.27-8.49-8.49z"/></svg>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <!-- ============================================================ -->
    <!-- PRE-FOOTER APP DOWNLOAD -->
    <!-- ============================================================ -->
    <section class="bg-brand border-b border-white/5">
        <div class="max-w-[1440px] mx-auto px-6 lg:px-10 py-12 md:py-16">
            <div class="flex flex-col lg:flex-row items-center justify-between gap-10">
                <div class="space-y-5 text-center lg:text-left">
                    <h4 class="font-bold text-sm uppercase tracking-widest text-white/30">Experience WADEXPRO on mobile</h4>
                    <div class="flex flex-wrap justify-center lg:justify-start gap-4">
                        <!-- Apple Store -->
                        <a href="#" class="flex items-center gap-4 bg-white/5 hover:bg-white/10 px-6 py-3 rounded-lg border border-white/10 transition-all group w-full sm:w-auto min-w-[180px]">
                            <svg class="w-8 h-8 text-white group-hover:text-accent transition-colors" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M18.71 19.5c-.83 1.24-1.71 2.45-3.05 2.47-1.34.03-1.77-.79-3.29-.79-1.53 0-2 .77-3.27.82-1.31.05-2.3-1.32-3.14-2.53C4.25 17 2.94 12.45 4.7 9.39c.87-1.52 2.43-2.48 4.12-2.51 1.28-.02 2.5.87 3.29.87.78 0 2.26-1.07 3.8-.91.65.03 2.47.26 3.64 1.98-.09.06-2.17 1.28-2.15 3.81.03 3.02 2.65 4.03 2.68 4.04-.03.07-.42 1.44-1.38 2.83M13 3.5c.73-.83 1.94-1.46 2.94-1.5.13 1.17-.34 2.35-1.04 3.19-.69.85-1.83 1.51-2.95 1.42-.15-1.15.41-2.35 1.05-3.11z"/>
                            </svg>
                            <div class="text-left">
                                <p class="text-[10px] uppercase font-bold text-white/40 leading-none">Download on the</p>
                                <p class="text-[16px] font-bold text-white leading-none mt-1.5">App Store</p>
                            </div>
                        </a>
                        <!-- Google Play -->
                        <a href="#" class="flex items-center gap-4 bg-white/5 hover:bg-white/10 px-6 py-3 rounded-lg border border-white/10 transition-all group w-full sm:w-auto min-w-[180px]">
                            <svg class="w-8 h-8 text-white group-hover:text-accent transition-colors" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M3 20.5v-17c0-.59.34-1.11.84-1.35L13.69 12l-9.85 9.85c-.5-.24-.84-.76-.84-1.35zm13.81-5.38L6.05 21.34l8.49-8.49 2.27 2.27zm2.59-1.44l-2.56-1.42-2.47 2.47 2.47 2.47 2.56-1.42c.52-.29.84-.84.84-1.41 0-.57-.32-1.12-.84-1.41l.01-.28zm-15.97-10l10.76 6.22-2.27 2.27-8.49-8.49z"/>
                            </svg>
                            <div class="text-left">
                                <p class="text-[10px] uppercase font-bold text-white/40 leading-none">Get it on</p>
                                <p class="text-[16px] font-bold text-white leading-none mt-1.5">Google Play</p>
                            </div>
                        </a>
                    </div>
                </div>

                <!-- QR Code Tile -->
                <div class="flex items-center gap-5 bg-white/[0.03] p-5 rounded-lg border border-white/5 backdrop-blur-sm group hover:bg-white/[0.05] transition-colors">
                    <div class="bg-white p-2 rounded-lg shadow-2xl transform group-hover:scale-105 transition-transform duration-500">
                        <img src="https://avatars.mds.yandex.net/get-lpc/9736426/3260b0a5-015f-44d1-8fee-25bd33dbfc86/orig" 
                             alt="WADEXPRO QR" 
                             class="w-16 h-16 object-contain">
                    </div>
                    <div>
                        <p class="text-xs font-bold text-accent uppercase tracking-widest">Scan to Install</p>
                        <p class="text-[10px] text-white/30 mt-1.5 leading-relaxed">Point your camera at<br>the code to download</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <x-global-footer />


    <!-- ============================================================ -->
    <!-- JAVASCRIPT -->
    <!-- ============================================================ -->
    <script>
        // Mobile Menu Toggle
        function toggleMobileMenu() {
            document.getElementById('mobileMenu').classList.toggle('open');
            document.getElementById('mobileOverlay').classList.toggle('open');
            document.body.classList.toggle('overflow-hidden');
        }

        // Close mobile menu on link click
        document.querySelectorAll('#mobileMenu a').forEach(link => {
            link.addEventListener('click', () => toggleMobileMenu());
        });

        // Scroll Animations (Intersection Observer)
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                }
            });
        }, { threshold: 0.1, rootMargin: '0px 0px -50px 0px' });

        document.querySelectorAll('.fade-up').forEach(el => observer.observe(el));
    </script>

</body>
</html>
