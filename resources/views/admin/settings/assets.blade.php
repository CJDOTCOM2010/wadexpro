@extends('admin.layout')
@section('title', 'Media Assets')
@section('content')

@php
$allFiles = $files ?? [];
$allDirs = $directories ?? [];
$currentDisk = $disk ?? 'public';
$currentPath = $directory ?? '';
$pathParts = $currentPath ? explode('/', $currentPath) : [];
$imageExts = ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp', 'bmp'];
$totalSize = 0;
foreach ($allFiles as $f) { $s = preg_replace('/[^0-9.]/', '', $f['size'] ?? '0'); $totalSize += (float)$s * 1024; }
@endphp

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

<div x-data="mediaManager()" x-init="init()" class="max-w-6xl mx-auto">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-black text-brand tracking-tight">Media Assets</h2>
            <p class="text-sm text-brand-muted font-medium mt-0.5">Manage your media library, images, and file assets.</p>
        </div>
        <div class="flex items-center gap-2">
            <button @click="viewMode = viewMode === 'grid' ? 'list' : 'grid'" class="p-2.5 bg-white border border-gray-200 rounded-lg hover:bg-surface transition-colors">
                <svg x-show="viewMode === 'grid'" class="w-4 h-4 text-brand-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                <svg x-show="viewMode === 'list'" class="w-4 h-4 text-brand-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6z"/></svg>
            </button>
            <button @click="showUpload = true" class="px-4 py-2.5 bg-brand text-white rounded-lg text-xs font-bold hover:bg-brand-light transition-colors flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                Upload
            </button>
            <button @click="showFolder = true" class="p-2.5 bg-white border border-gray-200 rounded-lg hover:bg-surface transition-colors" title="New Folder">
                <svg class="w-4 h-4 text-brand-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m-5 5h10a2 2 0 002-2V9a2 2 0 00-2-2h-4l-2-2H5a2 2 0 00-2 2v11a2 2 0 002 2z"/></svg>
            </button>
        </div>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-6">
        <div class="bg-white border border-gray-100 rounded-xl p-3.5">
            <p class="text-lg font-black text-brand">{{ count($allFiles) + count($allDirs) }}</p>
            <p class="text-[10px] font-bold text-brand-muted uppercase tracking-wider">Total Items</p>
        </div>
        <div class="bg-white border border-gray-100 rounded-xl p-3.5">
            <p class="text-lg font-black text-brand">{{ count($allFiles) }}</p>
            <p class="text-[10px] font-bold text-brand-muted uppercase tracking-wider">Files</p>
        </div>
        <div class="bg-white border border-gray-100 rounded-xl p-3.5">
            <p class="text-lg font-black text-brand">{{ count($allDirs) }}</p>
            <p class="text-[10px] font-bold text-brand-muted uppercase tracking-wider">Folders</p>
        </div>
        <div class="bg-white border border-gray-100 rounded-xl p-3.5">
            <p class="text-lg font-black text-brand">{{ $stats['storage_driver'] ?? 'local' }}</p>
            <p class="text-[10px] font-bold text-brand-muted uppercase tracking-wider">Driver</p>
        </div>
    </div>

    {{-- Breadcrumb + Search --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
        <div class="flex items-center gap-1.5 text-xs">
            <a href="{{ route('orchestrator.settings.assets') }}" class="font-bold text-brand hover:text-accent transition-colors">Media</a>
            @foreach($pathParts as $i => $part)
            <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <a href="{{ route('orchestrator.settings.assets', ['disk' => $currentDisk, 'path' => implode('/', array_slice($pathParts, 0, $i + 1))]) }}" class="font-bold text-brand hover:text-accent transition-colors">{{ $part }}</a>
            @endforeach
        </div>
        <div class="relative">
            <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input type="text" x-model="search" placeholder="Filter files..." class="bg-white border border-gray-200 rounded-lg pl-9 pr-3 py-2 text-sm outline-none focus:ring-2 focus:ring-accent/20 w-48 sm:w-64">
        </div>
    </div>

    {{-- Main Content --}}
    <div class="bg-white border border-gray-100 rounded-xl overflow-hidden">
        {{-- Toolbar --}}
        <div class="px-4 py-3 border-b border-gray-100 bg-surface/20 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="text-xs font-bold text-brand-muted" x-text="filteredCount() + ' items'"></span>
            </div>
            <div class="flex items-center gap-2">
                <select @change="sortBy = $event.target.value" class="bg-white border border-gray-200 rounded-lg px-2.5 py-1.5 text-[10px] font-bold outline-none">
                    <option value="name">Name</option>
                    <option value="date">Date</option>
                    <option value="size">Size</option>
                </select>
            </div>
        </div>

        {{-- Grid View --}}
        <div x-show="viewMode === 'grid'" class="p-4">
            <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 lg:grid-cols-6 gap-3">
                {{-- Up one level --}}
                @if($currentPath)
                <a href="{{ route('orchestrator.settings.assets', ['disk' => $currentDisk, 'path' => dirname($currentPath) === '.' ? '' : dirname($currentPath)]) }}" class="flex flex-col items-center justify-center gap-2 p-4 rounded-lg border border-dashed border-gray-200 hover:border-accent/40 hover:bg-accent/5 transition-colors cursor-pointer aspect-square">
                    <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    <span class="text-[10px] font-bold text-gray-400">Up</span>
                </a>
                @endif

                {{-- Folders --}}
                @foreach($allDirs as $dir)
                <a href="{{ route('orchestrator.settings.assets', ['disk' => $currentDisk, 'path' => $dir['path']]) }}" class="flex flex-col items-center gap-2 p-4 rounded-lg border border-gray-100 hover:border-accent/40 hover:bg-accent/5 transition-colors cursor-pointer group aspect-square" @click="navigateFolder">
                    <div class="w-full flex items-center justify-center">
                        <svg class="w-10 h-10 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
                    </div>
                    <span class="text-[10px] font-bold text-brand text-center truncate w-full">{{ $dir['name'] }}</span>
                </a>
                @endforeach

                {{-- Files --}}
                @foreach($allFiles as $file)
                @php $ext = strtolower($file['extension'] ?? ''); $isImg = in_array($ext, $imageExts); @endphp
                <div x-show="search === '' || '{{ strtolower($file['name']) }}'.includes(search.toLowerCase())" class="flex flex-col items-center gap-2 p-3 rounded-lg border border-gray-100 hover:border-accent/40 hover:bg-accent/5 transition-colors cursor-pointer group relative" @click="previewFile('{{ $file['path'] }}', '{{ $file['name'] }}', '{{ $file['url'] }}', '{{ $file['size'] }}', '{{ $isImg ? 'image' : $ext }}')">
                    <button @click.stop="confirmDelete('{{ $file['path'] }}', '{{ $file['name'] }}')" class="absolute top-1.5 right-1.5 w-6 h-6 rounded bg-white/80 border border-gray-100 flex items-center justify-center opacity-0 group-hover:opacity-100 hover:bg-red-50 transition-all z-10" title="Delete">
                        <svg class="w-3 h-3 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                    <div class="w-full flex items-center justify-center h-16">
                        @if($isImg)
                        <img src="{{ $file['url'] }}" class="max-h-16 max-w-full rounded object-contain" onerror="this.parentElement.innerHTML='<svg class=\'w-8 h-8 text-gray-300\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'1.5\' d=\'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z\'/></svg>'">
                        @else
                        <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        @endif
                    </div>
                    <span class="text-[9px] font-bold text-brand text-center truncate w-full">{{ $file['name'] }}</span>
                    <span class="text-[8px] text-brand-muted">{{ $file['size'] }}</span>
                </div>
                @endforeach
            </div>
        </div>

        {{-- List View --}}
        <div x-show="viewMode === 'list'" class="divide-y divide-gray-50">
            @if($currentPath)
            <a href="{{ route('orchestrator.settings.assets', ['disk' => $currentDisk, 'path' => dirname($currentPath) === '.' ? '' : dirname($currentPath)]) }}" class="flex items-center gap-3 px-5 py-3 hover:bg-surface/20 transition-colors">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                <span class="text-xs font-bold text-brand-muted">..</span>
            </a>
            @endif
            @foreach($allDirs as $dir)
            <a href="{{ route('orchestrator.settings.assets', ['disk' => $currentDisk, 'path' => $dir['path']]) }}" class="flex items-center gap-3 px-5 py-3 hover:bg-surface/20 transition-colors">
                <svg class="w-4 h-4 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
                <span class="text-xs font-bold text-brand">{{ $dir['name'] }}</span>
            </a>
            @endforeach
            @foreach($allFiles as $file)
            @php $ext = strtolower($file['extension'] ?? ''); $isImg = in_array($ext, $imageExts); @endphp
            <div x-show="search === '' || '{{ strtolower($file['name']) }}'.includes(search.toLowerCase())" class="flex items-center gap-3 px-5 py-3 hover:bg-surface/20 transition-colors group cursor-pointer" @click="previewFile('{{ $file['path'] }}', '{{ $file['name'] }}', '{{ $file['url'] }}', '{{ $file['size'] }}', '{{ $isImg ? 'image' : $ext }}')">
                @if($isImg)
                <img src="{{ $file['url'] }}" class="w-8 h-8 rounded object-cover" onerror="this.style.display='none'">
                @endif
                <svg class="w-4 h-4 {{ $isImg ? 'hidden' : '' }} text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-bold text-brand truncate">{{ $file['name'] }}</p>
                    <p class="text-[9px] text-brand-muted">{{ $file['size'] }} · {{ $file['last_modified'] }}</p>
                </div>
                <button @click.stop="confirmDelete('{{ $file['path'] }}', '{{ $file['name'] }}')" class="w-7 h-7 rounded-lg flex items-center justify-center opacity-0 group-hover:opacity-100 text-gray-300 hover:text-red-500 hover:bg-red-50 transition-all" title="Delete">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </button>
            </div>
            @endforeach
        </div>

        @if(count($allFiles) === 0 && count($allDirs) === 0 && !$currentPath)
        <div class="flex flex-col items-center justify-center py-16 text-brand-muted">
            <svg class="w-16 h-16 mb-4 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            <p class="text-sm font-bold">Media library is empty</p>
            <p class="text-xs mt-1">Upload files or create folders to get started.</p>
            <button @click="showUpload = true" class="mt-4 px-4 py-2 bg-brand text-white rounded-lg text-xs font-bold hover:bg-brand-light transition-colors">Upload Files</button>
        </div>
        @endif
    </div>

    {{-- Upload Modal --}}
    <div x-show="showUpload" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-brand/50 backdrop-blur-sm" @click="showUpload = false"></div>
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg relative z-10" @click.outside="showUpload = false">
            <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 bg-accent/10 rounded-lg flex items-center justify-center text-accent">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-brand">Upload Assets</h3>
                        <p class="text-xs text-brand-muted">Select files to upload to current folder.</p>
                    </div>
                </div>
                <button @click="showUpload = false" class="w-7 h-7 bg-surface rounded-lg flex items-center justify-center text-brand-muted hover:text-brand transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form action="{{ route('orchestrator.settings.assets.upload') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
                @csrf
                <input type="hidden" name="path" value="{{ $currentPath }}">
                <input type="hidden" name="disk" value="{{ $currentDisk }}">
                <div class="border-2 border-dashed border-gray-200 rounded-lg p-8 text-center hover:border-accent/40 transition-colors">
                    <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                    <p class="text-sm font-bold text-brand mb-1">Drop files here or click to browse</p>
                    <p class="text-[10px] text-brand-muted">Max 10MB per file</p>
                    <input type="file" name="files[]" multiple required class="mt-3 text-sm">
                </div>
                <div class="flex justify-end gap-2 pt-2 border-t border-gray-100">
                    <button type="button" @click="showUpload = false" class="px-4 py-2 text-xs font-bold text-brand-muted hover:text-brand transition-colors">Cancel</button>
                    <button type="submit" class="px-5 py-2 bg-brand text-white rounded-lg text-xs font-bold hover:bg-brand-light transition-colors">Upload</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Create Folder Modal --}}
    <div x-show="showFolder" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-brand/50 backdrop-blur-sm" @click="showFolder = false"></div>
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-sm relative z-10" @click.outside="showFolder = false">
            <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 bg-accent/10 rounded-lg flex items-center justify-center text-accent">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m-5 5h10a2 2 0 002-2V9a2 2 0 00-2-2h-4l-2-2H5a2 2 0 00-2 2v11a2 2 0 002 2z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-brand">New Folder</h3>
                        <p class="text-xs text-brand-muted">Create a new folder in the current directory.</p>
                    </div>
                </div>
                <button @click="showFolder = false" class="w-7 h-7 bg-surface rounded-lg flex items-center justify-center text-brand-muted hover:text-brand transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form action="{{ route('orchestrator.settings.assets.create-folder') }}" method="POST" class="p-6 space-y-4">
                @csrf
                <input type="hidden" name="path" value="{{ $currentPath }}">
                <input type="hidden" name="disk" value="{{ $currentDisk }}">
                <div>
                    <label class="text-[10px] font-bold text-brand-muted uppercase tracking-wider mb-1.5 block">Folder Name</label>
                    <input type="text" name="name" required placeholder="e.g. Images, Documents" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium outline-none focus:ring-2 focus:ring-accent/20 transition-shadow">
                </div>
                <div class="flex justify-end gap-2 pt-2 border-t border-gray-100">
                    <button type="button" @click="showFolder = false" class="px-4 py-2 text-xs font-bold text-brand-muted hover:text-brand transition-colors">Cancel</button>
                    <button type="submit" class="px-5 py-2 bg-brand text-white rounded-lg text-xs font-bold hover:bg-brand-light transition-colors">Create</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Preview Modal --}}
    <div x-show="showPreview" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-brand/60 backdrop-blur-sm" @click="showPreview = false"></div>
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg relative z-10" @click.outside="showPreview = false">
            <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
                <div class="flex items-center gap-3 min-w-0">
                    <div>
                        <h3 class="text-base font-bold text-brand truncate" x-text="preview.name"></h3>
                        <p class="text-xs text-brand-muted" x-text="preview.size + ' · ' + preview.type"></p>
                    </div>
                </div>
                <button @click="showPreview = false" class="w-7 h-7 bg-surface rounded-lg flex items-center justify-center text-brand-muted hover:text-brand transition-colors shrink-0 ml-2">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="p-6 flex flex-col items-center">
                <template x-if="preview.isImage">
                    <img :src="preview.url" class="max-h-80 rounded-lg object-contain">
                </template>
                <template x-if="!preview.isImage">
                    <div class="py-10 text-center text-brand-muted">
                        <svg class="w-16 h-16 mx-auto mb-3 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        <p class="text-sm font-bold">Preview not available</p>
                    </div>
                </template>
                <div class="mt-4 w-full">
                    <label class="text-[10px] font-bold text-brand-muted uppercase tracking-wider mb-1.5 block">File URL</label>
                    <div class="flex gap-2">
                        <input type="text" :value="preview.url" readonly class="flex-1 bg-surface border border-gray-100 rounded-lg px-4 py-2 text-xs font-mono outline-none">
                        <button @click="copyUrl(preview.url)" class="px-3 py-2 bg-brand text-white rounded-lg text-[10px] font-bold hover:bg-brand-light transition-colors shrink-0">Copy</button>
                    </div>
                </div>
            </div>
            <div class="px-6 py-4 border-t border-gray-100 flex justify-end gap-2 bg-surface/20">
                <a :href="preview.url" target="_blank" class="px-4 py-2 bg-white border border-gray-200 text-xs font-bold rounded-lg hover:bg-surface transition-colors">Open</a>
                <button @click="showPreview = false; confirmDelete(preview.path, preview.name)" class="px-4 py-2 bg-red-50 text-red-600 text-xs font-bold rounded-lg hover:bg-red-100 transition-colors">Delete</button>
            </div>
        </div>
    </div>

    {{-- Delete Confirmation --}}
    <div x-show="deleteStep > 0" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-brand/60 backdrop-blur-sm" @click="closeDelete()"></div>
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md relative z-10" @click.outside="closeDelete()">
            <template x-if="deleteStep === 1">
                <div class="p-6">
                    <div class="w-14 h-14 bg-red-50 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-7 h-7 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h3 class="text-lg font-bold text-brand text-center mb-2">Delete File?</h3>
                    <p class="text-sm text-brand-muted text-center mb-6">Permanently delete <strong class="text-brand" x-text="deleteLabel"></strong>?</p>
                    <div class="flex gap-2">
                        <button type="button" @click="closeDelete()" class="flex-1 px-4 py-2.5 bg-surface text-brand-muted rounded-lg text-xs font-bold hover:bg-gray-100">Cancel</button>
                        <button type="button" @click="executeDelete()" class="flex-1 px-4 py-2.5 bg-red-600 text-white rounded-lg text-xs font-bold hover:bg-red-700">Delete</button>
                    </div>
                </div>
            </template>
        </div>
    </div>
