@extends('admin.layout')
@section('title', 'System Error Monitoring')
@section('content')

<div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <h1 class="text-3xl font-black text-brand tracking-tight">Error Monitoring</h1>
        <p class="text-brand-muted font-medium mt-1">Real-time application exceptions and system logs</p>
    </div>
    <div class="flex items-center gap-3">
        <form action="{{ route('orchestrator.error_monitoring.clear') }}" method="POST" onsubmit="return confirm('Are you sure you want to flush all system logs? This action cannot be undone.')">
            @csrf
            <button type="submit" class="px-5 py-2.5 bg-red-50 text-red-600 hover:bg-red-100 font-bold rounded-xl transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                Flush Logs
            </button>
        </form>
    </div>
</div>

@if(session('success'))
<div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl text-green-700 text-sm font-bold flex items-center gap-3">
    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    {{ session('success') }}
</div>
@endif

<!-- Stats Overview -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
    <a href="{{ route('orchestrator.error_monitoring') }}" class="p-5 bg-white border border-gray-100 rounded-2xl shadow-sm hover:shadow-md transition">
        <p class="text-[10px] font-black text-brand-muted uppercase tracking-widest mb-2">Total Logs</p>
        <p class="text-3xl font-black text-brand">{{ number_format($stats['total']) }}</p>
    </a>
    <a href="{{ route('orchestrator.error_monitoring', ['level' => 'error']) }}" class="p-5 bg-red-50 border border-red-100 rounded-2xl shadow-sm hover:shadow-md transition relative overflow-hidden group">
        <div class="absolute -right-4 -bottom-4 opacity-10 text-red-500 transform group-hover:scale-110 transition duration-500">
            <svg class="w-24 h-24" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
        </div>
        <p class="text-[10px] font-black text-red-600 uppercase tracking-widest mb-2">Critical / Errors</p>
        <p class="text-3xl font-black text-red-700">{{ number_format($stats['errors']) }}</p>
    </a>
    <a href="{{ route('orchestrator.error_monitoring', ['level' => 'warning']) }}" class="p-5 bg-amber-50 border border-amber-100 rounded-2xl shadow-sm hover:shadow-md transition">
        <p class="text-[10px] font-black text-amber-600 uppercase tracking-widest mb-2">Warnings</p>
        <p class="text-3xl font-black text-amber-700">{{ number_format($stats['warnings']) }}</p>
    </a>
    <a href="{{ route('orchestrator.error_monitoring', ['level' => 'info']) }}" class="p-5 bg-blue-50 border border-blue-100 rounded-2xl shadow-sm hover:shadow-md transition">
        <p class="text-[10px] font-black text-blue-600 uppercase tracking-widest mb-2">Info / Debug</p>
        <p class="text-3xl font-black text-blue-700">{{ number_format($stats['info']) }}</p>
    </a>
</div>

<!-- Logs List -->
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="p-5 border-b border-gray-50 flex items-center justify-between">
        <h3 class="font-black text-brand">System Activity</h3>
        @if(request('level'))
        <a href="{{ route('orchestrator.error_monitoring') }}" class="text-xs font-bold text-accent hover:text-accent-hover">Clear Filters</a>
        @endif
    </div>

    @if($paginator->isEmpty())
    <div class="p-12 text-center text-brand-muted">
        <div class="w-16 h-16 bg-surface rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <p class="font-bold">No system logs found.</p>
        <p class="text-sm mt-1">The system is operating normally without recorded exceptions.</p>
    </div>
    @else
    <div class="divide-y divide-gray-50">
        @foreach($paginator as $log)
        <div x-data="{ expanded: false }" class="p-5 hover:bg-surface/50 transition">
            <div class="flex items-start gap-4">
                @php
                    $levelColor = match(strtoupper($log['level'])) {
                        'ERROR', 'CRITICAL', 'EMERGENCY', 'ALERT' => 'bg-red-100 text-red-600',
                        'WARNING' => 'bg-amber-100 text-amber-600',
                        'INFO', 'NOTICE' => 'bg-blue-100 text-blue-600',
                        default => 'bg-gray-100 text-gray-600'
                    };
                    $icon = match(strtoupper($log['level'])) {
                        'ERROR', 'CRITICAL', 'EMERGENCY', 'ALERT' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>',
                        'WARNING' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>',
                        default => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>'
                    };
                @endphp
                <div class="mt-1 w-8 h-8 rounded-lg flex-shrink-0 flex items-center justify-center {{ $levelColor }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $icon !!}</svg>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between gap-4 mb-1">
                        <div class="flex items-center gap-2">
                            <span class="text-[10px] font-black uppercase tracking-wider {{ str_replace('bg-', 'text-', str_replace('text-', 'fill-', $levelColor)) }}">{{ $log['level'] }}</span>
                            <span class="text-xs text-gray-400 font-medium">{{ \Carbon\Carbon::parse($log['timestamp'])->diffForHumans() }}</span>
                        </div>
                        <button @click="expanded = !expanded" class="text-xs font-bold text-accent hover:text-accent-hover transition flex items-center gap-1">
                            <span x-show="!expanded">View Details</span>
                            <span x-show="expanded">Hide Details</span>
                        </button>
                    </div>
                    <p class="font-bold text-sm text-brand truncate">{{ $log['message'] }}</p>
                    
                    <div x-show="expanded" x-collapse x-cloak class="mt-3">
                        <div class="bg-gray-900 rounded-xl p-4 overflow-x-auto">
                            <pre class="text-[11px] font-mono text-gray-300 leading-relaxed">{{ $log['stack_trace'] }}</pre>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    
    <div class="p-5 border-t border-gray-50">
        {{ $paginator->links() }}
    </div>
    @endif
</div>

@endsection
