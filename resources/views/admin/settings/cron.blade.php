@extends('admin.layout')
@section('title', 'Cron Jobs')
@section('content')

<div class="max-w-6xl mx-auto" x-data="cronManager()">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
            <div class="flex items-center gap-2 text-[10px] font-bold text-accent uppercase tracking-wider mb-1">
                <a href="{{ route('orchestrator.settings') }}" class="hover:text-brand transition-colors">Settings Hub</a>
                <span class="text-gray-300">/</span>
                <span>Cron Jobs</span>
            </div>
            <h2 class="text-2xl font-black text-brand tracking-tight">Cron Jobs</h2>
            <p class="text-sm text-brand-muted font-medium mt-0.5">Schedule and manage automated tasks</p>
        </div>
        <div class="flex items-center gap-2">
            <button @click="showInstallDefaults = true" class="bg-white border border-gray-200 text-brand-muted hover:text-brand hover:border-gray-300 px-5 py-2.5 rounded-lg text-xs font-bold transition-colors flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                Add Defaults
            </button>
            <button @click="showCreate = true" class="bg-brand text-white px-5 py-2.5 rounded-lg text-xs font-bold hover:bg-brand-light transition-colors flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Add Cron Job
            </button>
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

    <!-- Cron Info Card -->
    <div class="bg-gradient-to-r from-brand to-accent rounded-xl p-6 mb-8 text-white">
        <div class="flex items-start gap-4">
            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div class="flex-1">
                <h3 class="text-lg font-bold mb-2">Server Cron Configuration</h3>
                <p class="text-sm text-white/80 mb-4">Add this line to your server's crontab to enable scheduled tasks:</p>
                <div class="bg-black/30 rounded-lg p-4 font-mono text-sm">
                    * * * * * php {{ base_path('artisan') }} schedule:run >> /dev/null 2>&1
                </div>
                <button @click="generateCrontab()" class="mt-4 bg-white/20 hover:bg-white/30 px-4 py-2 rounded-lg text-xs font-bold transition-colors flex items-center gap-2 inline-flex">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Generate Crontab
                </button>
            </div>
        </div>
    </div>

    <!-- Crontab Output -->
    <div x-show="generatedCrontab" x-cloak class="mb-8 bg-gray-900 rounded-xl p-5">
        <div class="flex items-center justify-between mb-3">
            <h4 class="text-sm font-bold text-white">Generated Crontab</h4>
            <button @click="copyCrontab()" class="text-xs text-white/60 hover:text-white flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                Copy
            </button>
        </div>
        <pre class="text-green-400 font-mono text-xs whitespace-pre-wrap" x-text="generatedCrontab"></pre>
    </div>

    <!-- Cron Jobs Table -->
    <div class="bg-white border border-gray-100 rounded-xl overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-sm font-bold text-brand">Scheduled Tasks</h3>
            <span class="text-xs text-brand-muted">{{ $cronJobs->count() }} jobs</span>
        </div>

        @if($cronJobs->isEmpty())
        <div class="flex flex-col items-center justify-center py-20 text-brand-muted">
            <svg class="w-16 h-16 mb-4 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <p class="text-sm font-bold">No cron jobs configured</p>
            <p class="text-xs mt-1">Add a cron job or install defaults to get started</p>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-surface/50">
                        <th class="text-left px-5 py-3.5 text-[10px] font-bold text-brand-muted uppercase tracking-wider">Status</th>
                        <th class="text-left px-5 py-3.5 text-[10px] font-bold text-brand-muted uppercase tracking-wider">Name</th>
                        <th class="text-left px-5 py-3.5 text-[10px] font-bold text-brand-muted uppercase tracking-wider">Command</th>
                        <th class="text-left px-5 py-3.5 text-[10px] font-bold text-brand-muted uppercase tracking-wider">Schedule</th>
                        <th class="text-left px-5 py-3.5 text-[10px] font-bold text-brand-muted uppercase tracking-wider">Last Run</th>
                        <th class="text-right px-5 py-3.5 text-[10px] font-bold text-brand-muted uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($cronJobs as $job)
                    <tr class="hover:bg-surface/30 transition-colors">
                        <td class="px-5 py-4">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold
                                {{ $job->is_active ? 'bg-green-50 text-green-600' : 'bg-gray-100 text-gray-500' }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $job->is_active ? 'bg-green-500' : 'bg-gray-400' }}"></span>
                                {{ $job->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="px-5 py-4">
                            <div>
                                <p class="text-xs font-bold text-brand">{{ $job->name }}</p>
                                @if($job->description)
                                <p class="text-[10px] text-brand-muted mt-0.5">{{ $job->description }}</p>
                                @endif
                            </div>
                        </td>
                        <td class="px-5 py-4">
                            <code class="text-[10px] bg-surface px-2 py-1 rounded text-brand font-mono">{{ $job->command }}</code>
                        </td>
                        <td class="px-5 py-4">
                            <span class="text-xs font-bold text-brand">{{ $scheduleOptions[$job->schedule] ?? $job->schedule }}</span>
                        </td>
                        <td class="px-5 py-4">
                            @if($job->last_run)
                            <span class="text-xs text-brand-muted">{{ $job->last_run->diffForHumans() }}</span>
                            @else
                            <span class="text-xs text-brand-muted">Never</span>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-right">
                            <div class="flex items-center justify-end gap-1.5">
                                <form action="{{ route('orchestrator.settings.cron.toggle', $job->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="w-8 h-8 bg-surface rounded-lg flex items-center justify-center text-brand-muted hover:text-brand hover:bg-brand/5 transition-colors" title="{{ $job->is_active ? 'Disable' : 'Enable' }}">
                                        @if($job->is_active)
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        @else
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        @endif
                                    </button>
                                </form>
                                <form action="{{ route('orchestrator.settings.cron.run', $job->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="w-8 h-8 bg-brand/5 rounded-lg flex items-center justify-center text-brand hover:bg-brand hover:text-white transition-colors" title="Run Now">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    </button>
                                </form>
                                <button @click="editJob({{ $job->id }}, '{{ $job->name }}', '{{ $job->command }}', '{{ $job->schedule }}', '{{ addslashes($job->description ?? '') }}', {{ $job->is_active ? 'true' : 'false' }})" class="w-8 h-8 bg-surface rounded-lg flex items-center justify-center text-brand-muted hover:text-brand hover:bg-brand/5 transition-colors" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </button>
                                <form action="{{ route('orchestrator.settings.cron.destroy', $job->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-8 h-8 bg-red-50 rounded-lg flex items-center justify-center text-red-600 hover:bg-red-600 hover:text-white transition-colors" title="Delete" onclick="return confirm('Delete this cron job?')">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

    <!-- Create Modal -->
    <div x-show="showCreate" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-6">
        <div class="absolute inset-0 bg-brand/40 backdrop-blur-sm" @click="showCreate = false"></div>
        <div class="bg-white rounded-xl w-full max-w-lg relative z-10 shadow-2xl">
            <div class="px-6 py-5 border-b border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-bold text-brand">Add Cron Job</h3>
                        <p class="text-xs text-brand-muted mt-0.5">Create a new scheduled task</p>
                    </div>
                    <button @click="showCreate = false" class="w-8 h-8 bg-surface rounded-lg flex items-center justify-center text-brand-muted hover:text-brand transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </div>
            <form action="{{ route('orchestrator.settings.cron.store') }}" method="POST" class="p-6">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-[10px] font-bold text-brand-muted uppercase tracking-wider mb-2">Job Name</label>
                        <input type="text" name="name" required class="w-full px-3 py-2.5 bg-surface border border-gray-200 rounded-lg text-xs font-bold text-brand focus:outline-none focus:border-brand" placeholder="e.g., Daily Cleanup">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-brand-muted uppercase tracking-wider mb-2">Command</label>
                        <input type="text" name="command" required class="w-full px-3 py-2.5 bg-surface border border-gray-200 rounded-lg text-xs font-bold text-brand font-mono focus:outline-none focus:border-brand" placeholder="e.g., backup:scheduled">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-brand-muted uppercase tracking-wider mb-2">Schedule</label>
                        <select name="schedule" required class="w-full px-3 py-2.5 bg-surface border border-gray-200 rounded-lg text-xs font-bold text-brand focus:outline-none focus:border-brand">
                            @foreach($scheduleOptions as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-brand-muted uppercase tracking-wider mb-2">Description (Optional)</label>
                        <textarea name="description" rows="2" class="w-full px-3 py-2.5 bg-surface border border-gray-200 rounded-lg text-xs font-bold text-brand focus:outline-none focus:border-brand" placeholder="What does this job do?"></textarea>
                    </div>
                    <div class="flex items-center gap-2">
                        <input type="checkbox" name="is_active" id="is_active" value="1" checked class="w-4 h-4 text-brand rounded border-gray-300 focus:ring-brand">
                        <label for="is_active" class="text-xs font-bold text-brand">Enable immediately</label>
                    </div>
                </div>
                <div class="flex justify-end gap-2 mt-6">
                    <button type="button" @click="showCreate = false" class="px-5 py-2.5 text-xs font-bold text-brand-muted hover:text-brand transition-colors">Cancel</button>
                    <button type="submit" class="px-6 py-2.5 bg-brand text-white rounded-lg text-xs font-bold hover:bg-brand-light transition-colors">Create Job</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Modal -->
    <div x-show="showEdit" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-6">
        <div class="absolute inset-0 bg-brand/40 backdrop-blur-sm" @click="showEdit = false"></div>
        <div class="bg-white rounded-xl w-full max-w-lg relative z-10 shadow-2xl">
            <div class="px-6 py-5 border-b border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-bold text-brand">Edit Cron Job</h3>
                        <p class="text-xs text-brand-muted mt-0.5">Update scheduled task</p>
                    </div>
                    <button @click="showEdit = false" class="w-8 h-8 bg-surface rounded-lg flex items-center justify-center text-brand-muted hover:text-brand transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </div>
            <form :action="`{{ route('orchestrator.settings.cron.update', '') }}/${editingId}`" method="POST" class="p-6">
                @csrf
                @method('PUT')
                <div class="space-y-4">
                    <div>
                        <label class="block text-[10px] font-bold text-brand-muted uppercase tracking-wider mb-2">Job Name</label>
                        <input type="text" name="name" x-model="editingName" required class="w-full px-3 py-2.5 bg-surface border border-gray-200 rounded-lg text-xs font-bold text-brand focus:outline-none focus:border-brand">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-brand-muted uppercase tracking-wider mb-2">Command</label>
                        <input type="text" name="command" x-model="editingCommand" required class="w-full px-3 py-2.5 bg-surface border border-gray-200 rounded-lg text-xs font-bold text-brand font-mono focus:outline-none focus:border-brand">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-brand-muted uppercase tracking-wider mb-2">Schedule</label>
                        <select name="schedule" x-model="editingSchedule" required class="w-full px-3 py-2.5 bg-surface border border-gray-200 rounded-lg text-xs font-bold text-brand focus:outline-none focus:border-brand">
                            @foreach($scheduleOptions as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-brand-muted uppercase tracking-wider mb-2">Description</label>
                        <textarea name="description" x-model="editingDescription" rows="2" class="w-full px-3 py-2.5 bg-surface border border-gray-200 rounded-lg text-xs font-bold text-brand focus:outline-none focus:border-brand"></textarea>
                    </div>
                    <div class="flex items-center gap-2">
                        <input type="checkbox" name="is_active" id="edit_is_active" x-model="editingActive" class="w-4 h-4 text-brand rounded border-gray-300 focus:ring-brand">
                        <label for="edit_is_active" class="text-xs font-bold text-brand">Active</label>
                    </div>
                </div>
                <div class="flex justify-end gap-2 mt-6">
                    <button type="button" @click="showEdit = false" class="px-5 py-2.5 text-xs font-bold text-brand-muted hover:text-brand transition-colors">Cancel</button>
                    <button type="submit" class="px-6 py-2.5 bg-brand text-white rounded-lg text-xs font-bold hover:bg-brand-light transition-colors">Update Job</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Install Defaults Modal -->
    <div x-show="showInstallDefaults" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-6">
        <div class="absolute inset-0 bg-brand/40 backdrop-blur-sm" @click="showInstallDefaults = false"></div>
        <div class="bg-white rounded-xl w-full max-w-md relative z-10 shadow-2xl p-6">
            <div class="text-center">
                <div class="w-14 h-14 bg-brand/10 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-6 h-6 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                </div>
                <h3 class="text-lg font-bold text-brand mb-2">Install Default Cron Jobs</h3>
                <p class="text-xs text-brand-muted mb-6">Add common scheduled tasks like backup, queue worker, and cleanup jobs.</p>
                <form action="{{ route('orchestrator.settings.cron.defaults') }}" method="POST">
                    @csrf
                    <div class="flex gap-2">
                        <button type="button" @click="showInstallDefaults = false" class="flex-1 px-4 py-2.5 bg-surface text-brand-muted rounded-lg text-xs font-bold hover:bg-gray-100 transition-colors">Cancel</button>
                        <button type="submit" class="flex-1 px-4 py-2.5 bg-brand text-white rounded-lg text-xs font-bold hover:bg-brand-light transition-colors">Install</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function cronManager() {
    return {
        showCreate: false,
        showEdit: false,
        showInstallDefaults: false,
        generatedCrontab: '',
        editingId: '',
        editingName: '',
        editingCommand: '',
        editingSchedule: '',
        editingDescription: '',
        editingActive: true,

        editJob(id, name, command, schedule, description, isActive) {
            this.editingId = id;
            this.editingName = name;
            this.editingCommand = command;
            this.editingSchedule = schedule;
            this.editingDescription = description;
            this.editingActive = isActive;
            this.showEdit = true;
        },

        async generateCrontab() {
            try {
                const res = await fetch('{{ route('orchestrator.settings.cron.generate') }}');
                const data = await res.json();
                this.generatedCrontab = data.crontab;
            } catch (e) {
                console.error('Failed to generate crontab', e);
            }
        },

        copyCrontab() {
            navigator.clipboard.writeText(this.generatedCrontab);
            alert('Crontab copied to clipboard!');
        }
    };
}
</script>

@endsection