@extends('admin.layout')
@section('title', 'System Backups')
@section('content')

@php
$totalSize = 0;
$backupCount = count($backups);
foreach ($backups as $b) {
    $size = preg_replace('/[^0-9.]/', '', $b['file_size'] ?? '0');
    $unit = preg_replace('/[0-9. ]/', '', $b['file_size'] ?? 'B');
    $mult = ['B' => 1, 'KB' => 1024, 'MB' => 1048576, 'GB' => 1073741824, 'TB' => 1099511627776];
    $totalSize += $size * ($mult[$unit] ?? 1);
}
$totalSizeFormatted = '';
foreach (['TB' => 1099511627776, 'GB' => 1073741824, 'MB' => 1048576, 'KB' => 1024] as $u => $m) {
    if ($totalSize >= $m) { $totalSizeFormatted = round($totalSize / $m, 2) . ' ' . $u; break; }
}
if (!$totalSizeFormatted) $totalSizeFormatted = round($totalSize, 0) . ' B';
$storagePercent = min(round(($totalSize / 10737418240) * 100), 100); // assume 10GB max
@endphp

<div class="max-w-6xl mx-auto" x-data="backupManager()">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
            <div class="flex items-center gap-2 text-[10px] font-bold text-accent uppercase tracking-wider mb-1">
                <a href="{{ route('orchestrator.settings') }}" class="hover:text-brand transition-colors">Settings Hub</a>
                <span class="text-gray-300">/</span>
                <span>Backups</span>
            </div>
            <h2 class="text-2xl font-black text-brand tracking-tight">System Backups</h2>
            <p class="text-sm text-brand-muted font-medium mt-0.5">Manage data snapshots, backups, and restoration points.</p>
        </div>
        <div class="flex items-center gap-2">
            <button @click="showCreate = true" class="bg-brand text-white px-5 py-2.5 rounded-lg text-xs font-bold hover:bg-brand-light transition-colors flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                New Backup
            </button>
            <form action="{{ route('orchestrator.settings.backups.clean') }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="bg-white border border-gray-200 text-brand-muted hover:text-brand hover:border-gray-300 px-5 py-2.5 rounded-lg text-xs font-bold transition-colors flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    Clean Old
                </button>
            </form>
        </div>
    </div>

    @if(session('success'))
    <div class="mb-6 p-3.5 bg-green-50 border border-green-200 rounded-lg flex items-center gap-2.5">
        <svg class="w-4 h-4 text-green-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        <p class="text-sm font-medium text-green-700">{{ session('success') }}</p>
    </div>
    @endif

    @if(session('error'))
    <div class="mb-6 p-3.5 bg-red-50 border border-red-200 rounded-lg flex items-center gap-2.5">
        <svg class="w-4 h-4 text-red-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <p class="text-sm font-medium text-red-700">{{ session('error') }}</p>
    </div>
    @endif

    <!-- Stats Row -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="bg-white border border-gray-100 rounded-xl p-4">
            <div class="flex items-center gap-2 mb-1">
                <div class="w-8 h-8 bg-brand/5 rounded-lg flex items-center justify-center text-brand">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"/></svg>
                </div>
                <span class="text-xs font-bold text-brand-muted uppercase tracking-wider">DB Tables</span>
            </div>
            <p class="text-2xl font-black text-brand mt-1">{{ $dbStats['table_count'] ?? '—' }}</p>
            <p class="text-[11px] text-brand-muted">in public schema</p>
        </div>
        <div class="bg-white border border-gray-100 rounded-xl p-4">
            <div class="flex items-center gap-2 mb-1">
                <div class="w-8 h-8 bg-green-50 rounded-lg flex items-center justify-center text-green-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                </div>
                <span class="text-xs font-bold text-brand-muted uppercase tracking-wider">DB Size</span>
            </div>
            <p class="text-2xl font-black text-brand mt-1">{{ $dbStats['db_size'] ?? '—' }}</p>
            <p class="text-[11px] text-brand-muted">live database size</p>
        </div>
        <div class="bg-white border border-gray-100 rounded-xl p-4">
            <div class="flex items-center gap-2 mb-1">
                <div class="w-8 h-8 bg-blue-50 rounded-lg flex items-center justify-center text-blue-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <span class="text-xs font-bold text-brand-muted uppercase tracking-wider">Total Rows</span>
            </div>
            <p class="text-2xl font-black text-brand mt-1">{{ $dbStats['total_rows'] ?? '—' }}</p>
            <p class="text-[11px] text-brand-muted">records across all tables</p>
        </div>
        <div class="bg-white border border-gray-100 rounded-xl p-4">
            <div class="flex items-center gap-2 mb-1">
                <div class="w-8 h-8 bg-amber-50 rounded-lg flex items-center justify-center text-amber-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                </div>
                <span class="text-xs font-bold text-brand-muted uppercase tracking-wider">Snapshots</span>
            </div>
            <p class="text-2xl font-black text-brand mt-1">{{ $backupCount }}</p>
            <p class="text-[11px] text-brand-muted">saved in vault</p>
        </div>
    </div>

    <!-- Storage Usage Bar -->
    <div class="bg-white border border-gray-100 rounded-xl p-5 mb-8">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-bold text-brand">Storage Usage</span>
            <span class="text-[11px] font-bold text-brand-muted">{{ $totalSizeFormatted }} / 10 GB</span>
        </div>
        <div class="h-2.5 bg-surface rounded-full overflow-hidden">
            <div class="h-full bg-brand rounded-full transition-all" style="width: {{ $storagePercent }}%"></div>
        </div>
        <div class="flex justify-between mt-2 text-[10px] font-bold text-brand-muted">
            <span>{{ $backupCount }} snapshots</span>
            <span>{{ $storagePercent }}% utilized</span>
        </div>
    </div>

    <!-- Active Jobs Tracking -->
    <template x-if="activeJobs.length > 0">
        <div class="mb-8 space-y-3">
            <template x-for="job in activeJobs" :key="job.id">
                <div class="bg-white border border-brand/10 rounded-xl p-5 shadow-sm relative overflow-hidden">
                    <!-- Progress Bar Background -->
                    <div class="absolute bottom-0 left-0 h-1 bg-brand/10 w-full">
                        <div class="h-full bg-brand transition-all duration-500" :style="`width: ${job.progress}%`"></div>
                    </div>

                    <div class="flex items-center justify-between relative z-10">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center"
                                 :class="job.status === 'failed' ? 'bg-red-50 text-red-600' : 'bg-brand/10 text-brand'">
                                <svg x-show="job.status === 'running' || job.status === 'pending'" class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <svg x-show="job.status === 'failed'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-brand flex items-center gap-2">
                                    <span x-text="job.type === 'only-db' ? 'Database Dump' : (job.type === 'only-files' ? 'Media Backup' : 'Full System Backup')"></span>
                                    <span x-show="job.status === 'running'" class="px-2 py-0.5 rounded text-[9px] font-black tracking-wider uppercase bg-brand/10 text-brand" x-text="`${job.progress}%`"></span>
                                    <span x-show="job.status === 'failed'" class="px-2 py-0.5 rounded text-[9px] font-black tracking-wider uppercase bg-red-100 text-red-600">Failed</span>
                                </h4>
                                <p class="text-[11px] font-medium mt-0.5"
                                   :class="job.status === 'failed' ? 'text-red-500' : 'text-brand-muted'"
                                   x-text="job.status === 'failed' ? job.error_message : job.current_step"></p>
                            </div>
                        </div>

                        <!-- Extended Stats for DB Dumps -->
                        <div x-show="job.tables_total > 0 && job.status === 'running'" class="text-right hidden sm:block">
                            <p class="text-[10px] font-bold text-brand-muted uppercase tracking-wider mb-1">Live Progress</p>
                            <p class="text-xs font-medium text-brand">
                                <span x-text="job.tables_done"></span> / <span x-text="job.tables_total"></span> tables
                            </p>
                            <p class="text-[10px] text-brand-muted">
                                <span x-text="new Intl.NumberFormat().format(job.rows_dumped)"></span> rows dumped
                            </p>
                        </div>
                        
                        <div class="ml-4">
                            <button @click="confirmCancel(job.id)" x-show="job.status === 'pending' || job.status === 'running'" class="w-8 h-8 bg-red-50 text-red-600 rounded-lg flex items-center justify-center hover:bg-red-600 hover:text-white transition-colors" title="Cancel Backup">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </template>

    <!-- Backup Table -->
    <div class="bg-white border border-gray-100 rounded-xl overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-sm font-bold text-brand">Snapshot History</h3>
            <div class="flex items-center gap-2 px-3 py-1.5 bg-surface rounded-lg border border-gray-100">
                <svg class="w-3.5 h-3.5 text-brand-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" x-model="search" placeholder="Filter..." class="text-xs bg-transparent outline-none border-none p-0 w-24 text-brand placeholder:text-brand-muted">
            </div>
        </div>

        @if(empty($backups))
        <div class="flex flex-col items-center justify-center py-20 text-brand-muted">
            <svg class="w-16 h-16 mb-4 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"/></svg>
            <p class="text-sm font-bold">No backups yet</p>
            <p class="text-xs mt-1">Create your first backup snapshot to appear here</p>
            <button @click="showCreate = true" class="mt-4 px-4 py-2 bg-brand text-white rounded-lg text-xs font-bold hover:bg-brand-light transition-colors flex items-center gap-2">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Create Backup
            </button>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-surface/50">
                        <th class="text-left px-5 py-3.5 text-[10px] font-bold text-brand-muted uppercase tracking-wider">File</th>
                        <th class="text-left px-5 py-3.5 text-[10px] font-bold text-brand-muted uppercase tracking-wider">Size</th>
                        <th class="text-left px-5 py-3.5 text-[10px] font-bold text-brand-muted uppercase tracking-wider">Created</th>
                        <th class="text-left px-5 py-3.5 text-[10px] font-bold text-brand-muted uppercase tracking-wider">Age</th>
                        <th class="text-right px-5 py-3.5 text-[10px] font-bold text-brand-muted uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($backups as $backup)
                    <tr class="hover:bg-surface/30 transition-colors">
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 bg-brand/5 rounded-lg flex items-center justify-center text-brand shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-xs font-bold text-brand truncate max-w-[200px]">{{ $backup['file_name'] }}</p>
                                    <p class="text-[10px] text-brand-muted">{{ $backup['last_modified'] }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-4">
                            <span class="px-2.5 py-1 bg-surface text-[10px] font-bold text-brand rounded-lg">{{ $backup['file_size'] }}</span>
                        </td>
                        <td class="px-5 py-4 text-xs text-brand-muted">{{ $backup['last_modified'] }}</td>
                        <td class="px-5 py-4">
                            <span class="text-xs font-bold text-brand">{{ $backup['age'] }}</span>
                        </td>
                        <td class="px-5 py-4 text-right">
                            <div class="flex items-center justify-end gap-1.5">
                                <a href="{{ route('orchestrator.settings.backups.download', $backup['file_name']) }}" class="w-8 h-8 bg-brand text-white rounded-lg flex items-center justify-center hover:bg-accent hover:text-brand transition-colors" title="Download">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                </a>
                                <button @click="confirmDelete('{{ $backup['file_name'] }}')" class="w-8 h-8 bg-red-50 text-red-600 rounded-lg flex items-center justify-center hover:bg-red-600 hover:text-white transition-colors" title="Delete">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

    <!-- Create Backup Modal -->
    <div x-show="showCreate" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-6">
        <div class="absolute inset-0 bg-brand/40 backdrop-blur-sm" @click="showCreate = false"></div>
        <div class="bg-white rounded-xl w-full max-w-lg relative z-10 shadow-2xl" @click.outside="showCreate = false">
            <div class="px-6 py-5 border-b border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-bold text-brand">New Backup</h3>
                        <p class="text-xs text-brand-muted mt-0.5">Select what to include in this snapshot.</p>
                    </div>
                    <button @click="showCreate = false" class="w-8 h-8 bg-surface rounded-lg flex items-center justify-center text-brand-muted hover:text-brand transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </div>
            <form action="{{ route('orchestrator.settings.backups.create') }}" method="POST" class="p-6">
                @csrf
                <div class="grid grid-cols-3 gap-3 mb-6">
                    <label class="cursor-pointer">
                        <input type="radio" name="option" value="all" checked class="peer sr-only">
                        <div class="p-4 bg-surface border-2 border-transparent peer-checked:border-brand peer-checked:bg-brand/5 rounded-lg text-center h-full transition-colors">
                            <svg class="w-7 h-7 text-brand mb-2 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                            <p class="text-xs font-bold text-brand">Full System</p>
                            <p class="text-[9px] text-brand-muted mt-0.5">DB + files</p>
                        </div>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="option" value="only-db" class="peer sr-only">
                        <div class="p-4 bg-surface border-2 border-transparent peer-checked:border-brand peer-checked:bg-brand/5 rounded-lg text-center h-full transition-colors">
                            <svg class="w-7 h-7 text-accent mb-2 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"/></svg>
                            <p class="text-xs font-bold text-brand">Database</p>
                            <p class="text-[9px] text-brand-muted mt-0.5">SQL dump</p>
                        </div>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="option" value="only-files" class="peer sr-only">
                        <div class="p-4 bg-surface border-2 border-transparent peer-checked:border-brand peer-checked:bg-brand/5 rounded-lg text-center h-full transition-colors">
                            <svg class="w-7 h-7 text-blue-500 mb-2 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
                            <p class="text-xs font-bold text-brand">Media Vault</p>
                            <p class="text-[9px] text-brand-muted mt-0.5">Files only</p>
                        </div>
                    </label>
                </div>
                <div class="p-3.5 bg-amber-50 border border-amber-200 rounded-lg mb-6">
                    <p class="text-[10px] font-bold text-amber-800 flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Performance may slightly degrade during full snapshots. The process runs asynchronously.
                    </p>
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" @click="showCreate = false" class="px-5 py-2.5 text-xs font-bold text-brand-muted hover:text-brand transition-colors">Cancel</button>
                    <button type="submit" class="px-6 py-2.5 bg-brand text-white rounded-lg text-xs font-bold hover:bg-brand-light transition-colors flex items-center gap-2">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        Start Backup
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Confirm Modal -->
    <div x-show="showDelete" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-6">
        <div class="absolute inset-0 bg-brand/40 backdrop-blur-sm" @click="showDelete = false"></div>
        <div class="bg-white rounded-xl w-full max-w-sm relative z-10 shadow-2xl p-6 text-center" @click.outside="showDelete = false">
            <div class="w-14 h-14 bg-red-50 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </div>
            <h3 class="text-lg font-bold text-brand mb-1">Delete Backup?</h3>
            <p class="text-xs text-brand-muted mb-6">This action is irreversible. <span class="font-bold text-brand" x-text="deletingFile"></span> will be permanently deleted.</p>
            <form method="POST" :action="deleteUrl">
                @csrf
                @method('DELETE')
                <div class="flex gap-2">
                    <button type="button" @click="showDelete = false" class="flex-1 px-4 py-2.5 bg-surface text-brand-muted rounded-lg text-xs font-bold hover:bg-gray-100 transition-colors">Cancel</button>
                    <button type="submit" class="flex-1 px-4 py-2.5 bg-red-600 text-white rounded-lg text-xs font-bold hover:bg-red-700 transition-colors">Delete</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Cancel Confirm Modal -->
    <div x-show="showCancel" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-6">
        <div class="absolute inset-0 bg-brand/40 backdrop-blur-sm" @click="showCancel = false"></div>
        <div class="bg-white rounded-xl w-full max-w-sm relative z-10 shadow-2xl p-6 text-center" @click.outside="showCancel = false">
            <div class="w-14 h-14 bg-red-50 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <h3 class="text-lg font-bold text-brand mb-1">Cancel Backup?</h3>
            <p class="text-xs text-brand-muted mb-6">Are you sure you want to cancel this backup process?</p>
            <form method="POST" :action="cancelUrl">
                @csrf
                <div class="flex gap-2">
                    <button type="button" @click="showCancel = false" class="flex-1 px-4 py-2.5 bg-surface text-brand-muted rounded-lg text-xs font-bold hover:bg-gray-100 transition-colors">No, Continue</button>
                    <button type="submit" class="flex-1 px-4 py-2.5 bg-red-600 text-white rounded-lg text-xs font-bold hover:bg-red-700 transition-colors">Yes, Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function backupManager() {
    return {
        showCreate: false,
        showDelete: false,
        showCancel: false,
        deletingFile: '',
        deleteUrl: '',
        cancelUrl: '',
        search: '',
        activeJobs: @json($activeJobs ?? []),
        pollingInterval: null,

        init() {
            if (this.activeJobs.length > 0) {
                this.startPolling();
            }
        },

        startPolling() {
            this.pollingInterval = setInterval(async () => {
                try {
                    const res = await fetch('{{ route('orchestrator.settings.backups.status') }}');
                    const jobs = await res.json();
                    
                    // Update jobs
                    this.activeJobs = jobs.filter(j => j.status === 'pending' || j.status === 'running');
                    
                    // If a job just completed, refresh the page to show the new file
                    const justCompleted = jobs.some(j => j.status === 'completed' && !this.activeJobs.find(aj => aj.id === j.id));
                    if (justCompleted) {
                        window.location.reload();
                    }

                    if (this.activeJobs.length === 0) {
                        clearInterval(this.pollingInterval);
                    }
                } catch (e) {
                    console.error('Failed to poll backup status', e);
                }
            }, 2000); // poll every 2 seconds
        },

        confirmDelete(file) {
            this.deletingFile = file;
            this.deleteUrl = '{{ route('orchestrator.settings.backups.delete', ['file' => '__FILE__']) }}'.replace('__FILE__', file);
            this.showDelete = true;
        },

        confirmCancel(id) {
            this.cancelUrl = '{{ route('orchestrator.settings.backups.cancel', ['id' => '__ID__']) }}'.replace('__ID__', id);
            this.showCancel = true;
        }
    };
}
</script>

@endsection