@extends('admin.layout')
@section('title', 'API Configuration')

@php
$settings = $settings ?? collect();
$driverBaseUrl = $settings->get('api_driver_base_url')?->value ?? 'https://wadexpro-4rexnj1k.on-forge.com/api/v1';
$driverSocketUrl = $settings->get('api_driver_socket_url')?->value ?? 'https://wadexpro-4rexnj1k.on-forge.com:3000';
$customerBaseUrl = $settings->get('api_customer_base_url')?->value ?? 'https://wadexpro-4rexnj1k.on-forge.com/api/v1';
$customerSocketUrl = $settings->get('api_customer_socket_url')?->value ?? 'https://wadexpro-4rexnj1k.on-forge.com:3000';
$apiTimeout = $settings->get('api_platform_timeout')?->castValue() ?? 30;
$retryAttempts = $settings->get('api_platform_retry_attempts')?->castValue() ?? 3;
@endphp

@section('content')
<div class="p-8 lg:p-12 max-w-5xl mx-auto">
    <form method="POST" action="{{ route('orchestrator.settings.update') }}">
        @csrf

        <div class="flex items-center justify-between mb-12">
            <div>
                <div class="flex items-center gap-2 text-[10px] font-black text-accent uppercase tracking-[0.2em] mb-2">
                    <a href="{{ route('orchestrator.settings') }}" class="hover:text-brand transition-colors">Settings Hub</a>
                    <span class="text-gray-300">/</span>
                    <span>API Configuration</span>
                </div>
                <h2 class="text-3xl font-black text-brand tracking-tight">API Configuration</h2>
                <p class="text-brand-muted font-medium mt-1">Configure mobile app API endpoints and socket connections.</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('orchestrator.settings') }}" class="bg-surface text-brand-muted hover:bg-gray-100 px-6 py-3 rounded-lg text-xs font-bold transition-all flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg> Back
                </a>
                <button type="submit" class="bg-brand text-white hover:bg-brand-light px-8 py-3 rounded-lg text-xs font-bold transition-all flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Save API Settings
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
            <!-- Driver App API -->
            <div class="bg-white rounded-2xl border border-gray-100 p-8 shadow-sm">
                <div class="flex items-center gap-4 mb-8">
                    <div class="w-12 h-12 bg-cyan-50 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-black text-brand">Driver App API</h3>
                        <p class="text-xs text-brand-muted">API endpoints for the Driver mobile application.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <label class="block text-xs font-bold text-brand mb-2">API Base URL</label>
                        <input type="url" name="settings[api_driver_base_url]" value="{{ $driverBaseUrl }}" placeholder="https://api.example.com/api/v1" class="w-full bg-surface border border-gray-100 rounded-lg px-4 py-3 text-sm font-medium text-brand outline-none focus:ring-2 focus:ring-accent/20">
                        <p class="text-[10px] text-brand-muted mt-1">REST API base URL for the Driver app</p>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-brand mb-2">WebSocket URL</label>
                        <input type="url" name="settings[api_driver_socket_url]" value="{{ $driverSocketUrl }}" placeholder="https://socket.example.com:3000" class="w-full bg-surface border border-gray-100 rounded-lg px-4 py-3 text-sm font-medium text-brand outline-none focus:ring-2 focus:ring-accent/20">
                        <p class="text-[10px] text-brand-muted mt-1">Real-time WebSocket server URL for driver communications</p>
                    </div>
                </div>
            </div>

            <!-- Customer App API -->
            <div class="bg-white rounded-2xl border border-gray-100 p-8 shadow-sm">
                <div class="flex items-center gap-4 mb-8">
                    <div class="w-12 h-12 bg-emerald-50 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-black text-brand">Customer App API</h3>
                        <p class="text-xs text-brand-muted">API endpoints for the Customer (Rider) mobile application.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <label class="block text-xs font-bold text-brand mb-2">API Base URL</label>
                        <input type="url" name="settings[api_customer_base_url]" value="{{ $customerBaseUrl }}" placeholder="https://api.example.com/api/v1" class="w-full bg-surface border border-gray-100 rounded-lg px-4 py-3 text-sm font-medium text-brand outline-none focus:ring-2 focus:ring-accent/20">
                        <p class="text-[10px] text-brand-muted mt-1">REST API base URL for the Customer app</p>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-brand mb-2">WebSocket URL</label>
                        <input type="url" name="settings[api_customer_socket_url]" value="{{ $customerSocketUrl }}" placeholder="https://socket.example.com:3000" class="w-full bg-surface border border-gray-100 rounded-lg px-4 py-3 text-sm font-medium text-brand outline-none focus:ring-2 focus:ring-accent/20">
                        <p class="text-[10px] text-brand-muted mt-1">Real-time WebSocket server URL for customer communications</p>
                    </div>
                </div>
            </div>

            <!-- Platform Settings -->
            <div class="bg-white rounded-2xl border border-gray-100 p-8 shadow-sm">
                <div class="flex items-center gap-4 mb-8">
                    <div class="w-12 h-12 bg-purple-50 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-black text-brand">Platform Configuration</h3>
                        <p class="text-xs text-brand-muted">Global API behavior settings for all mobile apps.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-bold text-brand mb-2">Request Timeout (seconds)</label>
                        <input type="number" name="settings[api_platform_timeout]" value="{{ $apiTimeout }}" min="5" max="120" class="w-full bg-surface border border-gray-100 rounded-lg px-4 py-3 text-sm font-medium text-brand outline-none focus:ring-2 focus:ring-accent/20">
                        <p class="text-[10px] text-brand-muted mt-1">Default timeout for API requests</p>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-brand mb-2">Retry Attempts</label>
                        <input type="number" name="settings[api_platform_retry_attempts]" value="{{ $retryAttempts }}" min="0" max="10" class="w-full bg-surface border border-gray-100 rounded-lg px-4 py-3 text-sm font-medium text-brand outline-none focus:ring-2 focus:ring-accent/20">
                        <p class="text-[10px] text-brand-muted mt-1">Number of retry attempts on failed requests</p>
                    </div>
                </div>
            </div>

            <!-- Info Box -->
            <div class="bg-cyan-50 border border-cyan-100 rounded-xl p-6">
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 bg-cyan-100 rounded-lg flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-cyan-800">Dynamic API Configuration</h4>
                        <p class="text-xs text-cyan-700 mt-1">Mobile apps will fetch these settings from the public API endpoint on startup. Changes here are automatically propagated to all connected devices within 5 minutes.</p>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection