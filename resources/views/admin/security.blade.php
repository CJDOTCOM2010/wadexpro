@extends('admin.layout')
@section('title', 'Fraud & Security Operations')
@section('content')

<!-- Error Alert -->
@if(session('error'))
<div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl flex items-center gap-3">
    <svg class="w-5 h-5 text-red-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
    <p class="text-sm font-medium text-red-700">{{ session('error') }}</p>
</div>
@endif

<div class="mb-12 flex items-center justify-between">
    <div>
        <h2 class="text-3xl font-black text-brand tracking-tight">Fraud & Security Operations</h2>
        <p class="text-brand-muted font-medium mt-1">Monitor suspicious activities, manage blocked devices, and review access audits.</p>
    </div>
    <div class="flex items-center gap-4">
        <button onclick="document.getElementById('block-device-modal').classList.remove('hidden')" class="px-6 py-3 bg-red-600 text-white font-bold rounded-xl hover:bg-red-700 transition flex items-center gap-2 shadow-lg">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
            Block Device Manually
        </button>
    </div>
</div>

<!-- Key Security Metrics -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
    <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-5 hover:shadow-md transition-all group">
        <div class="w-14 h-14 bg-amber-50 rounded-2xl flex items-center justify-center text-amber-600 group-hover:bg-amber-500 group-hover:text-white transition-all">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        </div>
        <div>
            <p class="text-[10px] font-black text-brand-muted uppercase tracking-widest">Open Fraud Alerts</p>
            <p class="text-2xl font-black text-brand">{{ number_format($stats['open_alerts'] ?? 0) }}</p>
        </div>
    </div>
    <div class="bg-white p-6 rounded-2xl border border-red-100 shadow-sm flex items-center gap-5 relative overflow-hidden group hover:shadow-md transition-all">
        <div class="absolute inset-0 bg-red-500/5 pointer-events-none group-hover:bg-red-500/10 transition"></div>
        <div class="w-14 h-14 bg-red-100 rounded-2xl flex items-center justify-center text-red-600 relative z-10 group-hover:bg-red-500 group-hover:text-white transition-all">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
        </div>
        <div class="relative z-10">
            <p class="text-[10px] font-black text-brand-muted uppercase tracking-widest">Critical / High Risk</p>
            <p class="text-2xl font-black text-red-600">{{ number_format($stats['high_risk'] ?? 0) }}</p>
        </div>
    </div>
    <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-5 hover:shadow-md transition-all group">
        <div class="w-14 h-14 bg-gray-100 rounded-2xl flex items-center justify-center text-gray-600 group-hover:bg-gray-500 group-hover:text-white transition-all">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
        </div>
        <div>
            <p class="text-[10px] font-black text-brand-muted uppercase tracking-widest">Blocked Devices</p>
            <p class="text-2xl font-black text-brand">{{ number_format($stats['blocked']) }}</p>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
    
    <!-- Fraud Alerts & Blocked Devices -->
    <div class="space-y-10 flex flex-col h-full">
        <!-- Fraud Alerts Table -->
        <div class="bg-white rounded-lg border border-gray-100 shadow-xl overflow-hidden flex flex-col flex-1">
            <div class="p-6 border-b border-gray-50 flex items-center justify-between">
                <div>
                    <h3 class="text-xl font-black text-brand tracking-tight">Active Fraud Alerts</h3>
                    <p class="text-xs text-brand-muted font-bold uppercase mt-1">Requires Analyst Review</p>
                </div>
            </div>

            <div class="overflow-x-auto flex-1">
                <table class="w-full text-left">
                    <thead>
                        <tr class="text-[10px] font-black text-brand-muted uppercase tracking-widest bg-surface/30">
                            <th class="p-4 border-b border-gray-50">User / Account</th>
                            <th class="p-4 border-b border-gray-50">Event Type</th>
                            <th class="p-4 border-b border-gray-50 text-center">Risk Level</th>
                            <th class="p-4 border-b border-gray-50 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm font-medium text-brand divide-y divide-gray-50">
                        @forelse($fraudAlerts as $alert)
                        <tr class="hover:bg-surface/50 transition">
                            <td class="p-4">
                                <div class="font-bold">{{ $alert->user->name ?? 'Unknown User' }}</div>
                                <div class="text-[10px] text-brand-muted font-mono">{{ $alert->user_type }} • ID: {{ substr($alert->user_id, 0, 8) }}...</div>
                            </td>
                            <td class="p-4">
                                <div>{{ ucwords(str_replace('_', ' ', $alert->event_type)) }}</div>
                                <div class="text-[10px] text-brand-muted mt-0.5">{{ $alert->created_at->diffForHumans() }}</div>
                            </td>
                            <td class="p-4 text-center">
                                <span class="px-2 py-1 rounded text-[10px] font-black uppercase tracking-widest {{ $alert->risk_badge }}">
                                    {{ $alert->risk_level }} ({{ $alert->risk_score }})
                                </span>
                            </td>
                            <td class="p-4 text-right">
                                <button onclick="openResolveModal('{{ $alert->id }}')" class="text-xs font-bold text-accent hover:text-accent-light transition">Review & Resolve</button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="p-8 text-center text-gray-500">
                                <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                No active fraud alerts.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-t border-gray-50">
                {{ $fraudAlerts->appends(request()->except('alerts_page'))->links() }}
            </div>
        </div>

        <!-- Blocked Devices Table -->
        <div class="bg-white rounded-lg border border-gray-100 shadow-xl overflow-hidden flex flex-col flex-1">
            <div class="p-6 border-b border-gray-50 flex items-center justify-between">
                <div>
                    <h3 class="text-xl font-black text-brand tracking-tight">Blocked Devices</h3>
                    <p class="text-xs text-brand-muted font-bold uppercase mt-1">Network-Level Bans</p>
                </div>
            </div>

            <div class="overflow-x-auto flex-1">
                <table class="w-full text-left">
                    <thead>
                        <tr class="text-[10px] font-black text-brand-muted uppercase tracking-widest bg-surface/30">
                            <th class="p-4 border-b border-gray-50">Fingerprint / Details</th>
                            <th class="p-4 border-b border-gray-50">Reason</th>
                            <th class="p-4 border-b border-gray-50 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm font-medium text-brand divide-y divide-gray-50">
                        @forelse($blockedDevices as $device)
                        <tr class="hover:bg-surface/50 transition {{ !$device->is_active ? 'opacity-50' : '' }}">
                            <td class="p-4">
                                <div class="font-mono text-xs font-bold">{{ substr($device->device_fingerprint, 0, 16) }}...</div>
                                <div class="text-[10px] text-brand-muted mt-0.5">Blocked {{ $device->blocked_at->format('M d, Y') }} by {{ $device->blockedBy->name ?? 'System' }}</div>
                            </td>
                            <td class="p-4 text-xs">
                                {{ Str::limit($device->reason, 40) }}
                                @if(!$device->is_active)
                                    <span class="block text-[9px] text-green-600 font-bold uppercase mt-1">Unblocked</span>
                                @endif
                            </td>
                            <td class="p-4 text-right">
                                @if($device->is_active)
                                <form action="{{ route('orchestrator.security.devices.unblock', $device->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to unblock this device?');">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="text-xs font-bold text-green-600 hover:text-green-700 transition">Unblock</button>
                                </form>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="p-8 text-center text-gray-500">No blocked devices.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-t border-gray-50">
                {{ $blockedDevices->appends(request()->except('devices_page'))->links() }}
            </div>
        </div>
    </div>

    <!-- Access Audit Trail -->
    <div class="bg-brand rounded-lg p-8 text-white relative overflow-hidden flex flex-col h-[800px]">
        <div class="absolute top-0 right-0 p-8 opacity-10">
            <svg class="w-48 h-48" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
        </div>
        
        <div class="relative z-10 flex flex-col h-full">
            <div class="flex items-center justify-between mb-8 pb-4 border-b border-white/10">
                <div>
                    <h3 class="text-xl font-black">System Access Audit</h3>
                    <p class="text-xs text-white/40 font-bold uppercase mt-1">Live Entry/Exit Telemetry</p>
                </div>
            </div>

            <div class="space-y-4 flex-1 overflow-y-auto pr-2 custom-scrollbar">
                @forelse($auditLogs as $log)
                <div class="flex items-start gap-4 p-4 rounded-lg border transition {{ str_contains($log->event, 'failed') ? 'bg-red-500/10 border-red-500/20' : 'bg-white/5 border-white/5 hover:bg-white/10' }}">
                    <div class="w-10 h-10 rounded-lg flex items-center justify-center shrink-0 {{ str_contains($log->event, 'failed') ? 'bg-red-500 text-white' : 'bg-surface text-brand' }}">
                        @if(str_contains($log->event, 'login'))
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                        @elseif(str_contains($log->event, 'permission') || str_contains($log->event, 'role'))
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        @else
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        @endif
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <p class="text-sm font-black {{ str_contains($log->event, 'failed') ? 'text-red-400' : 'text-white' }}">{{ ucwords(str_replace('_', ' ', $log->event)) }}</p>
                            @if($log->user)
                                <span class="px-1.5 py-0.5 bg-white/10 text-white/80 text-[8px] font-black rounded uppercase">{{ $log->user->name }}</span>
                            @endif
                        </div>
                        <p class="text-xs text-white/50 mt-1">Origin: {{ $log->ip_address }} • {{ $log->user_type ?? 'Guest' }}</p>
                        <p class="text-[9px] font-bold uppercase mt-2 {{ str_contains($log->event, 'failed') ? 'text-red-500/60' : 'text-white/30' }}">
                            {{ $log->logged_at->diffForHumans() }}
                        </p>
                    </div>
                </div>
                @empty
                <div class="text-center text-white/40 p-8">No recent security logs.</div>
                @endforelse
            </div>
        </div>
    </div>

