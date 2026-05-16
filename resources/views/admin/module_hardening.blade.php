@extends('admin.layout')
@section('title', 'Module Hardening')
@section('content')

@php $modules = \App\Modules\Admin\Models\Module::orderBy('name')->get(); @endphp

@if(session('error'))
<div class="mb-4 p-3.5 bg-red-50 border border-red-200 rounded-lg flex items-center gap-2.5">
    <svg class="w-4 h-4 text-red-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    <p class="text-sm font-medium text-red-700">{{ session('error') }}</p>
</div>
@endif
@if(session('success'))
<div class="mb-4 p-3.5 bg-green-50 border border-green-200 rounded-lg flex items-center gap-2.5">
    <svg class="w-4 h-4 text-green-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    <p class="text-sm font-medium text-green-700">{{ session('success') }}</p>
</div>
@endif

<div class="max-w-6xl mx-auto">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-black text-brand tracking-tight">System Integrations & Hardening</h2>
            <p class="text-sm text-brand-muted font-medium mt-0.5">Manage platform extensions, security policies, and third-party API configurations.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Modules --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white border border-gray-100 rounded-xl p-6">
                <h3 class="text-sm font-bold text-brand mb-5">Core Service Modules</h3>
                <div class="space-y-3">
                    @forelse($modules as $module)
                    <div class="flex items-center justify-between p-4 border border-gray-100 rounded-lg hover:border-accent/40 transition-colors">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-10 h-10 rounded-lg flex items-center justify-center shrink-0 {{ $module->is_enabled ? 'bg-brand text-accent' : 'bg-surface text-gray-400' }}">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 4a2 2 0 114 0v1a2 2 0 01-2 2H3a2 2 0 01-2-2V4a2 2 0 114 0v1a2 2 0 012 2h4a2 2 0 012-2V4z"/></svg>
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-bold text-brand truncate">{{ $module->name }}</p>
                                <p class="text-[10px] text-brand-muted truncate">{{ $module->description ?? 'No description' }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 shrink-0 ml-2">
                            <span class="flex items-center gap-1.5 text-[10px] font-bold {{ $module->is_enabled ? 'text-green-600' : 'text-gray-400' }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $module->is_enabled ? 'bg-green-500' : 'bg-gray-300' }}"></span>
                                {{ $module->is_enabled ? 'Active' : 'Disabled' }}
                            </span>
                            <form action="{{ route('orchestrator.infrastructure.modules.toggle', $module->id) }}" method="POST" class="inline">
                                @csrf @method('PATCH')
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" onchange="this.form.submit()" class="sr-only peer" {{ $module->is_enabled ? 'checked' : '' }}>
                                    <div class="w-9 h-5 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[0.5px] after:left-[0.5px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-green-500"></div>
                                </label>
                            </form>
                        </div>
                    </div>
                    @empty
                    <div class="flex flex-col items-center justify-center py-10 text-brand-muted">
                        <svg class="w-12 h-12 mb-3 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 4a2 2 0 114 0v1a2 2 0 01-2 2H3a2 2 0 01-2-2V4a2 2 0 114 0v1a2 2 0 012 2h4a2 2 0 012-2V4z"/></svg>
                        <p class="text-sm font-bold">No modules configured</p>
                    </div>
                    @endforelse
                </div>
            </div>

            {{-- Third Party Integrations --}}
            <div class="bg-white border border-gray-100 rounded-xl p-6">
                <h3 class="text-sm font-bold text-brand mb-5">Third-Party Integrations</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @foreach([
                        ['Paystack', 'Payment processing', 'bg-blue-50 text-blue-600'],
                        ['Twilio', 'SMS & Voice', 'bg-emerald-50 text-emerald-600'],
                        ['Google Maps', 'Geocoding & Maps', 'bg-red-50 text-red-600'],
                        ['Firebase', 'Push notifications', 'bg-amber-50 text-amber-600'],
                        ['Flutterwave', 'Payment gateway', 'bg-purple-50 text-purple-600'],
                        ['Mapbox', 'Alternative maps', 'bg-brand/5 text-brand'],
                    ] as [$name, $desc, $color])
                    <div class="flex items-center justify-between p-3.5 bg-surface/50 rounded-lg border border-gray-100">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-8 h-8 rounded-lg flex items-center justify-center text-xs font-bold shrink-0 {{ $color }}">{{ substr($name, 0, 2) }}</div>
                            <div class="min-w-0">
                                <p class="text-xs font-bold text-brand truncate">{{ $name }}</p>
                                <p class="text-[9px] text-brand-muted truncate">{{ $desc }}</p>
                            </div>
                        </div>
                        <span class="flex items-center gap-1 text-[9px] font-bold text-green-600 shrink-0 ml-2"><span class="w-1 h-1 bg-green-500 rounded-full"></span> Live</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Right: Security & Audit --}}
        <div class="space-y-6">
            {{-- Security Posture --}}
            <div class="bg-brand rounded-xl p-6 text-white">
                <div class="flex items-center gap-2 mb-5">
                    <svg class="w-4 h-4 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    <h3 class="text-sm font-bold">Security Posture</h3>
                </div>
                <div class="space-y-4">
                    @foreach([
                        ['API Encryption (mTLS)', 'Enforced on all mobile-backend traffic.'],
                        ['Geo-Fencing', 'Blocks out-of-boundary access.'],
                        ['Rate Limiting', 'Max requests per IP enforcement.'],
                    ] as [$label, $desc])
                    <div class="flex items-center justify-between border-b border-white/10 pb-3 last:border-b-0 last:pb-0">
                        <div>
                            <p class="text-xs font-bold">{{ $label }}</p>
                            <p class="text-[10px] text-white/50">{{ $desc }}</p>
                        </div>
                        <span class="px-2 py-0.5 bg-accent/20 text-accent text-[9px] font-bold rounded">On</span>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Audit Logs --}}
            <div class="bg-white border border-gray-100 rounded-xl p-6">
                <h3 class="text-sm font-bold text-brand mb-5">Audit Logs</h3>
                <div class="space-y-3">
                    @forelse($auditLogs as $log)
                    <div class="flex gap-2.5">
                        <span class="text-[10px] font-mono text-brand-muted shrink-0 w-10 pt-0.5">{{ $log->created_at->format('H:i') }}</span>
                        <div>
                            <p class="text-xs font-bold {{ str_contains(strtolower($log->action ?? ''), 'fail') || str_contains(strtolower($log->action ?? ''), 'error') ? 'text-red-500' : 'text-brand' }}">{{ $log->action }}</p>
                            <p class="text-[10px] text-brand-muted">{{ $log->details }}</p>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-6 text-brand-muted">
                        <svg class="w-8 h-8 mx-auto mb-2 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <p class="text-xs font-bold">No recent audit logs</p>
                    </div>
                    @endforelse
                </div>
                <a href="{{ route('orchestrator.error_monitoring') }}" class="mt-4 block text-center text-[10px] font-bold text-accent hover:underline">View Full Audit Trail →</a>
            </div>
        </div>
    </div>
</div>
@endsection