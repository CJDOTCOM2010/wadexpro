@extends('admin.layout')
@section('title', 'System Settings Hub')
@section('content')

<div class="p-6 lg:p-10 max-w-5xl mx-auto">
    <div class="mb-10">
        <h2 class="text-3xl font-black text-brand tracking-tighter">System Settings Hub</h2>
        <p class="text-brand-muted font-medium mt-1 text-sm md:text-base">Centralized command center for platform parameters, security bridging, and fleet manifests.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <!-- Branding Card -->
        <a href="{{ route('orchestrator.settings.branding') }}" class="group relative overflow-hidden bg-white border border-gray-100 rounded-lg p-5 shadow-sm hover:shadow-lg transition-all duration-300 hover:-translate-y-1 flex items-center gap-5">
            <div class="absolute right-0 top-1/2 -translate-y-1/2 -mr-4 opacity-[0.02] group-hover:opacity-[0.06] transition-opacity pointer-events-none">
                <svg class="w-32 h-32" fill="currentColor" viewBox="0 0 24 24"><path d="M12.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
            
            <div class="w-14 h-14 shrink-0 bg-brand/5 rounded-lg flex items-center justify-center text-brand group-hover:bg-brand group-hover:text-white transition-colors relative z-10">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </div>
            
            <div class="relative z-10 flex-1">
                <h3 class="text-[17px] font-black text-brand mb-1">General Branding</h3>
                <p class="text-[11px] text-brand-muted font-bold tracking-tight leading-relaxed">Enterprise labels, logos, and clearances.</p>
            </div>
            
            <div class="relative z-10 shrink-0 text-accent opacity-0 group-hover:opacity-100 transition-all transform translate-x-2 group-hover:translate-x-0 duration-300">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </div>
        </a>
        
        <!-- Dashboard Branding Card -->
        <a href="{{ route('orchestrator.settings.dashboard_branding') }}" class="group relative overflow-hidden bg-white border border-gray-100 rounded-lg p-5 shadow-sm hover:shadow-lg transition-all duration-300 hover:-translate-y-1 flex items-center gap-5">
            <div class="absolute right-0 top-1/2 -translate-y-1/2 -mr-4 opacity-[0.02] group-hover:opacity-[0.06] transition-opacity pointer-events-none">
                <svg class="w-32 h-32" fill="currentColor" viewBox="0 0 24 24"><path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
            </div>
            
            <div class="w-14 h-14 shrink-0 bg-accent/5 rounded-lg flex items-center justify-center text-accent group-hover:bg-accent group-hover:text-brand transition-colors relative z-10">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            </div>
            
            <div class="relative z-10 flex-1">
                <h3 class="text-[17px] font-black text-brand mb-1">Dashboard Branding</h3>
                <p class="text-[11px] text-brand-muted font-bold tracking-tight leading-relaxed">Customize logos, favicons, and portal identity.</p>
            </div>
            
            <div class="relative z-10 shrink-0 text-accent opacity-0 group-hover:opacity-100 transition-all transform translate-x-2 group-hover:translate-x-0 duration-300">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </div>
        </a>

        <!-- Identity Card -->
        <a href="{{ route('orchestrator.settings.auth') }}" class="group relative overflow-hidden bg-white border border-gray-100 rounded-lg p-5 shadow-sm hover:shadow-lg transition-all duration-300 hover:-translate-y-1 flex items-center gap-5">
            <div class="absolute right-0 top-1/2 -translate-y-1/2 -mr-4 opacity-[0.02] group-hover:opacity-[0.06] transition-opacity pointer-events-none">
                <svg class="w-32 h-32" fill="currentColor" viewBox="0 0 24 24"><path d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
            </div>

            <div class="w-14 h-14 shrink-0 bg-brand/5 rounded-lg flex items-center justify-center text-brand group-hover:bg-brand group-hover:text-white transition-colors relative z-10">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
            </div>

            <div class="relative z-10 flex-1">
                <h3 class="text-[17px] font-black text-brand mb-1">Auth & Identity</h3>
                <p class="text-[11px] text-brand-muted font-bold tracking-tight leading-relaxed">Google & Facebook identity sync and bridging.</p>
            </div>

            <div class="relative z-10 shrink-0 text-accent opacity-0 group-hover:opacity-100 transition-all transform translate-x-2 group-hover:translate-x-0 duration-300">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </div>
        </a>

        <!-- Manifest Card -->
        <a href="{{ route('orchestrator.settings.manifest') }}" class="group relative overflow-hidden bg-white border border-gray-100 rounded-lg p-5 shadow-sm hover:shadow-lg transition-all duration-300 hover:-translate-y-1 flex items-center gap-5">
            <div class="absolute right-0 top-1/2 -translate-y-1/2 -mr-4 opacity-[0.02] group-hover:opacity-[0.06] transition-opacity pointer-events-none">
                <svg class="w-32 h-32" fill="currentColor" viewBox="0 0 24 24"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>

            <div class="w-14 h-14 shrink-0 bg-brand/5 rounded-lg flex items-center justify-center text-brand group-hover:bg-brand group-hover:text-white transition-colors relative z-10">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
            </div>

            <div class="relative z-10 flex-1">
                <h3 class="text-[17px] font-black text-brand mb-1">Mobile Manifest</h3>
                <p class="text-[11px] text-brand-muted font-bold tracking-tight leading-relaxed">Cross-fleet version parity and store target management.</p>
            </div>

            <div class="relative z-10 shrink-0 text-accent opacity-0 group-hover:opacity-100 transition-all transform translate-x-2 group-hover:translate-x-0 duration-300">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </div>
        </a>

        <!-- Localization Card -->
        <a href="{{ route('orchestrator.settings.localization') }}" class="group relative overflow-hidden bg-white border border-gray-100 rounded-lg p-5 shadow-sm hover:shadow-lg transition-all duration-300 hover:-translate-y-1 flex items-center gap-5">
            <div class="absolute right-0 top-1/2 -translate-y-1/2 -mr-4 opacity-[0.02] group-hover:opacity-[0.06] transition-opacity pointer-events-none">
                <svg class="w-32 h-32" fill="currentColor" viewBox="0 0 24 24"><path d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>

            <div class="w-14 h-14 shrink-0 bg-brand/5 rounded-lg flex items-center justify-center text-brand group-hover:bg-brand group-hover:text-white transition-colors relative z-10">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/></svg>
            </div>

            <div class="relative z-10 flex-1">
                <h3 class="text-[17px] font-black text-brand mb-1">Localization</h3>
                <p class="text-[11px] text-brand-muted font-bold tracking-tight leading-relaxed">Regional parameters, currency syntax, and nodes.</p>
            </div>

            <div class="relative z-10 shrink-0 text-accent opacity-0 group-hover:opacity-100 transition-all transform translate-x-2 group-hover:translate-x-0 duration-300">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </div>
        </a>

        <!-- Communication & Notifications Card -->
        <a href="{{ route('orchestrator.settings.notifications') }}" class="group relative overflow-hidden bg-white border border-gray-100 rounded-lg p-5 shadow-sm hover:shadow-lg transition-all duration-300 hover:-translate-y-1 flex items-center gap-5">
            <div class="absolute right-0 top-1/2 -translate-y-1/2 -mr-4 opacity-[0.02] group-hover:opacity-[0.06] transition-opacity pointer-events-none">
                <svg class="w-32 h-32" fill="currentColor" viewBox="0 0 24 24"><path d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
            </div>

            <div class="w-14 h-14 shrink-0 bg-orange-50 rounded-lg flex items-center justify-center text-orange-600 group-hover:bg-orange-600 group-hover:text-white transition-colors relative z-10">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
            </div>

            <div class="relative z-10 flex-1">
                <h3 class="text-[17px] font-black text-brand mb-1">Communication Channels</h3>
                <p class="text-[11px] text-brand-muted font-bold tracking-tight leading-relaxed">Email, SMS, WhatsApp, Push, and In-Call API configurations.</p>
            </div>

            <div class="relative z-10 shrink-0 text-accent opacity-0 group-hover:opacity-100 transition-all transform translate-x-2 group-hover:translate-x-0 duration-300">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </div>
        </a>
        <a href="{{ route('orchestrator.settings.onboarding', 'customer') }}" class="group relative overflow-hidden bg-white border border-gray-100 rounded-lg p-5 shadow-sm hover:shadow-lg transition-all duration-300 hover:-translate-y-1 flex items-center gap-5">
            <div class="absolute right-0 top-1/2 -translate-y-1/2 -mr-4 opacity-[0.02] group-hover:opacity-[0.06] transition-opacity pointer-events-none">
                <svg class="w-32 h-32" fill="currentColor" viewBox="0 0 24 24"><path d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
            </div>

            <div class="w-14 h-14 shrink-0 bg-emerald-50 rounded-lg flex items-center justify-center text-emerald-600 group-hover:bg-emerald-600 group-hover:text-white transition-colors relative z-10">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122"/></svg>
            </div>

            <div class="relative z-10 flex-1">
                <h3 class="text-[17px] font-black text-brand mb-1">Customer Onboarding</h3>
                <p class="text-[11px] text-brand-muted font-bold tracking-tight leading-relaxed">Manage first-launch slides for the Customer app.</p>
            </div>

            <div class="relative z-10 shrink-0 text-accent opacity-0 group-hover:opacity-100 transition-all transform translate-x-2 group-hover:translate-x-0 duration-300">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </div>
        </a>

        <!-- Driver Onboarding Card -->
        <a href="{{ route('orchestrator.settings.onboarding', 'driver') }}" class="group relative overflow-hidden bg-white border border-gray-100 rounded-lg p-5 shadow-sm hover:shadow-lg transition-all duration-300 hover:-translate-y-1 flex items-center gap-5">
            <div class="absolute right-0 top-1/2 -translate-y-1/2 -mr-4 opacity-[0.02] group-hover:opacity-[0.06] transition-opacity pointer-events-none">
                <svg class="w-32 h-32" fill="currentColor" viewBox="0 0 24 24"><path d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/></svg>
            </div>

            <div class="w-14 h-14 shrink-0 bg-violet-50 rounded-lg flex items-center justify-center text-violet-600 group-hover:bg-violet-600 group-hover:text-white transition-colors relative z-10">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1"/></svg>
            </div>

            <div class="relative z-10 flex-1">
                <h3 class="text-[17px] font-black text-brand mb-1">Driver Onboarding</h3>
                <p class="text-[11px] text-brand-muted font-bold tracking-tight leading-relaxed">Manage first-launch slides for the Driver app.</p>
            </div>

            <div class="relative z-10 shrink-0 text-accent opacity-0 group-hover:opacity-100 transition-all transform translate-x-2 group-hover:translate-x-0 duration-300">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </div>
        </a>

        <!-- System Backups Card -->
        <a href="{{ route('orchestrator.settings.backups') }}" class="group relative overflow-hidden bg-white border border-gray-100 rounded-lg p-5 shadow-sm hover:shadow-lg transition-all duration-300 hover:-translate-y-1 flex items-center gap-5">
            <div class="absolute right-0 top-1/2 -translate-y-1/2 -mr-4 opacity-[0.02] group-hover:opacity-[0.06] transition-opacity pointer-events-none">
                <svg class="w-32 h-32" fill="currentColor" viewBox="0 0 24 24"><path d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"/></svg>
            </div>

            <div class="w-14 h-14 shrink-0 bg-brand text-white rounded-lg flex items-center justify-center relative z-10 group-hover:bg-accent group-hover:text-brand transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>

            <div class="relative z-10 flex-1">
                <h3 class="text-[17px] font-black text-brand mb-1">System Backups</h3>
                <p class="text-[11px] text-brand-muted font-bold tracking-tight leading-relaxed">High-integrity data snapshots and automated restoration nodes.</p>
            </div>

            <div class="relative z-10 shrink-0 text-accent opacity-0 group-hover:opacity-100 transition-all transform translate-x-2 group-hover:translate-x-0 duration-300">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </div>
        </a>

        <!-- Payment Gateways Card -->
        <a href="{{ route('orchestrator.settings.payments') }}" class="group relative overflow-hidden bg-white border border-gray-100 rounded-lg p-5 shadow-sm hover:shadow-lg transition-all duration-300 hover:-translate-y-1 flex items-center gap-5">
            <div class="absolute right-0 top-1/2 -translate-y-1/2 -mr-4 opacity-[0.02] group-hover:opacity-[0.06] transition-opacity pointer-events-none">
                <svg class="w-32 h-32" fill="currentColor" viewBox="0 0 24 24"><path d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
            </div>

            <div class="w-14 h-14 shrink-0 bg-blue-50 rounded-lg flex items-center justify-center text-blue-600 group-hover:bg-blue-600 group-hover:text-white transition-colors relative z-10">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            </div>

            <div class="relative z-10 flex-1">
                <h3 class="text-[17px] font-black text-brand mb-1">Payment Gateways</h3>
                <p class="text-[11px] text-brand-muted font-bold tracking-tight leading-relaxed">Dynamic provider switching, API keys, and transaction modes.</p>
            </div>

            <div class="relative z-10 shrink-0 text-accent opacity-0 group-hover:opacity-100 transition-all transform translate-x-2 group-hover:translate-x-0 duration-300">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </div>
        </a>
    </div>
</div>

@endsection
