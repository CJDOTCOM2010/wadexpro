<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WADEXPRO | Tactical Fleet Dispatcher</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Outfit', sans-serif; }
        .carto-dots {
            background-image: radial-gradient(#F8B803 1.5px, transparent 1.5px);
            background-size: 50px 50px;
        }
    </style>
</head>
<body class="bg-[#0A0A1A] text-white overflow-hidden select-none" x-data="{ sidebarOpen: true }">

    <!-- Primary Dispatch Grid -->
    <div class="h-screen w-screen flex relative">
        
        <!-- Sidebar: Active Operations -->
        <aside class="w-96 bg-brand/40 backdrop-blur-3xl border-r border-white/5 flex flex-col transition-all duration-500 z-30" :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">
            <div class="h-20 flex items-center px-8 border-b border-white/5">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-accent rounded-lg flex items-center justify-center font-black text-brand text-xs">D</div>
                    <span class="font-black tracking-widest text-sm uppercase">Tactical Dispatch</span>
                </div>
            </div>

            <div class="flex-1 overflow-y-auto p-6 space-y-6">
                <!-- Fleet Stats -->
                <div class="grid grid-cols-2 gap-4">
                    <div class="p-5 bg-white/5 rounded-2xl border border-white/10">
                        <p class="text-[10px] font-black text-white/30 uppercase mb-1">Fleet Load</p>
                        <p class="text-2xl font-black text-accent">84%</p>
                    </div>
                    <div class="p-5 bg-white/5 rounded-2xl border border-white/10">
                        <p class="text-[10px] font-black text-white/30 uppercase mb-1">Active Ridrs</p>
                        <p class="text-2xl font-black text-white">1,240</p>
                    </div>
                </div>

                <!-- Priority Orders Queue -->
                <div class="space-y-4">
                    <p class="text-[10px] font-black text-accent uppercase tracking-widest">Priority Interceptions</p>
                    
                    <div class="bg-white/5 p-5 rounded-2xl border border-white/5 hover:border-accent/30 transition cursor-pointer group">
                        <div class="flex items-center justify-between mb-3 text-[10px] font-black uppercase tracking-widest text-white/40">
                            <span>#ORD-9922</span>
                            <span class="text-red-500">Urgent</span>
                        </div>
                        <p class="text-sm font-bold text-white mb-2">Pharmaceutical Cluster C-4</p>
                        <div class="flex items-center gap-2">
                            <div class="w-5 h-5 bg-green-500 rounded-full animate-pulse shadow-[0_0_8px_rgba(34,197,94,0.5)]"></div>
                            <p class="text-[10px] font-bold text-white/60">Node DR-122 assigned</p>
                        </div>
                    </div>

                    <div class="bg-white/5 p-5 rounded-2xl border border-white/5 hover:border-accent/30 transition cursor-pointer group">
                        <div class="flex items-center justify-between mb-3 text-[10px] font-black uppercase tracking-widest text-white/40">
                            <span>#ORD-9923</span>
                            <span class="text-accent">High Value</span>
                        </div>
                        <p class="text-sm font-bold text-white mb-2">Enterprise Hardware Sync</p>
                        <div class="flex items-center gap-2 text-[10px] font-bold text-white/60 italic">
                            Awaiting Node Response...
                        </div>
                    </div>
                </div>
            </div>

            <div class="p-6 border-t border-white/5 bg-brand/20">
                <a href="{{ route('orchestrator.dashboard') }}" class="w-full py-4 flex items-center justify-center gap-3 text-[10px] font-black uppercase text-white/40 hover:text-white transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Close Secure Channel
                </a>
            </div>
        </aside>

        <!-- Main Map Area -->
        <main class="flex-1 relative overflow-hidden bg-[#0A0A1A]">
            
            <!-- CartoDB Styled Placeholder Map -->
            <div class="absolute inset-0 carto-dots opacity-20"></div>
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[120%] h-[120%] opacity-10">
                <svg viewBox="0 0 1000 1000" class="w-full h-full fill-none stroke-accent/40 stroke-[0.5]">
                    <circle cx="500" cy="500" r="100" />
                    <circle cx="500" cy="500" r="250" />
                    <circle cx="500" cy="500" r="400" />
                    <line x1="0" y1="500" x2="1000" y2="500" />
                    <line x1="500" y1="0" x2="500" y2="1000" />
                </svg>
            </div>

            <!-- Animated Fleet Nodes -->
            <div class="absolute top-1/4 left-1/3">
                <div class="relative group">
                    <div class="absolute inset-0 bg-accent rounded-full blur-xl opacity-0 group-hover:opacity-40 transition-opacity"></div>
                    <div class="relative w-3 h-3 bg-accent rounded-full shadow-[0_0_15px_#F8B803] flex items-center justify-center cursor-pointer">
                        <div class="absolute -top-10 left-1/2 -translate-x-1/2 whitespace-nowrap bg-brand/90 backdrop-blur-md px-3 py-1 rounded-lg border border-white/10 opacity-0 group-hover:opacity-100 transition-opacity text-[10px] font-bold uppercase">
                            Node: DR-421 | V: 42km/h
                        </div>
                    </div>
                </div>
            </div>

            <div class="absolute bottom-1/3 right-1/4">
                <div class="relative group">
                    <div class="absolute inset-0 bg-green-500 rounded-full blur-xl opacity-0 group-hover:opacity-40 transition-opacity"></div>
                    <div class="relative w-3 h-3 bg-green-500 rounded-full shadow-[0_0_15px_#22C55E] flex items-center justify-center cursor-pointer">
                        <div class="absolute -top-10 left-1/2 -translate-x-1/2 whitespace-nowrap bg-brand/90 backdrop-blur-md px-3 py-1 rounded-lg border border-white/10 opacity-0 group-hover:opacity-100 transition-opacity text-[10px] font-bold uppercase">
                            Node: TR-902 | Idle
                        </div>
                    </div>
                </div>
            </div>

            <!-- Top HUD Overlay -->
            <div class="absolute top-8 left-1/2 -translate-x-1/2 flex items-center gap-4 z-20">
                <div class="bg-brand/80 backdrop-blur-xl px-8 py-4 rounded-full border border-white/10 flex items-center gap-12 shadow-2xl">
                    <div class="text-center">
                        <p class="text-[9px] font-black text-white/30 uppercase tracking-widest mb-1">Global Coverage</p>
                        <p class="text-xl font-black text-accent">98.2%</p>
                    </div>
                    <div class="w-[1px] h-8 bg-white/10"></div>
                    <div class="text-center">
                        <p class="text-[9px] font-black text-white/30 uppercase tracking-widest mb-1">Signal Latency</p>
                        <p class="text-xl font-black text-white">4ms <span class="text-[10px] text-green-500">OPTIMAL</span></p>
                    </div>
                    <div class="w-[1px] h-8 bg-white/10"></div>
                    <div class="text-center">
                        <p class="text-[9px] font-black text-white/30 uppercase tracking-widest mb-1">Network Time</p>
                        <p class="text-xl font-black text-white font-mono">{{ now()->format('H:i:s') }} <span class="text-[10px]">UTC</span></p>
                    </div>
                </div>
            </div>

            <!-- Bottom Tracking HUD -->
            <div class="absolute bottom-12 left-12 right-12 flex justify-between items-end z-20 pointer-events-none">
                <div class="flex flex-col gap-3 pointer-events-auto">
                    <div class="bg-brand/60 backdrop-blur-lg p-6 rounded-3xl border border-white/10 shadow-2xl max-w-sm">
                        <div class="flex items-center gap-3 mb-4">
                            <span class="w-2 h-2 bg-accent rounded-full animate-pulse"></span>
                            <p class="text-xs font-black uppercase tracking-widest">Active Interception Hub</p>
                        </div>
                        <p class="text-sm font-medium text-white/70 leading-relaxed italic">"Dynamic node reassignment in progress for Accra Metropolitan Area Sector G-7. Predicting 4.2ms ETA reduction."</p>
                    </div>
                    <div class="flex gap-2">
                        <button class="px-6 py-3 bg-white/5 border border-white/10 rounded-xl font-black text-[9px] uppercase tracking-widest hover:bg-white/10 transition">Recalibrate Sensors</button>
                        <button class="px-6 py-3 bg-accent text-brand rounded-xl font-black text-[9px] uppercase tracking-widest shadow-xl shadow-accent/20 hover:scale-105 transition">Toggle Fleet Viz</button>
                    </div>
                </div>

                <div class="bg-brand/60 backdrop-blur-lg px-8 py-6 rounded-3xl border border-white/10 shadow-2xl flex flex-col items-end gap-2 pointer-events-auto">
                    <p class="text-[10px] font-black text-white/30 uppercase tracking-[0.3em]">Operational Node</p>
                    <p class="text-2xl font-black text-white tracking-widest">WADEX-PRO-X1</p>
                    <div class="flex items-center gap-2 mt-2">
                        <div class="flex gap-1">
                            <div class="w-1 h-3 bg-accent"></div>
                            <div class="w-1 h-3 bg-accent"></div>
                            <div class="w-1 h-3 bg-accent"></div>
                            <div class="w-1 h-3 bg-accent/20"></div>
                        </div>
                        <span class="text-[10px] font-bold text-accent">SECURE UPLINK 14.2</span>
                    </div>
                </div>
            </div>
        </main>

        <!-- Floating Sidebar Toggle -->
        <button @click="sidebarOpen = !sidebarOpen" class="absolute left-8 top-8 z-40 p-2 bg-white/5 border border-white/10 rounded-lg hover:bg-white hover:text-brand transition duration-300">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h8m-8 6h16"/></svg>
        </button>

    </div>

</body>
</html>
