@extends('admin.layout')
@section('title', 'System Resilience & Backups')
@section('content')

<div class="p-8 lg:p-12 max-w-[1600px] mx-auto" x-data="{ createModal: false, deleteModal: false, selectedBackup: null }">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-12 gap-6">
        <div>
            <div class="flex items-center gap-2 text-[10px] font-black text-accent uppercase tracking-[0.2em] mb-2">
                <a href="{{ route('orchestrator.settings') }}" class="hover:text-brand transition-colors">Settings Hub</a>
                <span class="text-gray-300">/</span>
                <span>Data Resilience</span>
            </div>
            <h2 class="text-3xl font-black text-brand tracking-tight">System Backup Registry</h2>
            <p class="text-sm text-brand-muted font-medium mt-1">High-integrity data snapshots and automated restoration nodes.</p>
        </div>
        
        <div class="flex items-center gap-3">
            <button @click="createModal = true" class="bg-brand text-white px-8 py-4 rounded-xl text-xs font-black shadow-xl hover:shadow-brand/20 hover:-translate-y-0.5 transition-all flex items-center gap-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Generate Snapshot
            </button>
            <form action="{{ route('orchestrator.settings.backups.clean') }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="bg-white border border-gray-100 text-brand-muted hover:bg-gray-50 px-6 py-4 rounded-xl text-xs font-black shadow-sm transition-all flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    Clean Old Backups
                </button>
            </form>
        </div>
    </div>

    @if(session('success'))
    <div class="mb-8 p-4 bg-green-50 border border-green-100 text-green-600 rounded-lg flex items-center gap-3 animate-fade-in">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        <span class="text-xs font-bold">{{ session('success') }}</span>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-10">
        <!-- Left: Resilience Stats -->
        <div class="lg:col-span-1 space-y-8">
            <div class="bg-brand rounded-3xl p-8 text-white relative overflow-hidden group shadow-2xl">
                <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-white/10 rounded-full blur-3xl group-hover:scale-150 transition-transform duration-1000"></div>
                <div class="relative z-10">
                    <p class="text-[10px] font-black text-white/40 uppercase tracking-[0.2em] mb-4">System Health Status</p>
                    <div class="flex items-center gap-4 mb-8">
                        <div class="w-14 h-14 bg-white/10 rounded-2xl flex items-center justify-center">
                            <svg class="w-7 h-7 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        </div>
                        <div>
                            <h4 class="text-xl font-black">Encrypted</h4>
                            <p class="text-[10px] font-bold text-white/50 uppercase tracking-widest leading-none">Resilience Level: High</p>
                        </div>
                    </div>
                    
                    <div class="space-y-6">
                        <div>
                            <div class="flex justify-between text-[10px] font-black text-white/40 uppercase mb-2">
                                <span>Recent Snapshots</span>
                                <span>{{ count($backups) }} Total</span>
                            </div>
                            <div class="h-2 bg-white/5 rounded-full overflow-hidden">
                                <div class="h-full bg-accent rounded-full transition-all duration-1000" style="width: {{ min(count($backups) * 10, 100) }}%"></div>
                            </div>
                        </div>
                        
                        <div class="p-4 bg-white/5 rounded-xl border border-white/10">
                            <p class="text-[10px] font-bold text-white/60 mb-1">Last Sync Check</p>
                            <p class="text-xs font-black">{{ count($backups) > 0 ? $backups[0]['age'] : 'Never' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Auto-Backup Config -->
            <div class="bg-white rounded-3xl border border-gray-50 shadow-xl p-8">
                <h3 class="text-sm font-black text-brand mb-6 flex items-center gap-2">
                    <svg class="w-4 h-4 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Scheduled Maintenance
                </h3>
                <p class="text-[11px] text-brand-muted font-bold leading-relaxed mb-6">Automated snapshots are performed daily at <span class="text-brand">01:00 AM</span> server time.</p>
                
                <div class="space-y-4">
                    <div class="flex items-center gap-3 p-4 bg-surface rounded-xl">
                        <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                        <div>
                            <p class="text-[10px] font-black text-brand uppercase">Database Node</p>
                            <p class="text-[9px] font-bold text-brand-muted">Real-time sync enabled</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 p-4 bg-surface rounded-xl">
                        <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                        <div>
                            <p class="text-[10px] font-black text-brand uppercase">File Integrity</p>
                            <p class="text-[9px] font-bold text-brand-muted">Checksum validation active</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right: Backup History -->
        <div class="lg:col-span-3">
            <div class="bg-white rounded-3xl border border-gray-50 shadow-2xl overflow-hidden min-h-[600px]">
                <div class="p-8 border-b border-gray-50 flex items-center justify-between bg-surface/50">
                    <h3 class="text-lg font-black text-brand">Snapshot History</h3>
                    <div class="flex items-center gap-2 px-4 py-2 bg-white rounded-lg border border-gray-100 shadow-sm">
                        <svg class="w-4 h-4 text-brand-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <input type="text" placeholder="Filter history..." class="text-[11px] font-bold bg-transparent outline-none border-none p-0 w-32">
                    </div>
                </div>

                @if(empty($backups))
                <div class="flex flex-col items-center justify-center py-32 opacity-20">
                    <svg class="w-24 h-24 mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"/></svg>
                    <p class="text-sm font-black uppercase tracking-widest">No snapshots found in system vault</p>
                </div>
                @else
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-surface/30">
                                <th class="text-left px-8 py-5 text-[10px] font-black text-brand-muted uppercase tracking-widest border-b border-gray-50">Timestamp</th>
                                <th class="text-left px-8 py-5 text-[10px] font-black text-brand-muted uppercase tracking-widest border-b border-gray-50">Payload Size</th>
                                <th class="text-left px-8 py-5 text-[10px] font-black text-brand-muted uppercase tracking-widest border-b border-gray-50">Age</th>
                                <th class="text-right px-8 py-5 text-[10px] font-black text-brand-muted uppercase tracking-widest border-b border-gray-50">Protocols</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($backups as $backup)
                            <tr class="hover:bg-surface/50 transition-colors group">
                                <td class="px-8 py-6">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 bg-brand/5 rounded-xl flex items-center justify-center text-brand">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                                        </div>
                                        <div>
                                            <p class="text-xs font-black text-brand">{{ $backup['file_name'] }}</p>
                                            <p class="text-[10px] font-bold text-brand-muted">{{ $backup['last_modified'] }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-6">
                                    <span class="px-3 py-1 bg-surface text-brand text-[10px] font-black rounded-lg border border-gray-100">{{ $backup['file_size'] }}</span>
                                </td>
                                <td class="px-8 py-6">
                                    <span class="text-[11px] font-bold text-brand-muted italic">{{ $backup['age'] }}</span>
                                </td>
                                <td class="px-8 py-6 text-right">
                                    <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity translate-x-4 group-hover:translate-x-0 transition-transform duration-300">
                                        <a href="{{ route('orchestrator.settings.backups.download', $backup['file_name']) }}" class="w-10 h-10 bg-brand text-white rounded-xl flex items-center justify-center hover:bg-accent hover:text-brand transition-all shadow-lg shadow-brand/10">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                        </a>
                                        <button @click="selectedBackup = @js($backup); deleteModal = true" class="w-10 h-10 bg-red-50 text-red-600 rounded-xl flex items-center justify-center hover:bg-red-600 hover:text-white transition-all shadow-lg shadow-red-100">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
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
        </div>
    </div>

    <!-- Create Modal -->
    <div x-show="createModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-6">
        <div class="absolute inset-0 bg-brand/60 backdrop-blur-sm" @click="createModal = false"></div>
        <div class="bg-white rounded-3xl w-full max-w-xl relative z-10 shadow-2xl overflow-hidden animate-fade-in-up">
            <div class="p-8 border-b border-gray-50">
                <h3 class="text-xl font-black text-brand">System Integrity Protocol</h3>
                <p class="text-xs text-brand-muted font-medium mt-1">Select the capture scope for this snapshot.</p>
            </div>
            <form action="{{ route('orchestrator.settings.backups.create') }}" method="POST" class="p-8 space-y-6">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <label class="relative cursor-pointer group">
                        <input type="radio" name="option" value="all" checked class="peer sr-only">
                        <div class="p-6 bg-surface border-2 border-transparent peer-checked:border-brand peer-checked:bg-brand/5 rounded-2xl transition-all h-full">
                            <svg class="w-8 h-8 text-brand mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                            <h4 class="text-xs font-black text-brand uppercase">Full System</h4>
                            <p class="text-[9px] font-bold text-brand-muted mt-1 leading-tight">Database & all assets included.</p>
                        </div>
                    </label>
                    <label class="relative cursor-pointer group">
                        <input type="radio" name="option" value="only-db" class="peer sr-only">
                        <div class="p-6 bg-surface border-2 border-transparent peer-checked:border-brand peer-checked:bg-brand/5 rounded-2xl transition-all h-full">
                            <svg class="w-8 h-8 text-accent mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"/></svg>
                            <h4 class="text-xs font-black text-brand uppercase">Database</h4>
                            <p class="text-[9px] font-bold text-brand-muted mt-1 leading-tight">High-speed SQL dump only.</p>
                        </div>
                    </label>
                    <label class="relative cursor-pointer group">
                        <input type="radio" name="option" value="only-files" class="peer sr-only">
                        <div class="p-6 bg-surface border-2 border-transparent peer-checked:border-brand peer-checked:bg-brand/5 rounded-2xl transition-all h-full">
                            <svg class="w-8 h-8 text-blue-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
                            <h4 class="text-xs font-black text-brand uppercase">Media Vault</h4>
                            <p class="text-[9px] font-bold text-brand-muted mt-1 leading-tight">Files and assets only.</p>
                        </div>
                    </label>
                </div>

                <div class="p-4 bg-accent/10 border border-accent/20 rounded-xl">
                    <p class="text-[10px] font-black text-brand uppercase mb-1 flex items-center gap-2">
                        <svg class="w-4 h-4 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Operational Warning
                    </p>
                    <p class="text-[9px] font-bold text-brand-muted leading-relaxed">System performance may slightly degrade during full snapshot cycles. The process will run asynchronously.</p>
                </div>

                <div class="flex justify-end gap-3 pt-4">
                    <button type="button" @click="createModal = false" class="px-6 py-3 text-xs font-black text-brand-muted hover:text-brand transition-colors uppercase">Cancel</button>
                    <button type="submit" class="px-8 py-4 bg-brand text-white text-xs font-black rounded-2xl shadow-xl shadow-brand/10 hover:shadow-brand/30 transition-all uppercase flex items-center gap-3">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        Execute Protocol
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Modal -->
    <div x-show="deleteModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-6">
        <div class="absolute inset-0 bg-brand/60 backdrop-blur-sm" @click="deleteModal = false"></div>
        <div class="bg-white rounded-3xl w-full max-w-md relative z-10 shadow-2xl overflow-hidden animate-fade-in-up p-8 text-center">
            <div class="w-20 h-20 bg-red-50 text-red-600 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </div>
            <h3 class="text-xl font-black text-brand mb-2">Purge Snapshot?</h3>
            <p class="text-xs text-brand-muted font-medium mb-8">This action is irreversible. The backup <span class="text-brand font-black" x-text="selectedBackup?.file_name"></span> will be permanently deleted from the vault.</p>
            
            <form :action="'{{ route('orchestrator.settings.backups.delete', '') }}/' + selectedBackup?.file_name" method="POST">
                @csrf
                @method('DELETE')
                <div class="flex items-center gap-3">
                    <button type="button" @click="deleteModal = false" class="flex-1 py-4 bg-surface text-brand-muted text-xs font-black rounded-xl hover:bg-gray-100 transition-all uppercase">Cancel</button>
                    <button type="submit" class="flex-1 py-4 bg-red-600 text-white text-xs font-black rounded-xl shadow-lg shadow-red-200 hover:bg-red-700 transition-all uppercase">Confirm Purge</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    @keyframes fade-in-up {
        0% { opacity: 0; transform: translateY(20px); }
        100% { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in-up {
        animation: fade-in-up 0.3s ease-out forwards;
    }
</style>

@endsection
