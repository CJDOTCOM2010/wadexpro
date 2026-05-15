@extends('admin.layout')
@section('title', 'Gallery Assets')
@section('content')

<div class="p-8 lg:p-12 max-w-[1600px] mx-auto" x-data="{ uploadModal: false, deleteModal: false, selectedAsset: null }">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-12 gap-6">
        <div>
            <div class="flex items-center gap-2 text-[10px] font-black text-accent uppercase tracking-[0.2em] mb-2">
                <span>System Management</span>
                <span class="text-gray-300">/</span>
                <span>Asset Library</span>
            </div>
            <h2 class="text-3xl font-black text-brand tracking-tight">Gallery Assets</h2>
            <p class="text-sm text-brand-muted font-medium mt-1">Centralized control for all system uploads, media, and storage nodes.</p>
        </div>
        
            <button @click="uploadModal = true" class="bg-brand text-white px-8 py-4 rounded-xl text-xs font-black shadow-xl hover:shadow-brand/20 hover:-translate-y-0.5 transition-all flex items-center gap-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                Upload New Assets
            </button>
        </div>
    </div>

    @if(session('error'))
    <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl flex items-center gap-3">
        <svg class="w-5 h-5 text-red-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        <p class="text-sm font-medium text-red-700">{{ session('error') }}</p>
    </div>
    @endif

    @if(session('success'))
    <div class="mb-8 p-4 bg-green-50 border border-green-200 rounded-xl flex items-center gap-3 animate-fade-in">
        <svg class="w-5 h-5 text-green-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        <span class="text-sm font-medium text-green-700">{{ session('success') }}</span>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-10">
        
        <!-- Left: Stats & Config -->
        <div class="lg:col-span-1 space-y-8">
            <!-- Storage Health -->
            <div class="bg-brand rounded-2xl p-8 text-white relative overflow-hidden group shadow-2xl">
                <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-white/10 rounded-full blur-3xl group-hover:scale-150 transition-transform duration-1000"></div>
                <div class="relative z-10">
                    <p class="text-[10px] font-black text-white/40 uppercase tracking-[0.2em] mb-4">Storage Node Status</p>
                    <div class="flex items-center gap-4 mb-6">
                        <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"/></svg>
                        </div>
                        <div>
                            <h4 class="text-xl font-black">{{ strtoupper($stats['disk']) }}</h4>
                            <p class="text-[10px] font-bold text-white/50 uppercase tracking-widest">{{ $stats['storage_driver'] }} driver active</p>
                        </div>
                    </div>
                    
                    <div class="space-y-4">
                        <div class="flex justify-between items-end">
                            <span class="text-[10px] font-black text-white/40 uppercase">Total Files</span>
                            <span class="text-sm font-black">{{ $stats['total_files'] }}</span>
                        </div>
                        <div class="flex justify-between items-end">
                            <span class="text-[10px] font-black text-white/40 uppercase">Directories</span>
                            <span class="text-sm font-black">{{ $stats['total_dirs'] }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Storage Config -->
            <div class="bg-white rounded-2xl border border-gray-50 shadow-xl p-8">
                <h3 class="text-sm font-black text-brand mb-6 flex items-center gap-2">
                    <svg class="w-4 h-4 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Storage Configuration
                </h3>
                
                <form action="{{ route('orchestrator.settings.assets.config') }}" method="POST" class="space-y-6">
                    @csrf
                    <div>
                        <label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-2">Default Storage Disk</label>
                        <select name="settings[default_storage_disk]" class="w-full bg-surface border-2 border-transparent focus:border-accent rounded-lg py-3 px-4 text-xs font-bold outline-none">
                            <option value="public" {{ \App\Modules\Admin\Models\SystemSetting::get('default_storage_disk') == 'public' ? 'selected' : '' }}>Local Public Disk</option>
                            <option value="s3" {{ \App\Modules\Admin\Models\SystemSetting::get('default_storage_disk') == 's3' ? 'selected' : '' }}>Amazon S3 / DigitalOcean</option>
                            <option value="supabase" {{ \App\Modules\Admin\Models\SystemSetting::get('default_storage_disk') == 'supabase' ? 'selected' : '' }}>Supabase Storage</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-brand-muted uppercase tracking-widest mb-2">Max Upload Size (MB)</label>
                        <input type="number" name="settings[max_upload_size]" value="{{ \App\Modules\Admin\Models\SystemSetting::get('max_upload_size', '10') }}" class="w-full bg-surface border-2 border-transparent focus:border-accent rounded-lg py-3 px-4 text-xs font-bold outline-none">
                    </div>

                    <button type="submit" class="w-full py-3 bg-brand text-white text-[10px] font-black rounded-lg hover:bg-brand-hover transition-all">Update Storage Node</button>
                </form>
            </div>
        </div>

        <!-- Right: File Browser -->
        <div class="lg:col-span-3">
            <div class="bg-white rounded-2xl border border-gray-50 shadow-2xl overflow-hidden min-h-[600px]">
                <!-- Toolbar -->
                <div class="p-6 border-b border-gray-50 flex items-center justify-between bg-surface/50">
                    <div class="flex items-center gap-4">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('orchestrator.settings.assets', ['disk' => $disk]) }}" class="text-xs font-bold text-brand hover:text-accent transition-colors">Root</a>
                            @if($directory)
                                @php $parts = explode('/', $directory); $currentPath = ''; @endphp
                                @foreach($parts as $part)
                                    @php $currentPath .= ($currentPath ? '/' : '') . $part; @endphp
                                    <span class="text-gray-300">/</span>
                                    <a href="{{ route('orchestrator.settings.assets', ['disk' => $disk, 'path' => $currentPath]) }}" class="text-xs font-bold text-brand hover:text-accent transition-colors">{{ $part }}</a>
                                @endforeach
                            @endif
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-3">
                        <div class="relative">
                            <input type="text" placeholder="Search assets..." class="bg-white border border-gray-100 rounded-lg py-2 pl-10 pr-4 text-[11px] font-medium outline-none focus:border-accent transition-all w-64 shadow-sm">
                            <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                    </div>
                </div>

                <!-- Content -->
                <div class="p-8">
                    @if(empty($files) && empty($directories))
                        <div class="flex flex-col items-center justify-center py-20 opacity-30">
                            <svg class="w-20 h-20 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
                            <p class="text-sm font-black uppercase tracking-widest">No assets found in this path</p>
                        </div>
                    @else
                        <div class="grid grid-cols-2 md:grid-cols-4 xl:grid-cols-5 gap-6">
                            <!-- Directories -->
                            @foreach($directories as $dir)
                            <a href="{{ route('orchestrator.settings.assets', ['disk' => $disk, 'path' => $dir['path']]) }}" class="group bg-surface rounded-xl p-4 border border-transparent hover:border-accent hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                                <div class="w-full aspect-square bg-accent/10 rounded-lg flex items-center justify-center mb-4 group-hover:bg-accent/20 transition-colors">
                                    <svg class="w-12 h-12 text-accent" fill="currentColor" viewBox="0 0 24 24"><path d="M10 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2h-8l-2-2z"/></svg>
                                </div>
                                <h5 class="text-[11px] font-black text-brand truncate">{{ $dir['name'] }}</h5>
                                <p class="text-[9px] font-bold text-brand-muted uppercase tracking-tighter">Directory</p>
                            </a>
                            @endforeach

                            <!-- Files -->
                            @foreach($files as $file)
                            <div class="group bg-white rounded-xl p-4 border border-gray-50 shadow-sm hover:border-accent hover:shadow-2xl hover:-translate-y-1 transition-all duration-300 relative">
                                <div class="w-full aspect-square bg-surface rounded-lg flex items-center justify-center mb-4 overflow-hidden relative">
                                    @if($file['is_image'])
                                        <img src="{{ $file['url'] }}" alt="{{ $file['name'] }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                    @else
                                        <div class="text-brand-muted opacity-50">
                                            <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                        </div>
                                    @endif
                                    
                                    <!-- Actions Overlay -->
                                    <div class="absolute inset-0 bg-brand/80 opacity-0 group-hover:opacity-100 transition-opacity flex flex-col items-center justify-center gap-2 p-4">
                                        <button @click="selectedAsset = @js($file); deleteModal = true" class="w-full py-2 bg-red-600 text-white text-[9px] font-black rounded-lg hover:bg-red-700 transition-colors uppercase">Delete</button>
                                        <a href="{{ $file['url'] }}" target="_blank" class="w-full py-2 bg-white text-brand text-[9px] font-black rounded-lg hover:bg-gray-100 transition-colors uppercase text-center">View Original</a>
                                        <button onclick="copyToClipboard('{{ $file['url'] }}')" class="w-full py-2 bg-accent text-brand text-[9px] font-black rounded-lg hover:bg-accent/80 transition-colors uppercase">Copy URL</button>
                                    </div>
                                </div>
                                <h5 class="text-[11px] font-black text-brand truncate" title="{{ $file['name'] }}">{{ $file['name'] }}</h5>
                                <div class="flex items-center justify-between mt-1">
                                    <p class="text-[9px] font-bold text-brand-muted uppercase">{{ $file['extension'] }}</p>
                                    <p class="text-[9px] font-bold text-brand-muted">{{ $file['size'] }}</p>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Upload Modal -->
    <div x-show="uploadModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-6">
        <div class="absolute inset-0 bg-brand/60 backdrop-blur-sm" @click="uploadModal = false"></div>
        <div class="bg-white rounded-3xl w-full max-w-xl relative z-10 shadow-2xl overflow-hidden animate-fade-in-up">
            <div class="p-8 border-b border-gray-50 flex items-center justify-between">
                <div>
                    <h3 class="text-xl font-black text-brand">Upload New Assets</h3>
                    <p class="text-xs text-brand-muted font-medium">To path: <span class="text-accent font-bold">{{ $directory ?: 'Root' }}</span></p>
                </div>
                <button @click="uploadModal = false" class="text-gray-300 hover:text-brand transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form action="{{ route('orchestrator.settings.assets.upload') }}" method="POST" enctype="multipart/form-data" class="p-8 space-y-6">
                @csrf
                <input type="hidden" name="disk" value="{{ $disk }}">
                <input type="hidden" name="path" value="{{ $directory }}">
                
                <div class="border-4 border-dashed border-gray-50 rounded-2xl p-12 text-center group hover:border-accent hover:bg-accent/5 transition-all cursor-pointer relative">
                    <input type="file" name="files[]" multiple class="absolute inset-0 opacity-0 cursor-pointer" id="file-input" onchange="updateFileList(this)">
                    <div class="pointer-events-none">
                        <svg class="w-16 h-16 text-accent mx-auto mb-4 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                        <p class="text-sm font-black text-brand">Drop files here or click to browse</p>
                        <p class="text-[10px] text-brand-muted font-bold mt-2 uppercase tracking-widest">Max file size: 10MB</p>
                    </div>
                </div>
                
                <div id="file-list" class="space-y-2 max-h-40 overflow-y-auto pr-2"></div>

                <div class="flex justify-end gap-3 pt-4">
                    <button type="button" @click="uploadModal = false" class="px-6 py-3 text-xs font-black text-brand-muted hover:text-brand transition-colors uppercase">Cancel</button>
                    <button type="submit" class="px-8 py-3 bg-brand text-white text-xs font-black rounded-xl shadow-lg hover:shadow-brand/20 transition-all uppercase">Start Upload</button>
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
            <h3 class="text-xl font-black text-brand mb-2">Delete Asset?</h3>
            <p class="text-xs text-brand-muted font-medium mb-8">This action is irreversible. The file <span class="text-brand font-black" x-text="selectedAsset?.name"></span> will be permanently purged.</p>
            
            <form action="{{ route('orchestrator.settings.assets.delete') }}" method="POST">
                @csrf
                <input type="hidden" name="disk" value="{{ $disk }}">
                <input type="hidden" name="path" x-model="selectedAsset?.path">
                
                <div class="flex items-center gap-3">
                    <button type="button" @click="deleteModal = false" class="flex-1 py-4 bg-surface text-brand-muted text-xs font-black rounded-xl hover:bg-gray-100 transition-all uppercase">Cancel</button>
                    <button type="submit" class="flex-1 py-4 bg-red-600 text-white text-xs font-black rounded-xl shadow-lg shadow-red-200 hover:bg-red-700 transition-all uppercase">Confirm Purge</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(() => {
            alert('URL copied to clipboard!');
        });
    }

    function updateFileList(input) {
        const list = document.getElementById('file-list');
        list.innerHTML = '';
        if (input.files.length > 0) {
            Array.from(input.files).forEach(file => {
                const div = document.createElement('div');
                div.className = 'flex items-center justify-between p-3 bg-surface rounded-lg';
                div.innerHTML = `
                    <span class="text-[10px] font-black text-brand truncate max-w-[200px]">${file.name}</span>
                    <span class="text-[9px] font-bold text-brand-muted">${(file.size / 1024 / 1024).toFixed(2)} MB</span>
                `;
                list.appendChild(div);
            });
        }
    }
</script>

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
