@extends('admin.layout')
@section('title', 'Orchestrator Control Center')

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
@endpush

@section('content')

<!-- Live Statistics Panel -->
<div x-data="dashboardAnalytics()" x-init="init()">
    
    <!-- Welcome Context -->
    <div class="mb-12 flex justify-between items-end">
        <div>
            <h2 class="text-4xl font-black text-brand tracking-tight">System Operational Summary</h2>
            <p class="text-brand-muted font-medium mt-1">Real-time status of the WADEXPRO global logistics grid.</p>
        </div>
        <div class="flex flex-col items-end gap-2">
            <div class="flex items-center gap-3 bg-surface px-4 py-2 rounded-lg border border-gray-100">
                <div class="w-1.5 h-1.5 bg-accent rounded-full animate-pulse"></div>
                <span class="text-[10px] font-black text-brand-muted uppercase tracking-widest">Live Sync Alpha</span>
            </div>
            <p class="text-[10px] font-bold text-gray-300 uppercase tracking-widest" x-text="`Last Updated: ${lastUpdated}`"></p>
        </div>
    </div>

    <!-- Primary Operational KPIs -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-12">
        <!-- Active Fleet (Drivers) -->
        <div class="bg-white p-8 rounded-lg border border-gray-100 shadow-sm hover:shadow-2xl hover:-translate-y-1 transition-all group relative overflow-hidden">
            <div class="flex items-center justify-between mb-6">
                <div class="w-14 h-14 bg-accent/10 rounded-lg flex items-center justify-center text-accent group-hover:bg-accent group-hover:text-brand transition-colors">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/></svg>
                </div>
                <div class="text-right">
                    <p class="text-[10px] font-black text-green-500 bg-green-50 px-2 py-1 rounded-lg uppercase tracking-wider inline-block" x-text="`${stats.drivers.online} Online`"></p>
                </div>
            </div>
            <p class="text-xs font-bold text-brand-muted uppercase tracking-widest">Fleet Orchestration</p>
            <div class="flex items-baseline gap-2 mt-2">
                <p class="text-4xl font-black tracking-tighter" x-text="stats.drivers.total"></p>
                <p class="text-sm font-bold text-brand-muted">Total Drivers</p>
            </div>
            
            <div class="mt-6 flex items-center gap-4 border-t border-gray-50 pt-4">
                <div class="flex-1">
                    <p class="text-[10px] font-black text-brand-muted uppercase tracking-widest">Active</p>
                    <p class="text-lg font-black text-brand" x-text="stats.drivers.active"></p>
                </div>
                <div class="w-px h-8 bg-gray-100"></div>
                <div class="flex-1">
                    <p class="text-[10px] font-black text-brand-muted uppercase tracking-widest">Inactive</p>
                    <p class="text-lg font-black text-gray-300" x-text="stats.drivers.inactive"></p>
                </div>
            </div>
        </div>

        <!-- Global Search (Customers) -->
        <div class="bg-white p-8 rounded-lg border border-gray-100 shadow-sm hover:shadow-2xl hover:-translate-y-1 transition-all group overflow-hidden">
            <div class="flex items-center justify-between mb-6">
                <div class="w-14 h-14 bg-brand/5 rounded-lg flex items-center justify-center text-brand group-hover:bg-brand group-hover:text-white transition-colors">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </div>
                <div class="text-right">
                    <p class="text-[10px] font-black text-blue-500 bg-blue-50 px-2 py-1 rounded-lg uppercase tracking-wider inline-block" x-text="`Queue: ${stats.active_search_count}`"></p>
                </div>
            </div>
            <p class="text-xs font-bold text-brand-muted uppercase tracking-widest">Customer Base</p>
            <div class="flex items-baseline gap-2 mt-2">
                <p class="text-4xl font-black tracking-tighter" x-text="stats.customers.total"></p>
                <p class="text-sm font-bold text-brand-muted">Total Users</p>
            </div>

            <div class="mt-6 flex items-center gap-4 border-t border-gray-50 pt-4">
                <div class="flex-1">
                    <p class="text-[10px] font-black text-brand-muted uppercase tracking-widest">Active (30d)</p>
                    <p class="text-lg font-black text-brand" x-text="stats.customers.active"></p>
                </div>
                <div class="w-px h-8 bg-gray-100"></div>
                <div class="flex-1">
                    <p class="text-[10px] font-black text-brand-muted uppercase tracking-widest">Dormant</p>
                    <p class="text-lg font-black text-gray-300" x-text="stats.customers.inactive"></p>
                </div>
            </div>
        </div>

        <!-- Net Revenue -->
        <div class="bg-white p-8 rounded-lg border border-gray-100 shadow-sm hover:shadow-2xl hover:-translate-y-1 transition-all group overflow-hidden">
            <div class="flex items-center justify-between mb-6">
                <div class="w-14 h-14 bg-green-50 rounded-lg flex items-center justify-center text-green-600 group-hover:bg-green-600 group-hover:text-white transition-colors">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div class="text-right">
                    <p class="text-[10px] font-black text-green-600 bg-green-50 px-2 py-1 rounded-lg uppercase tracking-wider inline-block">M-Share (20%)</p>
                </div>
            </div>
            <p class="text-xs font-bold text-brand-muted uppercase tracking-widest">Monthly Net Revenue</p>
            <p class="text-4xl font-black mt-2 tracking-tighter" x-text="formatCurrency(stats.monthly_net_commission)"></p>
            
            <div class="mt-6 flex items-center justify-between border-t border-gray-50 pt-4">
                <div>
                    <p class="text-[10px] font-black text-brand-muted uppercase tracking-widest">Gross Yield</p>
                    <p class="text-sm font-bold text-brand" x-text="formatCurrency(stats.monthly_gross_revenue)"></p>
                </div>
                <div class="text-right">
                    <p class="text-[10px] font-black text-brand-muted uppercase tracking-widest">Growth</p>
                    <p class="text-sm font-bold text-green-500">+12.4%</p>
                </div>
            </div>
        </div>

        <!-- System Safety -->
        <div class="bg-white p-8 rounded-lg border border-gray-100 shadow-sm hover:shadow-2xl hover:-translate-y-1 transition-all group overflow-hidden">
            <div class="flex items-center justify-between mb-6">
                <div class="w-14 h-14 bg-red-50 rounded-lg flex items-center justify-center text-red-600 group-hover:bg-red-600 group-hover:text-white transition-colors">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <div class="text-right">
                    <span class="text-[10px] font-black px-2 py-1 rounded-lg uppercase tracking-wider" :class="stats.pending_sos > 0 ? 'bg-red-500 text-white animate-pulse' : 'bg-gray-50 text-gray-400'">Secure</span>
                </div>
            </div>
            <p class="text-xs font-bold text-brand-muted uppercase tracking-widest">Security Disruptions</p>
            <p class="text-4xl font-black mt-2 tracking-tighter" x-text="stats.pending_sos"></p>
            
            <div class="mt-6 border-t border-gray-50 pt-4 flex items-center justify-between">
                <p class="text-[10px] font-black text-brand-muted uppercase tracking-widest">Active Alarms</p>
                <button class="text-[10px] font-black text-accent uppercase tracking-widest hover:underline">View Watchlist</button>
            </div>
        </div>
    </div>

    <!-- Analytical Visualizations -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-12">
        <!-- Revenue & Ride Velocity Chart -->
        <div class="lg:col-span-2 bg-white rounded-lg p-8 border border-gray-100 shadow-sm relative overflow-hidden">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h3 class="text-xl font-black text-brand tracking-tight">Growth Velocity</h3>
                    <p class="text-xs text-brand-muted font-medium mt-1">Net commission and ride volume trends (Last 30 Days).</p>
                </div>
                <div class="flex gap-2">
                    <button class="px-3 py-1.5 bg-surface text-[10px] font-black uppercase rounded-lg border border-gray-100">Export PDF</button>
                </div>
            </div>
            <div id="revenueChart" class="w-full h-[350px]"></div>
        </div>

        <!-- User & Fleet Distribution -->
        <div class="bg-white rounded-lg p-8 border border-gray-100 shadow-sm flex flex-col relative overflow-hidden">
            <h3 class="text-xl font-black text-brand tracking-tight mb-2">User Ecology</h3>
            <p class="text-xs text-brand-muted font-medium mb-8">Structural breakdown of platform actors.</p>
            
            <div id="distributionChart" class="flex-1 min-h-[300px]"></div>

            <div class="mt-6 space-y-3">
                <div class="flex items-center justify-between p-4 bg-surface rounded-lg group hover:bg-brand transition-colors">
                    <div class="flex items-center gap-3">
                        <div class="w-2 h-2 bg-accent rounded-full"></div>
                        <span class="text-xs font-bold text-brand group-hover:text-white transition-colors">Verified Drivers</span>
                    </div>
                    <span class="text-xs font-black text-accent" x-text="stats.drivers.total"></span>
                </div>
                <div class="flex items-center justify-between p-4 bg-surface rounded-lg group hover:bg-brand transition-colors">
                    <div class="flex items-center gap-3">
                        <div class="w-2 h-2 bg-brand rounded-full"></div>
                        <span class="text-xs font-bold text-brand group-hover:text-white transition-colors">Global Customers</span>
                    </div>
                    <span class="text-xs font-black text-accent" x-text="stats.customers.total"></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Tactical Fleet Density Map -->
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8 mb-12">
        <div class="lg:col-span-3 bg-white rounded-lg p-2 border border-gray-100 shadow-2xl relative overflow-hidden h-[600px]">
            <div id="densityMap" class="w-full h-full rounded-lg bg-[#0A0A1A]"></div>
            
            <!-- Map HUD Overlay -->
            <div class="absolute top-8 left-8 z-[1000] flex flex-col gap-4">
                <div class="bg-brand/80 backdrop-blur-xl p-5 rounded-lg border border-white/10 shadow-2xl">
                    <p class="text-[9px] font-black text-accent uppercase tracking-widest mb-1">Geospatial Telemetry</p>
                    <div class="flex items-center gap-3">
                        <span class="w-2.5 h-2.5 bg-green-500 rounded-full" :class="loadingMap && 'animate-pulse'"></span>
                        <span class="text-sm font-black text-white">Live Node Grid</span>
                    </div>
                </div>
                
                <div class="bg-white/95 backdrop-blur-md p-4 rounded-lg border border-gray-100 shadow-xl w-48">
                    <p class="text-[9px] font-black text-brand-muted uppercase tracking-widest mb-3">Operational Hotspots</p>
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] font-bold text-brand">Greater Accra</span>
                            <span class="text-[10px] font-black text-accent">98%</span>
                        </div>
                        <div class="w-full h-1 bg-gray-100 rounded-lg overflow-hidden">
                            <div class="h-full bg-accent" style="width: 98%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Map Interactions HUD -->
            <div class="absolute bottom-8 right-8 z-[1000]">
                <div class="flex gap-2">
                    <button class="w-12 h-12 bg-white/90 backdrop-blur rounded-lg border border-gray-200 flex items-center justify-center text-brand hover:bg-brand hover:text-white transition shadow-xl">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                    </button>
                    <button class="w-12 h-12 bg-white/90 backdrop-blur rounded-lg border border-gray-200 flex items-center justify-center text-brand hover:bg-brand hover:text-white transition shadow-xl">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Command Node Alerts -->
        <div class="flex flex-col gap-6">
            <div class="bg-brand rounded-lg p-8 text-white relative overflow-hidden group h-[280px]">
                <div class="absolute top-0 right-0 p-8 opacity-10 group-hover:scale-110 transition-transform duration-700">
                    <svg class="w-32 h-32" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                </div>
                <div class="relative z-10 h-full flex flex-col justify-between">
                    <div>
                        <h3 class="text-2xl font-black mb-2">Command Center</h3>
                        <p class="text-white/60 text-xs leading-relaxed">Intercede in platform-wide parameters or launch dispatcher.</p>
                    </div>
                    <div class="space-y-2">
                        <a href="{{ route('orchestrator.dispatcher') }}" class="w-full py-3.5 bg-accent text-brand font-black rounded-lg transition flex items-center justify-center gap-2 shadow-lg shadow-accent/20 hover:scale-[1.02] active:scale-95 text-xs">
                            Launch Dispatcher
                        </a>
                        <button class="w-full py-3.5 bg-white/10 hover:bg-white text-white hover:text-brand font-bold rounded-lg transition flex items-center justify-center gap-2 text-xs">
                            System Overrides
                        </button>
                    </div>
                </div>
            </div>

            <div class="flex-1 bg-white rounded-lg p-8 border border-gray-100 shadow-sm flex flex-col overflow-hidden">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-black text-brand tracking-tight">Active Coverage</h3>
                    <div class="w-2 h-2 bg-accent rounded-full animate-pulse"></div>
                </div>
                <div class="space-y-4 flex-1 overflow-y-auto">
                    <div class="p-4 bg-surface rounded-lg flex items-center justify-between group hover:bg-brand transition-colors">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-lg bg-white flex items-center justify-center text-[10px] font-black border border-gray-50">GH</div>
                            <span class="text-xs font-bold text-brand group-hover:text-white transition-colors">Greater Accra</span>
                        </div>
                        <span class="text-[10px] font-black text-accent" x-text="`${stats.drivers.online} Nodes`"></span>
                    </div>
                    <div class="p-4 bg-surface rounded-lg flex items-center justify-between group hover:bg-brand transition-colors opacity-40">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-lg bg-white flex items-center justify-center text-[10px] font-black border border-gray-50">NG</div>
                            <span class="text-xs font-bold text-brand group-hover:text-white transition-colors">Lagos Central</span>
                        </div>
                        <span class="text-[10px] font-black text-accent uppercase tracking-widest">Locked</span>
                    </div>
                </div>
                <button class="mt-6 w-full py-4 bg-surface hover:bg-brand hover:text-white text-brand font-black text-[10px] uppercase rounded-lg transition">Geographic Report</button>
            </div>
        </div>
    </div>
</div>

<script>
    function dashboardAnalytics() {
        return {
            stats: {
                total_rides: 0,
                today_rides: 0,
                monthly_gross_revenue: 0,
                monthly_net_commission: 0,
                active_search_count: 0,
                pending_sos: 0,
                drivers: { total: 0, active: 0, inactive: 0, online: 0 },
                customers: { total: 0, active: 0, inactive: 0 }
            },
            lastUpdated: '-',
            map: null,
            markersLayer: null,
            loadingMap: false,
            revenueChart: null,
            distributionChart: null,

            init() {
                this.initMap();
                this.fetchStats(true); // Initial fetch with chart creation
                this.fetchTelemetry();

                // Periodic Updates
                setInterval(() => this.fetchStats(false), 30000); 
                setInterval(() => this.fetchTelemetry(), 10000); 
            },

            initMap() {
                this.map = L.map('densityMap', {
                    zoomControl: false,
                    attributionControl: false
                }).setView([5.6037, -0.1870], 13);

                L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
                    maxZoom: 19
                }).addTo(this.map);

                this.markersLayer = L.layerGroup().addTo(this.map);
            },

            fetchStats(isInitial) {
                axios.get('/api/v1/logistics/admin/analytics/overview')
                    .then(res => {
                        this.stats = res.data.data;
                        this.lastUpdated = new Date().toLocaleTimeString();
                        
                        if (isInitial) {
                            this.initRevenueChart();
                            this.initDistributionChart();
                        } else {
                            this.updateCharts();
                        }
                    })
                    .catch(err => console.error('Stats Load Failed:', err));
            },

            initRevenueChart() {
                const options = {
                    series: [{
                        name: 'Commission (GHS)',
                        data: [120, 150, 180, 220, 190, 250, 310, 280, 350, 420, 380, 450]
                    }],
                    chart: {
                        type: 'area',
                        height: 350,
                        toolbar: { show: false },
                        fontFamily: 'Outfit, sans-serif'
                    },
                    colors: ['#F8B803'],
                    fill: {
                        type: 'gradient',
                        gradient: { shadeIntensity: 1, opacityFrom: 0.7, opacityTo: 0.1, stops: [0, 90, 100] }
                    },
                    dataLabels: { enabled: false },
                    stroke: { curve: 'smooth', width: 3 },
                    xaxis: {
                        categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                        axisBorder: { show: false },
                        axisTicks: { show: false }
                    },
                    yaxis: { labels: { formatter: (val) => `GH₵${val}` } },
                    grid: { borderColor: '#F6F6F6' }
                };

                this.revenueChart = new ApexCharts(document.querySelector("#revenueChart"), options);
                this.revenueChart.render();
            },

            initDistributionChart() {
                const options = {
                    series: [this.stats.drivers.total, this.stats.customers.total],
                    chart: {
                        type: 'donut',
                        height: 300,
                        fontFamily: 'Outfit, sans-serif'
                    },
                    labels: ['Drivers', 'Customers'],
                    colors: ['#F8B803', '#0A0A0A'],
                    plotOptions: {
                        pie: {
                            donut: {
                                size: '75%',
                                labels: {
                                    show: true,
                                    total: {
                                        show: true,
                                        label: 'Ecology',
                                        formatter: () => this.stats.drivers.total + this.stats.customers.total
                                    }
                                }
                            }
                        }
                    },
                    legend: { show: false },
                    dataLabels: { enabled: false }
                };

                this.distributionChart = new ApexCharts(document.querySelector("#distributionChart"), options);
                this.distributionChart.render();
            },

            updateCharts() {
                if (this.distributionChart) {
                    this.distributionChart.updateSeries([this.stats.drivers.total, this.stats.customers.total]);
                }
            },

            fetchTelemetry() {
                this.loadingMap = true;
                axios.get('/api/v1/logistics/admin/live-map/drivers')
                    .then(res => {
                        this.renderMarkers(res.data.data);
                    })
                    .finally(() => this.loadingMap = false);
            },

            renderMarkers(drivers) {
                this.markersLayer.clearLayers();
                drivers.forEach(driver => {
                    const iconColor = driver.status === 'online' ? '#F8B803' : '#22C55E';
                    L.circleMarker([driver.lat, driver.lng], {
                        radius: 8,
                        fillColor: iconColor,
                        color: '#fff',
                        weight: 3,
                        opacity: 1,
                        fillOpacity: 1
                    }).addTo(this.markersLayer)
                      .bindPopup(`<div class="p-2 font-outfit"><b>${driver.name}</b><br><span class="text-xs">ID: ${driver.id.substring(0,8)}</span></div>`);
                });
            },

            formatCurrency(val) {
                return new Intl.NumberFormat('en-GH', { style: 'currency', currency: 'GHS' }).format(val);
            }
        }
    }
</script>

@endsection
