@extends('admin.layout')
@section('title', 'Security Settings')

@php
$settings = $settings ?? collect();
$minLength = $settings->get('password_min_length')?->castValue() ?? 8;
$requireSpecial = $settings->get('password_require_special')?->castValue() ?? true;
$requireNumbers = $settings->get('password_require_numbers')?->castValue() ?? true;
$expiryDays = $settings->get('password_expiry_days')?->castValue() ?? 0;
$sessionTimeout = $settings->get('session_timeout_minutes')?->castValue() ?? 120;
$maxAttempts = $settings->get('max_login_attempts')?->castValue() ?? 5;
$lockoutMinutes = $settings->get('account_lockout_minutes')?->castValue() ?? 30;
$twoFactor = $settings->get('two_factor_auth')?->castValue() ?? false;
$ipWhitelist = $settings->get('ip_whitelist')?->castValue() ?? '';
$ipWhitelistEnabled = $settings->get('ip_whitelist_enabled')?->castValue() ?? false;
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
                    <span>Security</span>
                </div>
                <h2 class="text-3xl font-black text-brand tracking-tight">Security & Access Control</h2>
                <p class="text-brand-muted font-medium mt-1">Password policy, session management, and IP restrictions.</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('orchestrator.settings') }}" class="bg-surface text-brand-muted hover:bg-gray-100 px-6 py-3 rounded-lg text-xs font-bold transition-all flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg> Back
                </a>
                <button type="submit" class="bg-brand text-white hover:bg-brand-light px-8 py-3 rounded-lg text-xs font-bold transition-all flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Save Security Settings
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
            <!-- Password Policy -->
            <div class="bg-white rounded-2xl border border-gray-100 p-8 shadow-sm">
                <div class="flex items-center gap-4 mb-8">
                    <div class="w-12 h-12 bg-red-50 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-black text-brand">Password Policy</h3>
                        <p class="text-xs text-brand-muted">Enforce password strength and rotation rules across all accounts.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-bold text-brand mb-2">Minimum Password Length</label>
                        <input type="number" name="settings[password_min_length]" value="{{ $minLength }}" min="4" max="128" class="w-full bg-surface border border-gray-100 rounded-lg px-4 py-3 text-sm font-medium text-brand outline-none focus:ring-2 focus:ring-accent/20">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-brand mb-2">Password Expiry (days)</label>
                        <input type="number" name="settings[password_expiry_days]" value="{{ $expiryDays }}" min="0" max="365" class="w-full bg-surface border border-gray-100 rounded-lg px-4 py-3 text-sm font-medium text-brand outline-none focus:ring-2 focus:ring-accent/20">
                        <p class="text-[10px] text-brand-muted mt-1">Set to 0 for no expiry</p>
                    </div>
                    <div>
                        <label class="flex items-center gap-4 p-4 bg-surface rounded-lg cursor-pointer hover:bg-red-50 transition-colors {{ $requireSpecial ? 'ring-2 ring-red-500' : '' }}">
                            <input type="hidden" name="settings[password_require_special]" value="false">
                            <input type="checkbox" name="settings[password_require_special]" value="true" {{ $requireSpecial ? 'checked' : '' }} class="w-5 h-5 text-red-600 rounded border-gray-300 focus:ring-red-500">
                            <div>
                                <span class="text-sm font-bold text-brand">Require Special Characters</span>
                                <p class="text-[10px] text-brand-muted">e.g., !@#$%^&*</p>
                            </div>
                        </label>
                    </div>
                    <div>
                        <label class="flex items-center gap-4 p-4 bg-surface rounded-lg cursor-pointer hover:bg-red-50 transition-colors {{ $requireNumbers ? 'ring-2 ring-red-500' : '' }}">
                            <input type="hidden" name="settings[password_require_numbers]" value="false">
                            <input type="checkbox" name="settings[password_require_numbers]" value="true" {{ $requireNumbers ? 'checked' : '' }} class="w-5 h-5 text-red-600 rounded border-gray-300 focus:ring-red-500">
                            <div>
                                <span class="text-sm font-bold text-brand">Require Numbers</span>
                                <p class="text-[10px] text-brand-muted">e.g., 0-9</p>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Session & Login -->
            <div class="bg-white rounded-2xl border border-gray-100 p-8 shadow-sm">
                <div class="flex items-center gap-4 mb-8">
                    <div class="w-12 h-12 bg-amber-50 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-black text-brand">Session & Login Security</h3>
                        <p class="text-xs text-brand-muted">Session timeouts, brute-force protection, and 2FA.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-bold text-brand mb-2">Session Timeout (minutes)</label>
                        <input type="number" name="settings[session_timeout_minutes]" value="{{ $sessionTimeout }}" min="5" max="1440" class="w-full bg-surface border border-gray-100 rounded-lg px-4 py-3 text-sm font-medium text-brand outline-none focus:ring-2 focus:ring-accent/20">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-brand mb-2">Max Login Attempts</label>
                        <input type="number" name="settings[max_login_attempts]" value="{{ $maxAttempts }}" min="3" max="50" class="w-full bg-surface border border-gray-100 rounded-lg px-4 py-3 text-sm font-medium text-brand outline-none focus:ring-2 focus:ring-accent/20">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-brand mb-2">Account Lockout Duration (minutes)</label>
                        <input type="number" name="settings[account_lockout_minutes]" value="{{ $lockoutMinutes }}" min="1" max="1440" class="w-full bg-surface border border-gray-100 rounded-lg px-4 py-3 text-sm font-medium text-brand outline-none focus:ring-2 focus:ring-accent/20">
                    </div>
                    <div>
                        <label class="flex items-center gap-4 p-4 bg-surface rounded-lg cursor-pointer hover:bg-amber-50 transition-colors {{ $twoFactor ? 'ring-2 ring-amber-500' : '' }}">
                            <input type="hidden" name="settings[two_factor_auth]" value="false">
                            <input type="checkbox" name="settings[two_factor_auth]" value="true" {{ $twoFactor ? 'checked' : '' }} class="w-5 h-5 text-amber-600 rounded border-gray-300 focus:ring-amber-500">
                            <div>
                                <span class="text-sm font-bold text-brand">Two-Factor Authentication</span>
                                <p class="text-[10px] text-brand-muted">Require 2FA for all admin accounts</p>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- IP Whitelist -->
            <div class="bg-white rounded-2xl border border-gray-100 p-8 shadow-sm">
                <div class="flex items-center gap-4 mb-8">
                    <div class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-black text-brand">IP Whitelist</h3>
                        <p class="text-xs text-brand-muted">Restrict admin access to trusted IP addresses.</p>
                    </div>
                </div>

                <div class="space-y-4">
                    <label class="flex items-center gap-4 p-4 bg-surface rounded-lg cursor-pointer hover:bg-blue-50 transition-colors {{ $ipWhitelistEnabled ? 'ring-2 ring-blue-500' : '' }}">
                        <input type="hidden" name="settings[ip_whitelist_enabled]" value="false">
                        <input type="checkbox" name="settings[ip_whitelist_enabled]" value="true" {{ $ipWhitelistEnabled ? 'checked' : '' }} class="w-5 h-5 text-blue-600 rounded border-gray-300 focus:ring-blue-500">
                        <div>
                            <span class="text-sm font-bold text-brand">Enable IP Whitelist</span>
                            <p class="text-[10px] text-brand-muted">Only allow access from listed IP addresses</p>
                        </div>
                    </label>

                    <div>
                        <label class="block text-xs font-bold text-brand mb-2">Allowed IP Addresses</label>
                        <textarea name="settings[ip_whitelist]" rows="4" class="w-full bg-surface border border-gray-100 rounded-lg px-4 py-3 text-sm font-mono text-brand outline-none focus:ring-2 focus:ring-accent/20" placeholder="One IP per line&#10;192.168.1.0/24&#10;10.0.0.1">{{ $ipWhitelist }}</textarea>
                        <p class="text-[10px] text-brand-muted mt-1">Enter one IP address or CIDR range per line</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-8 flex justify-end gap-4">
            <a href="{{ route('orchestrator.settings') }}" class="px-8 py-3.5 bg-surface text-brand-muted hover:bg-gray-100 rounded-lg text-xs font-bold transition-all">Cancel</a>
            <button type="submit" class="px-10 py-3.5 bg-brand text-white hover:bg-brand-light rounded-lg text-xs font-bold transition-all flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Save Security Settings
            </button>
        </div>
    </form>
</div>
@endsection