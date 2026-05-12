@extends('admin.layout')
@section('title', 'Analytics & Reports')
@section('content')

<div class="mb-8 flex items-center justify-between">
    <div>
        <h2 class="text-2xl font-black text-brand tracking-tight">Business Intelligence</h2>
        <p class="text-brand-muted font-medium mt-1">Real-time metrics, revenue analysis, and growth reports.</p>
    </div>
    <div class="flex gap-4 items-center">
        <select class="bg-white border border-gray-100 rounded-lg px-4 py-2 text-sm font-bold text-brand outline-none focus:ring-2 focus:ring-brand/20 cursor-pointer shadow-sm">
            <option>Last 7 Days</option>
            <option selected>Last 30 Days</option>
            <option>This Quarter</option>
            <option>Year to Date</option>
        </select>
        <button class="px-6 py-2.5 bg-brand text-white font-bold rounded-lg hover:bg-brand-light transition flex items-center gap-2 shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
            Export CSV
        </button>
    </div>
</div>

<!-- Key Performance Indicators -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white p-6 rounded-lg border border-gray-100 shadow-sm relative overflow-hidden group">
        <div class="absolute right-0 top-0 w-16 h-full bg-brand/5 group-hover:bg-brand/10 transition flex items-center justify-center">
            <svg class="w-8 h-8 text-brand/20 group-hover:text-brand/40 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <p class="text-[10px] font-black text-brand-muted uppercase tracking-widest relative z-10">Total Revenue</p>
        <p class="text-3xl font-black text-brand tracking-tight mt-2 relative z-10">₵ 142,500</p>
        <p class="text-xs font-bold text-green-500 mt-2 flex items-center gap-1 relative z-10"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg> +12.5% vs last period</p>
    </div>
    
    <div class="bg-white p-6 rounded-lg border border-gray-100 shadow-sm relative overflow-hidden group">
        <div class="absolute right-0 top-0 w-16 h-full bg-brand/5 group-hover:bg-brand/10 transition flex items-center justify-center">
            <svg class="w-8 h-8 text-brand/20 group-hover:text-brand/40 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
        </div>
        <p class="text-[10px] font-black text-brand-muted uppercase tracking-widest relative z-10">Total Rides</p>
        <p class="text-3xl font-black text-brand tracking-tight mt-2 relative z-10">18,240</p>
        <p class="text-xs font-bold text-green-500 mt-2 flex items-center gap-1 relative z-10"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg> +8.2% vs last period</p>
    </div>

    <div class="bg-white p-6 rounded-lg border border-gray-100 shadow-sm relative overflow-hidden group">
        <div class="absolute right-0 top-0 w-16 h-full bg-brand/5 group-hover:bg-brand/10 transition flex items-center justify-center">
            <svg class="w-8 h-8 text-brand/20 group-hover:text-brand/40 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
        </div>
        <p class="text-[10px] font-black text-brand-muted uppercase tracking-widest relative z-10">New Customers</p>
        <p class="text-3xl font-black text-brand tracking-tight mt-2 relative z-10">1,402</p>
        <p class="text-xs font-bold text-red-500 mt-2 flex items-center gap-1 relative z-10"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0v-8m0 8l-8-8-4 4-6-6"/></svg> -2.1% vs last period</p>
    </div>

    <div class="bg-white p-6 rounded-lg border border-gray-100 shadow-sm relative overflow-hidden group">
        <div class="absolute right-0 top-0 w-16 h-full bg-brand/5 group-hover:bg-brand/10 transition flex items-center justify-center">
            <svg class="w-8 h-8 text-brand/20 group-hover:text-brand/40 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"/></svg>
        </div>
        <p class="text-[10px] font-black text-brand-muted uppercase tracking-widest relative z-10">Cancellation Rate</p>
        <p class="text-3xl font-black text-brand tracking-tight mt-2 relative z-10">4.8%</p>
        <p class="text-xs font-bold text-green-500 mt-2 flex items-center gap-1 relative z-10"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0v-8m0 8l-8-8-4 4-6-6"/></svg> Improved by 1.2%</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    
    <!-- Main Chart -->
    <div class="lg:col-span-2 bg-white rounded-lg border border-gray-100 shadow-sm p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-sm font-black text-brand uppercase tracking-widest">Revenue vs Completed Rides</h3>
            <div class="flex gap-2">
                <span class="flex items-center gap-1 text-[10px] font-bold text-brand-muted uppercase"><div class="w-2 h-2 rounded-full bg-brand"></div> Revenue</span>
                <span class="flex items-center gap-1 text-[10px] font-bold text-brand-muted uppercase"><div class="w-2 h-2 rounded-full bg-accent"></div> Rides</span>
            </div>
        </div>
        <!-- Mock Chart Container -->
        <div class="h-72 flex items-end justify-between gap-2 px-2 pb-2 border-b border-l border-gray-100 relative pt-10">
            <!-- Grid lines -->
            <div class="absolute top-0 left-0 w-full border-t border-gray-50"></div>
            <div class="absolute top-1/3 left-0 w-full border-t border-gray-50"></div>
            <div class="absolute top-2/3 left-0 w-full border-t border-gray-50"></div>
            
            <!-- Bars -->
            <div class="w-full flex flex-col justify-end items-center gap-1 group"><div class="w-full bg-brand/80 group-hover:bg-brand transition-colors rounded-t" style="height: 40%"></div><div class="text-[9px] text-brand-muted">Mon</div></div>
            <div class="w-full flex flex-col justify-end items-center gap-1 group"><div class="w-full bg-brand/80 group-hover:bg-brand transition-colors rounded-t" style="height: 55%"></div><div class="text-[9px] text-brand-muted">Tue</div></div>
            <div class="w-full flex flex-col justify-end items-center gap-1 group"><div class="w-full bg-brand/80 group-hover:bg-brand transition-colors rounded-t" style="height: 48%"></div><div class="text-[9px] text-brand-muted">Wed</div></div>
            <div class="w-full flex flex-col justify-end items-center gap-1 group"><div class="w-full bg-brand/80 group-hover:bg-brand transition-colors rounded-t" style="height: 70%"></div><div class="text-[9px] text-brand-muted">Thu</div></div>
            <div class="w-full flex flex-col justify-end items-center gap-1 group"><div class="w-full bg-brand/80 group-hover:bg-brand transition-colors rounded-t" style="height: 85%"></div><div class="text-[9px] text-brand-muted">Fri</div></div>
            <div class="w-full flex flex-col justify-end items-center gap-1 group"><div class="w-full bg-accent/80 group-hover:bg-accent transition-colors rounded-t relative" style="height: 100%"><div class="absolute -top-8 left-1/2 -translate-x-1/2 bg-gray-900 text-white text-[10px] font-bold py-1 px-2 rounded opacity-0 group-hover:opacity-100 transition whitespace-nowrap">Peak: ₵3,400</div></div><div class="text-[9px] text-brand font-bold">Sat</div></div>
            <div class="w-full flex flex-col justify-end items-center gap-1 group"><div class="w-full bg-brand/80 group-hover:bg-brand transition-colors rounded-t" style="height: 60%"></div><div class="text-[9px] text-brand-muted">Sun</div></div>
        </div>
    </div>

    <!-- Right Sidebar Stats -->
    <div class="space-y-8">
        
        <!-- Top Regions -->
        <div class="bg-white rounded-lg border border-gray-100 shadow-sm p-6">
            <h3 class="text-[10px] font-black text-brand-muted uppercase tracking-widest mb-6">Top Performing Regions</h3>
            <div class="space-y-4">
                <div>
                    <div class="flex justify-between text-xs font-bold text-brand mb-1">
                        <span>East Legon</span>
                        <span>42%</span>
                    </div>
                    <div class="w-full bg-surface rounded-full h-2"><div class="bg-brand h-2 rounded-full" style="width: 42%"></div></div>
                </div>
                <div>
                    <div class="flex justify-between text-xs font-bold text-brand mb-1">
                        <span>Cantonments</span>
                        <span>28%</span>
                    </div>
                    <div class="w-full bg-surface rounded-full h-2"><div class="bg-brand/80 h-2 rounded-full" style="width: 28%"></div></div>
                </div>
                <div>
                    <div class="flex justify-between text-xs font-bold text-brand mb-1">
                        <span>Osu</span>
                        <span>15%</span>
                    </div>
                    <div class="w-full bg-surface rounded-full h-2"><div class="bg-brand/60 h-2 rounded-full" style="width: 15%"></div></div>
                </div>
                <div>
                    <div class="flex justify-between text-xs font-bold text-brand mb-1">
                        <span>Spintex</span>
                        <span>10%</span>
                    </div>
                    <div class="w-full bg-surface rounded-full h-2"><div class="bg-brand/40 h-2 rounded-full" style="width: 10%"></div></div>
                </div>
            </div>
        </div>

        <!-- Vehicle Type Split -->
        <div class="bg-white rounded-lg border border-gray-100 shadow-sm p-6">
            <h3 class="text-[10px] font-black text-brand-muted uppercase tracking-widest mb-4">Ride Distribution</h3>
            <div class="flex items-center justify-center py-4">
                <!-- Mock Donut -->
                <div class="w-32 h-32 rounded-full border-[12px] border-surface relative flex items-center justify-center" style="border-top-color: var(--color-brand); border-right-color: var(--color-brand); border-bottom-color: var(--color-accent);">
                    <span class="text-xl font-black text-brand">100%</span>
                </div>
            </div>
            <div class="flex justify-center gap-4 mt-2">
                <span class="flex items-center gap-1 text-[10px] font-bold text-brand-muted uppercase"><div class="w-2 h-2 rounded-full bg-brand"></div> Sedan</span>
                <span class="flex items-center gap-1 text-[10px] font-bold text-brand-muted uppercase"><div class="w-2 h-2 rounded-full bg-accent"></div> SUV</span>
            </div>
        </div>

    </div>
</div>

@endsection
