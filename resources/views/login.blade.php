<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>WADEXPRO | Log in</title>
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
                        surface: { DEFAULT: '#F6F6F6', dark: '#EEEEEE' },
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
        .input-focus-ring:focus-within { box-shadow: 0 0 0 2px #0A0A0A; }
        .glass-panel {
            background: rgba(10, 10, 10, 0.4);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        @keyframes fadeSlideUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-up { animation: fadeSlideUp 0.8s ease-out forwards; }
    </style>
</head>
<body class="h-full bg-white text-brand overflow-hidden" x-data="loginHandler()">

    <div class="flex h-full w-full">
        <!-- LEFT PANEL: VISUAL SHOWCASE (60%) -->
        <div class="hidden lg:flex lg:w-[60%] h-full relative overflow-hidden bg-brand">
            <!-- Background Image -->
            <img src="https://images.unsplash.com/photo-1557804506-669a67965ba0?auto=format&fit=crop&q=80&w=1974" 
                 class="absolute inset-0 w-full h-full object-cover transform scale-105 hover:scale-100 transition-transform duration-[10s] ease-linear opacity-60" 
                 alt="WADEXPRO Business">
            
            <!-- Gradient Overlays -->
            <div class="absolute inset-0 bg-gradient-to-t from-brand via-brand/20 to-transparent"></div>
            <div class="absolute inset-0 bg-gradient-to-r from-brand/80 via-transparent to-transparent"></div>

            <!-- Content Container -->
            <div class="relative z-10 w-full h-full flex flex-col justify-between p-16">
                <!-- Branding -->
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-accent rounded-lg flex items-center justify-center font-black text-brand text-lg shadow-xl">W</div>
                    <span class="text-white text-2xl font-black tracking-tighter uppercase">WADEX<span class="text-accent">PRO</span></span>
                </div>

                <!-- Info/Ads Cards -->
                <div class="space-y-6 max-w-md" x-data="{ active: 0 }" x-init="setInterval(() => { active = (active + 1) % 3 }, 5000)">
                    <h2 class="text-white text-5xl font-black tracking-tight leading-tight mb-8">
                        Africa's most precise<br>
                        <span class="text-accent">mobility engine.</span>
                    </h2>

                    <!-- Ad Cards -->
                    <div class="space-y-4">
                        <template x-if="active === 0">
                            <div class="glass-panel p-6 rounded-lg animate-fade-up">
                                <p class="text-accent font-black text-xs uppercase tracking-widest mb-2">Ride with Confidence</p>
                                <h4 class="text-white text-xl font-bold mb-2">Move with Precision</h4>
                                <p class="text-white/60 text-sm leading-relaxed">Book a ride in seconds and reach your destination with Ghana's most professional driver network.</p>
                            </div>
                        </template>
                        <template x-if="active === 1">
                            <div class="glass-panel p-6 rounded-lg animate-fade-up">
                                <p class="text-accent font-black text-xs uppercase tracking-widest mb-2">Business Logistics</p>
                                <h4 class="text-white text-xl font-bold mb-2">Scale Your Operations</h4>
                                <p class="text-white/60 text-sm leading-relaxed">From single packages to fleet management, WADEXPRO Logistics is the heartbeat of your supply chain.</p>
                            </div>
                        </template>
                        <template x-if="active === 2">
                            <div class="glass-panel p-6 rounded-lg animate-fade-up">
                                <p class="text-accent font-black text-xs uppercase tracking-widest mb-2">Driver Partnership</p>
                                <h4 class="text-white text-xl font-bold mb-2">Join the Elite Fleet</h4>
                                <p class="text-white/60 text-sm leading-relaxed">Thousands of drivers are earning more every day. Turn your vehicle into a high-performance business asset.</p>
                            </div>
                        </template>
                    </div>

                    <!-- Paginator -->
                    <div class="flex gap-2 pt-4">
                        <div class="h-1 rounded-full transition-all duration-500" :class="active === 0 ? 'w-8 bg-accent' : 'w-4 bg-white/20'"></div>
                        <div class="h-1 rounded-full transition-all duration-500" :class="active === 1 ? 'w-8 bg-accent' : 'w-4 bg-white/20'"></div>
                        <div class="h-1 rounded-full transition-all duration-500" :class="active === 2 ? 'w-8 bg-accent' : 'w-4 bg-white/20'"></div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="text-white/30 text-xs flex gap-6 font-medium">
                    <span>Precision</span>
                    <span>Safety</span>
                    <span>Reliability</span>
                </div>
            </div>
        </div>

        <!-- RIGHT PANEL: LOGIN FORM (40%) -->
        <div class="w-full lg:w-[40%] h-full bg-white flex flex-col p-8 md:p-16 lg:p-12 xl:p-20 overflow-y-auto">
            <!-- Mobile Navigation / Logo -->
            <div class="flex items-center justify-between mb-16 lg:mb-12">
                <a href="{{ route('home', ['country' => $country, 'lang' => $lang]) }}" class="inline-flex items-center gap-2 group transition-colors hover:text-brand-muted">
                    <svg class="w-5 h-5 transform group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    <span class="text-sm font-semibold">Back</span>
                </a>
                <div class="lg:hidden w-8 h-8 bg-accent rounded-lg flex items-center justify-center font-black text-brand text-xs">W</div>
            </div>

            <div class="max-w-[420px] w-full mx-auto my-auto space-y-8">
                <!-- Title -->
                <div class="space-y-4">
                    <h1 class="text-[32px] md:text-[40px] font-black tracking-tight leading-tight">
                        Log in or<br>
                        create account.
                    </h1>
                    <p class="text-brand-muted text-lg font-light">What's your phone number or email?</p>
                </div>

                <!-- Identifier Form -->
                <form @submit.prevent="submit" class="space-y-4">
                    @csrf
                    <!-- Input Group -->
                    <div class="space-y-2">
                        <div class="flex items-stretch bg-surface rounded-lg p-1 input-focus-ring transition-shadow border border-transparent">
                            <!-- Country Selector (GH) -->
                            <div class="flex items-center gap-2 px-3 hover:bg-surface-dark transition-colors rounded-lg cursor-pointer border-r border-gray-200 shrink-0">
                                <span class="text-xl">🇬🇭</span>
                                <svg class="w-4 h-4 text-brand-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </div>

                            <!-- Combined Input -->
                            <input 
                                type="text" 
                                name="identifier" 
                                x-model="identifier"
                                class="flex-1 bg-transparent px-4 py-3.5 placeholder-brand/30 text-lg font-medium outline-none"
                                placeholder="Phone number or email"
                                required
                                autofocus
                            >
                        </div>
                        
                        <!-- Error Message Area -->
                        <div class="min-h-[20px]">
                            <template x-if="error">
                                <p class="text-red-600 text-xs font-semibold animate-shake" x-text="error"></p>
                            </template>
                        </div>
                    </div>

                    <!-- Continue Button -->
                    <button 
                        type="submit" 
                        class="w-full flex justify-center items-center py-4 px-4 bg-brand text-white text-[15px] font-bold rounded-lg hover:bg-brand-light transition-all shadow-lg active:scale-[0.98] disabled:opacity-50"
                        :disabled="loading || !identifier"
                    >
                        <template x-if="!loading">
                            <span>Continue</span>
                        </template>
                        <template x-if="loading">
                             <div class="flex items-center gap-2 text-accent">
                                <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                <span>Verifying...</span>
                             </div>
                        </template>
                    </button>
                </form>

                <!-- Divider -->
                <div class="relative py-2">
                    <div class="absolute inset-0 flex items-center" aria-hidden="true">
                        <div class="w-full border-t border-gray-100"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-4 bg-white text-brand-muted font-medium italic">or</span>
                    </div>
                </div>

                <!-- Social Logins -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <button class="flex items-center justify-center gap-3 bg-surface hover:bg-surface-dark transition-all py-3.5 px-4 rounded-lg font-bold border border-transparent shadow-sm active:scale-[0.98]">
                        <svg class="w-5 h-5 flex-shrink-0" viewBox="0 0 24 24">
                            <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31l3.57 2.77c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                            <path fill="#34A853" d="M12 23c3.11 0 5.72-1.01 7.63-2.74l-3.57-2.77c-1.02.68-2.33 1.09-4.06 1.09-3.12 0-5.76-2.11-6.71-4.94L1.7 16.38A11.49 11.49 0 0 0 12 23z"/>
                        </svg>
                        <span class="text-sm">Google</span>
                    </button>
                    <button class="flex items-center justify-center gap-3 bg-surface hover:bg-surface-dark transition-all py-3.5 px-4 rounded-lg font-bold border border-transparent shadow-sm active:scale-[0.98]">
                        <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M17.073 10.717c-.12 1.49-.66 2.64-1.61 3.42-.9.74-2.07 1.11-3.48 1.11-1.39 0-2.52-.37-3.42-1.11-.9-.74-1.35-1.78-1.35-3.1 0-1.42.48-2.54 1.44-3.36 1-.85 2.27-1.28 3.82-1.28s2.83.43 3.83 1.28c.1.09.2.18.29.28-.01.07-.02.14-.02.21-.01.52.4.94.92.94.55 0 .96-.4.96-.92 0-.25-.09-.48-.24-.65-.12-.13-.26-.26-.4-.39-1.29-1.07-2.99-1.61-5.1-1.61s-3.83.56-5.18 1.68C5.55 8.78 4.7 10.38 4.7 12.5s.85 3.72 2.54 4.86c1.35 1.12 3.08 1.68 5.18 1.68s3.83-.56 5.18-1.68c1.69-1.14 2.54-2.72 2.54-4.86 0-.52-.41-.94-.96-.94-.52 0-.93.42-.93.94 0 .07.01.14.02.21l-.01-.28z"/>
                        </svg>
                        <span class="text-sm">Apple</span>
                    </button>
                </div>

                <!-- Footer Legal -->
                <p class="text-[12px] text-brand-muted leading-relaxed pt-8 font-light">
                    By continuing, you agree to calls, including by autodialer, WhatsApp, or texts from <span class="text-brand font-semibold">WADEXPRO</span> and its affiliates.
                </p>
            </div>

            <!-- Footer Small Secondary -->
            <div class="mt-auto pt-10 text-[10px] text-brand-muted/50 flex flex-wrap gap-4 font-medium uppercase tracking-tighter">
                <a href="#" class="hover:text-brand transition-colors">Accessibility</a>
                <a href="#" class="hover:text-brand transition-colors">Privacy</a>
                <a href="#" class="hover:text-brand transition-colors">Terms</a>
            </div>
        </div>
    </div>

    <script>
        function loginHandler() {
            return {
                identifier: '',
                loading: false,
                error: '',
                submit() {
                    this.loading = true;
                    this.error = '';

                    fetch("{{ route('login.submit', ['country' => $country, 'lang' => $lang]) }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ identifier: this.identifier })
                    })
                    .then(res => {
                        if (!res.ok) return res.json().then(data => { throw data; });
                        return res.json();
                    })
                    .then(data => {
                        if (data.status === 'success') {
                            window.location.href = data.redirect;
                        } else {
                            this.error = data.message || 'Verification failed.';
                        }
                    })
                    .catch(err => {
                        this.error = err.message || 'Identifier not recognized or system error.';
                    })
                    .finally(() => {
                        this.loading = false;
                    });
                }
            }
        }
    </script>
    <style>
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            10%, 30%, 50%, 70%, 90% { transform: translateX(-2px); }
            20%, 40%, 60%, 80% { transform: translateX(2px); }
        }
        .animate-shake { animation: shake 0.4s cubic-bezier(.36,.07,.19,.97) both; }
    </style>
</body>
</html>
