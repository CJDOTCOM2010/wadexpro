<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>WADEXPRO | Sign up</title>
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
<body class="h-full bg-white text-brand overflow-hidden" x-data="registerHandler()">

    <div class="flex h-full w-full">
        <!-- LEFT PANEL: VISUAL SHOWCASE (60%) -->
        <div class="hidden lg:flex lg:w-[60%] h-full relative overflow-hidden bg-brand">
            <!-- Background Image -->
            <img src="https://images.unsplash.com/photo-1449960232330-797379f82601?auto=format&fit=crop&q=80&w=1974" 
                 class="absolute inset-0 w-full h-full object-cover transform scale-105 hover:scale-100 transition-transform duration-[10s] ease-linear opacity-60" 
                 alt="WADEXPRO Fleet">
            
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

                <!-- Info -->
                <div class="space-y-6 max-w-xl">
                    <h2 class="text-white text-5xl font-black tracking-tight leading-tight">
                        Join the future of<br>
                        <span class="text-accent">African Logistics.</span>
                    </h2>
                    <p class="text-white/60 text-lg leading-relaxed max-w-md">
                        Create an account to start booking rides, tracking packages, and managing your enterprise logistics in one seamless dashboard.
                    </p>
                </div>

                <!-- Footer -->
                <div class="text-white/30 text-xs flex gap-6 font-medium">
                    <span>Precision</span>
                    <span>Safety</span>
                    <span>Reliability</span>
                </div>
            </div>
        </div>

        <!-- RIGHT PANEL: REGISTER FORM (40%) -->
        <div class="w-full lg:w-[40%] h-full bg-white flex flex-col p-8 md:p-16 lg:p-12 xl:p-20 overflow-y-auto">
            <!-- Back Navigation -->
            <div class="flex items-center justify-between mb-12">
                <a href="{{ route('home', ['country' => request()->route('country', 'gh'), 'lang' => request()->route('lang', 'en')]) }}" 
                   class="inline-flex items-center gap-2 group transition-colors hover:text-brand-muted">
                    <svg class="w-5 h-5 transform group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    <span class="text-sm font-semibold">Back to website</span>
                </a>
            </div>

            <div class="max-w-[420px] w-full mx-auto my-auto space-y-8">
                <!-- Title -->
                <div class="space-y-4">
                    <h1 class="text-[32px] md:text-[40px] font-black tracking-tight leading-tight">
                        Create your<br>
                        account.
                    </h1>
                    <p class="text-brand-muted text-lg font-light">Join the WADEXPRO community today.</p>
                </div>

                <!-- Register Form -->
                <form action="{{ route('register.submit', ['country' => request()->route('country', 'gh'), 'lang' => request()->route('lang', 'en')]) }}" 
                      method="POST" 
                      class="space-y-4">
                    @csrf
                    
                    @if ($errors->any())
                        <div class="bg-red-50 p-4 rounded-lg mb-6">
                            <ul class="list-disc list-inside text-sm text-red-600 font-medium">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Name -->
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold uppercase tracking-widest text-brand-muted ml-1">Full Name</label>
                        <div class="bg-surface rounded-lg p-1 input-focus-ring transition-shadow border border-transparent">
                            <input type="text" name="name" value="{{ old('name') }}" required 
                                   class="w-full bg-transparent px-4 py-3 placeholder-brand/30 text-[15px] font-medium outline-none"
                                   placeholder="Enter your full name">
                        </div>
                    </div>

                    <!-- Email -->
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold uppercase tracking-widest text-brand-muted ml-1">Email Address</label>
                        <div class="bg-surface rounded-lg p-1 input-focus-ring transition-shadow border border-transparent">
                            <input type="email" name="email" value="{{ old('email') }}" required 
                                   class="w-full bg-transparent px-4 py-3 placeholder-brand/30 text-[15px] font-medium outline-none"
                                   placeholder="name@company.com">
                        </div>
                    </div>

                    <!-- Password -->
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold uppercase tracking-widest text-brand-muted ml-1">Password</label>
                        <div class="bg-surface rounded-lg p-1 input-focus-ring transition-shadow border border-transparent">
                            <input type="password" name="password" required 
                                   class="w-full bg-transparent px-4 py-3 placeholder-brand/30 text-[15px] font-medium outline-none"
                                   placeholder="Create a strong password">
                        </div>
                    </div>

                    <!-- Password Confirmation -->
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold uppercase tracking-widest text-brand-muted ml-1">Confirm Password</label>
                        <div class="bg-surface rounded-lg p-1 input-focus-ring transition-shadow border border-transparent">
                            <input type="password" name="password_confirmation" required 
                                   class="w-full bg-transparent px-4 py-3 placeholder-brand/30 text-[15px] font-medium outline-none"
                                   placeholder="Repeat your password">
                        </div>
                    </div>

                    <div class="pt-4">
                        <button type="submit" 
                                class="w-full flex justify-center items-center py-4 px-4 bg-brand text-white text-[15px] font-bold rounded-lg hover:bg-brand-light transition-all shadow-lg active:scale-[0.98]">
                            Join WADEXPRO
                        </button>
                    </div>
                </form>

                <div class="pt-6 text-center">
                    <p class="text-[14px] text-brand-muted">
                        Already have an account? 
                        <a href="{{ route('login', ['country' => request()->route('country', 'gh'), 'lang' => request()->route('lang', 'en')]) }}" 
                           class="text-brand font-bold hover:underline decoration-accent decoration-2 underline-offset-4">Log in</a>
                    </p>
                </div>
            </div>

            <!-- Footer Small Secondary -->
            <div class="mt-auto pt-10 text-[10px] text-brand-muted/50 flex flex-wrap gap-4 font-medium uppercase tracking-tighter">
                <a href="#" class="hover:text-brand transition-colors">Privacy Policy</a>
                <a href="#" class="hover:text-brand transition-colors">Terms of Service</a>
            </div>
        </div>
    </div>

    <script>
        function registerHandler() {
            return {
                // Future Alpine logic for validation
            }
        }
    </script>
</body>
</html>
