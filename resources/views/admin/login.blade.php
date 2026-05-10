<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Orchestrator Login | WADEXPRO</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
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
    <style>body { font-family: 'Outfit', sans-serif; }</style>
</head>
<body class="bg-surface text-brand min-h-screen flex overflow-hidden">

    <!-- LEFT COMPONENT: 30% Width for Login Entry -->
    <div class="w-full lg:w-[30%] bg-white h-screen shrink-0 flex flex-col justify-center px-10 xl:px-16 shadow-2xl relative z-20">
        
        <div class="mb-10">
            <span class="text-3xl font-black tracking-tight block">WADEX<span class="text-accent">PRO</span></span>
            <span class="text-xs font-bold uppercase tracking-widest text-brand-muted mt-2 block">Orchestrator Access</span>
        </div>

        <form x-data="loginForm()" @submit.prevent="submit" class="space-y-6">
            @csrf
            
            <template x-if="errorMessage">
                <div class="p-4 bg-red-50 border border-red-100 rounded-lg flex items-start gap-3">
                    <svg class="w-5 h-5 text-red-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <p class="text-sm font-medium text-red-800 leading-tight" x-text="errorMessage"></p>
                </div>
            </template>

            <div>
                <label class="block text-xs font-bold text-brand-muted uppercase tracking-wider mb-2">Clearance Email</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    </div>
                    <input type="email" x-model="form.email" required
                           class="w-full bg-surface border-none rounded-lg py-3.5 pl-12 pr-4 text-[15px] font-medium outline-none focus:ring-2 focus:ring-accent transition-shadow">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-brand-muted uppercase tracking-wider mb-2">Secure Passcode</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    </div>
                    <input type="password" x-model="form.password" required
                           class="w-full bg-surface border-none rounded-lg py-3.5 pl-12 pr-4 text-[15px] font-medium outline-none focus:ring-2 focus:ring-accent transition-shadow">
                </div>
            </div>

            <button type="submit" :disabled="isLoading" 
                    class="w-full bg-brand text-white font-bold py-3.5 rounded-lg hover:bg-brand-light transition-colors flex items-center justify-center gap-2 group mt-8">
                <span x-text="isLoading ? 'Authenticating...' : 'Establish Secure Connection'"></span>
                <template x-if="!isLoading">
                    <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </template>
                <template x-if="isLoading">
                    <div class="w-5 h-5 border-2 border-white/30 border-t-white rounded-full animate-spin"></div>
                </template>
            </button>
            
            <p class="text-center text-xs text-brand-muted font-medium pt-8">
                Unauthorised access attempts are logged and reported.
            </p>
        </form>
    </div>

    <!-- RIGHT COMPONENT: 70% Width Atmospheric Display -->
    <div class="hidden lg:flex w-[70%] relative flex-col justify-between items-end p-12 lg:p-20 bg-brand">
        <!-- Base Background Image -->
        <img src="https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?q=80&w=2670&auto=format&fit=crop" 
             class="absolute inset-0 w-full h-full object-cover opacity-60 mix-blend-luminosity" alt="Logistics Background">
        
        <!-- Gradient + Blur Overlay -->
        <div class="absolute inset-0 bg-gradient-to-r from-brand/90 via-brand/60 to-transparent backdrop-blur-[6px]"></div>

        <!-- Overlaid Information (Top Right) -->
        <div class="relative z-10 w-full flex justify-between items-start">
            <div></div> <!-- Spacer for flex -->
            <div class="flex flex-col items-end gap-3">
                <div class="px-4 py-2 bg-white/10 backdrop-blur-md rounded-full border border-white/20 flex items-center gap-3">
                    <div class="w-2.5 h-2.5 bg-green-400 rounded-full animate-pulse shadow-[0_0_8px_rgba(74,222,128,0.6)]"></div>
                    <span class="text-sm font-semibold text-white tracking-wide">Primary Nodes Online</span>
                </div>
                <div class="px-4 py-2 bg-white/10 backdrop-blur-md rounded-full border border-white/20 flex items-center gap-3">
                    <svg class="w-4 h-4 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    <span class="text-sm font-semibold text-white tracking-wide">W-Guard Security Active</span>
                </div>
            </div>
        </div>

        <!-- Overlaid Actions / Main Info (Bottom Right Focus) -->
        <div class="relative z-10 w-full flex justify-between items-end">
            <!-- Informational context matching user request -->
            <div class="max-w-xl">
                <h1 class="text-5xl lg:text-7xl font-black text-white leading-tight tracking-tight mb-6">
                    Central<br><span class="text-accent">Orchestrator</span>
                </h1>
                <p class="text-lg text-white/80 leading-relaxed font-medium">
                    Welcome to the Global Logistics Command Center. <br>
                    You are accessing highly restricted infrastructure. Authentication triggers telemetry monitoring and session lock.
                </p>
            </div>
            
            <!-- Quick Action Buttons -->
            <div class="flex flex-col gap-4 min-w-[200px]">
                <a href="#" class="flex items-center justify-between p-4 bg-white/10 hover:bg-white/20 backdrop-blur-md border border-white/10 rounded-lg transition-all group">
                    <span class="text-sm font-bold text-white">System Logs</span>
                    <svg class="w-5 h-5 text-white/50 group-hover:text-accent transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </a>
                <a href="#" class="flex items-center justify-between p-4 bg-white/10 hover:bg-white/20 backdrop-blur-md border border-white/10 rounded-lg transition-all group">
                    <span class="text-sm font-bold text-white">Documentation</span>
                    <svg class="w-5 h-5 text-white/50 group-hover:text-accent transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                </a>
                <a href="#" class="flex items-center justify-between p-4 bg-white/10 hover:bg-white/20 backdrop-blur-md border border-white/10 rounded-lg transition-all group">
                    <span class="text-sm font-bold text-white">Emergency Lock</span>
                    <svg class="w-5 h-5 text-white/50 group-hover:text-red-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                </a>
            </div>
        </div>
    </div>

    <!-- Alpine.js Application Logic -->
    <script>
        function loginForm() {
            return {
                form: {
                    email: '',
                    password: ''
                },
                errorMessage: '',
                isLoading: false,
                
                init() {
                    // Set up CSRF token for Axios automatically
                    axios.defaults.withCredentials = true;
                    let token = document.head.querySelector('meta[name="csrf-token"]');
                    if (token) {
                        axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
                    }
                },

                async submit() {
                    this.errorMessage = '';
                    this.isLoading = true;
                    try {
                        const path = window.location.pathname; // Gets current /orchestrator/login dynamically based on env
                        const res = await axios.post(path, this.form);
                        
                        // If authentication returns success, redirect
                        if (res.data.status === 'success') {
                            window.location.href = res.data.redirect;
                        }
                    } catch (err) {
                        // Extract server validation or controller error
                        let msg = 'System failure during connection attempt.';
                        if (err.response?.data?.message) {
                            msg = err.response.data.message;
                        } else if (err.response?.data?.errors) {
                            msg = Object.values(err.response.data.errors)[0][0];
                        }
                        this.errorMessage = msg;
                    }
                    this.isLoading = false;
                }
            }
        }
    </script>
</body>
</html>
