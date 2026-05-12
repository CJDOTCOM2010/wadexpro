@extends('admin.layout')
@section('title', 'Module Hardening')
@section('content')

<div class="mb-8 flex items-center justify-between">
    <div>
        <h2 class="text-2xl font-black text-brand tracking-tight">System Integrations & Hardening</h2>
        <p class="text-brand-muted font-medium mt-1">Manage platform extensions, security policies, and third-party API configurations.</p>
    </div>
    <div class="flex gap-4">
        <button class="px-6 py-3 bg-red-50 text-red-600 font-bold rounded-lg border border-red-100 hover:bg-red-100 transition flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            Trigger Global Lockdown
        </button>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    
    <!-- Active Modules Matrix -->
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white rounded-lg border border-gray-100 shadow-sm p-6">
            <h3 class="text-sm font-black text-brand uppercase tracking-widest mb-6">Core Service Modules</h3>
            
            <div class="space-y-4">
                <!-- Payment Gateway -->
                <div class="p-5 border border-gray-100 rounded-xl flex items-center justify-between hover:border-brand transition">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-surface rounded-lg flex items-center justify-center text-brand">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-brand">Payment Gateway Orchestrator</p>
                            <p class="text-xs text-brand-muted mt-1">Paystack / Stripe failover protocol enabled.</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="flex items-center gap-2">
                            <div class="w-2 h-2 rounded-full bg-green-500 shadow-[0_0_8px_rgba(34,197,94,0.4)]"></div>
                            <span class="text-[10px] font-black uppercase text-green-600 tracking-widest">Active</span>
                        </div>
                        <button class="px-4 py-2 bg-surface text-[10px] font-black uppercase tracking-widest rounded hover:bg-gray-200 transition">Configure</button>
                    </div>
                </div>

                <!-- KYC & Verification -->
                <div class="p-5 border border-gray-100 rounded-xl flex items-center justify-between hover:border-brand transition">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-surface rounded-lg flex items-center justify-center text-brand">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-brand">Identity & KYC Engine</p>
                            <p class="text-xs text-brand-muted mt-1">Automated biometric and document verification via API.</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="flex items-center gap-2">
                            <div class="w-2 h-2 rounded-full bg-green-500 shadow-[0_0_8px_rgba(34,197,94,0.4)]"></div>
                            <span class="text-[10px] font-black uppercase text-green-600 tracking-widest">Active</span>
                        </div>
                        <button class="px-4 py-2 bg-surface text-[10px] font-black uppercase tracking-widest rounded hover:bg-gray-200 transition">Configure</button>
                    </div>
                </div>

                <!-- Map telemetry -->
                <div class="p-5 border border-red-100 bg-red-50/20 rounded-xl flex items-center justify-between transition">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center text-red-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-red-600">Google Maps Telemetry Core</p>
                            <p class="text-xs text-red-500 mt-1">API Quota Exceeded. Automatic fallback to CartoDB active.</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="flex items-center gap-2">
                            <div class="w-2 h-2 rounded-full bg-red-500 animate-pulse"></div>
                            <span class="text-[10px] font-black uppercase text-red-600 tracking-widest">Degraded</span>
                        </div>
                        <button class="px-4 py-2 bg-red-600 text-white text-[10px] font-black uppercase tracking-widest rounded hover:bg-red-700 transition shadow-lg shadow-red-500/20">Resolve</button>
                    </div>
                </div>
                
                <!-- Communication -->
                <div class="p-5 border border-gray-100 rounded-xl flex items-center justify-between hover:border-brand transition">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-surface rounded-lg flex items-center justify-center text-brand">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-brand">Twilio SMS & Voice Mesh</p>
                            <p class="text-xs text-brand-muted mt-1">Cross-platform messaging and OTP delivery.</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="flex items-center gap-2">
                            <div class="w-2 h-2 rounded-full bg-green-500 shadow-[0_0_8px_rgba(34,197,94,0.4)]"></div>
                            <span class="text-[10px] font-black uppercase text-green-600 tracking-widest">Active</span>
                        </div>
                        <button class="px-4 py-2 bg-surface text-[10px] font-black uppercase tracking-widest rounded hover:bg-gray-200 transition">Configure</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Security & Hardening Policies -->
    <div class="space-y-6">
        <div class="bg-brand text-white rounded-lg p-6 relative overflow-hidden group shadow-2xl shadow-brand/20">
            <div class="absolute -right-8 -top-8 w-32 h-32 bg-white/5 rounded-full group-hover:scale-150 transition-transform duration-700"></div>
            
            <h3 class="text-sm font-black uppercase tracking-widest mb-6 relative z-10 flex items-center gap-2">
                <svg class="w-4 h-4 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                Security Posture
            </h3>
            
            <div class="space-y-4 relative z-10">
                <div class="flex items-center justify-between border-b border-white/10 pb-4">
                    <div>
                        <p class="text-xs font-bold">API Encryption (mTLS)</p>
                        <p class="text-[10px] text-white/50 mt-0.5">Enforced on all mobile-backend traffic.</p>
                    </div>
                    <div class="w-10 h-5 bg-accent rounded-full relative shadow-[0_0_10px_rgba(248,184,3,0.3)]">
                        <div class="w-4 h-4 bg-brand rounded-full absolute right-0.5 top-0.5"></div>
                    </div>
                </div>

                <div class="flex items-center justify-between border-b border-white/10 pb-4">
                    <div>
                        <p class="text-xs font-bold">Geo-Fencing Limits</p>
                        <p class="text-[10px] text-white/50 mt-0.5">Blocks out-of-boundary access.</p>
                    </div>
                    <div class="w-10 h-5 bg-accent rounded-full relative shadow-[0_0_10px_rgba(248,184,3,0.3)]">
                        <div class="w-4 h-4 bg-brand rounded-full absolute right-0.5 top-0.5"></div>
                    </div>
                </div>

                <div class="flex items-center justify-between pb-2">
                    <div>
                        <p class="text-xs font-bold">Rate Limiting (WAF)</p>
                        <p class="text-[10px] text-white/50 mt-0.5">Max 120 req/min per IP.</p>
                    </div>
                    <div class="w-10 h-5 bg-accent rounded-full relative shadow-[0_0_10px_rgba(248,184,3,0.3)]">
                        <div class="w-4 h-4 bg-brand rounded-full absolute right-0.5 top-0.5"></div>
                    </div>
                </div>
            </div>
            
            <button class="mt-6 w-full py-3 border border-white/20 text-white font-black text-[10px] uppercase tracking-widest rounded hover:bg-white/10 transition">Run Vulnerability Scan</button>
        </div>

        <div class="bg-white rounded-lg border border-gray-100 shadow-sm p-6">
            <h3 class="text-sm font-black text-brand uppercase tracking-widest mb-4">Audit Logs</h3>
            
            <div class="space-y-3">
                @forelse($auditLogs as $log)
                <div class="flex gap-3">
                    <p class="text-[10px] font-mono text-brand-muted pt-0.5">{{ $log->created_at->format('H:i') }}</p>
                    <div>
                        <p class="text-xs font-bold {{ str_contains(strtolower($log->action), 'fail') || str_contains(strtolower($log->action), 'error') ? 'text-red-500' : 'text-brand' }}">{{ $log->action }}</p>
                        <p class="text-[10px] text-brand-muted">{{ $log->details }}</p>
                    </div>
                </div>
                @empty
                <div class="text-center py-4">
                    <p class="text-xs text-brand-muted font-bold">No recent audit logs available.</p>
                </div>
                @endforelse
            </div>
            
            <button class="mt-4 w-full text-center text-[10px] font-black text-accent uppercase tracking-widest hover:underline">View Full Audit Trail</button>
        </div>
    </div>
</div>

@endsection
