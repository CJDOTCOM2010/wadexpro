<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>WADEXPRO | Verify Code</title>
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
        .otp-input:focus { border-color: #0A0A0A; outline: none; box-shadow: 0 0 0 1px #0A0A0A; }
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
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            10%, 30%, 50%, 70%, 90% { transform: translateX(-2px); }
            20%, 40%, 60%, 80% { transform: translateX(2px); }
        }
        .animate-shake { animation: shake 0.4s cubic-bezier(.36,.07,.19,.97) both; }
    </style>
</head>
<body class="h-full bg-white text-brand overflow-hidden" x-data="otpHandler()">

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

        <!-- RIGHT PANEL: VERIFY OTP FORM (40%) -->
        <div class="w-full lg:w-[40%] h-full bg-white flex flex-col p-8 md:p-16 lg:p-12 xl:p-20 overflow-y-auto">
            <!-- Navigation -->
            <div class="flex items-center justify-between mb-16 lg:mb-12">
                <a href="{{ route('login', ['country' => $country, 'lang' => $lang]) }}" class="inline-flex items-center gap-2 group transition-colors hover:text-brand-muted">
                    <svg class="w-5 h-5 transform group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    <span class="text-sm font-semibold">Edit identifier</span>
                </a>
                <div class="lg:hidden w-8 h-8 bg-accent rounded-lg flex items-center justify-center font-black text-brand text-xs">W</div>
            </div>

            <div class="max-w-[420px] w-full mx-auto my-auto space-y-8">
                <!-- Title -->
                <div class="space-y-4">
                    <h1 class="text-[32px] md:text-[40px] font-black tracking-tight leading-tight">
                        Enter the code
                    </h1>
                    <p class="text-brand-muted text-[17px] leading-relaxed font-light">
                        We sent a 6-digit code to <span class="text-brand font-bold">{{ $identifier }}</span>
                    </p>
                </div>

                <!-- OTP Form -->
                <form @submit.prevent="verify" class="space-y-8 w-full">
                    @csrf
                    <!-- Code Inputs -->
                    <div class="flex items-center justify-between gap-3">
                        <template x-for="(i, index) in 6" :key="index">
                            <input 
                                type="text" 
                                maxlength="1"
                                x-model="codes[index]"
                                @input="handleInput($event, index)"
                                @keydown.backspace="handleBackspace($event, index)"
                                class="w-full aspect-square text-center text-3xl font-bold bg-surface rounded-lg border-2 border-transparent transition-all otp-input"
                                :class="error ? 'border-red-500' : ''"
                                autofocus
                            >
                        </template>
                    </div>

                    <!-- Error Message -->
                    <div class="min-h-[20px] flex justify-center">
                        <template x-if="error">
                            <p class="text-red-600 text-[13px] font-semibold text-center animate-shake" x-text="error"></p>
                        </template>
                    </div>

                    <!-- Verify Button -->
                    <button 
                        type="submit" 
                        class="w-full flex justify-center items-center py-4 px-4 bg-brand text-white text-[15px] font-bold rounded-lg hover:bg-brand-light transition-all shadow-lg active:scale-[0.98] disabled:opacity-50"
                        :disabled="loading || codes.join('').length < 6"
                    >
                        <template x-if="!loading">
                            <span>Verify and Log in</span>
                        </template>
                        <template x-if="loading">
                             <div class="flex items-center gap-2 text-accent">
                                <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                <span>Verifying...</span>
                             </div>
                        </template>
                    </button>
                </form>

                <!-- Resend Option -->
                <div class="text-center space-y-4 pt-4 border-t border-gray-100">
                    <p class="text-sm text-brand-muted">
                        I didn't receive a code
                    </p>
                    <button 
                        @click="resend()" 
                        class="text-sm font-bold text-brand hover:underline"
                        :disabled="resendTimeout > 0"
                    >
                        <span x-show="resendTimeout === 0">Resend code via SMS</span>
                        <span x-show="resendTimeout > 0" x-text="'Resend in ' + resendTimeout + 's'"></span>
                    </button>
                </div>
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
        function otpHandler() {
            return {
                codes: ['', '', '', '', '', ''],
                loading: false,
                error: '',
                resendTimeout: 60,
                init() {
                    const timer = setInterval(() => {
                        if (this.resendTimeout > 0) this.resendTimeout--;
                        else clearInterval(timer);
                    }, 1000);
                },
                handleInput(e, index) {
                    const val = e.target.value;
                    if (!/^\d*$/.test(val)) {
                        this.codes[index] = '';
                        return;
                    }
                    if (val && index < 5) {
                        e.target.nextElementSibling.focus();
                    }
                },
                handleBackspace(e, index) {
                    if (!this.codes[index] && index > 0) {
                        e.target.previousElementSibling.focus();
                    }
                },
                verify() {
                    this.loading = true;
                    this.error = '';
                    const code = this.codes.join('');

                    fetch("{{ route('login.verify', ['country' => $country, 'lang' => $lang]) }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ 
                            identifier: "{{ $identifier }}",
                            code: code 
                        })
                    })
                    .then(res => {
                        if (!res.ok) return res.json().then(data => { throw data; });
                        return res.json();
                    })
                    .then(data => {
                        if (data.status === 'success') {
                            window.location.href = data.redirect;
                        } else {
                            this.error = data.message || 'Incorrect code.';
                        }
                    })
                    .catch(err => {
                        this.error = err.message || 'Verification failed.';
                    })
                    .finally(() => {
                        this.loading = false;
                    });
                },
                resend() {
                    // This logic triggers a backend request to re-generate OTP
                    // Placeholder reset for UI demonstration
                    this.resendTimeout = 60;
                    this.init();
                }
            }
        }
    </script>
</body>
</html>
