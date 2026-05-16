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

<div x-data="mediaManager()" x-init="init()" class="bg-[#f0f2f5] border border-gray-200 rounded-lg shadow-sm flex flex-col h-[calc(100vh-100px)] overflow-hidden text-sm text-[#1c2c44]">
    
    {{-- Top Toolbar --}}
    <div class="flex items-center justify-between px-4 py-3 border-b border-gray-200 bg-white shrink-0 gap-4 overflow-x-auto">
        <div class="flex items-center gap-2 shrink-0">
            <!-- Upload -->
            <button @click="showUpload = true" class="flex items-center gap-2 px-3 py-1.5 bg-brand text-white font-medium rounded hover:bg-brand-light transition-colors shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                Upload
                <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
            
            <!-- New Folder -->
            <button @click="showFolder = true" class="p-1.5 bg-brand text-white rounded hover:bg-brand-light transition-colors shadow-sm" title="New Folder">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m-5 5h10a2 2 0 002-2V9a2 2 0 00-2-2h-4l-2-2H5a2 2 0 00-2 2v11a2 2 0 002 2z"/></svg>
            </button>
            
            <!-- Refresh -->
            <a href="{{ route('orchestrator.settings.assets', ['disk' => $currentDisk, 'path' => $currentPath]) }}" class="p-1.5 bg-brand text-white rounded hover:bg-brand-light transition-colors shadow-sm" title="Refresh">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
            </a>
            
            <!-- Filters -->
            <button class="flex items-center gap-2 px-3 py-1.5 bg-brand text-white font-medium rounded hover:bg-brand-light transition-colors shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                Everything
                <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <button class="flex items-center gap-2 px-3 py-1.5 bg-brand text-white font-medium rounded hover:bg-brand-light transition-colors shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                All media
                <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
        </div>
        
        <div class="relative max-w-xs w-full shrink-0">
            <input type="text" x-model="search" placeholder="Search in current folder" class="w-full pl-3 pr-10 py-1.5 border border-gray-300 text-gray-700 rounded outline-none focus:border-blue-500 transition-colors text-sm">
            <svg class="w-4 h-4 text-gray-400 absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        </div>
    </div>

    {{-- Secondary Toolbar --}}
    <div class="flex items-center justify-between px-4 py-2 border-b border-gray-200 bg-white shrink-0">
        <div class="flex items-center gap-2 text-[#2563EB] font-medium text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            <a href="{{ route('orchestrator.settings.assets') }}" class="hover:underline">All media</a>
            @foreach($pathParts as $i => $part)
            <span class="text-gray-400">/</span>
            <a href="{{ route('orchestrator.settings.assets', ['disk' => $currentDisk, 'path' => implode('/', array_slice($pathParts, 0, $i + 1))]) }}" class="hover:underline">{{ $part }}</a>
            @endforeach
        </div>
        
        <div class="flex items-center gap-3">
            <div class="flex items-center border border-gray-200 rounded overflow-hidden">
                <span class="px-2 py-1 text-xs text-gray-800 font-medium bg-gray-50 border-r border-gray-200 tracking-wide">A-Z</span>
                <select @change="sortBy = $event.target.value" class="py-1 px-2 pr-6 text-sm text-gray-700 outline-none bg-white cursor-pointer">
                    <option value="name">Sort</option>
                    <option value="date">Date</option>
                    <option value="size">Size</option>
                </select>
            </div>
            
            <button class="flex items-center gap-1.5 px-3 py-1 text-gray-600 font-medium border border-gray-200 rounded hover:bg-gray-50 transition-colors">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122"/></svg>
                Actions
            </button>
            
            <div class="flex border border-gray-200 rounded overflow-hidden bg-white">
                <button @click="viewMode = 'grid'" class="p-1 text-gray-500 hover:text-blue-600 hover:bg-blue-50 transition-colors" :class="viewMode === 'grid' && 'text-blue-600 bg-blue-50'">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                </button>
                <div class="w-px bg-gray-200"></div>
                <button @click="viewMode = 'list'" class="p-1 text-gray-500 hover:text-blue-600 hover:bg-blue-50 transition-colors" :class="viewMode === 'list' && 'text-blue-600 bg-blue-50'">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                </button>
            </div>
            
            <button @click="sidebarOpen = !sidebarOpen" class="p-1 text-gray-500 border border-gray-200 rounded hover:bg-gray-50 transition-colors" :class="sidebarOpen && 'bg-gray-100'">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"/></svg>
            </button>
        </div>
    </div>

    {{-- Main Split View --}}
    <div class="flex flex-1 min-h-0 relative bg-white">
        
        {{-- Assets Area --}}
        <div class="flex-1 overflow-y-auto p-4 bg-white relative">
            {{-- Grid View --}}
            <div x-show="viewMode === 'grid'" class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-6 lg:grid-cols-8 gap-3">
                {{-- Up one level --}}
                @if($currentPath)
                <a href="{{ route('orchestrator.settings.assets', ['disk' => $currentDisk, 'path' => dirname($currentPath) === '.' ? '' : dirname($currentPath)]) }}" class="flex flex-col bg-[#F8F9FA] hover:bg-[#F1F3F5] transition-colors cursor-pointer overflow-hidden h-32 relative border border-gray-100 rounded">
                    <div class="flex-1 flex items-center justify-center p-4">
                        <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    </div>
                    <div class="h-7 bg-[#E9ECEF] flex items-center justify-center px-2">
                        <span class="text-xs font-medium text-gray-600">..</span>
                    </div>
                </a>
                @endif

                {{-- Folders --}}
                @foreach($allDirs as $dir)
                <a href="{{ route('orchestrator.settings.assets', ['disk' => $currentDisk, 'path' => $dir['path']]) }}" class="flex flex-col bg-[#F8F9FA] hover:bg-[#F1F3F5] transition-colors cursor-pointer overflow-hidden h-32 relative border border-gray-100 rounded">
                    <div class="flex-1 flex items-center justify-center p-4">
                        <svg class="w-10 h-10 text-[#1e293b]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
                    </div>
                    <div class="h-7 bg-[#F1F3F5] flex items-center justify-center px-2">
                        <span class="text-xs font-medium text-gray-600 truncate text-center w-full">{{ $dir['name'] }}</span>
                    </div>
                </a>
                @endforeach

                {{-- Files --}}
                @foreach($allFiles as $file)
                @php $ext = strtolower($file['extension'] ?? ''); $isImg = in_array($ext, $imageExts); @endphp
                <div x-show="search === '' || '{{ strtolower($file['name']) }}'.includes(search.toLowerCase())" class="flex flex-col bg-[#F8F9FA] hover:ring-2 hover:ring-blue-500 hover:ring-offset-1 transition-all cursor-pointer overflow-hidden h-32 relative border border-gray-100 rounded group" @click="previewFile('{{ $file['path'] }}', '{{ $file['name'] }}', '{{ $file['url'] }}', '{{ $file['size'] }}', '{{ $isImg ? 'image' : $ext }}')" :class="selectedFile.path === '{{ $file['path'] }}' && 'ring-2 ring-blue-500 ring-offset-1'">
                    <div class="flex-1 flex items-center justify-center p-0 w-full h-full overflow-hidden">
                        @if($isImg)
                        <img src="{{ $file['url'] }}" class="w-full h-full object-cover">
                        @else
                        <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>

        {{-- List View --}}
        <div x-show="viewMode === 'list'" class="border border-gray-200 rounded-lg overflow-hidden bg-white">
            <table class="w-full text-left text-sm text-gray-600">
                <thead class="bg-gray-50 border-b border-gray-200 text-gray-800">
                    <tr>
                        <th class="px-4 py-2 font-medium">Name</th>
                        <th class="px-4 py-2 font-medium">Size</th>
                        <th class="px-4 py-2 font-medium">Date Modified</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @if($currentPath)
                    <tr class="hover:bg-gray-50 cursor-pointer">
                        <td class="px-4 py-2" colspan="3">
                            <a href="{{ route('orchestrator.settings.assets', ['disk' => $currentDisk, 'path' => dirname($currentPath) === '.' ? '' : dirname($currentPath)]) }}" class="flex items-center gap-2 text-blue-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                                <span class="font-medium">..</span>
                            </a>
                        </td>
                    </tr>
                    @endif
                    @foreach($allDirs as $dir)
                    <tr class="hover:bg-gray-50 cursor-pointer" @click="window.location.href = '{{ route('orchestrator.settings.assets', ['disk' => $currentDisk, 'path' => $dir['path']]) }}'">
                        <td class="px-4 py-2 flex items-center gap-2">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
                            <span class="font-medium text-gray-800">{{ $dir['name'] }}</span>
                        </td>
                        <td class="px-4 py-2 text-gray-500">-</td>
                        <td class="px-4 py-2 text-gray-500">-</td>
                    </tr>
                    @endforeach
                    @foreach($allFiles as $file)
                    @php $ext = strtolower($file['extension'] ?? ''); $isImg = in_array($ext, $imageExts); @endphp
                    <tr x-show="search === '' || '{{ strtolower($file['name']) }}'.includes(search.toLowerCase())" class="hover:bg-blue-50 cursor-pointer transition-colors" @click="previewFile('{{ $file['path'] }}', '{{ $file['name'] }}', '{{ $file['url'] }}', '{{ $file['size'] }}', '{{ $isImg ? 'image' : $ext }}')" :class="selectedFile.path === '{{ $file['path'] }}' && 'bg-blue-50'">
                        <td class="px-4 py-2 flex items-center gap-2 min-w-[200px]">
                            @if($isImg)
                            <img src="{{ $file['url'] }}" class="w-6 h-6 rounded object-cover" onerror="this.style.display='none'">
                            @else
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                            @endif
                            <span class="truncate">{{ $file['name'] }}</span>
                        </td>
                        <td class="px-4 py-2 text-gray-500 whitespace-nowrap">{{ $file['size'] }}</td>
                        <td class="px-4 py-2 text-gray-500 whitespace-nowrap">{{ $file['last_modified'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if(count($allFiles) === 0 && count($allDirs) === 0 && !$currentPath)
        <div class="flex flex-col items-center justify-center py-16 text-gray-400">
            <svg class="w-16 h-16 mb-4 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            <p class="text-sm font-medium">Media library is empty</p>
            <p class="text-xs mt-1">Upload files or create folders to get started.</p>
            <button @click="showUpload = true" class="mt-4 px-4 py-2 bg-blue-600 text-white rounded text-sm font-medium hover:bg-blue-700 transition-colors">Upload Files</button>
        </div>
        @endif
        </div> <!-- End of Assets Area -->
        
        {{-- Right Sidebar Preview Pane --}}
        <div x-show="sidebarOpen" class="w-72 border-l border-gray-200 bg-[#F8F9FA] flex flex-col shrink-0 overflow-y-auto z-10 shadow-[-4px_0_15px_-3px_rgba(0,0,0,0.05)]" x-transition>
            <template x-if="selectedFile.path">
                <div class="p-6 flex flex-col items-center text-center">
                    <template x-if="selectedFile.isImage">
                        <div class="w-full bg-white border border-gray-200 rounded p-2 mb-4 shadow-sm flex items-center justify-center">
                            <img :src="selectedFile.url" class="max-w-full max-h-48 object-contain">
                        </div>
                    </template>
                    <template x-if="!selectedFile.isImage">
                        <div class="w-full aspect-square bg-white border border-gray-200 rounded p-2 mb-4 shadow-sm flex items-center justify-center text-gray-300">
                            <svg class="w-20 h-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                    </template>
                    
                    <p class="font-bold text-gray-800 break-all w-full mb-1" x-text="selectedFile.name"></p>
                    <p class="text-xs text-gray-500 mb-6" x-text="selectedFile.size + ' • ' + selectedFile.type"></p>
                    
                    <div class="w-full space-y-2 border-t border-gray-200 pt-6">
                        <a :href="selectedFile.url" target="_blank" class="w-full px-4 py-2 text-sm font-medium text-blue-600 bg-white border border-blue-200 rounded hover:bg-blue-50 transition-colors block">Open in New Tab</a>
                        <button @click="copyUrl(selectedFile.url)" class="w-full px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-50 transition-colors block">Copy Link Address</button>
                        <button @click="confirmDelete(selectedFile.path, selectedFile.name)" class="w-full px-4 py-2 text-sm font-medium text-red-600 bg-white border border-red-200 rounded hover:bg-red-50 transition-colors block mt-4">Delete Permanently</button>
                    </div>
                </div>
            </template>
            <template x-if="!selectedFile.path">
                <div class="h-full flex flex-col items-center justify-center p-6 text-gray-400">
                    <svg class="w-16 h-16 mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <p class="text-sm font-medium text-center">Select an item to view details</p>
                </div>
            </template>
        </div>
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
        showUpload: false, showFolder: false, sidebarOpen: true,
        search: '', sortBy: 'name',
        selectedFile: { name: '', path: '', url: '', size: '', type: '', isImage: false },
        deleteStep: 0, deletePath: '', deleteLabel: '', deleteConfirm: '',
        init() {},
        filteredCount() {
            return 0; // handled by Alpine x-show
        },
        previewFile(path, name, url, size, type) {
            this.selectedFile = { path, name, url, size, type, isImage: type === 'image' };
            this.sidebarOpen = true;
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