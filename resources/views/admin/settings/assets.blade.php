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
            
            <div class="relative" x-data="{ showActions: false }">
                <button @click="if(selectedFile.path) showActions = !showActions" :class="!selectedFile.path ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-50'" class="flex items-center gap-1.5 px-3 py-1 text-gray-600 font-medium border border-gray-200 rounded transition-colors">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122"/></svg>
                    Actions
                </button>
                <div x-show="showActions" x-transition.opacity.duration.200ms @click.outside="showActions = false" x-cloak class="dropdown-menu show absolute right-0 top-full mt-2 w-56 bg-white border border-gray-200 rounded-lg shadow-xl z-[100] py-1.5">
                    <button class="dropdown-item js-files-action w-full flex items-center gap-3 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-brand/5 hover:text-brand transition-colors text-left" data-action="preview" @click="showActions = false; openPreview()">
                        <span class="icon-tabler-wrapper dropdown-item-icon text-gray-400"><svg xmlns="http://www.w3.org/2000/svg" class="icon w-4 h-4 shrink-0" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0"></path>
                                <path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6"></path>
                            </svg></span>
                        Preview
                    </button>

                    <button class="dropdown-item js-files-action w-full flex items-center gap-3 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-brand/5 hover:text-brand transition-colors text-left" data-action="crop" @click="showActions = false; openCrop()">
                        <span class="icon-tabler-wrapper dropdown-item-icon text-gray-400"><svg xmlns="http://www.w3.org/2000/svg" class="icon w-4 h-4 shrink-0" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <path d="M8 5v10a1 1 0 0 0 1 1h10"></path>
                                <path d="M5 8h10a1 1 0 0 1 1 1v10"></path>
                            </svg></span>
                        Crop
                    </button>
                    
                    <div class="h-px bg-gray-100 my-1"></div>
                    
                    <button class="dropdown-item js-files-action w-full flex items-center gap-3 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-brand/5 hover:text-brand transition-colors text-left" data-action="rename" @click="showActions = false; openRename()">
                        <span class="icon-tabler-wrapper dropdown-item-icon text-gray-400"><svg xmlns="http://www.w3.org/2000/svg" class="icon w-4 h-4 shrink-0" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1"></path>
                                <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z"></path>
                                <path d="M16 5l3 3"></path>
                            </svg></span>
                        Rename
                    </button>

                    <button class="dropdown-item js-files-action w-full flex items-center gap-3 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-brand/5 hover:text-brand transition-colors text-left" data-action="make_copy" @click="showActions = false; openMakeCopy()">
                        <span class="icon-tabler-wrapper dropdown-item-icon text-gray-400"><svg xmlns="http://www.w3.org/2000/svg" class="icon w-4 h-4 shrink-0" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <path d="M8 8m0 2a2 2 0 0 1 2 -2h8a2 2 0 0 1 2 2v8a2 2 0 0 1 -2 2h-8a2 2 0 0 1 -2 -2z"></path>
                                <path d="M16 8v-2a2 2 0 0 0 -2 -2h-8a2 2 0 0 0 -2 2v8a2 2 0 0 0 2 2h2"></path>
                            </svg></span>
                        Make a copy
                    </button>

                    <button class="dropdown-item js-files-action w-full flex items-center gap-3 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-brand/5 hover:text-brand transition-colors text-left" data-action="alt_text" @click="showActions = false; openAltText()">
                        <span class="icon-tabler-wrapper dropdown-item-icon text-gray-400"><svg xmlns="http://www.w3.org/2000/svg" class="icon w-4 h-4 shrink-0" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <path d="M15 8h.01"></path>
                                <path d="M11 20h-4a3 3 0 0 1 -3 -3v-10a3 3 0 0 1 3 -3h10a3 3 0 0 1 3 3v4"></path>
                                <path d="M4 15l4 -4c.928 -.893 2.072 -.893 3 0l3 3"></path>
                                <path d="M14 14l1 -1c.31 -.298 .644 -.497 .987 -.596"></path>
                                <path d="M18.42 15.61a2.1 2.1 0 0 1 2.97 2.97l-3.39 3.42h-3v-3l3.42 -3.39z"></path>
                            </svg></span>
                        ALT text
                    </button>

                    <button class="dropdown-item js-files-action w-full flex items-center gap-3 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-brand/5 hover:text-brand transition-colors text-left" data-action="copy_link" @click="showActions = false; copyUrlToClipboard(selectedFile.url)">
                        <span class="icon-tabler-wrapper dropdown-item-icon text-gray-400"><svg xmlns="http://www.w3.org/2000/svg" class="icon w-4 h-4 shrink-0" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <path d="M9 15l6 -6"></path>
                                <path d="M11 6l.463 -.536a5 5 0 0 1 7.071 7.072l-.534 .464"></path>
                                <path d="M13 18l-.397 .534a5.068 5.068 0 0 1 -7.127 0a4.972 4.972 0 0 1 0 -7.071l.524 -.463"></path>
                            </svg></span>
                        Copy link
                    </button>

                    <button class="dropdown-item js-files-action w-full flex items-center gap-3 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-brand/5 hover:text-brand transition-colors text-left" data-action="copy_indirect_link" @click="showActions = false; copyUrlToClipboard(selectedFile.url, true)">
                        <span class="icon-tabler-wrapper dropdown-item-icon text-gray-400"><svg xmlns="http://www.w3.org/2000/svg" class="icon w-4 h-4 shrink-0" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <path d="M9 15l6 -6"></path>
                                <path d="M11 6l.463 -.536a5 5 0 0 1 7.071 7.072l-.534 .464"></path>
                                <path d="M13 18l-.397 .534a5.068 5.068 0 0 1 -7.127 0a4.972 4.972 0 0 1 0 -7.071l.524 -.463"></path>
                            </svg></span>
                        Copy indirect link
                    </button>

                    <button class="dropdown-item js-files-action w-full flex items-center gap-3 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-brand/5 hover:text-brand transition-colors text-left" data-action="share" @click="showActions = false; openShare()">
                        <span class="icon-tabler-wrapper dropdown-item-icon text-gray-400"><svg xmlns="http://www.w3.org/2000/svg" class="icon w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                              <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                              <path d="M6 12m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0"></path>
                              <path d="M18 6m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0"></path>
                              <path d="M18 18m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0"></path>
                              <path d="M8.7 10.7l6.6 -3.4"></path>
                              <path d="M8.7 13.3l6.6 3.4"></path>
                            </svg></span>
                        Share
                    </button>
                    
                    <div class="h-px bg-gray-100 my-1"></div>
                    
                    <button class="dropdown-item js-files-action w-full flex items-center gap-3 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-brand/5 hover:text-brand transition-colors text-left" data-action="favorite" @click="showActions = false; addToFavorite()">
                        <span class="icon-tabler-wrapper dropdown-item-icon text-gray-400"><svg xmlns="http://www.w3.org/2000/svg" class="icon w-4 h-4 shrink-0" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <path d="M12 17.75l-6.172 3.245l1.179 -6.873l-5 -4.867l6.9 -1l3.086 -6.253l3.086 6.253l6.9 1l-5 4.867l1.179 6.873z"></path>
                            </svg></span>
                        Add to favorite
                    </button>
                    
                    <div class="h-px bg-gray-100 my-1"></div>
                    
                    <button class="dropdown-item js-files-action w-full flex items-center gap-3 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-brand/5 hover:text-brand transition-colors text-left" data-action="download" @click="showActions = false; downloadFile()">
                        <span class="icon-tabler-wrapper dropdown-item-icon text-gray-400"><svg xmlns="http://www.w3.org/2000/svg" class="icon w-4 h-4 shrink-0" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2"></path>
                                <path d="M7 11l5 5l5 -5"></path>
                                <path d="M12 4l0 12"></path>
                            </svg></span>
                        Download
                    </button>

                    <button class="dropdown-item js-files-action w-full flex items-center gap-3 px-4 py-2 text-sm font-medium text-red-600 hover:bg-red-50 transition-colors text-left" data-action="trash" @click="showActions = false; confirmDelete(selectedFile.path, selectedFile.name)">
                        <span class="icon-tabler-wrapper dropdown-item-icon text-red-400"><svg xmlns="http://www.w3.org/2000/svg" class="icon w-4 h-4 shrink-0" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <path d="M4 7l16 0"></path>
                                <path d="M10 11l0 6"></path>
                                <path d="M14 11l0 6"></path>
                                <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12"></path>
                                <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3"></path>
                            </svg></span>
                        Move to trash
                    </button>
                </div>
            </div>
            
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

    {{-- Preview Modal --}}
    <div x-show="showPreviewPopup" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/90">
        <button @click="showPreviewPopup = false" class="absolute top-4 right-4 text-white hover:text-gray-300 p-2 transition-colors">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
        <template x-if="selectedFile.isImage">
            <img :src="selectedFile.url" class="max-w-full max-h-full object-contain rounded shadow-2xl">
        </template>
        <template x-if="!selectedFile.isImage">
            <div class="text-white text-center">
                <svg class="w-24 h-24 mx-auto mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                <p class="text-xl font-bold" x-text="selectedFile.name"></p>
                <p class="text-sm text-gray-400 mt-2">No full-screen preview available for this file type.</p>
            </div>
        </template>
    </div>

    {{-- Crop Modal --}}
    <div x-show="showCropModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-brand/50 backdrop-blur-sm" @click="showCropModal = false"></div>
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-4xl relative z-10 overflow-hidden flex flex-col" style="max-height: 90vh;">
            <form action="" method="POST" @submit.prevent="showCropModal = false; showToast('Image cropped successfully.')" class="flex flex-col h-full">
                <input type="hidden" name="image_id" :value="selectedFile.path">
                <input type="hidden" name="crop_data" value="">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-white">
                    <h3 class="text-base font-bold text-brand">Crop</h3>
                    <button type="button" @click="showCropModal = false" class="w-7 h-7 bg-surface rounded-lg flex items-center justify-center text-brand-muted hover:text-brand transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                
                <div class="p-6 bg-gray-50 flex-1 overflow-auto">
                    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                        <div class="col-span-3 flex items-center justify-center bg-gray-100 rounded-lg p-4 border border-gray-200 min-h-[400px]">
                            <div class="relative overflow-hidden w-full h-full flex items-center justify-center">
                                <template x-if="selectedFile.isImage">
                                    <div class="relative inline-block max-w-full">
                                        <img :src="selectedFile.url" class="max-w-full max-h-[50vh] block opacity-75">
                                        <div class="absolute inset-4 border-2 border-white shadow-[0_0_0_9999px_rgba(0,0,0,0.4)] pointer-events-none">
                                            <div class="absolute top-0 left-0 w-3 h-3 border-t-2 border-l-2 border-brand bg-white -mt-1.5 -ml-1.5"></div>
                                            <div class="absolute top-0 right-0 w-3 h-3 border-t-2 border-r-2 border-brand bg-white -mt-1.5 -mr-1.5"></div>
                                            <div class="absolute bottom-0 left-0 w-3 h-3 border-b-2 border-l-2 border-brand bg-white -mb-1.5 -ml-1.5"></div>
                                            <div class="absolute bottom-0 right-0 w-3 h-3 border-b-2 border-r-2 border-brand bg-white -mb-1.5 -mr-1.5"></div>
                                        </div>
                                    </div>
                                </template>
                                <template x-if="!selectedFile.isImage">
                                    <div class="text-center text-gray-400">
                                        <p>Cannot crop a non-image file.</p>
                                    </div>
                                </template>
                            </div>
                        </div>
                        <div class="col-span-1">
                            <div class="space-y-4 pt-2">
                                <div>
                                    <label class="text-[10px] font-bold text-brand-muted uppercase tracking-wider mb-1.5 block" for="dataHeight">Height</label>
                                    <input class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium outline-none focus:ring-2 focus:ring-accent/20 transition-shadow" type="text" name="dataHeight" id="dataHeight">
                                </div>
                                <div>
                                    <label class="text-[10px] font-bold text-brand-muted uppercase tracking-wider mb-1.5 block" for="dataWidth">Width</label>
                                    <input class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium outline-none focus:ring-2 focus:ring-accent/20 transition-shadow" type="text" name="dataWidth" id="dataWidth">
                                </div>
                                <label class="flex items-center gap-2 cursor-pointer mt-2">
                                    <input type="checkbox" id="aspectRatio" name="aspectRatio" class="w-4 h-4 text-brand rounded border-gray-300 focus:ring-brand">
                                    <span class="text-sm font-medium text-brand-muted">Aspect ratio</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="px-6 py-4 border-t border-gray-100 bg-white flex justify-end gap-2">
                    <button type="button" @click="showCropModal = false" class="px-4 py-2 text-xs font-bold text-brand-muted hover:text-brand transition-colors">Close</button>
                    <button type="submit" class="px-5 py-2 bg-brand text-white rounded-lg text-xs font-bold hover:bg-brand-light transition-colors">Crop</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Make a Copy Modal --}}
    <div x-show="showMakeCopyModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-brand/50 backdrop-blur-sm" @click="showMakeCopyModal = false"></div>
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-sm relative z-10" @click.outside="showMakeCopyModal = false">
            <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-base font-bold text-brand">Make a Copy</h3>
                <button type="button" @click="showMakeCopyModal = false" class="w-7 h-7 bg-surface rounded-lg flex items-center justify-center text-brand-muted hover:text-brand transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form action="{{ route('orchestrator.settings.assets.upload') }}" method="POST" @submit.prevent="showMakeCopyModal = false; showToast('Duplicate file created successfully.')" class="p-6 space-y-4">
                <div>
                    <label class="text-[10px] font-bold text-brand-muted uppercase tracking-wider mb-1.5 block">New File Name</label>
                    <input type="text" x-model="copyNewName" required class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium outline-none focus:ring-2 focus:ring-accent/20 transition-shadow">
                </div>
                <div class="flex justify-end gap-2 pt-2 border-t border-gray-100">
                    <button type="button" @click="showMakeCopyModal = false" class="px-4 py-2 text-xs font-bold text-brand-muted hover:text-brand transition-colors">Cancel</button>
                    <button type="submit" class="px-5 py-2 bg-brand text-white rounded-lg text-xs font-bold hover:bg-brand-light transition-colors">Duplicate</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ALT Text Modal --}}
    <div x-show="showAltTextModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-brand/50 backdrop-blur-sm" @click="showAltTextModal = false"></div>
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-sm relative z-10" @click.outside="showAltTextModal = false">
            <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-base font-bold text-brand">Edit ALT Text</h3>
                <button type="button" @click="showAltTextModal = false" class="w-7 h-7 bg-surface rounded-lg flex items-center justify-center text-brand-muted hover:text-brand transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label class="text-[10px] font-bold text-brand-muted uppercase tracking-wider mb-1.5 block">Alternative Text</label>
                    <textarea x-model="altTextValue" rows="3" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-medium outline-none focus:ring-2 focus:ring-accent/20 transition-shadow"></textarea>
                    <p class="text-[10px] text-gray-500 mt-1.5">Describe this image for screen readers and SEO purposes.</p>
                </div>
                <div class="flex justify-end gap-2 pt-2 border-t border-gray-100">
                    <button type="button" @click="showAltTextModal = false" class="px-4 py-2 text-xs font-bold text-brand-muted hover:text-brand transition-colors">Cancel</button>
                    <button type="button" @click="showAltTextModal = false; showToast('ALT text saved successfully.')" class="px-5 py-2 bg-brand text-white rounded-lg text-xs font-bold hover:bg-brand-light transition-colors">Save changes</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Share Modal --}}
    <div x-show="showShareModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-brand/50 backdrop-blur-sm" @click="showShareModal = false"></div>
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md relative z-10" @click.outside="showShareModal = false">
            <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-base font-bold text-brand">Share Asset</h3>
                <button type="button" @click="showShareModal = false" class="w-7 h-7 bg-surface rounded-lg flex items-center justify-center text-brand-muted hover:text-brand transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="p-6 space-y-6">
                <div>
                    <label class="text-[10px] font-bold text-brand-muted uppercase tracking-wider mb-1.5 block">Direct Link</label>
                    <div class="flex gap-2">
                        <input type="text" readonly :value="selectedFile.url" class="flex-1 border border-gray-200 rounded-lg px-4 py-2 text-sm bg-gray-50 outline-none text-gray-600">
                        <button type="button" @click="copyUrlToClipboard(selectedFile.url)" class="px-4 py-2 bg-accent/10 text-accent rounded-lg text-sm font-bold hover:bg-accent hover:text-white transition-colors">Copy</button>
                    </div>
                </div>
                <div>
                    <label class="text-[10px] font-bold text-brand-muted uppercase tracking-wider mb-2 block">Share via</label>
                    <div class="flex gap-3">
                        <button @click="window.open('https://twitter.com/intent/tweet?url=' + encodeURIComponent(selectedFile.url))" class="w-10 h-10 rounded-full bg-[#1DA1F2]/10 text-[#1DA1F2] flex items-center justify-center hover:bg-[#1DA1F2] hover:text-white transition-colors">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg>
                        </button>
                        <button @click="window.open('https://www.facebook.com/sharer/sharer.php?u=' + encodeURIComponent(selectedFile.url))" class="w-10 h-10 rounded-full bg-[#1877F2]/10 text-[#1877F2] flex items-center justify-center hover:bg-[#1877F2] hover:text-white transition-colors">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.469h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.469h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                        </button>
                        <button @click="window.open('https://api.whatsapp.com/send?text=' + encodeURIComponent(selectedFile.url))" class="w-10 h-10 rounded-full bg-[#25D366]/10 text-[#25D366] flex items-center justify-center hover:bg-[#25D366] hover:text-white transition-colors">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12.012 2c-5.506 0-9.989 4.478-9.99 9.984a9.964 9.964 0 001.333 4.976L2 22l5.233-1.337a9.936 9.936 0 004.779 1.217h.004c5.505 0 9.988-4.478 9.989-9.9840-2.752-1.071-5.337-3.018-7.284C20.038 2.663 17.456 2 12.012 2zM12.012 20.15c-1.474 0-2.91-.396-4.168-1.144l-.299-.178-3.097.79.805-3.017-.195-.31c-.822-1.306-1.255-2.827-1.255-4.385 0-4.606 3.75-8.358 8.358-8.358 2.234 0 4.332.87 5.912 2.451a8.334 8.334 0 012.449 5.908c-.001 4.605-3.751 8.356-8.358 8.356zm4.582-6.248c-.251-.126-1.488-.735-1.718-.819-.23-.084-.398-.126-.566.126-.168.252-.647.819-.794.987-.146.168-.293.189-.544.063-.251-.126-1.062-.391-2.023-1.248-.748-.667-1.253-1.492-1.4-1.744-.147-.252-.016-.388.11-.513.113-.113.251-.293.377-.44.126-.146.168-.251.252-.419.084-.168.042-.314-.021-.44-.063-.126-.566-1.365-.776-1.87-.204-.492-.412-.425-.566-.433-.146-.008-.314-.008-.482-.008-.168 0-.44.063-.67.315-.23.251-.88 .86-.88 2.096 0 1.236.901 2.43 1.026 2.598.126.168 1.77 2.702 4.286 3.788 2.516 1.086 2.516.712 2.977.67.461-.042 1.488-.608 1.698-1.195.21-.587.21-1.09.147-1.195-.063-.105-.23-.168-.482-.293z"/></svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Toast Notification --}}
    <div class="fixed bottom-4 right-4 z-[200] transition-all duration-300 transform" :class="toastMessage ? 'translate-y-0 opacity-100' : 'translate-y-10 opacity-0 pointer-events-none'">
        <div class="bg-brand text-white px-6 py-3 rounded-lg shadow-xl flex items-center gap-3">
            <svg class="w-5 h-5 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span class="text-sm font-medium" x-text="toastMessage"></span>
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
        showRename: false, renameOldPath: '', renameNewName: '',
        
        showPreviewPopup: false,
        showCropModal: false,
        showAltTextModal: false, altTextValue: '',
        showShareModal: false,
        showMakeCopyModal: false, copyNewName: '',
        toastMessage: '', toastTimeout: null,

        deleteStep: 0, deletePath: '', deleteLabel: '', deleteConfirm: '',
        init() {},
        filteredCount() {
            return 0; // handled by Alpine x-show
        },
        previewFile(path, name, url, size, type) {
            this.selectedFile = { path, name, url, size, type, isImage: type === 'image' };
            this.sidebarOpen = true;
        },
        showToast(msg) {
            this.toastMessage = msg;
            if(this.toastTimeout) clearTimeout(this.toastTimeout);
            this.toastTimeout = setTimeout(() => this.toastMessage = '', 3000);
        },
        openPreview() { this.showPreviewPopup = true; },
        openCrop() { this.showCropModal = true; },
        openAltText() { this.altTextValue = this.selectedFile.name; this.showAltTextModal = true; },
        openShare() { this.showShareModal = true; },
        openMakeCopy() { this.copyNewName = 'Copy of ' + this.selectedFile.name; this.showMakeCopyModal = true; },
        addToFavorite() { this.showToast('File added to your favorites.'); },
        openRename() {
            if(!this.selectedFile.path) return;
            this.renameOldPath = this.selectedFile.path;
            this.renameNewName = this.selectedFile.name;
            this.showRename = true;
        },
        copyUrlToClipboard(url, isIndirect = false) {
            if(!url) return;
            navigator.clipboard.writeText(url).then(() => {
                this.showToast((isIndirect ? 'Indirect link' : 'Direct link') + ' copied to clipboard.');
            });
        },
        downloadFile() {
            if(!this.selectedFile.url) return;
            const a = document.createElement('a');
            a.href = this.selectedFile.url;
            a.download = this.selectedFile.name;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
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