</div>

<!-- Block Device Modal -->
<div id="block-device-modal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-lg font-black text-brand">Manually Block Device</h3>
            <button onclick="document.getElementById('block-device-modal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
        </div>
        <form action="{{ route('orchestrator.security.devices.block') }}" method="POST">
            @csrf
            <div class="space-y-4 mb-6">
                <div>
                    <label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-1">Device Fingerprint Hash</label>
                    <input type="text" name="device_fingerprint" required class="w-full bg-surface border border-gray-200 rounded p-2 text-sm focus:ring-2 focus:ring-red-500/20 outline-none font-mono">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-1">Reason for Ban</label>
                    <textarea name="reason" rows="3" required class="w-full bg-surface border border-gray-200 rounded p-2 text-sm focus:ring-2 focus:ring-red-500/20 outline-none"></textarea>
                </div>
            </div>
            <div class="flex justify-end pt-4 border-t border-gray-100 gap-3">
                <button type="button" onclick="document.getElementById('block-device-modal').classList.add('hidden')" class="px-4 py-2 text-brand font-bold text-sm">Cancel</button>
                <button type="submit" class="px-6 py-2 bg-red-600 text-white font-bold rounded shadow-sm hover:bg-red-700 transition">Enforce Ban</button>
            </div>
        </form>
    </div>
