@extends('admin.layout')
@section('title', 'Security & Fraud Operations')
@section('content')

<div x-data="securityManager()" class="max-w-6xl mx-auto">

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

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-black text-brand tracking-tight">Security & Fraud Operations</h2>
            <p class="text-sm text-brand-muted font-medium mt-0.5">Monitor suspicious activities, manage blocked devices, and review access audits.</p>
        </div>
        <button @click="showBlockModal = true" class="px-5 py-2.5 bg-red-600 text-white rounded-lg text-xs font-bold hover:bg-red-700 transition-colors flex items-center gap-2 shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
            Block Device
        </button>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="bg-white border border-gray-100 rounded-xl p-4 flex items-center gap-3.5">
            <div class="w-10 h-10 bg-amber-50 rounded-lg flex items-center justify-center text-amber-600 shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <div>
                <p class="text-lg font-black text-brand">{{ number_format($stats['open_alerts'] ?? 0) }}</p>
                <p class="text-[10px] font-bold text-brand-muted uppercase tracking-wider">Open Fraud Alerts</p>
            </div>
        </div>
        <div class="bg-white border border-gray-100 rounded-xl p-4 flex items-center gap-3.5">
            <div class="w-10 h-10 bg-red-50 rounded-lg flex items-center justify-center text-red-600 shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            </div>
            <div>
                <p class="text-lg font-black text-red-600">{{ number_format($stats['high_risk'] ?? 0) }}</p>
                <p class="text-[10px] font-bold text-brand-muted uppercase tracking-wider">Critical / High Risk</p>
            </div>
        </div>
        <div class="bg-white border border-gray-100 rounded-xl p-4 flex items-center gap-3.5">
            <div class="w-10 h-10 bg-gray-50 rounded-lg flex items-center justify-center text-gray-600 shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
            </div>
            <div>
                <p class="text-lg font-black text-brand">{{ number_format($stats['blocked'] ?? 0) }}</p>
                <p class="text-[10px] font-bold text-brand-muted uppercase tracking-wider">Blocked Devices</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Left Column --}}
        <div class="space-y-6">
            {{-- Fraud Alerts --}}
            <div class="bg-white border border-gray-100 rounded-xl overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 bg-surface/20">
                    <h3 class="text-sm font-bold text-brand">Active Fraud Alerts</h3>
                </div>
                <div class="divide-y divide-gray-50">
                    @forelse($fraudAlerts as $alert)
                    <div class="px-5 py-4 hover:bg-surface/20 transition-colors">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-2 mb-0.5">
                                    <span class="text-sm font-bold text-brand truncate">{{ $alert->user->name ?? 'Unknown' }}</span>
                                    <span class="px-1.5 py-0.5 rounded text-[9px] font-bold uppercase {{ $alert->risk_badge }}">{{ $alert->risk_level }}</span>
                                </div>
                                <p class="text-xs text-brand-muted">{{ ucwords(str_replace('_', ' ', $alert->event_type)) }}</p>
                                <div class="flex items-center gap-3 mt-1.5 text-[10px] text-brand-muted">
                                    <span class="font-mono">ID: {{ substr($alert->user_id, 0, 8) }}</span>
                                    <span>{{ $alert->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                            <button @click="openResolve('{{ $alert->id }}')" class="px-3 py-1.5 bg-accent/10 text-accent rounded-lg text-[10px] font-bold hover:bg-accent hover:text-brand transition-colors shrink-0">Review</button>
                        </div>
                    </div>
                    @empty
                    <div class="flex flex-col items-center justify-center py-12 text-brand-muted">
                        <svg class="w-12 h-12 mb-3 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <p class="text-sm font-bold">No active fraud alerts</p>
                    </div>
                    @endforelse
                </div>
                @if($fraudAlerts->hasPages())
                <div class="px-5 py-3 border-t border-gray-100 bg-surface/20">{{ $fraudAlerts->appends(request()->except('alerts_page'))->links() }}</div>
                @endif
            </div>

            {{-- Blocked Devices --}}
            <div class="bg-white border border-gray-100 rounded-xl overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 bg-surface/20">
                    <h3 class="text-sm font-bold text-brand">Blocked Devices</h3>
                </div>
                <div class="divide-y divide-gray-50">
                    @forelse($blockedDevices as $device)
                    <div class="px-5 py-4 hover:bg-surface/20 transition-colors {{ !$device->is_active ? 'opacity-50' : '' }}">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-2 mb-0.5">
                                    <span class="text-xs font-bold font-mono text-brand">{{ substr($device->device_fingerprint, 0, 20) }}...</span>
                                    @if(!$device->is_active)<span class="px-1.5 py-0.5 bg-green-50 text-green-600 text-[9px] font-bold rounded">Unblocked</span>@endif
                                </div>
                                <p class="text-xs text-brand-muted">{{ Str::limit($device->reason, 50) }}</p>
                                <p class="text-[10px] text-brand-muted mt-1">Blocked {{ $device->blocked_at->format('M d, Y') }} by {{ $device->blockedBy->name ?? 'System' }}</p>
                            </div>
                            @if($device->is_active)
                            <button @click="confirmUnblock('{{ $device->id }}')" class="px-3 py-1.5 bg-green-50 text-green-600 rounded-lg text-[10px] font-bold hover:bg-green-600 hover:text-white transition-colors shrink-0">Unblock</button>
                            @endif
                        </div>
                    </div>
                    @empty
                    <div class="flex flex-col items-center justify-center py-12 text-brand-muted">
                        <svg class="w-12 h-12 mb-3 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        <p class="text-sm font-bold">No blocked devices</p>
                    </div>
                    @endforelse
                </div>
                @if($blockedDevices->hasPages())
                <div class="px-5 py-3 border-t border-gray-100 bg-surface/20">{{ $blockedDevices->appends(request()->except('devices_page'))->links() }}</div>
                @endif
            </div>
        </div>

        {{-- Right: Access Audit Log --}}
        <div class="bg-brand rounded-xl overflow-hidden flex flex-col">
            <div class="px-5 py-4 border-b border-white/10">
                <h3 class="text-sm font-bold text-white">System Access Audit</h3>
                <p class="text-[10px] text-white/40 font-bold mt-0.5">Live entry/exit telemetry</p>
            </div>
            <div class="overflow-y-auto p-4 space-y-2 flex-1" style="max-height: 600px;">
                @forelse($auditLogs as $log)
                <div class="flex items-start gap-3 p-3 rounded-lg transition-colors {{ str_contains($log->event, 'failed') ? 'bg-red-500/10' : 'bg-white/5 hover:bg-white/10' }}">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0 {{ str_contains($log->event, 'failed') ? 'bg-red-500/20 text-red-400' : 'bg-white/10 text-white/60' }}">
                        @if(str_contains($log->event, 'login'))
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                        @else
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        @endif
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-bold {{ str_contains($log->event, 'failed') ? 'text-red-400' : 'text-white' }}">{{ ucwords(str_replace('_', ' ', $log->event)) }}</span>
                            @if($log->user)<span class="px-1.5 py-0.5 bg-white/10 text-white/60 text-[8px] font-bold rounded uppercase">{{ $log->user->name }}</span>@endif
                        </div>
                        <p class="text-[10px] text-white/40 mt-0.5">{{ $log->ip_address }} · {{ $log->user_type ?? 'Guest' }}</p>
                        <p class="text-[9px] {{ str_contains($log->event, 'failed') ? 'text-red-500/60' : 'text-white/20' }}">{{ $log->logged_at->diffForHumans() }}</p>
                    </div>
                </div>
                @empty
                <div class="flex flex-col items-center justify-center py-12 text-white/30">
                    <p class="text-sm font-bold">No recent audit logs</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Block Device Modal --}}
    <div x-show="showBlockModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-brand/50 backdrop-blur-sm" @click="showBlockModal = false"></div>
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md relative z-10" @click.outside="showBlockModal = false">
            <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 bg-red-50 rounded-lg flex items-center justify-center text-red-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-brand">Block Device</h3>
                        <p class="text-xs text-brand-muted">Manually add a device fingerprint to the blocklist.</p>
                    </div>
                </div>
                <button @click="showBlockModal = false" class="w-7 h-7 bg-surface rounded-lg flex items-center justify-center text-brand-muted hover:text-brand transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form action="{{ route('orchestrator.security.devices.block') }}" method="POST" class="p-6 space-y-4">
                @csrf
                <div>
                    <label class="text-[10px] font-bold text-brand-muted uppercase tracking-wider mb-1.5 block">Device Fingerprint</label>
                    <input type="text" name="device_fingerprint" required placeholder="e.g. a1b2c3d4e5f6..." class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-mono font-medium outline-none focus:ring-2 focus:ring-red-300 transition-shadow">
                </div>
                <div>
                    <label class="text-[10px] font-bold text-brand-muted uppercase tracking-wider mb-1.5 block">Reason for Ban</label>
                    <textarea name="reason" rows="3" required placeholder="Why this device is being blocked..." class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium outline-none focus:ring-2 focus:ring-red-300 transition-shadow resize-none"></textarea>
                </div>
                <div class="flex justify-end gap-2 pt-2 border-t border-gray-100">
                    <button type="button" @click="showBlockModal = false" class="px-4 py-2 text-xs font-bold text-brand-muted hover:text-brand transition-colors">Cancel</button>
                    <button type="submit" class="px-5 py-2 bg-red-600 text-white rounded-lg text-xs font-bold hover:bg-red-700 transition-colors">Block Device</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Resolve Alert Modal --}}
    <div x-show="showResolve" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-brand/50 backdrop-blur-sm" @click="showResolve = false"></div>
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md relative z-10" @click.outside="showResolve = false">
            <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 bg-accent/10 rounded-lg flex items-center justify-center text-accent">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-brand">Resolve Alert</h3>
                        <p class="text-xs text-brand-muted">Review and close this fraud alert.</p>
                    </div>
                </div>
                <button @click="showResolve = false" class="w-7 h-7 bg-surface rounded-lg flex items-center justify-center text-brand-muted hover:text-brand transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form method="POST" :action="resolveUrl" class="p-6 space-y-4">
                @csrf @method('PATCH')
                <div>
                    <label class="text-[10px] font-bold text-brand-muted uppercase tracking-wider mb-1.5 block">Resolution</label>
                    <select name="status" required class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium outline-none focus:ring-2 focus:ring-accent/20 transition-shadow">
                        <option value="resolved">Confirmed & Handled</option>
                        <option value="false_positive">Mark as False Positive</option>
                    </select>
                </div>
                <div>
                    <label class="text-[10px] font-bold text-brand-muted uppercase tracking-wider mb-1.5 block">Analyst Notes</label>
                    <textarea name="resolution_notes" rows="3" required placeholder="Describe what was found and what actions were taken..." class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium outline-none focus:ring-2 focus:ring-accent/20 transition-shadow resize-none"></textarea>
                </div>
                <div class="flex justify-end gap-2 pt-2 border-t border-gray-100">
                    <button type="button" @click="showResolve = false" class="px-4 py-2 text-xs font-bold text-brand-muted hover:text-brand transition-colors">Cancel</button>
                    <button type="submit" class="px-5 py-2 bg-brand text-white rounded-lg text-xs font-bold hover:bg-brand-light transition-colors">Save Resolution</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Unblock Confirm Modal --}}
    <div x-show="showUnblockConfirm" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-brand/50 backdrop-blur-sm" @click="showUnblockConfirm = false"></div>
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-sm relative z-10 p-6 text-center" @click.outside="showUnblockConfirm = false">
            <div class="w-14 h-14 bg-green-50 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <h3 class="text-lg font-bold text-brand mb-1">Unblock Device?</h3>
            <p class="text-xs text-brand-muted mb-6">This will re-enable access for the blocked device fingerprint.</p>
            <form method="POST" :action="unblockUrl">
                @csrf @method('PATCH')
                <div class="flex gap-2">
                    <button type="button" @click="showUnblockConfirm = false" class="flex-1 px-4 py-2.5 bg-surface text-brand-muted rounded-lg text-xs font-bold hover:bg-gray-100">Cancel</button>
                    <button type="submit" class="flex-1 px-4 py-2.5 bg-green-600 text-white rounded-lg text-xs font-bold hover:bg-green-700">Unblock</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function securityManager() {
    return {
        showBlockModal: false,
        showResolve: false,
        resolveUrl: '',
        showUnblockConfirm: false,
        unblockUrl: '',
        openResolve(id) {
            this.resolveUrl = '{{ url(env('ORCHESTRATOR_PATH', 'orchestrator').'/security/alerts') }}/' + id + '/resolve';
            this.showResolve = true;
        },
        confirmUnblock(id) {
            this.unblockUrl = '{{ url(env('ORCHESTRATOR_PATH', 'orchestrator').'/security/devices') }}/' + id + '/unblock';
            this.showUnblockConfirm = true;
        }
    };
}
</script>
<style>[x-cloak] { display: none !important; }</style>
@endsection