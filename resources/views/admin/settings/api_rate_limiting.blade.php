@extends('admin.layout')
@section('title', 'API Rate Limiting')

@php
$settings = $settings ?? collect();
$rateLimit = $settings->get('api_rate_limit')?->castValue() ?? 60;
$rateBurst = $settings->get('api_rate_limit_burst')?->castValue() ?? 100;
$debugMode = $settings->get('api_debug_mode')?->castValue() ?? false;
$maxConcurrent = $settings->get('max_concurrent_requests')?->castValue() ?? 10;
$webhookRetries = $settings->get('webhook_retry_attempts')?->castValue() ?? 3;
$webhookDelay = $settings->get('webhook_retry_delay')?->castValue() ?? 10;
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
                    <span>API Rate Limiting</span>
                </div>
                <h2 class="text-3xl font-black text-brand tracking-tight">API Rate Limiting</h2>
                <p class="text-brand-muted font-medium mt-1">Control API throughput, throttling, and webhook behavior.</p>
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
            <!-- Rate Limits -->
            <div class="bg-white rounded-2xl border border-gray-100 p-8 shadow-sm">
                <div class="flex items-center gap-4 mb-8">
                    <div class="w-12 h-12 bg-brand/10 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-black text-brand">Throttle Configuration</h3>
                        <p class="text-xs text-brand-muted">API request limits and burst allowances.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-bold text-brand mb-2">API Rate Limit (requests/minute)</label>
                        <input type="number" name="settings[api_rate_limit]" value="{{ $rateLimit }}" min="10" max="10000" class="w-full bg-surface border border-gray-100 rounded-lg px-4 py-3 text-sm font-medium text-brand outline-none focus:ring-2 focus:ring-accent/20">
                        <p class="text-[10px] text-brand-muted mt-1">Max requests allowed per minute per API key</p>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-brand mb-2">Burst Limit</label>
                        <input type="number" name="settings[api_rate_limit_burst]" value="{{ $rateBurst }}" min="10" max="50000" class="w-full bg-surface border border-gray-100 rounded-lg px-4 py-3 text-sm font-medium text-brand outline-none focus:ring-2 focus:ring-accent/20">
                        <p class="text-[10px] text-brand-muted mt-1">Max burst requests before throttling kicks in</p>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-brand mb-2">Max Concurrent Requests (per user)</label>
                        <input type="number" name="settings[max_concurrent_requests]" value="{{ $maxConcurrent }}" min="1" max="100" class="w-full bg-surface border border-gray-100 rounded-lg px-4 py-3 text-sm font-medium text-brand outline-none focus:ring-2 focus:ring-accent/20">
                    </div>
                    <div>
                        <label class="flex items-center gap-4 p-4 bg-surface rounded-lg cursor-pointer hover:bg-brand/5 transition-colors {{ $debugMode ? 'ring-2 ring-accent' : '' }}">
                            <input type="hidden" name="settings[api_debug_mode]" value="false">
                            <input type="checkbox" name="settings[api_debug_mode]" value="true" {{ $debugMode ? 'checked' : '' }} class="w-5 h-5 text-accent rounded border-gray-300 focus:ring-accent">
                            <div>
                                <span class="text-sm font-bold text-brand">API Debug Mode</span>
                                <p class="text-[10px] text-brand-muted">Return detailed error messages (disable in production)</p>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Webhooks -->
            <div class="bg-white rounded-2xl border border-gray-100 p-8 shadow-sm">
                <div class="flex items-center gap-4 mb-8">
                    <div class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-black text-brand">Webhook Settings</h3>
                        <p class="text-xs text-brand-muted">Retry and delivery behavior for outgoing webhooks.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-bold text-brand mb-2">Webhook Retry Attempts</label>
                        <input type="number" name="settings[webhook_retry_attempts]" value="{{ $webhookRetries }}" min="0" max="20" class="w-full bg-surface border border-gray-100 rounded-lg px-4 py-3 text-sm font-medium text-brand outline-none focus:ring-2 focus:ring-accent/20">
                        <p class="text-[10px] text-brand-muted mt-1">Number of retry attempts for failed webhooks</p>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-brand mb-2">Webhook Retry Delay (seconds)</label>
                        <input type="number" name="settings[webhook_retry_delay]" value="{{ $webhookDelay }}" min="1" max="3600" class="w-full bg-surface border border-gray-100 rounded-lg px-4 py-3 text-sm font-medium text-brand outline-none focus:ring-2 focus:ring-accent/20">
                        <p class="text-[10px] text-brand-muted mt-1">Delay between retry attempts</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-8 flex justify-end gap-4">
            <a href="{{ route('orchestrator.settings') }}" class="px-8 py-3.5 bg-surface text-brand-muted hover:bg-gray-100 rounded-lg text-xs font-bold transition-all">Cancel</a>
            <button type="submit" class="px-10 py-3.5 bg-brand text-white hover:bg-brand-light rounded-lg text-xs font-bold transition-all flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Save API Settings
            </button>
        </div>
    </form>
</div>
@endsection