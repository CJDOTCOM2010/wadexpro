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

@section('content')
<div class="p-8 lg:p-12 max-w-5xl mx-auto">
    <form method="POST" action="{{ route('orchestrator.settings.update') }}" enctype="multipart/form-data">
        @csrf

        <div class="flex items-center justify-between mb-12">
            <div>
                <div class="flex items-center gap-2 text-[10px] font-black text-accent uppercase tracking-[0.2em] mb-2">
                    <a href="{{ route('orchestrator.settings') }}" class="hover:text-brand transition-colors">Settings Hub</a>
                    <span class="text-gray-300">/</span>
                    <span>Apps Branding</span>
                </div>
                <h2 class="text-3xl font-black text-brand tracking-tight">Apps Branding</h2>
                <p class="text-brand-muted font-medium mt-1">Configure app icons and splash screens for Driver and Customer apps.</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('orchestrator.settings') }}" class="bg-surface text-brand-muted hover:bg-gray-100 px-6 py-3 rounded-lg text-xs font-bold transition-all flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg> Back
                </a>
                <button type="submit" class="bg-brand text-white hover:bg-brand-light px-8 py-3 rounded-lg text-xs font-bold transition-all flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Save Settings
                </button>
            </div>
        </div>

        @if(session('success'))
        <div class="mb-8 p-4 bg-green-50 border border-green-200 rounded-xl flex items-center gap-3">
            <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <p class="text-sm font-medium text-green-700">{{ session('success') }}</p>
        </div>
        @endif

        <div class="space-y-8">
            <!-- Driver App -->
            <div class="bg-white rounded-2xl border border-gray-100 p-8 shadow-sm">
                <div class="flex items-center gap-4 mb-8">
                    <div class="w-12 h-12 bg-cyan-50 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-black text-brand">Driver App</h3>
                        <p class="text-xs text-brand-muted">App icon and splash for the Driver application.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <label class="block text-xs font-bold text-brand mb-3">App Name</label>
                        <input type="text" name="settings[driver_app_display_name]" value="{{ $driverAppName }}" class="w-full bg-surface border border-gray-100 rounded-lg px-4 py-3 text-sm font-medium text-brand outline-none focus:ring-2 focus:ring-accent/20">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-brand mb-3">App Icon</label>
                        <div class="flex items-center gap-4">
                            @if($driverAppIcon)
                            <div class="w-16 h-16 rounded-xl border border-gray-200 overflow-hidden bg-gray-50">
                                <img src="{{ $driverAppIcon }}" alt="Driver App Icon" class="w-full h-full object-cover">
                            </div>
                            @else
                            <div class="w-16 h-16 rounded-xl border border-gray-200 bg-gray-50 flex items-center justify-center">
                                <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                            </div>
                            @endif
                            <div class="flex-1">
                                <input type="file" name="driver_app_icon" accept="image/png" class="block w-full text-xs text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-brand/10 file:text-brand hover:file:bg-brand/20">
                                <p class="text-[10px] text-brand-muted mt-1">PNG, 1024x1024 recommended</p>
                            </div>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-brand mb-3">Splash Background Color</label>
                        <div class="flex items-center gap-3">
                            <input type="color" name="settings[driver_splash_background]" value="{{ $driverSplashBg }}" class="w-12 h-12 rounded-lg border border-gray-200 cursor-pointer">
                            <input type="text" name="settings[driver_splash_background]" value="{{ $driverSplashBg }}" class="flex-1 bg-surface border border-gray-100 rounded-lg px-4 py-2 text-sm font-mono text-brand">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Customer App -->
            <div class="bg-white rounded-2xl border border-gray-100 p-8 shadow-sm">
                <div class="flex items-center gap-4 mb-8">
                    <div class="w-12 h-12 bg-emerald-50 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-black text-brand">Customer (Rider) App</h3>
                        <p class="text-xs text-brand-muted">App icon and splash for the Customer application.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <label class="block text-xs font-bold text-brand mb-3">App Name</label>
                        <input type="text" name="settings[customer_app_display_name]" value="{{ $customerAppName }}" class="w-full bg-surface border border-gray-100 rounded-lg px-4 py-3 text-sm font-medium text-brand outline-none focus:ring-2 focus:ring-accent/20">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-brand mb-3">App Icon</label>
                        <div class="flex items-center gap-4">
                            @if($customerAppIcon)
                            <div class="w-16 h-16 rounded-xl border border-gray-200 overflow-hidden bg-gray-50">
                                <img src="{{ $customerAppIcon }}" alt="Customer App Icon" class="w-full h-full object-cover">
                            </div>
                            @else
                            <div class="w-16 h-16 rounded-xl border border-gray-200 bg-gray-50 flex items-center justify-center">
                                <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            </div>
                            @endif
                            <div class="flex-1">
                                <input type="file" name="customer_app_icon" accept="image/png" class="block w-full text-xs text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-brand/10 file:text-brand hover:file:bg-brand/20">
                                <p class="text-[10px] text-brand-muted mt-1">PNG, 1024x1024 recommended</p>
                            </div>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-brand mb-3">Splash Background Color</label>
                        <div class="flex items-center gap-3">
                            <input type="color" name="settings[customer_splash_background]" value="{{ $customerSplashBg }}" class="w-12 h-12 rounded-lg border border-gray-200 cursor-pointer">
                            <input type="text" name="settings[customer_splash_background]" value="{{ $customerSplashBg }}" class="flex-1 bg-surface border border-gray-100 rounded-lg px-4 py-2 text-sm font-mono text-brand">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection