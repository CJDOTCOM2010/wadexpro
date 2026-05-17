@extends('admin.layout')
@section('title', 'System Settings Hub')
@section('content')

@if(session('error'))
<div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl flex items-center gap-3">
    <svg class="w-5 h-5 text-red-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
    <p class="text-sm font-medium text-red-700">{{ session('error') }}</p>
</div>
@endif

@if(session('success'))
<div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl flex items-center gap-3">
    <svg class="w-5 h-5 text-green-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    <p class="text-sm font-medium text-green-700">{{ session('success') }}</p>
</div>
@endif

<div class="max-w-6xl mx-auto">
    <div class="mb-8">
        <h2 class="text-2xl font-black text-brand tracking-tight">System Settings Hub</h2>
        <p class="text-sm text-brand-muted font-medium mt-1">Configure branding, payments, notifications, and system parameters.</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <a href="{{ route('orchestrator.settings.branding') }}" class="bg-white border border-gray-100 rounded-xl p-4 hover:border-accent/40 hover:bg-accent/[0.02] transition-colors">
            <div class="w-10 h-10 bg-brand/5 rounded-lg flex items-center justify-center text-brand mb-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </div>
            <h3 class="text-sm font-bold text-brand">Branding</h3>
            <p class="text-[11px] text-brand-muted mt-0.5 leading-snug">Enterprise labels, logos, brand colors</p>
        </a>

        <a href="{{ route('orchestrator.settings.dashboard_branding') }}" class="bg-white border border-gray-100 rounded-xl p-4 hover:border-accent/40 hover:bg-accent/[0.02] transition-colors">
            <div class="w-10 h-10 bg-accent/10 rounded-lg flex items-center justify-center text-accent mb-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            </div>
            <h3 class="text-sm font-bold text-brand">Dashboard Branding</h3>
            <p class="text-[11px] text-brand-muted mt-0.5 leading-snug">Logos, favicons, portal identity</p>
        </a>

        <a href="{{ route('orchestrator.settings.prefixes') }}" class="bg-white border border-gray-100 rounded-xl p-4 hover:border-accent/40 hover:bg-accent/[0.02] transition-colors">
            <div class="w-10 h-10 bg-brand/5 rounded-lg flex items-center justify-center text-brand mb-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
            </div>
            <h3 class="text-sm font-bold text-brand">ID Prefixes</h3>
            <p class="text-[11px] text-brand-muted mt-0.5 leading-snug">Naming schemas for tickets, orders, users</p>
        </a>

        <a href="{{ route('orchestrator.settings.geolocation') }}" class="bg-white border border-gray-100 rounded-xl p-4 hover:border-accent/40 hover:bg-accent/[0.02] transition-colors">
            <div class="w-10 h-10 bg-brand/5 rounded-lg flex items-center justify-center text-brand mb-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
            <h3 class="text-sm font-bold text-brand">Geolocation</h3>
            <p class="text-[11px] text-brand-muted mt-0.5 leading-snug">Map APIs, geocoding, location services</p>
        </a>

        <a href="{{ route('orchestrator.settings.social_auth') }}" class="bg-white border border-gray-100 rounded-xl p-4 hover:border-accent/40 hover:bg-accent/[0.02] transition-colors">
            <div class="w-10 h-10 bg-brand/5 rounded-lg flex items-center justify-center text-brand mb-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
            </div>
            <h3 class="text-sm font-bold text-brand">Social Auth</h3>
            <p class="text-[11px] text-brand-muted mt-0.5 leading-snug">OAuth providers, identity bridging</p>
        </a>

        <a href="{{ route('orchestrator.settings.auth') }}" class="bg-white border border-gray-100 rounded-xl p-4 hover:border-accent/40 hover:bg-accent/[0.02] transition-colors">
            <div class="w-10 h-10 bg-brand/5 rounded-lg flex items-center justify-center text-brand mb-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
            </div>
            <h3 class="text-sm font-bold text-brand">Auth & Identity</h3>
            <p class="text-[11px] text-brand-muted mt-0.5 leading-snug">Platform identity sync and bridging</p>
        </a>

        <a href="{{ route('orchestrator.settings.manifest') }}" class="bg-white border border-gray-100 rounded-xl p-4 hover:border-accent/40 hover:bg-accent/[0.02] transition-colors">
            <div class="w-10 h-10 bg-brand/5 rounded-lg flex items-center justify-center text-brand mb-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
            </div>
            <h3 class="text-sm font-bold text-brand">Mobile Manifest</h3>
            <p class="text-[11px] text-brand-muted mt-0.5 leading-snug">App versioning, store links</p>
        </a>

        <a href="{{ route('orchestrator.settings.localization') }}" class="bg-white border border-gray-100 rounded-xl p-4 hover:border-accent/40 hover:bg-accent/[0.02] transition-colors">
            <div class="w-10 h-10 bg-brand/5 rounded-lg flex items-center justify-center text-brand mb-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/></svg>
            </div>
            <h3 class="text-sm font-bold text-brand">Localization</h3>
            <p class="text-[11px] text-brand-muted mt-0.5 leading-snug">Regional params, currency, timezone</p>
        </a>

        <a href="{{ route('orchestrator.settings.notifications') }}" class="bg-white border border-gray-100 rounded-xl p-4 hover:border-accent/40 hover:bg-accent/[0.02] transition-colors">
            <div class="w-10 h-10 bg-orange-50 rounded-lg flex items-center justify-center text-orange-600 mb-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
            </div>
            <h3 class="text-sm font-bold text-brand">Notifications</h3>
            <p class="text-[11px] text-brand-muted mt-0.5 leading-snug">Email, SMS, push, in-call configs</p>
        </a>

        <a href="{{ route('orchestrator.settings.backups') }}" class="bg-white border border-gray-100 rounded-xl p-4 hover:border-accent/40 hover:bg-accent/[0.02] transition-colors">
            <div class="w-10 h-10 bg-brand/5 rounded-lg flex items-center justify-center text-brand mb-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <h3 class="text-sm font-bold text-brand">Backups</h3>
            <p class="text-[11px] text-brand-muted mt-0.5 leading-snug">Data snapshots, restore management</p>
        </a>

        <a href="{{ route('orchestrator.settings.payments') }}" class="bg-white border border-gray-100 rounded-xl p-4 hover:border-accent/40 hover:bg-accent/[0.02] transition-colors">
            <div class="w-10 h-10 bg-blue-50 rounded-lg flex items-center justify-center text-blue-600 mb-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            </div>
            <h3 class="text-sm font-bold text-brand">Payments</h3>
            <p class="text-[11px] text-brand-muted mt-0.5 leading-snug">Gateways, keys, transaction modes</p>
        </a>

        <a href="{{ route('orchestrator.settings.security') }}" class="bg-white border border-gray-100 rounded-xl p-4 hover:border-accent/40 hover:bg-accent/[0.02] transition-colors">
            <div class="w-10 h-10 bg-red-50 rounded-lg flex items-center justify-center text-red-600 mb-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
            </div>
            <h3 class="text-sm font-bold text-brand">Security</h3>
            <p class="text-[11px] text-brand-muted mt-0.5 leading-snug">Password policy, 2FA, IP whitelist</p>
        </a>

        <a href="{{ route('orchestrator.settings.api_rate_limiting') }}" class="bg-white border border-gray-100 rounded-xl p-4 hover:border-accent/40 hover:bg-accent/[0.02] transition-colors">
            <div class="w-10 h-10 bg-purple-50 rounded-lg flex items-center justify-center text-purple-600 mb-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            </div>
            <h3 class="text-sm font-bold text-brand">API Rate Limiting</h3>
            <p class="text-[11px] text-brand-muted mt-0.5 leading-snug">Throttling, burst, webhook retries</p>
        </a>

        <a href="{{ route('orchestrator.settings.api_configuration') }}" class="bg-white border border-gray-100 rounded-xl p-4 hover:border-accent/40 hover:bg-accent/[0.02] transition-colors">
            <div class="w-10 h-10 bg-cyan-50 rounded-lg flex items-center justify-center text-cyan-600 mb-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
            <h3 class="text-sm font-bold text-brand">API Configuration</h3>
            <p class="text-[11px] text-brand-muted mt-0.5 leading-snug">Mobile app endpoints, socket URLs</p>
        </a>

        <a href="{{ route('orchestrator.settings.onboarding', 'customer') }}" class="bg-white border border-gray-100 rounded-xl p-4 hover:border-accent/40 hover:bg-accent/[0.02] transition-colors">
            <div class="w-10 h-10 bg-emerald-50 rounded-lg flex items-center justify-center text-emerald-600 mb-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122"/></svg>
            </div>
            <h3 class="text-sm font-bold text-brand">Customer Onboarding</h3>
            <p class="text-[11px] text-brand-muted mt-0.5 leading-snug">First-launch slides, splash screen</p>
        </a>

        <a href="{{ route('orchestrator.settings.onboarding', 'driver') }}" class="bg-white border border-gray-100 rounded-xl p-4 hover:border-accent/40 hover:bg-accent/[0.02] transition-colors">
            <div class="w-10 h-10 bg-violet-50 rounded-lg flex items-center justify-center text-violet-600 mb-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1"/></svg>
            </div>
            <h3 class="text-sm font-bold text-brand">Driver Onboarding</h3>
            <p class="text-[11px] text-brand-muted mt-0.5 leading-snug">First-launch slides, splash screen</p>
        </a>

        <a href="{{ route('orchestrator.settings.assets') }}" class="bg-white border border-gray-100 rounded-xl p-4 hover:border-accent/40 hover:bg-accent/[0.02] transition-colors">
            <div class="w-10 h-10 bg-brand/5 rounded-lg flex items-center justify-center text-brand mb-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </div>
            <h3 class="text-sm font-bold text-brand">Media Assets</h3>
            <p class="text-[11px] text-brand-muted mt-0.5 leading-snug">Media library, file management</p>
        </a>
    </div>
</div>

@endsection