</div>

<script>
function mediaManager() {
    return {
        viewMode: 'grid',
        showUpload: false, showFolder: false, showPreview: false,
        search: '', sortBy: 'name',
        preview: { name: '', path: '', url: '', size: '', type: '', isImage: false },
        deleteStep: 0, deletePath: '', deleteLabel: '', deleteConfirm: '',
        init() {},
        filteredCount() {
            return 0; // handled by Alpine x-show
        },
        previewFile(path, name, url, size, type) {
            this.preview = { path, name, url, size, type, isImage: type === 'image' };
            this.showPreview = true;
        },
        copyUrl(url) {
            navigator.clipboard.writeText(url);
        },
        confirmDelete(path, label) {
            this.deletePath = path; this.deleteLabel = label; this.deleteStep = 1;
        },
        closeDelete() { this.deleteStep = 0; this.deleteConfirm = ''; },
        executeDelete() {
            const f = document.createElement('form'); f.method = 'POST'; f.action = '{{ route('orchestrator.settings.assets.delete') }}';
            const c = document.createElement('input'); c.type = 'hidden'; c.name = '_token'; c.value = '{{ csrf_token() }}'; f.appendChild(c);
            const p = document.createElement('input'); p.type = 'hidden'; p.name = 'path'; p.value = this.deletePath; f.appendChild(p);
            const d = document.createElement('input'); d.type = 'hidden'; d.name = 'disk'; d.value = '{{ $currentDisk }}'; f.appendChild(d);
            document.body.appendChild(f); f.submit();
        }
    };
}
</script>
<style>[x-cloak] { display: none !important; }</style>
@endsection