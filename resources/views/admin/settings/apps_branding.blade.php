@extends('admin.layout')
@section('title', 'Apps Branding')

@php
$settings = $settings ?? collect();
$driverAppIcon = $settings->get('driver_app_icon_url')?->value ?? '';
$customerAppIcon = $settings->get('customer_app_icon_url')?->value ?? '';
$driverSplashBg = $settings->get('driver_splash_background')?->value ?? '#156400';
$customerSplashBg = $settings->get('customer_splash_background')?->value ?? '#156400';
$driverAppName = $settings->get('driver_app_display_name')?->value ?? 'WADEXPRO Driver';
$customerAppName = $settings->get('customer_app_display_name')?->value ?? 'WADEXPRO';
@endphp

@push('styles')
<style>
    @keyframes shimmer {
        0% { background-position: -200% 0; }
        100% { background-position: 200% 0; }
    }
    @keyframes float-up {
        0% { transform: translateY(8px); opacity: 0; }
        100% { transform: translateY(0); opacity: 1; }
    }
    @keyframes glow-pulse {
        0%, 100% { box-shadow: 0 0 6px rgba(248, 184, 3, 0.3); }
        50% { box-shadow: 0 0 18px rgba(248, 184, 3, 0.6); }
    }
    .glass-card {
        background: linear-gradient(135deg, rgba(255,255,255,0.95), rgba(255,255,255,0.85));
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid rgba(255,255,255,0.8);
    }
    .glass-panel {
        background: linear-gradient(135deg, rgba(255,255,255,0.6), rgba(246,246,246,0.4));
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
        border: 1px solid rgba(255,255,255,0.5);
    }
    .gradient-border {
        position: relative;
    }
    .gradient-border::before {
        content: '';
        position: absolute;
        inset: 0;
        border-radius: inherit;
        padding: 1px;
        background: linear-gradient(135deg, rgba(248,184,3,0.3), rgba(248,184,3,0.05), rgba(248,184,3,0.3));
        -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
        -webkit-mask-composite: xor;
        mask-composite: exclude;
        pointer-events: none;
    }
    .premium-shadow {
        box-shadow: 0 4px 24px rgba(0,0,0,0.06), 0 1px 4px rgba(0,0,0,0.04);
    }
    .premium-shadow-hover:hover {
        box-shadow: 0 8px 32px rgba(0,0,0,0.1), 0 2px 8px rgba(0,0,0,0.05);
    }
    .shimmer-bg {
        background: linear-gradient(90deg, transparent, rgba(248,184,3,0.05), transparent);
        background-size: 200% 100%;
        animation: shimmer 3s infinite;
    }
    .phone-glass {
        background: linear-gradient(180deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0.03) 100%);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
    }
    .file-input-btn {
        position: relative;
        overflow: hidden;
    }
    .file-input-btn::after {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(135deg, rgba(255,255,255,0.08), transparent);
        opacity: 0;
        transition: opacity 0.3s;
    }
    .file-input-btn:hover::after {
        opacity: 1;
    }
</style>
@endpush

