<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
    <title>WADEXPRO | Moto (Okada) — Fast & Affordable</title>
    
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
                        brand: { DEFAULT: '#0A0A0A', light: '#1A1A1A', muted: '#6B6B6B' },
                        accent: { DEFAULT: '#F8B803', hover: '#FFD60A' },
                        surface: { DEFAULT: '#F3F4F6', dark: '#E5E7EB' },
                    },
                    fontFamily: {
                        outfit: ['Outfit', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Outfit', sans-serif; -webkit-font-smoothing: antialiased; }
        .hero-gradient { 
            background: linear-gradient(135deg, #0A0A0A 0%, #1A1A1A 100%); 
        }
        [x-cloak] { display: none !important; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>
<body class="h-full bg-white text-brand overflow-x-hidden">

    <x-global-header theme="dark" />

    <!-- Mobility Sub-Navigation (Fixed below global header) -->
    <nav class="fixed top-[72px] left-0 right-0 z-[90] bg-white border-b border-gray-100 overflow-x-auto no-scrollbar shadow-sm">
        <div class="max-w-[1440px] mx-auto px-6 lg:px-10 h-[56px] flex items-center gap-8">
            <div class="flex items-center gap-1 shrink-0">
                <span class="text-sm font-bold border-b-2 border-brand pb-4 mt-4">Moto</span>
            </div>
            <div class="flex items-center gap-6 h-full text-[13px] font-bold text-brand/70 whitespace-nowrap">
                <a href="#booking-form" class="hover:text-brand transition-colors">Request</a>
                <a href="#booking-form" class="hover:text-brand transition-colors">Reserve</a>
                <a href="#booking-form" class="hover:text-brand transition-colors pointer-events-none opacity-50">See prices</a>
                <a href="#explore-rides" class="hover:text-brand transition-colors">Explore options</a>
                <a href="#explore-rides" class="hover:text-brand transition-colors">Airports</a>
            </div>
        </div>
    </nav>

    <main class="pt-[128px]">
        
        <!-- Hero Section with Booking Form -->
        <section class="relative bg-brand text-white overflow-hidden lg:min-h-[600px] flex items-center">
            <div class="max-w-[1240px] mx-auto w-full px-6 py-12 lg:py-20 grid lg:grid-cols-2 gap-12 items-center relative z-10">
                
                <!-- Left: Booking Form -->
                <div class="space-y-8 animate-fadeIn">
                    <div class="space-y-4">
                        <h1 class="text-[42px] lg:text-[64px] font-black tracking-tighter leading-tight">WADEXPRO Moto</h1>
                        <p class="text-white/70 text-lg lg:text-xl max-w-lg">
                            Affordable motorbike rides at your doorstep. Fast, efficient, and built to beat the city traffic.
                        </p>
                    </div>

                    <!-- Interactive Form Card -->
                    <div id="booking-form" class="bg-white rounded-[16px] p-6 lg:p-8 space-y-6 shadow-2xl scroll-mt-[120px]">
                        <div class="relative space-y-4">
                            <!-- Line -->
                            <div class="absolute left-[19px] top-7 bottom-7 w-0.5 bg-gray-100 pointer-events-none"></div>

                            <!-- Pickup -->
                            <div class="flex items-center gap-4 bg-surface rounded-[8px] px-4 py-3.5 group focus-within:ring-2 focus-within:ring-brand/5 transition-all">
                                <div class="relative z-10 w-2.5 h-2.5 rounded-full bg-brand shrink-0"></div>
                                <div class="flex-1 flex flex-col">
                                    <label class="text-[10px] uppercase tracking-widest font-black text-brand-muted mb-0.5">Pickup</label>
                                    <input type="text" placeholder="Enter pickup location" class="bg-transparent text-[15px] font-semibold text-brand outline-none placeholder-brand/30 w-full">
                                </div>
                            </div>

                            <!-- Dropoff -->
                            <div class="flex items-center gap-4 bg-surface rounded-[8px] px-4 py-3.5 group focus-within:ring-2 focus-within:ring-brand/5 transition-all">
                                <div class="relative z-10 w-2.5 h-2.5 rounded-sm bg-brand shrink-0"></div>
                                <div class="flex-1 flex flex-col">
                                    <label class="text-[10px] uppercase tracking-widest font-black text-brand-muted mb-0.5">Dropoff</label>
                                    <input type="text" placeholder="Enter dropoff location" class="bg-transparent text-[15px] font-semibold text-brand outline-none placeholder-brand/30 w-full">
                                </div>
                            </div>
                        </div>

                        <button class="w-full bg-brand text-white py-4 rounded-xl font-bold hover:bg-brand-light transition-all shadow-lg hover:shadow-brand/20">
                            See prices
                        </button>
                    </div>
                </div>

                <!-- Right: High Fidelity Product Visual -->
                <div class="relative hidden lg:flex justify-end pr-10">
                    <!-- Glow effect -->
                    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[500px] h-[500px] bg-accent/20 blur-[120px] rounded-full"></div>
                    
                    <img src="https://d1a3f4spazzrp4.cloudfront.net/car-types/haloProductImages/v1.1/Moto_v1.png" 
                         alt="3D Moto" 
                         class="w-[500px] drop-shadow-[0_35px_35px_rgba(248,184,3,0.3)] animate-float">
                </div>
            </div>

            <!-- Background subtle pattern -->
            <div class="absolute inset-0 opacity-10 pointer-events-none">
                <svg width="100%" height="100%" xmlns="http://www.w3.org/2000/svg"><defs><pattern id="grid" width="40" height="40" patternUnits="userSpaceOnUse"><path d="M 40 0 L 0 0 0 40" fill="none" stroke="white" stroke-width="1"/></pattern></defs><rect width="100%" height="100%" fill="url(#grid)" /></svg>
            </div>
        </section>

        <!-- Why Ride Section -->
        <section class="py-20 bg-white">
            <div class="max-w-[1240px] mx-auto px-6">
                <div class="mb-16">
                    <h2 class="text-3xl lg:text-[44px] font-black tracking-tight leading-tight">Why ride with WADEXPRO Moto</h2>
                </div>

                <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-10">
                    <!-- Reason 1 -->
                    <div class="space-y-6 group">
                        <div class="w-14 h-14 bg-surface rounded-2xl flex items-center justify-center group-hover:bg-accent transition-colors duration-300">
                             <img src="https://tb-static.uber.com/prod/udam-assets/c38b3e70-29b5-4c83-a018-bf7186d05847.svg" class="w-8 h-8">
                        </div>
                        <h3 class="text-xl font-bold">On-demand</h3>
                        <p class="text-brand-muted leading-relaxed">No need to wait for a bus. Get a ride in minutes at the tap of a button.</p>
                    </div>

                    <!-- Reason 2 -->
                    <div class="space-y-6 group">
                        <div class="w-14 h-14 bg-surface rounded-2xl flex items-center justify-center group-hover:bg-accent transition-colors duration-300">
                             <img src="https://tb-static.uber.com/prod/udam-assets/0db55b6d-9fd3-4b7e-940a-96d1e11b149f.svg" class="w-8 h-8">
                        </div>
                        <h3 class="text-xl font-bold">Reach faster</h3>
                        <p class="text-brand-muted leading-relaxed">Beat the traffic and navigate narrow lanes easily to save valuable time.</p>
                    </div>

                    <!-- Reason 3 -->
                    <div class="space-y-6 group">
                        <div class="w-14 h-14 bg-surface rounded-2xl flex items-center justify-center group-hover:bg-accent transition-colors duration-300">
                             <img src="https://tb-static.uber.com/prod/udam-assets/748180a7-62f4-43f2-8e7f-f7f32ed71cb6.svg" class="w-8 h-8">
                        </div>
                        <h3 class="text-xl font-bold">Ride comfortably</h3>
                        <p class="text-brand-muted leading-relaxed">Skip crowded public transport. Enjoy the breeze and a private trip.</p>
                    </div>

                    <!-- Reason 4 -->
                    <div class="space-y-6 group">
                        <div class="w-14 h-14 bg-surface rounded-2xl flex items-center justify-center group-hover:bg-accent transition-colors duration-300">
                             <img src="https://tb-static.uber.com/prod/udam-assets/c95c000d-2dc2-4d6f-964e-c637619be46c.svg" class="w-8 h-8">
                        </div>
                        <h3 class="text-xl font-bold">Pocket-friendly</h3>
                        <p class="text-brand-muted leading-relaxed">Our most affordable ride option, perfect for short trips or daily commutes.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- How to Ride Section (Enhanced) -->
        <section class="py-24 bg-surface/30 border-t border-gray-100">
            <div class="max-w-[1240px] mx-auto px-6">
                <!-- Header -->
                <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-6 mb-16">
                    <div class="space-y-4">
                        <h2 class="text-[32px] lg:text-[44px] font-black tracking-tight leading-tight">How to ride with WADEXPRO Moto</h2>
                        <p class="text-brand-muted text-lg max-w-xl">Everything you need to know from the first tap to your final destination.</p>
                    </div>
                    <div class="pb-2">
                        <a href="#" class="inline-flex items-center font-bold border-b-2 border-brand pb-1 hover:text-accent hover:border-accent transition-all">
                            Read more about how riding works
                        </a>
                    </div>
                </div>

                <!-- Steps Grid -->
                <div class="grid lg:grid-cols-3 gap-12">
                    <!-- Step 1 -->
                    <div class="space-y-6 group">
                        <div class="w-16 h-16 bg-white rounded-2xl flex items-center justify-center shadow-sm group-hover:bg-accent transition-colors duration-300">
                             <div class="text-2xl font-black">1</div>
                        </div>
                        <div class="space-y-4">
                            <h3 class="text-2xl font-bold">Request</h3>
                            <div class="space-y-4 text-brand-muted leading-relaxed">
                                <p>Open the app and enter your destination in the “Where to?” box. Once you confirm that your pickup and destination addresses are correct, select <strong class="text-brand">WADEXPRO Moto</strong>.</p>
                                <p class="text-sm">Once you’ve been matched with a driver, you’ll see their picture and vehicle details and can track their arrival on the map.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Step 2 -->
                    <div class="space-y-6 group">
                        <div class="w-16 h-16 bg-white rounded-2xl flex items-center justify-center shadow-sm group-hover:bg-accent transition-colors duration-300">
                             <div class="text-2xl font-black">2</div>
                        </div>
                        <div class="space-y-4">
                            <h3 class="text-2xl font-bold">Ride</h3>
                            <div class="space-y-4 text-brand-muted leading-relaxed">
                                <p>Check that the vehicle details match what you see in the app before getting on the motorbike.</p>
                                <p class="text-sm">Your driver has your destination and directions for the fastest way to get there, but you can always request a specific route.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Step 3 -->
                    <div class="space-y-6 group">
                        <div class="w-16 h-16 bg-white rounded-2xl flex items-center justify-center shadow-sm group-hover:bg-accent transition-colors duration-300">
                             <div class="text-2xl font-black">3</div>
                        </div>
                        <div class="space-y-4">
                            <h3 class="text-2xl font-bold">Hop out</h3>
                            <div class="space-y-4 text-brand-muted leading-relaxed">
                                <p>You’ll be automatically charged through your payment method on file, so you can exit the vehicle as soon as you arrive.</p>
                                <p class="text-sm">Remember to rate your driver to help keep WADEXPRO safe and enjoyable for everyone.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Rides Around the World Section -->
        <section id="explore-rides" class="py-24 bg-white overflow-hidden scroll-mt-[120px]" x-data="{ 
            activeTab: 'featured',
            products: {
                featured: [
                    { title: 'WADEXPRO Reserve', desc: 'Book a ride in advance', img: 'https://cn-geo1.uber.com/image-proc/crop/resizecrop/udam/format=auto/width=598/height=336/srcb64=aHR0cHM6Ly90Yi1zdGF0aWMudWJlci5jb20vcHJvZC91ZGFtLWFzc2V0cy85ZjU1YmIyMC05Mjg4LTQyYTYtOTMxZC1jNDI5OTZjNmY1OTMucG5n' },
                    { title: 'WADEXPRO Rent', desc: 'Pick a car. See the price. Get moving.', img: 'https://cn-geo1.uber.com/image-proc/crop/resizecrop/udam/format=auto/width=598/height=336/srcb64=aHR0cHM6Ly90Yi1zdGF0aWMudWJlci5jb20vcHJvZC91ZGFtLWFzc2V0cy9jMjg4Y2RkZC1lZWZjLTQxY2EtYWM5YS04YzFjZjE4MzcyMTEucG5n' },
                    { title: 'WADEXPRO Taxi', desc: 'Local taxi cabs at the tap of a button', img: 'https://cn-geo1.uber.com/image-proc/crop/resizecrop/udam/format=auto/width=598/height=336/srcb64=aHR0cHM6Ly90Yi1zdGF0aWMudWJlci5jb20vcHJvZC91ZGFtLWFzc2V0cy8zNTdmMTJmNi1iMzhiLTQwM2QtYjNlZS04NGM2YzdhM2QwODAuanBn' }
                ],
                wheels: [
                    { title: 'WADEXPRO Moto', desc: 'Affordable, convenient motorcycle rides', img: 'https://cn-geo1.uber.com/image-proc/crop/resizecrop/udam/format=auto/width=552/height=552/srcb64=aHR0cHM6Ly90Yi1zdGF0aWMudWJlci5jb20vcHJvZC91ZGFtLWFzc2V0cy9lZjA5NThiZC1kNDMwLTQ1ZWYtYmU2Yi0zYmZiY2JmMDYyZjYucG5n' },
                    { title: 'WADEXPRO Bike', desc: 'On-demand electric bikes', img: 'https://cn-geo1.uber.com/image-proc/crop/resizecrop/udam/format=auto/width=598/height=336/srcb64=aHR0cHM6Ly90Yi1zdGF0aWMudWJlci5jb20vcHJvZC91ZGFtLWFzc2V0cy84ZmQwYjhkMi0zYjIzLTQ5MGItYjliYS01ZDUyYmI5N2M3OTMucG5n' },
                    { title: 'WADEXPRO Scooter', desc: 'Electric scooters for city trips', img: 'https://cn-geo1.uber.com/image-proc/crop/resizecrop/udam/format=auto/width=598/height=336/srcb64=aHR0cHM6Ly90Yi1zdGF0aWMudWJlci5jb20vcHJvZC91ZGFtLWFzc2V0cy82YzU2YjhkMi0zYjIzLTQ5MGItYjliYS01ZDUyYmI5N2M3OTMucG5n' }
                ],
                extra: [
                    { title: 'WADEXPRO XL', desc: 'Affordable rides for groups up to 6', img: 'https://cn-geo1.uber.com/image-proc/crop/resizecrop/udam/format=auto/width=598/height=336/srcb64=aHR0cHM6Ly90Yi1zdGF0aWMudWJlci5jb20vcHJvZC91ZGFtLWFzc2V0cy9mMGQ0YjhkMi0zYjIzLTQ5MGItYjliYS01ZDUyYmI5N2M3OTMucG5n' },
                    { title: 'WADEXPRO Black SUV', desc: 'Premium rides for 6 in luxury SUVs', img: 'https://cn-geo1.uber.com/image-proc/crop/resizecrop/udam/format=auto/width=598/height=336/srcb64=aHR0cHM6Ly90Yi1zdGF0aWMudWJlci5jb20vcHJvZC91ZGFtLWFzc2V0cy9nMGQ0YjhkMi0zYjIzLTQ5MGItYjliYS01ZDUyYmI5N2M3OTMucG5n' }
                ]
            }
        }">
            <div class="max-w-[1240px] mx-auto px-6">
                <!-- Header -->
                <div class="mb-12 space-y-4">
                    <h2 class="text-[32px] lg:text-[44px] font-black tracking-tight leading-tight">Rides around the world</h2>
                    <p class="text-brand-muted text-lg max-w-2xl leading-relaxed">
                        There’s more than one way to move with WADEXPRO, no matter where you are or where you’re headed next. Check the app to see which ride options are available near you.
                    </p>
                </div>

                <!-- Tab Navigation -->
                <div class="flex border-b border-gray-100 mb-10 overflow-x-auto no-scrollbar">
                    <button @click="activeTab = 'featured'" :class="activeTab === 'featured' ? 'text-brand border-brand' : 'text-brand-muted border-transparent hover:text-brand'" class="px-8 py-4 font-bold text-lg border-b-4 transition-all whitespace-nowrap">Featured</button>
                    <button @click="activeTab = 'wheels'" :class="activeTab === 'wheels' ? 'text-brand border-brand' : 'text-brand-muted border-transparent hover:text-brand'" class="px-8 py-4 font-bold text-lg border-b-4 transition-all whitespace-nowrap">2 or 3 wheels</button>
                    <button @click="activeTab = 'extra'" :class="activeTab === 'extra' ? 'text-brand border-brand' : 'text-brand-muted border-transparent hover:text-brand'" class="px-8 py-4 font-bold text-lg border-b-4 transition-all whitespace-nowrap">Extra room</button>
                </div>

                <!-- Carousel Cards -->
                <div class="relative group">
                    <div class="flex gap-6 overflow-x-auto scroll-smooth no-scrollbar pb-8" id="product-carousel">
                        <template x-for="product in products[activeTab]" :key="product.title">
                            <div class="min-w-[300px] lg:min-w-[380px] bg-surface/30 rounded-[20px] overflow-hidden group/card hover:bg-surface/50 transition-all duration-300">
                                <div class="aspect-[16/9] overflow-hidden">
                                    <img :src="product.img" :alt="product.title" class="w-full h-full object-contain group-hover/card:scale-105 transition-transform duration-500 p-4">
                                </div>
                                <div class="p-8 space-y-4">
                                    <h3 class="text-xl font-black tracking-tight" x-text="product.title"></h3>
                                    <p class="text-brand-muted text-[15px] leading-relaxed" x-text="product.desc"></p>
                                    <div class="pt-2">
                                        <a href="#" class="inline-flex items-center gap-2 font-bold group-hover/card:text-accent transition-colors">
                                            Learn more
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </section>

        <!-- Legal/Disclaimer -->
        <section class="py-12 border-t border-gray-100 bg-white">
            <div class="max-w-[1240px] mx-auto px-6">
                <p class="text-white/40 text-xs text-brand-muted/40 max-w-3xl">
                    *The material provided on this web page is intended for informational purposes only and may not be applicable in your country, region, or city. It is subject to change and may be updated without notice.
                </p>
            </div>
        </section>

    </main>

    <x-global-footer />

    <style>
        @keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes float { 0% { transform: translateY(0); } 50% { transform: translateY(-20px); } 100% { transform: translateY(0); } }
        .animate-fadeIn { animation: fadeIn 0.8s ease-out forwards; }
        .animate-float { animation: float 6s ease-in-out infinite; }
    </style>

</body>
</html>
