@extends('admin.layout')
@section('title', 'Error Monitoring')
@section('content')

<div x-data="errorManager()" class="max-w-6xl mx-auto">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-black text-brand tracking-tight">Error Monitoring</h2>
            <p class="text-sm text-brand-muted font-medium mt-0.5">Real-time application exceptions and system logs.</p>
        </div>
        <button @click="showFlush = true" class="px-5 py-2.5 bg-red-50 text-red-600 rounded-lg text-xs font-bold hover:bg-red-100 transition-colors flex items-center gap-2 shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            Flush Logs
        </button>
    </div>

    @if(session('success'))
    <div class="mb-4 p-3.5 bg-green-50 border border-green-200 rounded-lg flex items-center gap-2.5">
        <svg class="w-4 h-4 text-green-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <p class="text-sm font-medium text-green-700">{{ session('success') }}</p>
    </div>
    @endif

    {{-- Stats --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
        <a href="{{ route('orchestrator.error_monitoring') }}" class="bg-white border border-gray-100 rounded-xl p-4 {{ !request('level') ? 'ring-2 ring-accent' : '' }}">
            <p class="text-lg font-black text-brand">{{ number_format($stats['total']) }}</p>
            <p class="text-[10px] font-bold text-brand-muted uppercase tracking-wider">Total Logs</p>
        </a>
        <a href="{{ route('orchestrator.error_monitoring', ['level' => 'error']) }}" class="bg-white border border-gray-100 rounded-xl p-4 {{ request('level') === 'error' ? 'ring-2 ring-red-500' : '' }}">
            <p class="text-lg font-black text-red-600">{{ number_format($stats['errors']) }}</p>
            <p class="text-[10px] font-bold text-brand-muted uppercase tracking-wider">Errors</p>
        </a>
        <a href="{{ route('orchestrator.error_monitoring', ['level' => 'warning']) }}" class="bg-white border border-gray-100 rounded-xl p-4 {{ request('level') === 'warning' ? 'ring-2 ring-amber-500' : '' }}">
            <p class="text-lg font-black text-amber-600">{{ number_format($stats['warnings']) }}</p>
            <p class="text-[10px] font-bold text-brand-muted uppercase tracking-wider">Warnings</p>
        </a>
        <a href="{{ route('orchestrator.error_monitoring', ['level' => 'info']) }}" class="bg-white border border-gray-100 rounded-xl p-4 {{ request('level') === 'info' ? 'ring-2 ring-blue-500' : '' }}">
            <p class="text-lg font-black text-blue-600">{{ number_format($stats['info']) }}</p>
            <p class="text-[10px] font-bold text-brand-muted uppercase tracking-wider">Info</p>
        </a>
    </div>

    {{-- Log List --}}
    <div class="bg-white border border-gray-100 rounded-xl overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 bg-surface/20 flex items-center justify-between">
            <h3 class="text-sm font-bold text-brand">System Activity</h3>
            @if(request('level'))
            <a href="{{ route('orchestrator.error_monitoring') }}" class="text-[10px] font-bold text-accent hover:underline">Clear filter</a>
            @endif
        </div>

        @if($paginator->isEmpty())
        <div class="flex flex-col items-center justify-center py-16 text-brand-muted">
            <svg class="w-14 h-14 mb-3 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <p class="text-sm font-bold">No system logs found</p>
            <p class="text-xs mt-1">The system is operating normally without recorded exceptions.</p>
        </div>
        @else
        <div class="divide-y divide-gray-50">
            @foreach($paginator as $log)
            @php
            $levelUpper = strtoupper($log['level']);
            $isError = in_array($levelUpper, ['ERROR', 'CRITICAL', 'EMERGENCY', 'ALERT']);
            $isWarning = $levelUpper === 'WARNING';
            $levelColor = $isError ? 'bg-red-50 text-red-600' : ($isWarning ? 'bg-amber-50 text-amber-600' : 'bg-blue-50 text-blue-600');
            $icon = $isError ? 'M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z' : ($isWarning ? 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z' : 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z');
            @endphp
            <div x-data="{ expanded: false }" class="px-5 py-4 hover:bg-surface/20 transition-colors">
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0 {{ $levelColor }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}"/></svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between gap-2 mb-0.5">
                            <div class="flex items-center gap-2">
                                <span class="text-[10px] font-bold uppercase tracking-wider {{ $isError ? 'text-red-600' : ($isWarning ? 'text-amber-600' : 'text-blue-600') }}">{{ $log['level'] }}</span>
                                <span class="text-[10px] text-brand-muted">{{ \Carbon\Carbon::parse($log['timestamp'])->diffForHumans() }}</span>
                            </div>
                            <button @click="expanded = !expanded" class="text-[10px] font-bold text-accent hover:underline shrink-0 ml-2">
                                <span x-text="expanded ? 'Hide' : 'Details'"></span>
                            </button>
                        </div>
                        <p class="text-xs font-bold text-brand truncate">{{ $log['message'] }}</p>
                        <div x-show="expanded" x-cloak class="mt-3">
                            <div class="bg-brand rounded-lg p-4 overflow-x-auto">
                                <pre class="text-[10px] font-mono text-white/70 leading-relaxed whitespace-pre-wrap">{{ $log['stack_trace'] }}</pre>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        @if($paginator->hasPages())
        <div class="px-5 py-3 border-t border-gray-100 bg-surface/20">{{ $paginator->links() }}</div>
        @endif
        @endif
    </div>

    {{-- Flush Confirm Modal --}}
    <div x-show="showFlush" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-brand/50 backdrop-blur-sm" @click="showFlush = false"></div>
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-sm relative z-10 p-6 text-center" @click.outside="showFlush = false">
            <div class="w-14 h-14 bg-red-50 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-7 h-7 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <h3 class="text-lg font-bold text-brand mb-1">Flush All Logs?</h3>
            <p class="text-xs text-brand-muted mb-6">All system log entries will be permanently deleted. This cannot be undone.</p>
            <form action="{{ route('orchestrator.error_monitoring.clear') }}" method="POST">
                @csrf
                <div class="flex gap-2">
                    <button type="button" @click="showFlush = false" class="flex-1 px-4 py-2.5 bg-surface text-brand-muted rounded-lg text-xs font-bold hover:bg-gray-100">Cancel</button>
                    <button type="submit" class="flex-1 px-4 py-2.5 bg-red-600 text-white rounded-lg text-xs font-bold hover:bg-red-700">Flush Logs</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function errorManager() {
    return { showFlush: false };
}
</script>
<style>[x-cloak] { display: none !important; }</style>
@endsection