@section('content')
<div class="p-8 lg:p-12 max-w-6xl mx-auto"
     x-data="appsBrandingManager()"
     x-init="init()">

    {{-- HEADER --}}
    <div class="flex items-center justify-between mb-12">
        <div>
            <div class="flex items-center gap-2 text-[10px] font-black text-accent uppercase tracking-[0.2em] mb-3">
                <a href="{{ route('orchestrator.settings') }}" class="hover:text-accent-hover transition-all duration-300 relative after:absolute after:bottom-0 after:left-0 after:h-0.5 after:w-0 hover:after:w-full after:bg-accent after:transition-all after:duration-300">Settings Hub</a>
                <span class="text-gray-300">/</span>
                <span class="text-brand-muted">Apps Branding</span>
            </div>
            <h2 class="text-4xl font-black text-brand tracking-tight leading-tight">Apps <span class="text-accent">Branding</span></h2>
            <p class="text-sm text-brand-muted mt-2 max-w-xl leading-relaxed">Configure app icons, display names, and splash backgrounds for your Driver and Customer mobile applications.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('orchestrator.settings') }}" class="group relative overflow-hidden bg-surface text-brand-muted hover:text-brand px-6 py-3.5 rounded-xl text-xs font-bold transition-all duration-300 flex items-center gap-2 border border-gray-100/80 premium-shadow hover:premium-shadow-hover">
                <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/50 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-700"></div>
                <svg class="w-4 h-4 transition-transform group-hover:-translate-x-1 duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Back to Hub
            </a>
            <button type="submit" form="branding-form"
                class="group relative overflow-hidden px-8 py-3.5 bg-brand text-white rounded-xl font-bold text-xs hover:shadow-2xl hover:shadow-brand/30 transition-all duration-300 flex items-center gap-2 premium-shadow">
                <div class="absolute inset-0 shimmer-bg opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                <svg class="w-4 h-4 text-accent relative z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span class="relative z-10">Save Changes</span>
            </button>
        </div>
    </div>

    {{-- Success Flash --}}
    @if(session('success'))
    <div class="mb-8 p-5 bg-gradient-to-r from-green-50 to-emerald-50/50 border border-green-100 text-green-700 rounded-2xl flex items-center gap-4 animate-[float-up_0.3s_ease-out] premium-shadow">
        <div class="w-10 h-10 rounded-xl bg-green-100 flex items-center justify-center shrink-0">
            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        </div>
        <span class="text-sm font-bold">{{ session('success') }}</span>
    </div>
    @endif

    {{-- Error Flash --}}
    @if($errors->any())
    <div class="mb-8 p-5 bg-gradient-to-r from-red-50 to-rose-50/50 border border-red-100 text-red-700 rounded-2xl flex items-start gap-4 animate-[float-up_0.3s_ease-out] premium-shadow">
        <div class="w-10 h-10 rounded-xl bg-red-100 flex items-center justify-center shrink-0 mt-0.5">
            <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        </div>
        <div>
            <span class="text-sm font-bold">Please correct the following errors:</span>
            <ul class="text-xs list-disc pl-4 mt-2 space-y-1 text-red-600/80">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
    @endif

    {{-- MAIN FORM --}}
    <form id="branding-form" method="POST" action="{{ route('orchestrator.settings.update') }}" enctype="multipart/form-data">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

            {{-- DRIVER APP CARD --}}
            <div class="glass-card rounded-3xl premium-shadow p-8 lg:p-10 space-y-8 gradient-border h-full">
                {{-- Card Header --}}
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-cyan-500/20 to-cyan-500/5 flex items-center justify-center">
                        <svg class="w-7 h-7 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-3">
                            <h3 class="text-xl font-black text-brand tracking-tight">Driver App</h3>
                            <span class="px-2.5 py-1 rounded-lg text-[9px] font-black uppercase tracking-wider bg-cyan-50 text-cyan-700 border border-cyan-100">App #1</span>
                        </div>
                        <p class="text-xs text-brand-muted mt-0.5">App icon, display name, and splash background for the Driver application.</p>
                    </div>
                </div>

                <div class="space-y-7">
                    {{-- App Name --}}
                    <div>
                        <label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-2.5">Display Name</label>
                        <div class="relative group">
                            <div class="absolute inset-0 bg-gradient-to-r from-accent/5 to-transparent rounded-xl opacity-0 group-focus-within:opacity-100 pointer-events-none transition-opacity duration-500"></div>
                            <input type="text" name="settings[driver_app_display_name]" x-model="driver.name" value="{{ $driverAppName }}" placeholder="e.g. WADEXPRO Driver"
                                   class="w-full bg-surface/80 border border-gray-100 focus:border-accent/50 focus:bg-white rounded-xl py-4 px-6 text-sm font-bold outline-none transition-all duration-300 premium-shadow focus:shadow-lg focus:shadow-accent/5">
                        </div>
                    </div>

                    {{-- App Icon --}}
                    <div>
                        <label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-2.5">App Icon</label>
                        <div class="flex items-start gap-5">
                            <div class="relative shrink-0">
                                <template x-if="driver.iconPreview">
                                    <div class="w-20 h-20 rounded-2xl border-2 border-gray-100 overflow-hidden bg-white shadow-lg shadow-black/5">
                                        <img :src="driver.iconPreview" class="w-full h-full object-cover">
                                    </div>
                                </template>
                                <template x-if="!driver.iconPreview && driver.existingIcon">
                                    <div class="w-20 h-20 rounded-2xl border-2 border-gray-100 overflow-hidden bg-white shadow-lg shadow-black/5">
                                        <img src="{{ $driverAppIcon }}" class="w-full h-full object-cover">
                                    </div>
                                </template>
                                <template x-if="!driver.iconPreview && !driver.existingIcon">
                                    <div class="w-20 h-20 rounded-2xl border-2 border-dashed border-gray-200 bg-gray-50 flex items-center justify-center">
                                        <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                    </div>
                                </template>
                                {{-- Glow --}}
                                <div class="absolute -inset-2 bg-gradient-to-br from-cyan-500/10 to-transparent rounded-3xl blur-xl -z-10"></div>
                            </div>
                            <div class="flex-1">
                                <input type="file" name="driver_app_icon" accept="image/png"
                                       @change="previewIcon($event, 'driver')"
                                       class="file-input-btn w-full bg-surface/80 rounded-xl py-3 px-4 text-sm font-bold border border-gray-100 file:mr-4 file:py-2.5 file:px-5 file:rounded-xl file:border-0 file:text-xs file:font-black file:bg-gradient-to-r file:from-brand file:to-brand-light file:text-white file:cursor-pointer hover:file:shadow-lg hover:file:shadow-brand/20 transition-all duration-300">
                                <p class="text-[9px] font-bold text-gray-400 mt-2.5 flex items-center gap-1.5">
                                    <svg class="w-3 h-3 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    PNG, 1024x1024 recommended
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Splash Background Color --}}
                    <div>
                        <label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-2.5">Splash Background Color</label>
                        <div class="flex items-center gap-3 bg-surface/80 p-2 rounded-xl border border-gray-100 focus-within:border-accent/50 focus-within:shadow-lg focus-within:shadow-accent/5 transition-all duration-300">
                            <input type="color" name="settings[driver_splash_background]" x-model="driver.splashBg" value="{{ $driverSplashBg }}" class="w-10 h-10 rounded-lg border-none cursor-pointer shrink-0">
                            <input type="text" x-model="driver.splashBg" class="bg-transparent border-none text-[10px] font-black w-full outline-none">
                        </div>
                    </div>
                </div>

                {{-- Driver Preview --}}
                <div class="pt-6 border-t border-gray-50/80">
                    <p class="text-[9px] font-black text-brand-muted uppercase tracking-widest mb-4 flex items-center gap-2">
                        <svg class="w-3 h-3 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        Live Preview
                    </p>
                    <div class="bg-gray-900 rounded-2xl p-5 flex items-center gap-5 ring-1 ring-white/10">
                        <template x-if="driver.iconPreview || '{{ $driverAppIcon }}'">
                            <div class="w-12 h-12 rounded-xl overflow-hidden bg-white shrink-0 ring-2 ring-white/20">
                                <img :src="driver.iconPreview || '{{ $driverAppIcon }}'" class="w-full h-full object-cover">
                            </div>
                        </template>
                        <template x-if="!driver.iconPreview && !'{{ $driverAppIcon }}'">
                            <div class="w-12 h-12 rounded-xl bg-cyan-600 flex items-center justify-center shrink-0">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                            </div>
                        </template>
                        <div>
                            <p class="text-white text-sm font-black" x-text="driver.name || '{{ $driverAppName }}'"></p>
                            <p class="text-gray-400 text-[10px] font-medium">Driver App</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- CUSTOMER APP CARD --}}
            <div class="glass-card rounded-3xl premium-shadow p-8 lg:p-10 space-y-8 gradient-border h-full">
                {{-- Card Header --}}
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-emerald-500/20 to-emerald-500/5 flex items-center justify-center">
                        <svg class="w-7 h-7 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-3">
                            <h3 class="text-xl font-black text-brand tracking-tight">Customer App</h3>
                            <span class="px-2.5 py-1 rounded-lg text-[9px] font-black uppercase tracking-wider bg-emerald-50 text-emerald-700 border border-emerald-100">App #2</span>
                        </div>
                        <p class="text-xs text-brand-muted mt-0.5">App icon, display name, and splash background for the Customer (Rider) application.</p>
                    </div>
                </div>

                <div class="space-y-7">
                    {{-- App Name --}}
                    <div>
                        <label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-2.5">Display Name</label>
                        <div class="relative group">
                            <div class="absolute inset-0 bg-gradient-to-r from-accent/5 to-transparent rounded-xl opacity-0 group-focus-within:opacity-100 pointer-events-none transition-opacity duration-500"></div>
                            <input type="text" name="settings[customer_app_display_name]" x-model="customer.name" value="{{ $customerAppName }}" placeholder="e.g. WADEXPRO"
                                   class="w-full bg-surface/80 border border-gray-100 focus:border-accent/50 focus:bg-white rounded-xl py-4 px-6 text-sm font-bold outline-none transition-all duration-300 premium-shadow focus:shadow-lg focus:shadow-accent/5">
                        </div>
                    </div>

                    {{-- App Icon --}}
                    <div>
                        <label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-2.5">App Icon</label>
                        <div class="flex items-start gap-5">
                            <div class="relative shrink-0">
                                <template x-if="customer.iconPreview">
                                    <div class="w-20 h-20 rounded-2xl border-2 border-gray-100 overflow-hidden bg-white shadow-lg shadow-black/5">
                                        <img :src="customer.iconPreview" class="w-full h-full object-cover">
                                    </div>
                                </template>
                                <template x-if="!customer.iconPreview && customer.existingIcon">
                                    <div class="w-20 h-20 rounded-2xl border-2 border-gray-100 overflow-hidden bg-white shadow-lg shadow-black/5">
                                        <img src="{{ $customerAppIcon }}" class="w-full h-full object-cover">
                                    </div>
                                </template>
                                <template x-if="!customer.iconPreview && !customer.existingIcon">
                                    <div class="w-20 h-20 rounded-2xl border-2 border-dashed border-gray-200 bg-gray-50 flex items-center justify-center">
                                        <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                    </div>
                                </template>
                                <div class="absolute -inset-2 bg-gradient-to-br from-emerald-500/10 to-transparent rounded-3xl blur-xl -z-10"></div>
                            </div>
                            <div class="flex-1">
                                <input type="file" name="customer_app_icon" accept="image/png"
                                       @change="previewIcon($event, 'customer')"
                                       class="file-input-btn w-full bg-surface/80 rounded-xl py-3 px-4 text-sm font-bold border border-gray-100 file:mr-4 file:py-2.5 file:px-5 file:rounded-xl file:border-0 file:text-xs file:font-black file:bg-gradient-to-r file:from-brand file:to-brand-light file:text-white file:cursor-pointer hover:file:shadow-lg hover:file:shadow-brand/20 transition-all duration-300">
                                <p class="text-[9px] font-bold text-gray-400 mt-2.5 flex items-center gap-1.5">
                                    <svg class="w-3 h-3 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    PNG, 1024x1024 recommended
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Splash Background Color --}}
                    <div>
                        <label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-2.5">Splash Background Color</label>
                        <div class="flex items-center gap-3 bg-surface/80 p-2 rounded-xl border border-gray-100 focus-within:border-accent/50 focus-within:shadow-lg focus-within:shadow-accent/5 transition-all duration-300">
                            <input type="color" name="settings[customer_splash_background]" x-model="customer.splashBg" value="{{ $customerSplashBg }}" class="w-10 h-10 rounded-lg border-none cursor-pointer shrink-0">
                            <input type="text" x-model="customer.splashBg" class="bg-transparent border-none text-[10px] font-black w-full outline-none">
                        </div>
                    </div>
                </div>

                {{-- Customer Preview --}}
                <div class="pt-6 border-t border-gray-50/80">
                    <p class="text-[9px] font-black text-brand-muted uppercase tracking-widest mb-4 flex items-center gap-2">
                        <svg class="w-3 h-3 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        Live Preview
                    </p>
                    <div class="bg-gray-900 rounded-2xl p-5 flex items-center gap-5 ring-1 ring-white/10">
                        <template x-if="customer.iconPreview || '{{ $customerAppIcon }}'">
                            <div class="w-12 h-12 rounded-xl overflow-hidden bg-white shrink-0 ring-2 ring-white/20">
                                <img :src="customer.iconPreview || '{{ $customerAppIcon }}'" class="w-full h-full object-cover">
                            </div>
                        </template>
                        <template x-if="!customer.iconPreview && !'{{ $customerAppIcon }}'">
                            <div class="w-12 h-12 rounded-xl bg-emerald-600 flex items-center justify-center shrink-0">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            </div>
                        </template>
                        <div>
                            <p class="text-white text-sm font-black" x-text="customer.name || '{{ $customerAppName }}'"></p>
                            <p class="text-gray-400 text-[10px] font-medium">Customer App</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- Footer --}}
        <div class="mt-8 p-5 bg-gradient-to-r from-brand/5 to-transparent rounded-2xl border border-brand/5 flex items-center justify-between">
            <p class="text-[9px] text-brand-muted flex items-center gap-2">
                <svg class="w-3 h-3 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                All branding changes take effect immediately on mobile apps after cache flush.
            </p>
            <button type="submit" form="branding-form"
                class="group relative overflow-hidden px-10 py-3 bg-brand text-white rounded-xl font-black text-xs hover:shadow-2xl hover:shadow-brand/30 transition-all duration-300 flex items-center gap-2 premium-shadow">
                <div class="absolute inset-0 shimmer-bg opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                <svg class="w-4 h-4 text-accent relative z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span class="relative z-10">Save Changes</span>
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    function appsBrandingManager() {
        return {
            driver: {
                name: '{{ $driverAppName }}',
                splashBg: '{{ $driverSplashBg }}',
                iconPreview: null,
                existingIcon: '{{ $driverAppIcon }}',
            },
            customer: {
                name: '{{ $customerAppName }}',
                splashBg: '{{ $customerSplashBg }}',
                iconPreview: null,
                existingIcon: '{{ $customerAppIcon }}',
            },
            init() {},
            previewIcon(event, app) {
                const file = event.target.files[0];
                if (!file) return;
                const reader = new FileReader();
                reader.onload = (e) => {
                    this[app].iconPreview = e.target.result;
                };
                reader.readAsDataURL(file);
            },
        };
    }
</script>
@endpush
