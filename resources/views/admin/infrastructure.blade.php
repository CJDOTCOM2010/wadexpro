@extends('admin.layout')
@section('title', 'Infrastructure Hub')
@section('content')

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
            <h2 class="text-2xl font-black text-brand tracking-tight">Infrastructure Hub</h2>
            <p class="text-sm text-brand-muted font-medium mt-0.5">Oversight of platform modules, core architecture, and system health.</p>
        </div>
        <span class="flex items-center gap-2 px-3 py-1.5 bg-white border border-gray-100 rounded-lg text-[10px] font-bold text-green-600">
            <span class="w-1.5 h-1.5 bg-green-500 rounded-full animate-pulse"></span>
            System Online
        </span>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Module Matrix --}}
        <div class="bg-white border border-gray-100 rounded-xl overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 bg-surface/20">
                <h3 class="text-sm font-bold text-brand">Module Matrix</h3>
                <p class="text-[10px] text-brand-muted">Enable or disable platform features</p>
            </div>
            <div class="p-5 space-y-3">
                @forelse($modules as $module)
                <div class="flex items-center justify-between p-4 bg-surface/50 rounded-lg border border-gray-100">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-9 h-9 rounded-lg flex items-center justify-center shrink-0 {{ $module->is_enabled ? 'bg-brand text-accent' : 'bg-gray-100 text-gray-400' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 4a2 2 0 114 0v1a2 2 0 01-2 2H3a2 2 0 01-2-2V4a2 2 0 114 0v1a2 2 0 012 2h4a2 2 0 012-2V4z"/></svg>
                        </div>
                        <div class="min-w-0">
                            <p class="text-sm font-bold text-brand truncate">{{ $module->name }}</p>
                            <p class="text-[10px] text-brand-muted truncate">{{ $module->description ?? $module->slug }}</p>
                        </div>
                    </div>
                    <form action="{{ route('orchestrator.infrastructure.modules.toggle', $module->id) }}" method="POST" class="shrink-0 ml-2">
                        @csrf @method('PATCH')
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" onchange="this.form.submit()" class="sr-only peer" {{ $module->is_enabled ? 'checked' : '' }}>
                            <div class="w-9 h-5 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[0.5px] after:left-[0.5px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-green-500"></div>
                        </label>
                    </form>
                </div>
                @empty
                <div class="flex flex-col items-center justify-center py-10 text-brand-muted">
                    <p class="text-sm font-bold">No modules configured</p>
                </div>
                @endforelse
            </div>
            <div class="px-5 py-3 border-t border-gray-100 bg-surface/20 text-center">
                <a href="{{ route('orchestrator.modules') }}" class="text-[10px] font-bold text-accent hover:underline">Advanced Module Hardening →</a>
            </div>
        </div>

        {{-- Right Column --}}
        <div class="space-y-6">
            {{-- System Health --}}
            <div class="bg-brand rounded-xl p-6 text-white">
                <h3 class="text-sm font-bold mb-5">System Health</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-white/10 rounded-lg p-4">
                        <p class="text-[10px] font-bold text-white/40 uppercase tracking-wider mb-1">Database</p>
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-bold text-accent">Healthy</span>
                            <span class="text-[10px] text-white/30">2ms</span>
                        </div>
                    </div>
                    <div class="bg-white/10 rounded-lg p-4">
                        <p class="text-[10px] font-bold text-white/40 uppercase tracking-wider mb-1">Cache</p>
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-bold text-accent">Active</span>
                            <span class="text-[10px] text-white/30">Redis</span>
                        </div>
                    </div>
                    <div class="bg-white/10 rounded-lg p-4">
                        <p class="text-[10px] font-bold text-white/40 uppercase tracking-wider mb-1">Storage</p>
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-bold text-white">{{ round(disk_free_space('/') / 1073741824, 1) }} GB free</span>
                        </div>
                    </div>
                    <div class="bg-white/10 rounded-lg p-4">
                        <p class="text-[10px] font-bold text-white/40 uppercase tracking-wider mb-1">PHP</p>
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-bold text-white">{{ PHP_VERSION }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Cache Commands --}}
            <div class="bg-white border border-gray-100 rounded-xl p-6">
                <h3 class="text-sm font-bold text-brand mb-5">Cache Commands</h3>
                <form action="{{ route('orchestrator.infrastructure.command') }}" method="POST" class="grid grid-cols-2 gap-3">
                    @csrf
                    <button type="submit" name="command" value="optimize" class="p-4 bg-surface rounded-lg border border-gray-100 text-left hover:border-accent/40 transition-colors">
                        <span class="text-[9px] font-bold text-brand-muted uppercase tracking-wider">Optimize</span>
                        <p class="text-xs font-bold text-brand mt-1">Clear all cache</p>
                    </button>
                    <button type="submit" name="command" value="config" class="p-4 bg-surface rounded-lg border border-gray-100 text-left hover:border-accent/40 transition-colors">
                        <span class="text-[9px] font-bold text-brand-muted uppercase tracking-wider">Config</span>
                        <p class="text-xs font-bold text-brand mt-1">Rebuild config cache</p>
                    </button>
                    <button type="submit" name="command" value="route" class="p-4 bg-surface rounded-lg border border-gray-100 text-left hover:border-accent/40 transition-colors">
                        <span class="text-[9px] font-bold text-brand-muted uppercase tracking-wider">Routes</span>
                        <p class="text-xs font-bold text-brand mt-1">Flush route cache</p>
                    </button>
                    <button type="submit" name="command" value="nuclear" class="p-4 bg-red-50 rounded-lg border border-red-100 text-left hover:bg-red-100 transition-colors">
                        <span class="text-[9px] font-bold text-red-600 uppercase tracking-wider">Nuclear</span>
                        <p class="text-xs font-bold text-red-600 mt-1">Clear all sessions</p>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
#operations-map { background: #0A0A1A; }
</style>
@endsection