</div>

<!-- Resolve Alert Modal (Placeholder) -->
<div id="resolve-alert-modal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-lg font-black text-brand">Resolve Fraud Alert</h3>
            <button onclick="document.getElementById('resolve-alert-modal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
        </div>
        <form id="resolve-form" method="POST">
            @csrf @method('PATCH')
            <div class="space-y-4 mb-6">
                <div>
                    <label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-1">Resolution Outcome</label>
                    <select name="status" required class="w-full bg-surface border border-gray-200 rounded p-2 text-sm focus:ring-2 focus:ring-brand/20 outline-none cursor-pointer">
                        <option value="resolved">Confirmed & Handled</option>
                        <option value="false_positive">Mark as False Positive</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-1">Analyst Notes</label>
                    <textarea name="resolution_notes" rows="3" required class="w-full bg-surface border border-gray-200 rounded p-2 text-sm focus:ring-2 focus:ring-brand/20 outline-none"></textarea>
                </div>
            </div>
            <div class="flex justify-end pt-4 border-t border-gray-100 gap-3">
                <button type="button" onclick="document.getElementById('resolve-alert-modal').classList.add('hidden')" class="px-4 py-2 text-brand font-bold text-sm">Cancel</button>
                <button type="submit" class="px-6 py-2 bg-brand text-white font-bold rounded shadow-sm hover:bg-brand-light transition">Save Resolution</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openResolveModal(alertId) {
        document.getElementById('resolve-form').action = "{{ url(env('ORCHESTRATOR_PATH', 'orchestrator').'/security/alerts') }}/" + alertId + "/resolve";
        document.getElementById('resolve-alert-modal').classList.remove('hidden');
    }
</script>

@endsection
